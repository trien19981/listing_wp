<?php
// header define - header('Content-type: text/css');
// ob start - ob_start("compress");

function bsa_pro_compress( $minify )
{
	/* remove comments */
	$minify = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify );

	/* remove tabs, spaces, newlines, etc. */
	$minify = str_replace( array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $minify );

	return $minify;
}

function bsa_pro_generate_css( $rtl = null )
{
	/* css files for combining */
	$get_templates = array_diff( scandir( __DIR__ ), Array( ".", "..", "asset", "template.css.php", "rtl-template.css.php", "all.css", "rtl-all.css", ".DS_Store" ) );
	$content = null;
	foreach ( $get_templates as $template ) {
		if ( strpos($template, 'rtl-') === false && $rtl == null ||
			 strpos($template, 'rtl-') !== false && $rtl == true ||
			 strpos($template, 'block-') !== false && $rtl == true ) {
			if ( isset( $template ) ) {
				$content .= file_get_contents(bsa_pro_compress(dirname(__FILE__) . '/' . $template));
			}
		}
	}

	file_put_contents(dirname(__FILE__) . '/'.($rtl == true ? 'rtl-' : '').'all.css', $content);
}

// ob flush - ob_end_flush();