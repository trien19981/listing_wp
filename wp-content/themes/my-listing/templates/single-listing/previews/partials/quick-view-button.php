<?php
/**
 * Quick View button for the preview card template.
 *
 * @since 2.2
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>
<li class="item-preview tooltip-element">
    <a aria-label="<?php echo esc_attr( _ex( 'Quick view button', 'Preview card qucik view button - SR', 'my-listing' ) ) ?>" href="#" type="button" class="c27-toggle-quick-view-modal" data-id="<?php echo esc_attr( $listing->get_id() ) ?>">
    	<i class="mi zoom_in"></i>
    </a>
    <span class="tooltip-container"><?php esc_attr_e( 'Quick view', 'my-listing' ) ?></span>
</li>