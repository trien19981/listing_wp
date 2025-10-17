<?php
/**
 * Package Selection Step.
 *
 * @since 1.0
 * @var   array $tree
 */

$item_count = count( $tree );
$checked = 1;
$selected = isset( $_GET['selected_package'] ) ? absint( $_GET['selected_package'] ) : null;

if ( ! empty( $GLOBALS['cts-add-listing-config'] ) && $GLOBALS['cts-add-listing-config']['packages_layout'] === 'compact' ) {
	$column_count = 4;
} else {
	$column_count = 3;
}

$item_wrapper = 'col-md-4';
if ( absint( $column_count ) === 4 && $item_count >= 4 ) {
	$item_wrapper = 'col-md-3';
}

$type_selection = '';
if ( ! empty( $_REQUEST['listing_type'] ) && ( $type = \MyListing\Src\Listing_Type::get_by_name( $_REQUEST['listing_type'] ) ) ) {
	$type_selection = $type;
}

wp_print_styles( 'mylisting-package-selection-widget' );
?>

<div class="row section-body row-eq-height">
	<?php if ( 1 === $item_count ): ?>
		<div class="col-md-4 col-sm-3 hidden-xs"></div>
	<?php elseif ( 2 === $item_count ): ?>
		<div class="col-md-2 hidden-sm hidden-xs"></div>
	<?php endif; ?>

	<?php foreach ( $tree as $item ):
		extract( $item );

		// Set checked item.
		$checked = ( intval( $selected ) === intval( $product->get_id() ) ) ? 1 : 0;

		$total_limit = '';
		if ( $type_selection ) {
			$meta_key = sprintf( '%s_limit', $type_selection->get_slug() );

			if ( $product->meta_exists( $meta_key ) ) {
				$total_limit = $product->get_meta( $meta_key );
			}
		}

		?>

		<div class="<?php echo esc_attr( $item_wrapper ) ?> col-sm-6 col-xs-12">
			<div class="pricing-item c27-pick-package cts-pricing-item <?php echo $checked ? 'c27-picked' : ''; ?> <?php echo $featured ? 'featured' : '' ?> <?php echo ! $product->is_purchasable() ? 'not-purchasable' : '' ?>">
				<?php if ( $featured ): ?>
					<div class="featured-plan-badge">
						<span class="icon-flash"></span>
					</div>
				<?php endif ?>

				<h2 class="plan-name"><?php echo $title ?></h2>

				<?php if ( $image ): ?>
					<img src="<?php echo esc_url( $image ) ?>" alt="Pricing plan image" class="plan-image">
				<?php endif ?>

				<h2 class="plan-price case27-primary-text"><?php echo $product->get_price_html(); ?></h2>
				<p class="plan-desc"><?php echo $product->get_short_description(); ?></p>
				<div class="plan-features">
					<?php if ( is_array( $description ) ): ?>
						<ul class="no-list-style">
							<?php foreach ( $description as $line ): ?>
								<li><?php echo $line ?></li>
							<?php endforeach ?>
						</ul>
					<?php else: ?>
						<?php echo $description ?>
					<?php endif ?>
				</div>
				<div class="select-package">

					<?php if ( $packages ): ?>
						<div class="package-available dropup">
							<a type="button" class="use-package-toggle dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<?php _e( 'You already own this package', 'my-listing' ) ?>
								<i class="mi arrow_drop_up"></i>
							</a>

							<div class="dropdown-menu">
								<ul class="no-list-style checkbox-plan-list owned-product-packages">
									<?php foreach ( $packages as $package ) :
										$listing_limit = '';
										if ( $total_limit ) {
											$listing_limit = \MyListing\Src\Paid_Listings\Util::get_count_listing_by_type( $type->get_slug(), $package->get_user_id(), $package->get_id(), 'case27_user_package' );
											
										}
									?>
										<li>
											<div class="md-checkbox">
												<input type="radio" name="listing_package" value="<?php echo esc_attr( $package->get_id() ) ?>" id="user-package-<?php echo esc_attr( $package->get_id() ) ?>">
												<label for="user-package-<?php echo esc_attr( $package->get_id() ); ?>"></label>
											</div>
											<label for="user-package-<?php echo esc_attr( $package->get_id() ) ?>" class="checkbox-plan-name"><?php echo $title ?></label>
											<p class="checkbox-plan-desc">
											<?php
											if ( $total_limit ) {
												printf( _n( 'You have posted %s out of %d %s listing', 'You have posted %s out of %d %s listings', count( $listing_limit ), 'my-listing' ), count( $listing_limit ), $total_limit, $type_selection->get_name() );
											} else if ( $package->get_limit() ) {
												printf( _n( '%s listing posted out of %d', '%s listings posted out of %d', $package->get_count(), 'my-listing' ), $package->get_count(), $package->get_limit() );
											} else {
												printf( _n( '%s listing posted', '%s listings posted', $package->get_count(), 'my-listing' ), $package->get_count() );
											}

											if ( $package->get_duration() ) {
												printf(  ', ' . _n( 'listed for %s day', 'listed for %s days', $package->get_duration(), 'my-listing' ), $package->get_duration() );
											}
											?>
											</p>
										</li>
									<?php endforeach ?>
									<?php if ( $product->is_purchasable() ): ?>
										<li>
											<a class="buttons button-5 cts-trigger-buy-new" href="#"><?php _e( 'Or buy new', 'my-listing' ) ?><i class="mi arrow_forward"></i></a>
										</li>
									<?php else: ?>
										<li class="purchase-disabled"><p><?php _e( 'This item can only be purchased once.', 'my-listing' ) ?></p></li>
									<?php endif ?>
								</ul>
							</div>
						</div>
					<?php endif ?>

					<?php if ( $packages ): ?>
						<a class="select-plan buttons button-2" href="#">
							<?php _e( 'Use Available Package', 'my-listing' ); ?>
							<i class="mi arrow_forward"></i>
						</a>
					<?php else: ?>
						<?php if ( $product->is_purchasable() ): ?>
							<a class="select-plan buttons button-2 cts-trigger-buy-new" href="#">
								<?php _e( 'Buy Package', 'my-listing' ); ?>
								<i class="mi arrow_forward"></i>
							</a>
						<?php else: ?>
							<p class="purchase-disabled"><?php _e( 'This item can only be purchased once.', 'my-listing' ) ?></p>
						<?php endif ?>
					<?php endif ?>

					<input type="radio" name="listing_package" class="cts-buy-new hide" value="<?php echo esc_attr( $product->get_id() ); ?>" id="package-<?php echo esc_attr( $product->get_id() ); ?>" />
				</div>
			</div>
		</div>

	<?php endforeach; ?>

</div>
