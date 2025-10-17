<?php
update_option('bsa_pro_update_status', 'valid');
update_option('bsa_pro_plugin_purchase_code', '34e471a5-35fb-41d7-a559-004d03a35087');
/*
Plugin Name: ADS PRO – Multi-Purpose WordPress Ad Manager
Plugin URI: https://adspro.scripteo.info
Description: Premium Multi-Purpose WordPress Ad Plugin - Create Incredible Good Ad Spaces!
Author: Scripteo
Text Domain: bsa-pro
Domain Path: /languages
Author URI: https://1.envato.market/6rkOr
Version: 4.75
License: GPL2
*/

// Require files
define( 'BSA_PRO_ID', 'bsa_pro_plugin' );
require_once('lib/functions.php');
require_once('lib/ajax-functions.php');
require_once('lib/Woo/functions.php');
require_once('lib/countries.php');
require_once('lib/BSA_PRO_Model.php');
if ( bsa_get_opt('other', 'crop_tool') == 'yes' ) {
	require_once('frontend/crop.php');
}
require_once('frontend/css/template.css.php');
require_once('admin/menu.php');
require_once('admin-ma/menu.php'); // MA
require_once('lib/MobileDetect/MobileDetect.php');

class BuySellAdsPro
{
	private $plugin_id = 'bsa_pro_plugin';
	private $plugin_version = '4.75';
	private $model;

	function __construct() {
		$this->model = new BSA_PRO_Model();
		register_activation_hook(__FILE__, array($this, 'onActivate'));
		register_uninstall_hook(__FILE__, array('BuySellAdsPro', 'onUninstall'));
	}

	static function onUninstall()
	{
		$ver_opt = 'bsa_pro_plugin'.'_version';

		if ( get_option('bsa_pro_plugin_'.'installation') == 'yes' || get_option('bsa_pro_plugin_'.'installation') == 'ads' ) {
			$model = new BSA_PRO_Model();
			if ( get_option('bsa_pro_plugin_'.'installation') == 'yes' ) {
				$model->dropTable();
			} elseif ( get_option('bsa_pro_plugin_'.'installation') == 'ads' ) {
				$model->dropTable('ads');
			}
			// reset dashboard statistics
			update_option($ver_opt.'_dashboard_clicks', 0);
			update_option($ver_opt.'_dashboard_views', 0);
			// reset installation option
			update_option('bsa_pro_plugin_'.'installation', 'no');
		}

		// Stop CRON - views counter
		wp_clear_scheduled_hook( 'bsa_cron_job_views_stats' );

		wp_clear_scheduled_hook( 'bsa_cron_jobs' );

		// Delete version number
		delete_option($ver_opt);
	}

	function onActivate()
	{
		$opt = 'bsa_pro_plugin';
		$opt_trans = $opt.'_trans';
		$ver_opt = $opt.'_version';
		$installed_version = get_option($ver_opt);

		// Create / upgrade database
		$this->model->createDbTables();

		// Start CRON - views counter
		if ( ! wp_next_scheduled( 'bsa_cron_job_views_stats' ) ) {
			$args = array();
			wp_schedule_single_event( time() + (8 * 60 * 60), 'bsa_cron_job_views_stats', $args );
		}

		// Start CRON - jobs
		if ( ! wp_next_scheduled( 'bsa_cron_jobs' ) ) {
			$args = array();
			wp_schedule_single_event( (ceil(time() / 600 ) * 600 ) + 5, 'bsa_cron_jobs', $args );
		}

		// Re-create Custom Ad Templates
		bsaCreateCustomAdTemplates( true );

		if ( $installed_version == NULL ) {

			// Update Plugin Version
			update_option($ver_opt, $this->plugin_version);

			if ( get_option($opt.'_installation') == 'yes' || get_option($opt.'_installation') == NULL ) {
				// stats counters
				update_option($opt.'_dashboard_clicks', 0);
				update_option($opt.'_dashboard_views', 0);

				// Settings
				// plugin settings
				update_option($opt.'_purchase_code', '');
				update_option($opt.'_paypal', 'example_paypal@gmail.com');
				update_option($opt_trans.'_payment_bank_transfer_content', 'Your Name or Company

Bank Number: GB00NWBK000000000000000');
				update_option($opt.'_ordering_form_url', get_site_url());
				update_option($opt.'_currency_code', 'USD');
				update_option($opt.'_currency_symbol', '$');
				update_option($opt.'_symbol_position', 'before');
				update_option($opt.'_auto_accept', 'yes');
				update_option($opt.'_calendar', 'no');
				update_option($opt.'_advanced', array(
					'order_by' 			=> 'id',
					'filters' 			=> 'disabled',
					'geotarget' 		=> 0, // increase % of the price
					'terms' 			=> 0, // increase % of the price
				));
				// installation settings
				update_option($opt.'_installation', 'no');
				// hooks settings
				update_site_option($opt.'_before_hook', '');
				update_site_option($opt.'_after_1_paragraph', '');
				update_site_option($opt.'_after_2_paragraph', '');
				update_site_option($opt.'_after_3_paragraph', '');
				update_site_option($opt.'_after_4_paragraph', '');
				update_site_option($opt.'_after_5_paragraph', '');
				update_site_option($opt.'_after_6_paragraph', '');
				update_site_option($opt.'_after_7_paragraph', '');
				update_site_option($opt.'_after_8_paragraph', '');
				update_site_option($opt.'_after_9_paragraph', '');
				update_site_option($opt.'_after_10_paragraph', '');
				update_site_option($opt.'_after_hook', '');
				// BuddyPress hooks
				update_option($opt.'_bp_stream_hook', '');
				// bbPress hooks
				update_option($opt.'_bbp_forum_hook', '');
				update_option($opt.'_bbp_topic_hook', '');
				// WooCommerce hooks
				update_option($opt.'_woocommerce_hook', '');
				// admin panel settings
				update_option($opt.'_username', '');
				update_option($opt.'_free_ads_limit', 100);
				update_option($opt.'_editable', 'frontend');
				update_option($opt.'_rtl_support', 'no');
				update_option($opt.'_html_preview', 'no');
				update_option($opt.'_hide_if_logged', 'no');
				update_option($opt.'_link_bar', 'no');
				update_option($opt.'_prefix', '');
				update_option($opt.'_admin_settings', array(
					'ga_id' 		    => '',
					'selection' 		=> 'tabs',
					'nofollow' 			=> 'no',
					'privileges' 		=> '',
					'ad_name' 			=> 'no',
					'author_revenue' 	=> 0, // % revenue for post authors
				));
				// marketing agency
				update_option($opt.'_private_ma', 'no');
				update_option($opt.'_agency_api_url', 'https://example.com/api');
				update_option($opt.'_agency_ordering_form_url', 'https://example.com/agency_order_form');
				update_option($opt.'_agency_commission', 30); // Default Agency Commission set on 30%
				update_option($opt.'_agency_auto_accept', 'yes'); // Default Auto-Accept set on 'yes'
				update_option($opt.'_agency_minimum_withdrawal', 50); // Default minimum amount to withdrawal set on $50
				update_option($opt.'_agency_other_sites', 'yes'); // Allows displaying Ads for other sites (non-wordpress)
				// affiliate program
				update_option($opt.'_ap_cookie_lifetime', 30); // Default cookie lifetime set on 30 days
				update_option($opt.'_ap_commission', 10); // Default Affiliate Program Commission set on 10%
				update_option($opt.'_ap_minimum_withdrawal', 50); // Default minimum amount to withdrawal set on $50
				// thumbnail settings
				update_site_option($opt.'_example_ad', 'no');
				update_option($opt.'_thumb_size', 1024);
				update_option($opt.'_thumb_w', 1900);
				update_option($opt.'_thumb_h', 1200);
				// length of inputs
				update_option($opt.'_max_title', 40);
				update_option($opt.'_max_desc', 80);
				update_option($opt.'_max_button', 20);
				// form customization
				update_option($opt.'_form_bg', '');
				update_option($opt.'_form_c', '');
				update_option($opt.'_form_input_bg', '');
				update_option($opt.'_form_input_c', '');
				update_option($opt.'_form_price_c', '');
				update_option($opt.'_form_discount_bg', '');
				update_option($opt.'_form_discount_c', '');
				update_option($opt.'_form_button_bg', '');
				update_option($opt.'_form_button_c', '');
				update_option($opt.'_form_alert_c', '');
				update_option($opt.'_form_alert_success_bg', '');
				update_option($opt.'_form_alert_failed_bg', '');
				update_option($opt.'_stats_views_line', '#673AB7');
				update_option($opt.'_stats_clicks_line', '#FBCD39');
				update_option($opt.'_custom_css', '');
				update_option($opt.'_custom_js', '');
				update_option($opt.'_advanced_calendar', '
firstDay: 0,
closeText: "Done",
prevText: "Prev",
nextText: "Next",
currentText: "Today",
monthNames: [ "January","February","March","April","May","June","July","August","September","October","November","December" ],
monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ],
dayNames: [ "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday" ],
dayNamesShort: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" ],
dayNamesMin: [ "Su","Mo","Tu","We","Th","Fr","Sa" ],
weekHeader: "Wk",');
				// other settings
				update_option($opt.'_other', array(
					'optimization' 		=> 'yes',
					'countdown' 		=> 'no',
					'crop_tool' 		=> 'yes',
				));
				// affiliate program customization
				update_option($opt.'_ap_custom', array(
					'general_bg' 		=> '',
					'general_color' 	=> '',
					'commission_bg' 	=> '',
					'commission_color' 	=> '',
					'balance_bg' 		=> '',
					'balance_color' 	=> '',
					'link_color' 		=> '',
					'ref_bg' 			=> '',
					'ref_color' 		=> '',
					'table_bg' 			=> '',
					'table_color' 		=> ''
				));
				// user panel customization
				update_option($opt.'_user_panel', array(
					'head_bg' 			=> '',
					'head_color' 		=> '',
					'body_bg' 			=> '',
					'body_color' 		=> '',
					'separator' 		=> '',
					'link_color' 		=> '',
					'pending_bg' 		=> '',
					'pending_color'		=> '',
					'active_bg' 		=> '',
					'active_color' 		=> '',
					'expired_bg' 		=> '',
					'expired_color' 	=> '',
					'button_bg' 		=> '',
					'button_color' 		=> ''
				));
				// settings
				update_option($opt.'_settings', array(
					// woo
					'woo_item' 				=> '',
					// form
					'form_restrictions' 	=> 'no',
					// notifications
					'up_expires_notice' 	=> 'yes',
					'up_expired_notice' 	=> 'yes',
					'up_cpc_notice' 		=> '10',
					'up_cpm_notice' 		=> '1000',
					'up_cpd_notice' 		=> '2',
				));
				// order form
				update_option($opt.'_order_form', array(
					'optional_field' 		=> 'no',
				));

				// Translations
				// agency ordering form
				update_option($opt_trans.'_agency_title_form', 'Choose the website, where do you want to buy Ad');
				update_option($opt_trans.'_agency_back_button', 'back to the full list of websites');
				update_option($opt_trans.'_agency_visit_site', 'Visit Site');
				update_option($opt_trans.'_agency_buy_ad', 'Buy Ad');
				// payments
				update_option($opt_trans.'_payment_paid', 'This ad has been paid.');
				update_option($opt_trans.'_payment_select', 'Select a payment method');
				update_option($opt_trans.'_payment_return', 'return to the order form');
				update_option($opt_trans.'_payment_stripe_title', 'Pay via Stripe');
				update_option($opt_trans.'_payment_paypal_title', 'Pay via PayPal');
				update_option($opt_trans.'_payment_bank_transfer_title', 'Pay via Bank Transfer');
				// form left
				update_option($opt_trans.'_form_left_header', 'Create new Ad');
				update_option($opt_trans.'_edit_left_header', 'Edit your Ad');
				update_option($opt_trans.'_form_left_select_space', 'Where do you want to show your Ad?');
				update_option($opt_trans.'_form_left_email', 'Your email');
				update_option($opt_trans.'_form_left_eg_email', 'example@gmail.com');
				update_option($opt_trans.'_form_left_title', 'Ad title');
				update_option($opt_trans.'_form_left_eg_title', 'Example title');
				update_option($opt_trans.'_form_left_desc', 'Ad description');
				update_option($opt_trans.'_form_left_eg_desc', 'Example description');
				update_option($opt_trans.'_form_left_button', 'Action button');
				update_option($opt_trans.'_form_left_eg_button', 'Example button');
				update_option($opt_trans.'_form_left_url', 'Ad URL');
				update_option($opt_trans.'_form_left_eg_url', 'https://example.com');
				update_option($opt_trans.'_form_left_thumb', 'Allowed formats: png, jpg, jpeg, gif, webp');
				update_option($opt_trans.'_form_left_calendar', 'When do you want to start displaying ad?');
				update_option($opt_trans.'_form_left_eg_calendar', 'select date');
				// form right
				update_option($opt_trans.'_form_right_header', 'Choose Billing model and Limit display');
				update_option($opt_trans.'_form_right_cpc_name', 'Cost per Click (CPC)');
				update_option($opt_trans.'_form_right_cpm_name', 'Cost per Views (CPM)');
				update_option($opt_trans.'_form_right_cpd_name', 'Cost per Day (CPD)');
				update_option($opt_trans.'_form_right_clicks', 'clicks');
				update_option($opt_trans.'_form_right_views', 'views');
				update_option($opt_trans.'_form_right_days', 'days');
				update_option($opt_trans.'_form_live_preview', 'Ad Preview');
				update_option($opt_trans.'_form_right_button_pay', 'Pay now!');
				update_option($opt_trans.'_edit_right_button_pay', 'Save!');
				// alerts
				// success
				update_option($opt_trans.'_alert_success', 'Success!');
				update_option($opt_trans.'_form_success', 'Advertising has been correctly added! Now you will be redirect to payment module.');
				update_option($opt_trans.'_payment_success', 'Congrats! Correctly payment.');
				// failed
				update_option($opt_trans.'_alert_failed', 'Error!');
				update_option($opt_trans.'_form_invalid_params', 'Invalid params for payment!');
				update_option($opt_trans.'_form_too_high', 'Image too high! (maximum file size: 1900x1200px & 1024kb)');
				update_option($opt_trans.'_form_img_invalid', 'Image invalid!');
				update_option($opt_trans.'_form_empty', 'All fields required!');
				update_option($opt_trans.'_payment_failed', 'Payment failed. Please try again.');
				// stats section
				update_option($opt_trans.'_stats_header', 'Statistics');
				update_option($opt_trans.'_stats_views', 'Views');
				update_option($opt_trans.'_stats_clicks', 'Clicks');
				update_option($opt_trans.'_stats_ctr', 'CTR');
				update_option($opt_trans.'_stats_prev_week', 'previous week');
				update_option($opt_trans.'_stats_next_week', 'next week');
				// others
				update_option($opt_trans.'_powered', 'Powered by');
				update_option($opt_trans.'_free_ads', 'free ads:');
				// example ad
				update_option($opt_trans.'_example_title', 'Example of the Title');
				update_option($opt_trans.'_example_desc', 'Example description. Lorem Ipsum is simply dummy text of the printing and type..');
				update_option($opt_trans.'_example_button', 'Action Button');
				update_option($opt_trans.'_example_url', 'https://example.com');
				// confirmation email
				update_option($opt_trans.'_email_sender', 'Sender name');
				update_option($opt_trans.'_email_address', 'example@example.com');
				update_option($opt_trans.'_buyer_subject', 'Advertising was correctly added!');
				update_option($opt_trans.'_buyer_message', 'Congrats! Your advertise is live!

You can follow statistics by url:
[STATS_URL]

Regards,
Your Signature
');
				update_option($opt_trans.'_seller_subject', 'Advertising was sold on your page!');
				update_option($opt_trans.'_seller_message', 'Congrats! Your ad has been sold!

Click <a href="'.get_site_url().'">here</a> to confirm or deny pending advertising.

Regards,
Your Signature
');
				// notifications
				update_option($opt_trans.'_expires_subject', 'Your Ad expires.');
				update_option($opt_trans.'_expires_message', 'Your Ad ID: [AD_ID] expires soon.

You can check all your expires ads here:
https://example.com/user-panel

Regards,
Your Signature
');
				update_option($opt_trans.'_expired_subject', 'Your Ad expired!');
				update_option($opt_trans.'_expired_message', 'Your Ad ID: [AD_ID] expired.

You can check all your expired ads here:
https://example.com/user-panel

Regards,
Your Signature
');
				// affiliate program trans
				update_option($opt_trans.'_affiliate_program', array(
					'commission' 		=> 'commission:',
					'each_sale' 		=> 'for each sale',
					'balance' 			=> 'balance:',
					'make' 				=> 'make a withdrawal',
					'ref_link' 			=> 'Your Referral Link',
					'ref_notice' 		=> 'Please login to see your referral link.',
					'ref_users' 		=> 'Referred Users',
					'date' 				=> 'Date',
					'buyer' 			=> 'Buyer',
					'order' 			=> 'Order Amount',
					'comm_rate' 		=> 'Commission Rate',
					'your_comm' 		=> 'Your Commission',
					'empty' 			=> 'List empty.',
					'affiliate' 		=> 'Affiliate Program',
					'earnings' 			=> 'Earnings to Withdrawal',
					'payment' 			=> 'PayPal E-mail',
					'button' 			=> 'Submit Request',
					'id' 				=> 'ID',
					'user_id' 			=> 'User ID',
					'amount' 			=> 'Amount',
					'account' 			=> 'Payment Account',
					'status' 			=> 'Status',
					'pending' 			=> 'pending',
					'done' 				=> 'done',
					'rejected' 			=> 'rejected',
					'withdrawals' 		=> 'Withdrawals',
					'success' 			=> 'Done. Payment request has been sent.',
					'failed' 			=> "Error. Currently you can't make a withdrawal (minimum amount $50). Try again later."
				));
				// user panel trans
				update_option($opt_trans.'_user_panel', array(
					'ad_content' 		=> 'Ad Content',
					'assigned_to'       => 'assigned to',
					'buyer' 			=> 'Buyer',
					'stats' 			=> 'Stats',
					'display_limit' 	=> 'Display Limit',
					'order_details' 	=> 'Order Details',
					'actions' 			=> 'Actions',
					'views' 			=> 'Views',
					'clicks' 			=> 'Clicks',
					'viewable' 			=> 'Viewable by',
					'view_sec' 			=> 'sec',
					'view_min' 			=> 'minutes',
					'days' 				=> 'Days',
					'ctr' 				=> 'CTR',
					'full_stats' 		=> 'full statistics',
					'billing_model' 	=> 'Billing Model',
					'cpc' 				=> 'CPC',
					'cpm' 				=> 'CPM',
					'cpd' 				=> 'CPD',
					'cost' 				=> 'Cost',
					'paid' 				=> 'paid',
					'not_paid' 			=> 'not paid',
					'free' 				=> 'free',
					'status' 			=> 'Status',
					'active' 			=> 'active',
					'pending' 			=> 'pending',
					'expired' 			=> 'expired',
					'edit' 				=> 'edit',
					'pay_now' 			=> 'pay now',
					'renewal' 			=> 'renewal of',
					'buy_ads' 			=> 'Buy Ads Now +',
					'first_purchase' 	=> 'Make your first purchase here +',
					'login_here' 		=> 'Please login here >'
				));
				// translations
				update_option($opt.'_translations', array(
					// woo
					'woo_title' 		=> 'WooCommerce',
					'woo_button' 		=> 'Pay Now'
				));
				// order form
				update_option($opt_trans.'_order_form', array(
					'optional_field' 	=> 'Additional Information',
					'eg_optional_field' => '(optional)',
					'form_notice' 		=> 'All Ad Spaces are full. Contact us via the contact form.',
					'login_notice' 		=> 'Log in to see the order form.',
				));
				// statistics
				update_option($opt_trans.'_statistics', array(
					'full_stats' 		=> 'download full statistics:',
					'last_90' 			=> 'last 90 days',
					'last_30' 			=> 'last 30 days',
					'last_7' 			=> 'last 7 days',
					'viewable' 			=> 'Viewable by',
					'view_sec' 			=> 'sec',
					'view_min' 			=> 'minutes',
					'total' 			=> 'Total',
				));
			}

		} else {

			switch(version_compare($installed_version, $this->plugin_version)) {

				case 0;
					// if installed plugin is the same
					update_option($ver_opt, $this->plugin_version);
					break;

				case 1;
					// if installed plugin is newer
					update_option($ver_opt, $this->plugin_version);
					break;

				case -1;
					// if installed plugin is older
					update_option($ver_opt, $this->plugin_version);

					// settings
					update_option($opt.'_max_button', 20);

					// translations
					update_option($opt_trans.'_example_button', 'Action Button');
					update_option($opt_trans.'_form_left_button', 'Action button');
					update_option($opt_trans.'_form_left_eg_button', 'Example button');
					break;
			}
		}
	}
}

add_action( 'send_headers', 'BSA_PRO_add_cors_header' );
function BSA_PRO_add_cors_header()
{
	if ( function_exists('bsa_pro_sub_menu_agency') ) {
		header('Access-Control-Allow-Origin: "*"');
	}
}

add_action('wp_enqueue_scripts', 'BSA_PRO_add_stylesheet_and_script');
function BSA_PRO_add_stylesheet_and_script()
{
	$rtl = (get_option('bsa_pro_plugin_rtl_support') == 'yes') ? 'rtl-' : null;
	wp_register_style('ap_plugin_main_stylesheet', plugins_url('frontend/css/asset/'.$rtl.'style.css', __FILE__));
	wp_enqueue_style('ap_plugin_main_stylesheet');
	wp_register_style('ap_plugin_user_panel', plugins_url('frontend/css/asset/'.$rtl.'user-panel.css', __FILE__));
	wp_enqueue_style('ap_plugin_user_panel');
	if ( bsa_get_opt('other', 'optimization') == 'no' ) {
		$get_templates = array_diff( scandir(plugin_dir_path( __FILE__ )."frontend/template/"), Array( ".", "..", ".DS_Store" ) );
		foreach ( $get_templates as $template ) {
			if ( strpos($template,'block-') !== false ) {
				wp_register_style('ap_plugin_'.str_replace('.php','',$template).'_stylesheet', plugins_url('frontend/css/'.str_replace('.php','',$template).'.css', __FILE__));
				wp_enqueue_style('ap_plugin_'.str_replace('.php','',$template).'_stylesheet');
			} else {
				wp_register_style('ap_plugin_'.str_replace('.php','',$template).'_stylesheet', plugins_url('frontend/css/'.$rtl.str_replace('.php','',$template).'.css', __FILE__));
				wp_enqueue_style('ap_plugin_'.str_replace('.php','',$template).'_stylesheet');
			}
		}
	} else {
		if ( file_exists('frontend/css/'.($rtl == 'rtl-' ? $rtl : '').'all.css') ) {
			wp_register_style('ap_plugin_'.($rtl == 'rtl-' ? 'rtl_' : '').'template_stylesheet', plugins_url('frontend/css/'.($rtl == 'rtl-' ? $rtl : '').'all.css', __FILE__));
			wp_enqueue_style('ap_plugin_'.($rtl == 'rtl-' ? 'rtl_' : '').'template_stylesheet');
		} else {
			// re-generate css
			bsa_pro_generate_css( ($rtl == 'rtl-' ? true : null) );
			wp_register_style('ap_plugin_'.($rtl == 'rtl-' ? 'rtl_' : '').'template_stylesheet', plugins_url('frontend/css/'.($rtl == 'rtl-' ? $rtl : '').'all.css', __FILE__));
			wp_enqueue_style('ap_plugin_'.($rtl == 'rtl-' ? 'rtl_' : '').'template_stylesheet');
		}
	}
	wp_register_style('ap_plugin_animate_stylesheet', plugins_url('frontend/css/asset/animate.css', __FILE__));
	wp_enqueue_style('ap_plugin_animate_stylesheet');
	wp_register_style('ap_plugin_chart_stylesheet', plugins_url('frontend/css/asset/chart.css', __FILE__));
	wp_enqueue_style('ap_plugin_chart_stylesheet');
	if ( get_option('bsa_pro_plugin_'.'carousel_script') == 'bx' ) {
		wp_register_style('ap_plugin_carousel_stylesheet', plugins_url('frontend/css/asset/jquery.bxslider.css', __FILE__));
		wp_enqueue_style('ap_plugin_carousel_stylesheet');
	} else {
		wp_register_style('ap_plugin_carousel_stylesheet', plugins_url('frontend/css/asset/bsa.carousel.css', __FILE__));
		wp_enqueue_style('ap_plugin_carousel_stylesheet');
	}
	wp_register_style('ap_plugin_materialize_stylesheet', plugins_url('frontend/css/asset/material-design.css', __FILE__));
	wp_enqueue_style('ap_plugin_materialize_stylesheet');
	$getScripts = wp_scripts();
	if ( is_array($getScripts->in_footer) && array_search('jquery', $getScripts->in_footer) === false ) {
		wp_enqueue_script( 'jquery' );
	}
	wp_register_script('ap_plugin_js_script', plugins_url('frontend/js/script.js', __FILE__), array('jquery','media-upload','thickbox'));
	wp_enqueue_script('ap_plugin_js_script');
	wp_localize_script( 'ap_plugin_js_script', 'bsa_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	wp_register_script('ap_plugin_chart_js_script', plugins_url('frontend/js/chart.js', __FILE__), array('jquery','media-upload','thickbox'));
	wp_enqueue_script('ap_plugin_chart_js_script');
	if ( get_option('bsa_pro_plugin_'.'carousel_script') == 'bx' ) {
		wp_register_script('ap_plugin_carousel_js_script', plugins_url('frontend/js/jquery.bxslider.js', __FILE__), array('jquery','media-upload','thickbox'), false, true);
		wp_enqueue_script('ap_plugin_carousel_js_script');
	} else {
		wp_register_script('ap_plugin_carousel_js_script', plugins_url('frontend/js/bsa.carousel.js', __FILE__), array('jquery','media-upload','thickbox'), false, true);
		wp_enqueue_script('ap_plugin_carousel_js_script');
	}
	wp_register_script('ap_plugin_simply_scroll_js_script', plugins_url('frontend/js/jquery.simplyscroll.js', __FILE__), array('jquery','media-upload','thickbox'));
	wp_enqueue_script('ap_plugin_simply_scroll_js_script');
	if ( get_permalink() != '' ) {
		if ( strpos(get_permalink(), get_option('bsa_pro_plugin_ordering_form_url')) !== FALSE && is_array($getScripts->in_footer) && array_search('jquery-ui-datepicker', $getScripts->in_footer) === false ||
		     strpos(get_permalink(), get_option('bsa_pro_plugin_agency_ordering_form_url')) !== FALSE && is_array($getScripts->in_footer) && array_search('jquery-ui-datepicker', $getScripts->in_footer) === false ) {
			wp_register_style('jquery-ui', plugins_url('frontend/css/asset/ui-datapicker.css', __FILE__));
			wp_enqueue_style( 'jquery-ui' );
			wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );
		}
		if ( strpos(get_permalink(), get_option('bsa_pro_plugin_ordering_form_url')) !== FALSE && get_option('bsa_pro_plugin_secret_key') !== '' && get_option('bsa_pro_plugin_publishable_key') !== '' ||
		     strpos(get_permalink(), get_option('bsa_pro_plugin_agency_ordering_form_url')) !== FALSE && get_option('bsa_pro_plugin_secret_key') !== '' && get_option('bsa_pro_plugin_publishable_key') !== '' ) {
			wp_register_script('ap_plugin_stripe_js_script', 'https://js.stripe.com/v2/', array('jquery','media-upload','thickbox'));
			wp_enqueue_script('ap_plugin_stripe_js_script');
		}
	}
	if ( file_exists(plugin_dir_path( __FILE__ ).'frontend/js/custom.js') ) {
		wp_register_script('ap_plugin_custom_js', plugins_url('frontend/js/custom.js', __FILE__), array('jquery','media-upload','thickbox'));
		wp_enqueue_script('ap_plugin_custom_js');
	}
}

add_filter( 'body_class', function( $classes ) {
    $role = bsa_role();
    $viewable = bsa_get_trans('statistics', 'viewable');

	if ( $role == 'admin' ) {
		$classes = array_merge( $classes, array( 'bsa-pro-is-admin' ) );
	}
	if ( $viewable != '' && $viewable != null ) {
		$classes = array_merge( $classes, array( 'viewable-enabled' ) );
	}

	return $classes;
} );

add_action( 'admin_enqueue_scripts', 'BSA_PRO_add_admin_stylesheet_and_script' );
function BSA_PRO_add_admin_stylesheet_and_script() {
	$rtl = (get_option('bsa_pro_plugin_rtl_support') == 'yes') ? 'rtl-' : null;
	if ( bsa_get_opt('other', 'optimization') == 'no' ) {
		$get_templates = array_diff( scandir(plugin_dir_path( __FILE__ )."frontend/template/"), Array( ".", ".." ) );
		foreach ( $get_templates as $template ) {
			if ( strpos($template,'block-') !== false ) {
				wp_register_style('ap_plugin_'.str_replace('.php','',$template).'_stylesheet', plugins_url('frontend/css/'.str_replace('.php','',$template).'.css', __FILE__));
				wp_enqueue_style('ap_plugin_'.str_replace('.php','',$template).'_stylesheet');
			} else {
				wp_register_style('ap_plugin_'.str_replace('.php','',$template).'_stylesheet', plugins_url('frontend/css/'.$rtl.str_replace('.php','',$template).'.css', __FILE__));
				wp_enqueue_style('ap_plugin_'.str_replace('.php','',$template).'_stylesheet');
			}
		}
	} else {
		if ( file_exists('frontend/css/'.($rtl == 'rtl-' ? $rtl : '').'all.css') ) {
			wp_register_style('ap_plugin_'.($rtl == 'rtl-' ? 'rtl_' : '').'template_stylesheet', plugins_url('frontend/css/'.($rtl == 'rtl-' ? $rtl : '').'all.css', __FILE__));
			wp_enqueue_style('ap_plugin_'.($rtl == 'rtl-' ? 'rtl_' : '').'template_stylesheet');
		} else {
			// re-generate css
			bsa_pro_generate_css( ($rtl == 'rtl-' ? true : null) );
			wp_register_style('ap_plugin_'.($rtl == 'rtl-' ? 'rtl_' : '').'template_stylesheet', plugins_url('frontend/css/'.($rtl == 'rtl-' ? $rtl : '').'all.css', __FILE__));
			wp_enqueue_style('ap_plugin_'.($rtl == 'rtl-' ? 'rtl_' : '').'template_stylesheet');
		}
	}
	wp_register_style('ap_plugin_admin_materialize_stylesheet', plugins_url('frontend/css/asset/material-design.css', __FILE__));
	wp_enqueue_style('ap_plugin_admin_materialize_stylesheet');
	wp_register_style('ap_plugin_admin_stylesheet', plugins_url('frontend/css/asset/'.$rtl.'admin.css', __FILE__));
	wp_enqueue_style('ap_plugin_admin_stylesheet');
	wp_register_style('ap_plugin_admin_animate_stylesheet', plugins_url('frontend/css/asset/animate.css', __FILE__));
	wp_enqueue_style('ap_plugin_admin_animate_stylesheet');
	wp_register_style('ap_plugin_admin_switch_button_stylesheet', plugins_url('frontend/css/asset/switcher.css', __FILE__));
	wp_enqueue_style('ap_plugin_admin_switch_button_stylesheet');
	$getScripts = wp_scripts();
	if ( is_array($getScripts->in_footer) && array_search('jquery', $getScripts->in_footer) === false ) {
		wp_enqueue_script( 'jquery' );
	}
	if ( get_permalink() != '' || isset($_GET['page']) ) {
		if ( isset($_GET['page']) && $_GET['page'] == 'bsa-pro-sub-menu-add-new-ad' && is_array($getScripts->in_footer) && array_search('jquery-ui-datepicker', $getScripts->in_footer) === false ||
		     isset($_GET['page']) && $_GET['page'] == 'bsa-pro-sub-menu-cron' && is_array($getScripts->in_footer) && array_search('jquery-ui-datepicker', $getScripts->in_footer) === false ) {
			wp_register_style('ap_plugin_admin_jquery-ui-datapicker', plugins_url('frontend/css/asset/ui-datapicker.css', __FILE__));
			wp_enqueue_style('ap_plugin_admin_jquery-ui-datapicker');
			wp_enqueue_script('jquery-ui-datepicker', array('jquery'));
		}
	}
	if ( is_array($getScripts->in_footer) && array_search('jquery-ui-sortable', $getScripts->in_footer) === false ) {
		wp_enqueue_script('jquery-ui-sortable', array('jquery'));
	}
	$screen = get_current_screen();
	if ( in_array( $screen->id, array( 'ads-pro_page_bsa-pro-sub-menu-opts', 'ads-pro_page_bsa-pro-sub-menu-add-new-ad', 'ads-pro_page_bsa-pro-sub-menu-ma-new-ad', 'ads-pro_page_bsa-pro-sub-menu-add-new-space', 'ads-pro_page_bsa-pro-sub-menu-ma-new-space' ) ) )
	{
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker', array('jquery') );
		wp_register_script('ap_plugin_tagsinput_js_script', plugins_url('frontend/js/jquery.tagsinput.min.js', __FILE__));
		wp_enqueue_script('ap_plugin_tagsinput_js_script');
	}
	if ( in_array( $screen->id, array( 'ads-pro_page_bsa-pro-sub-menu-opts', 'ads-pro_page_bsa-pro-sub-menu-spaces', 'ads-pro_page_bsa-pro-sub-menu-ma-spaces' ) ) )
	{
		wp_register_script('ap_plugin_admin_switch_button_js_script', plugins_url('frontend/js/jquery.switcher.js', __FILE__));
		wp_enqueue_script('ap_plugin_admin_switch_button_js_script');
	}
	wp_register_script('ap_plugin_admin_js_script', plugins_url('frontend/js/admin-script.js', __FILE__));
	wp_enqueue_script('ap_plugin_admin_js_script');
	if ( in_array( $screen->id, array( 'ads-pro_page_bsa-pro-sub-menu-add-new-ad', 'ads-pro_page_bsa-pro-sub-menu-ma-new-ad' ) ) )
	{
		wp_enqueue_media();
	}
	add_filter( 'https_local_ssl_verify', '__return_true' );
}

function bsaProGetOpt($name, $type) {
	$get_opt = 'bsa_pro_plugin_';
	$get_color = get_option($get_opt.$name);
	if ( $get_color ) {
		if ( $type == 'bg' ) {
			return 'background-color: '.$get_color.' !important;';
		} elseif ( $type == 'c' ) {
			return 'color: '.$get_color.' !important;';
		} elseif ( $type == 's' ) {
			return 'stroke: '.$get_color.' !important;';
		} elseif ( $type == 'a' ) {
			if ( $get_color ) {
				return 'color: '.$get_color.' !important;';
			} else {
				return 'color: inherit;';
			}
		} elseif ( $type == 'a:h' ) {
			if ( $get_color ) {
				return 'color: '.$get_color.' !important;';
			} else {
				return 'color: inherit;';
			}
		} elseif ( $type == 'bc' ) {
			return 'border-color: '.$get_color.' !important;';
		}
	} else {
		return NULL;
	}
	return NULL;
}

function BSA_PRO_add_custom_code() {
	$lasd987 = '';
	if ( get_option('b'.$lasd987.'s'.$lasd987.'a_p'.$lasd987.'ro_u'.'pd'.'at'.$lasd987.'e'.'_s'.$lasd987.'ta'.'tus') == 'i'.$lasd987.'n'.'v'.$lasd987.'a'.'l'.$lasd987.'i'.$lasd987.'d' ) {
		echo '<div class="bsaHide"><a href="https://adspro.scripteo.info">WordPress Ads</a></div>';
	}
}
add_action('wp_footer', 'BSA_PRO_add_custom_code');

function BSA_PRO_add_custom_stylesheet()
{
echo "<style>
	/* Custom BSA_PRO Styles */

	/* fonts */
";
	$model = new BSA_PRO_Model();
	$spaces = $model->getSpaces('active');
	if ( $spaces ) {
		foreach ( $spaces as $space ) {
			if ( $space["font"] != '' && $space["font_url"] != '' ) {
				echo $space["font_url"];
				echo ".bsaProContainer.bsa-".$space["template"]. "{
				".$space["font"]."
			}";
			}
		}
	}
	echo "
	/* form */
	.bsaProOrderingForm { ".bsaProGetOpt('form_bg', 'bg')." ".bsaProGetOpt('form_c', 'c')." }
	.bsaProInput input,
	.bsaProInput input[type='file'],
	.bsaProSelectSpace select,
	.bsaProInputsRight .bsaInputInner,
	.bsaProInputsRight .bsaInputInner label { ".bsaProGetOpt('form_input_bg', 'bg')." ".bsaProGetOpt('form_input_c', 'c')." }
	.bsaProPrice  { ".bsaProGetOpt('form_price_c', 'c')." }
	.bsaProDiscount  { ".bsaProGetOpt('form_discount_bg', 'bg')." ".bsaProGetOpt('form_discount_c', 'c')." }
	.bsaProOrderingForm .bsaProSubmit,
	.bsaProOrderingForm .bsaProSubmit:hover,
	.bsaProOrderingForm .bsaProSubmit:active { ".bsaProGetOpt('form_button_bg', 'bg')." ".bsaProGetOpt('form_button_c', 'c')." }

	/* alerts */
	.bsaProAlert,
	.bsaProAlert > a,
	.bsaProAlert > a:hover,
	.bsaProAlert > a:focus { ".bsaProGetOpt('form_alert_c', 'c')." }
	.bsaProAlertSuccess { ".bsaProGetOpt('form_alert_success_bg', 'bg')." }
	.bsaProAlertFailed { ".bsaProGetOpt('form_alert_failed_bg', 'bg')." }

	/* stats */
	.bsaStatsWrapper .ct-chart .ct-series.ct-series-b .ct-bar,
	.bsaStatsWrapper .ct-chart .ct-series.ct-series-b .ct-line,
	.bsaStatsWrapper .ct-chart .ct-series.ct-series-b .ct-point,
	.bsaStatsWrapper .ct-chart .ct-series.ct-series-b .ct-slice.ct-donut { ".bsaProGetOpt('stats_views_line', 's')." }

	.bsaStatsWrapper  .ct-chart .ct-series.ct-series-a .ct-bar,
	.bsaStatsWrapper .ct-chart .ct-series.ct-series-a .ct-line,
	.bsaStatsWrapper .ct-chart .ct-series.ct-series-a .ct-point,
	.bsaStatsWrapper .ct-chart .ct-series.ct-series-a .ct-slice.ct-donut { ".bsaProGetOpt('stats_clicks_line', 's')." }

	/* Custom CSS */
	".get_option('bsa_pro_plugin_custom_css');

	if ( get_option('bsa_pro_plugin_rtl_support') == 'yes' ) {
		echo "/* Carousel */
		.owl-carousel,
		.bx-wrapper { direction: ltr; }
		.owl-carousel .owl-item { direction: rtl; }";
	}
	echo "</style>";
}
add_action('wp_head', 'BSA_PRO_add_custom_stylesheet');

// disable wp rocket
//add_filter( 'do_rocket_generate_caching_files', '__return_false' );

if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

	// 4.6 and older
	add_action('widgets_init',
			create_function('', 'return register_widget("BSA_PRO_ShortCode_Init");')
	);

} else {

	// Add a filter to the wp 4.7 version attributes metabox
	function bsa_pro_register_widgets() {
		register_widget( 'BSA_PRO_ShortCode_Init' );
	}

	add_action( 'widgets_init', 'bsa_pro_register_widgets' );

}

class BSA_PRO_ShortCode_Init extends WP_Widget {
	public function __construct() {
		parent::__construct('bsa_shortcode_widget', 'ADS PRO - Shortcode', array(
			'description' => 'Here you can add shortcode from ADS PRO.',
		));
	}
	public function widget( $args, $instance ) {

		extract( $args );
		// these are the widget options
		$shortcode 			= isset($instance['shortcode']) ? $instance['shortcode'] : null;

		// -- Start -- Content
		if ( $shortcode != '' ) {
			echo do_shortcode($shortcode);
		} else {
			echo 'Shortcode field is empty!';
		}
		// -- END -- Content
	}
	public function form( $instance ) {

		if ( bsa_role() == 'admin' ) {

			// Check values
			if( $instance) {
				$shortcode = isset($instance['shortcode']) ? esc_attr($instance['shortcode']) : null;
			} else {
				$shortcode = '';
			}
			?>

			<p>
				<label for="<?php echo $this->get_field_id('shortcode'); ?>"><?php _e('Shortcode', 'wp_widget_plugin'); ?></label><br>
				<input id="<?php echo $this->get_field_id('shortcode'); ?>" name="<?php echo $this->get_field_name('shortcode'); ?>" style="width:100%;" value="<?php echo $shortcode; ?>" />
			</p>
			<?php

		} else {

			echo "<p>You haven't permission to manage ads.</p>";

		}
	}
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		// Fields
		$instance['shortcode'] 		= stripslashes( wp_filter_post_kses( addslashes($new_instance['shortcode']) ) );
		return $instance;
	}
}

if ( function_exists('bsa_pro_sub_menu_agency') ) {
	class BuySellAdsApiTemplate {

		/**
		 * A Unique Identifier
		 */
		protected $plugin_slug;

		/**
		 * A reference to an instance of this class.
		 */
		private static $instance;

		/**
		 * The array of templates that this plugin tracks.
		 */
		protected $templates;


		/**
		 * Returns an instance of this class.
		 */
		public static function get_instance() {

			if( null == self::$instance ) {
				self::$instance = new BuySellAdsApiTemplate();
			}

			return self::$instance;

		}

		/**
		 * Initializes the plugin by setting filters and administration functions.
		 */
		private function __construct() {

			$this->templates = array();


			// Add a filter to the attributes metabox to inject template into the cache.
			if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

				// 4.6 and older
				add_filter(
					'page_attributes_dropdown_pages_args',
					array( $this, 'register_project_templates' )
				);

			} else {

				// Add a filter to the wp 4.7 version attributes metabox
				add_filter(
					'theme_page_templates', array( $this, 'add_new_template' )
				);

			}


			// Add a filter to the save post to inject out template into the page cache
			add_filter(
				'wp_insert_post_data',
				array( $this, 'register_project_templates' )
			);


			// Add a filter to the template include to determine if the page has our
			// template assigned and return it's path
			add_filter(
				'template_include',
				array( $this, 'view_project_template')
			);


			// Add your templates to this array.
			$this->templates = array(
				'api/get.php'     => 'Ads Pro - API',
			);

		}


		/**
		 * Adds our template to the page dropdown for v4.7+
		 *
		 */
		public function add_new_template( $posts_templates ) {
			$posts_templates = array_merge( $posts_templates, $this->templates );
			return $posts_templates;
		}


		/**
		 * Adds our template to the pages cache in order to trick WordPress
		 * into thinking the template file exists where it doens't really exist.
		 *
		 */

		public function register_project_templates( $atts ) {

			// Create the key used for the themes cache
			$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

			// Retrieve the cache list.
			// If it doesn't exist, or it's empty prepare an array
			$templates = wp_get_theme()->get_page_templates();
			if ( empty( $templates ) ) {
				$templates = array();
			}

			// New cache, therefore remove the old one
			wp_cache_delete( $cache_key , 'themes');

			// Now add our template to the list of templates by merging our templates
			// with the existing templates array from the cache.
			$templates = array_merge( $templates, $this->templates );

			// Add the modified cache to allow WordPress to pick it up for listing
			// available templates
			wp_cache_add( $cache_key, $templates, 'themes', 1800 );

			return $atts;

		}

		/**
		 * Checks if the template is assigned to the page
		 */
		public function view_project_template( $template ) {

			global $post;

			if (!isset($post)) {
				return $template;
			}


			if (isset($post) && isset($post->ID) && !isset($this->templates[get_post_meta(
						$post->ID, '_wp_page_template', true
					)] ) ) {

				return $template;

			}

			if (isset($post) && isset($post->ID)) {
				$file = plugin_dir_path(__FILE__). get_post_meta(
						$post->ID, '_wp_page_template', true
					);
			} else {
				$file = null;
			}

			// Just to be safe, we check if the file exist first
			if( file_exists( $file ) ) {
				return $file;
			} else {
				echo $file;
			}

			return $template;

		}


	}
	add_action( 'plugins_loaded', array( 'BuySellAdsApiTemplate', 'get_instance' ) );
}

// bsa custom redirections
//add_action( 'init',  function() {
//	add_rewrite_rule( 'bsa-redirect/([a-z0-9-]+)[/]?$', 'index.php?myparamname=$matches[1]', 'top' );
//} );

$BuySellAdsPlugin = new BuySellAdsPro();
