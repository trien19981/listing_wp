<?php
/**
 * Template for rendering the `carousel-with-preview` template for gallery block in single listing page.
 *
 * @since 1.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

wp_enqueue_script( 'mylisting-owl' );
wp_enqueue_script( 'mylisting-photoswipe' );
wp_enqueue_script( 'mylisting-gallery-carousel-preview' );
wp_print_styles('mylisting-photoswipe');
wp_print_styles('mylisting-gallery-carousel-preview');

$items_per_row = min( 3, count( $gallery_items ) );
?>

<div class="<?php echo esc_attr( $block->get_wrapper_classes() ) ?>" id="<?php echo esc_attr( $block->get_wrapper_id() ) ?>">
	<div class="element slider-padding gallery-block">
		<div class="pf-head">
			<div class="title-style-1">
				<i class="<?php echo esc_attr( $block->get_icon() ) ?>"></i>
				<h5><?php echo esc_html( $block->get_title() ) ?></h5>
			</div>
		</div>

		<div class="pf-body">
			<div class="gallerySlider">
				<div class="owl-carousel galleryPreview photoswipe-gallery">
					<?php foreach ( $gallery_items as $item ): ?>
						<a aria-label="<?php echo esc_attr( _ex( 'Listing gallery thumb', 'Gallery block thumb - SR', 'my-listing' ) ) ?>" class="item photoswipe-item" href="<?php echo esc_url( $item['full_size_url'] ) ?>">
							<?php echo apply_filters( 'post_thumbnail_html', '<img src="'. esc_url( $item['url'] ).'" alt="'. esc_attr( $item['alt'] ?? '' ).'" description="' . esc_attr( $item['description'] ?? '' ) . '" caption="' . esc_attr( $item['caption'] ?? '' ) . '" title="' . esc_attr( $item['title'] ?? '' ) . '" >' ); ?>
						</a>
					<?php endforeach ?>
				</div>

				<?php if ( count( $gallery_items ) > 1 ): ?>
					<div class="gallery-thumb owl-carousel" data-items="<?php echo absint( $items_per_row ) ?>"
						data-items-mobile="<?php echo absint( $items_per_row ) ?>">
						<?php foreach ( $gallery_items as $key => $item ): ?>
							<a
								aria-label="<?php echo esc_attr( _ex( 'Listing gallery item', 'Gallery block items - SR', 'my-listing' ) ) ?>"
								class="item slide-thumb"
								data-slide-no="<?php echo esc_attr( $key ) ?>"
								href="<?php echo esc_url( $item['url'] ) ?>"
								style="background-image: url('<?php echo esc_url( $item['url'] ) ?>')"
							></a>
						<?php endforeach ?>
					</div>
				<?php endif ?>

				<?php if ( count( $gallery_items ) > 3 ): ?>
					<div class="gallery-nav">
						<ul class="no-list-style">
							<li><a aria-label="<?php echo esc_attr( _ex( 'Gallery navigation previous', 'Gallery block arrows - SR', 'my-listing' ) ) ?>" href="#" class="gallery-prev-btn"><i class="mi keyboard_arrow_left"></i></a></li>
							<li><a aria-label="<?php echo esc_attr( _ex( 'Gallery navigation next', 'Gallery block arrows - SR', 'my-listing' ) ) ?>" href="#" class="gallery-next-btn"><i class="mi keyboard_arrow_right"></i></a></li>
						</ul>
					</div>
				<?php endif ?>
			</div>
		</div>
	</div>
</div>