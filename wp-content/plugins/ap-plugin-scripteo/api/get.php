<?php
/*
Template Name: Ads Pro - API
*/

// -- START -- Errors
// 704 - invalid site url
// 705 - invalid id param
// 706 - invalid secure key
// 707 - invalid type
// -- END -- Errors

// Get Space
$id 			= ( isset($_GET['id']) ) ? $_GET['id'] : NULL;
$type 			= ( isset($_GET['type']) ) ? $_GET['type'] : NULL;
$key 			= ( isset($_GET['secure']) ) ? $_GET['secure'] : NULL;
$url1 			= ( isset($_GET['url1']) ) ? str_replace('/', '', str_replace('www.', '', str_replace('http://', '', str_replace('https://', '', $_GET['url1'])))) : NULL;
$url2 			= ( isset($_GET['url2']) ) ? str_replace('/', '', str_replace('www.', '', str_replace('http://', '', str_replace('https://', '', $_GET['url2'])))) : NULL;
$max_width 		= ( isset($_GET['max_width']) ) ? $_GET['max_width'] : NULL;
$delay 			= ( isset($_GET['delay']) ) ? $_GET['delay'] : NULL;
$padding_top 	= ( isset($_GET['padding_top']) ) ? $_GET['padding_top'] : NULL;
$attachment 	= ( isset($_GET['attachment']) ) ? $_GET['attachment'] : NULL;
$crop 			= ( isset($_GET['crop']) ) ? $_GET['crop'] : NULL;

$domain 		= ( isset($id) ? bsa_site(bsa_space($id, 'site_id'), 'url') : null);
$domain_str 	= str_replace('/', '', str_replace('www.', '', str_replace('http://', '', str_replace('https://', '', $domain))));
if ( isset($url1) && $url1 != '' ) {
    $domain1 	= str_replace('/', '', str_replace('www.', '', str_replace('http://', '', str_replace('https://', '', $url1))));
} else {
	$domain1    = null;
}
if ( isset($url2) && $url2 != '' ) {
	$domain2 	= str_replace('/', '', str_replace('www.', '', str_replace('http://', '', str_replace('https://', '', $url2))));
} else {
    $domain2 	= null;
}
$referer		= ( isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '' ? $_SERVER['HTTP_REFERER'] : null);
if ( isset($referer) && $referer != '' ) {
	$parse_ref 	= parse_url($referer);
} else {
	$parse_ref 	= null;
}
$domain3 		= (isset($parse_ref['host']) && $parse_ref['host'] != '' ? $parse_ref['host'] : null);

//echo '<pre>';
//var_dump($type);
//var_dump($key);
//var_dump($url1);
//var_dump($url2);
//var_dump($max_width);
//var_dump($delay);
//var_dump($position);
//
//var_dump($domain);
//var_dump($domain_str);
//var_dump($domain1);
//var_dump($domain2);
//var_dump(strpos($domain, $url1));
//var_dump(strpos($domain, $url2));
//var_dump(strpos($url1, $domain));
//var_dump(strpos($url2, $domain));
//echo '</pre>';

if ( isset($url1) && strpos($domain, $url1) !== false || isset($url2) && strpos($domain, $url2) !== false || bsa_space($id, 'advanced_opt') == 'multiple' && !isset($_GET['i']) ) {

	if ( isset($id) && $id != '' && bsa_space($id, 'id') != NULL && bsa_space($id, 'status') == 'active' && bsa_site(bsa_space($id, 'site_id'), 'status') == 'active' ) {

		if ( isset($key) && $key === hash('sha1', $id.$domain1.'bsa_pro') || isset($key) && $key === hash('sha1', $id.$domain2.'bsa_pro') || bsa_space($id, 'advanced_opt') == 'multiple' ) {

			// get space
			if ( $type == 'space' ) {

				echo bsa_pro_ad_space($id, $max_width, $delay, $padding_top, $attachment, $crop); // Print items

				// get css styles
			} elseif ( $type == 'styles' ) {

				echo 'get styles';

				// get js scripts
			} elseif ( $type == 'scripts' ) {

				echo 'get scripts';

				// get domain api
			} elseif ( $type == 'template' ) {

				echo bsa_space($id, 'template');

			} elseif ( $type == 'domain' ) {

				echo plugins_url();

			} else {

				echo '(error 707) No access to the API. Invalid Type.';
			}

		} else {
			echo '(error 706) No access to the API. Invalid Secure Key. Learn more about <a href="https://adspro.scripteo.info">Ads Pro</a>';
		}

	} else {
		echo '(error 705) No access to the API. Invalid Id Param. Learn more about <a href="https://adspro.scripteo.info">Ads Pro</a>';
	}

} elseif ( isset($_GET['i']) ) { // iframe

	if (isset($id) && $id != '' && bsa_space($id, 'id') != NULL && bsa_space($id, 'status') == 'active' && bsa_site(bsa_space($id, 'site_id'), 'status') == 'active') {

		if ( isset($key) && $key === hash('sha1', $id.$domain3.'bsa_pro') || bsa_space($id, 'advanced_opt') == 'multiple' ) {

			echo bsa_pro_ad_space($id, $max_width, $delay, $padding_top, $attachment, $crop); // print items

			?>
			<style>
				.apPluginContainer .bsaProHeader { font-family: Verdana, Arial, sans-serif !important; }
				.apPluginContainer .bsaProItemInner__copy { font-family: Verdana, Arial, sans-serif; }
				<?php echo file_get_contents(plugin_dir_path( __FILE__ ) . '/../frontend/css/'.bsa_space($id, 'template').'.css') ?>
				<?php echo file_get_contents(plugin_dir_path( __FILE__ ) . '/../frontend/css/asset/style.css'); ?>
				<?php echo file_get_contents(plugin_dir_path( __FILE__ ) . '/../frontend/css/asset/material-design.css'); ?>
				<?php echo (get_option('bsa_pro_plugin_custom_css') != '') ? get_option('bsa_pro_plugin_custom_css') : null; ?>
			</style>
			<?php

		} else {
			echo '(error 706) No access to the API. Invalid Secure Key. Learn more about <a href="https://adspro.scripteo.info">Ads Pro</a>';
		}

	} else {
		echo '(error 705) No access to the API. Invalid Id Param. Learn more about <a href="https://adspro.scripteo.info">Ads Pro</a>';
	}

} else {
	echo 'Success. API Page configured correctly. Use the Parser Plugin or Iframe option to display Ads on external sites. Learn more about <a href="https://adspro.scripteo.info">Ads Pro</a>.'; // 704
}
