<?php
	$data = c27()->merge_options([
			'title' => '',
			'show_breadcrumbs' => true,
			'ref' => '',
		], $data);
	wp_print_styles('mylisting-breadcrumbs-widget');
?>

<section class="<?php echo esc_attr( apply_filters( 'case27_title_bar_classes', 'ml-breadcrumbs', $data ) ) ?>">
	<div class="">

		<div>
			<h1><?php echo $data['title'] ? $data['title'] : the_title() ?></h1>
		</div>

		<?php if ($data['show_breadcrumbs']): ?>

			<div>

				<?php new MyListing\Utils\Breadcrumbs( [
					'before' => '<ul class="page-directory no-list-style" itemscope itemtype="https://schema.org/BreadcrumbList">',
					'after' => '</ul>',
					'standard' => '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">%s %s</li>',
					'current' => '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="current"><span itemprop="name">%s</span> %s</li>',
					'link' => '<a href="%s" itemprop="item"><div itemprop="name">%s</div></a>',
				], [
					'show_htfpt' => true,
					'separator' => '',
				], [
					'home' => '<span class="icon-places-home-3"></span>' . __( 'Home', 'my-listing' ),
				]
			) ?>

		</div>

	<?php endif ?>

</div>
</section>
