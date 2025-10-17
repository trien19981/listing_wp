<?php
/**
 * Single blog post template.
 *
 * @since 1.0
 */

global $page, $numpages;

$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
$thumbnail = wp_get_attachment_image_src( $thumbnail_id, 'large' );
$image = $thumbnail ? array_shift( $thumbnail ) : false;
$image_alt = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );

$categories = c27()->get_terms( get_the_ID(), 'category' );

// $ids = c27()->get_post_relations(get_the_ID());
// if (!empty($ids)) {
// 	$listings = get_posts( [
// 		'post_type' => 'job_listing',
// 		'post_status' => 'publish',
// 		'post__in' => $ids,
// 		'posts_per_page' => -1,
// 		'orderby' => 'post__in',
// 		'order' => 'DESC',
// 	] );
// }

wp_print_styles('mylisting-single-blog');
wp_enqueue_script( 'mylisting-share-modal' );
?>
<section class="i-section blogpost-section">
	<div class="container">
		<div class="row blog-title">
			<div class="col-md-8 col-md-offset-2">
				<h1 class="case27-primary-text"><?php the_title() ?></h1>
					<div class="post-cover-buttons">
						<ul class="no-list-style">
							<?php foreach ( (array) $categories as $category ): ?>
								<li>
									<a href="<?php echo esc_url( $category['link'] ) ?>">
										<i class="mi bookmark"></i>
										<?php echo esc_html( $category['name'] ) ?>
									</a>
								</li>
							<?php endforeach ?>

							<li>
								<div>
									<i class="fa fa-calendar"></i>
									<?php $date_format = apply_filters('mylisting/blog/date-format', 'M d'); ?>
									<span><?php echo get_the_date($date_format) ?></span>
								</div>
							</li>



							<li class="dropdown">
								<?php $links = mylisting()->sharer()->get_links([
									'permalink' => get_permalink(),
									'image' => $image,
									'title' => get_the_title(),
									'description' => get_the_content(),
									] ) ?>
									<a href="#" data-toggle="modal" data-target="#social-share-modal">
										<i class="fa fa-share-alt"></i>
										<?php _e( 'Share post', 'my-listing' ) ?>
									</a>

									<?php
							/**
							 * Output the markup for the share modal in the site footer,
							 * to prevent layout issues/cutout modal.
							 */
							add_action( 'mylisting/get-footer', function() use ( $links ) { 
								wp_print_styles('mylisting-share-modal');
								?>
								<div id="social-share-modal" class="social-share-modal modal modal-27">
									<ul class="share-options no-list-style">
										<?php foreach ( $links as $link ):
										if ( empty( trim( $link ) ) ) continue; ?>
										<li><?php mylisting()->sharer()->print_link( $link ) ?></li>
									<?php endforeach ?>
								</ul>
							</div>
							<?php } ) ?>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<?php if ( ! empty( $image ) ): ?>
			<div class="row blog-featured-image">
				<div class="col-md-12">
					<img src="<?php echo esc_url( $image ) ?>" alt="<?php echo esc_attr( $image_alt ) ?>">
				</div>
			</div>
		<?php endif ?>

		<div class="row section-body">
			<div class="col-md-8 col-md-offset-2 c27-content-wrapper">
				<?php the_content() ?>
			</div>
		</div>
		<?php if ( ! empty( get_the_tags() ) ): ?>
			<div class="row tags-list">
				<div class="col-md-8 col-md-offset-2">
					<ul class="tags no-list-style">
						<li><?php the_tags('', '<li>', '') ?></li>
					</ul>
				</div>
			</div>
		<?php endif ?>

		<?php if ( $numpages > 1 ): ?>
			<div class="row c27-post-pages">
				<?php if ($page == 1): ?>
					<div class="col-md-6 text-left"></div>
				<?php endif ?>

				<?php wp_link_pages([
					'before'           => '',
					'after'            => '',
					'link_before'      => '',
					'link_after'       => '',
					'next_or_number'   => 'next',
					'separator'        => ' ',
					'nextpagelink'     => '<div class="col-md-6 text-right">Next page</div>',
					'previouspagelink' => '<div class="col-md-6 text-left">Previous page</div>',
					'pagelink'         => '%',
					'echo'             => 1
				]) ?>
			</div>
		<?php endif ?>

		<div class="row c27-post-changer">
			<div class="col-xs-4 col-sm-5 text-left">
				<?php previous_post_link('%link', esc_html__('Previous Post', 'my-listing')) ?>
			</div>
			<div class="col-xs-4 col-sm-2 text-center">
				<?php if ( get_option( 'page_for_posts' ) ): ?>
					<a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ) ?>">
						<i class="material-icons mi grid_on"></i>
					</a>
				<?php endif ?>
			</div>
			<div class="col-xs-4 col-sm-5 text-right">
				<?php next_post_link('%link', esc_html__('Next Post', 'my-listing')) ?>
			</div>
		</div>
	</div>
</section>

<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ): ?>
	<section class="i-section">
		<div class="container">
			<div class="row section-title">
				<h2 class="case27-primary-text"><?php _e( 'Comments', 'my-listing' ) ?></h2>
			</div>
		</div>
		<?php comments_template() ?>
	</section>
<?php endif ?>


