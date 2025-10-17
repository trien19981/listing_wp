<?php
if (get_option('bsa_pro_plugin_symbol_position') == 'before') {
	$before = get_option('bsa_pro_plugin_currency_symbol');
} else {
	$before = '';
}
if (get_option('bsa_pro_plugin_symbol_position') != 'before') {
	$after = get_option('bsa_pro_plugin_currency_symbol');
} else {
	$after = '';
}

$model				= new BSA_PRO_Model();
$getUsers 			= $model->getUsersList();
$getUserActiveAds 	= (is_array($model->getUserAds(get_current_user_id())) ? $model->getUserAds(get_current_user_id()) : array());
$getUserPendingAds 	= $model->getUserAds(get_current_user_id(), 'pending');
$get_free_ads 		= $model->getUserCol(get_current_user_id(), 'free_ads');

if ( bsa_role() == 'admin' ): // ADMIN SECTION ?>

<?php $wersdadp = ''; ?>

	<h2>
		<span class="dashicons dashicons-admin-users"></span> Users Manager
		<?php if ( get_option('bsa_pro_u'.$wersdadp.'pd'.'at'.$wersdadp.'e'.'_s'.$wersdadp.'ta'.'tus') != 'i'.$wersdadp.'n'.'v'.$wersdadp.'a'.'l'.$wersdadp.'i'.$wersdadp.'d' ): ?>
			<?php if ( isset($_GET['bsa-form']) && $_GET['bsa-form'] == 'free-ads' ): ?>
				 - Set free ads
			<?php elseif ( isset($_GET['bsa-form']) && $_GET['bsa-form'] == 'give-access' ): ?>
				 - Set access to ads
			<?php endif; ?>
			<?php if ( isset($_GET['bsa-form']) ): ?>
				<p><span class="dashicons dashicon-14 dashicons-arrow-left-alt"></span> <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-users">back to <strong>users</strong></a></p>
			<?php else: ?>
				<a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-users&bsa-form=free-ads" class="add-new-h2">Set free ads</a> <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-users&bsa-form=give-access" class="add-new-h2">Set access to ads</a>
			<?php endif; ?>
		<?php endif; ?>
	</h2>

<?php if ( get_option('bsa_pro_u'.$wersdadp.'pd'.'at'.$wersdadp.'e'.'_s'.$wersdadp.'ta'.'tus') == 'i'.$wersdadp.'n'.'v'.$wersdadp.'a'.'l'.$wersdadp.'i'.$wersdadp.'d' ): ?>

	<tr class="bsaAdminTable showAdvanced">
		<td colspan="2">
			<div class="bsaLockedContent bsaLockedWhite">
				<strong>Trial Version</strong><br>
				Use purchase code to unlock Users Manager and other features.<br><br>
				Paste purchase code in the <a href="<?php echo admin_url() ?>admin.php?page=bsa-pro-sub-menu-opts">settings</a> (Ads Pro > Settings > Purchase Code).<br>
				Where is your purchase code? <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">Learn more</a>.<br><br>
				- or -<br><br>
				<a href="https://1.envato.market/buy-regular-ads-pro-6" target="_blank">Buy now</a> the latest version of Ads Pro.
			</div>
		</td>
	</tr>

<?php else: ?>

	<?php if ( isset($_GET['bsa-form']) && $_GET['bsa-form'] == 'free-ads' ): ?>

		<form action="" method="post" class="bsaFreeAds">
			<input type="hidden" value="free-ads" name="bsaProAction">
			<table class="bsaAdminTable form-table">
				<tbody class="bsaTbody">
				<tr>
					<th colspan="2">
						<h3><span class="dashicons dashicons-exerpt-view"></span> Add free ads</h3>
					</th>
				</tr>
				<tr>
					<th scope="row"><label for="crease_method">Increase / Decrease</label></th>
					<td>
						<select id="crease_method" name="crease_method">
							<option value="increase">increase</option>
							<option value="decrease">decrease</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="free_ads">free Ads</label></th>
					<td>
						<select id="free_ads" name="free_ads">
							<option value="">select number of free ads</option>
							<?php for ( $i = 1; $i <= 10; $i++ ) {
								echo '<option value="'.$i.'">' . $i . ' free ad' . (($i>1) ? 's' : '') . '</option>';
							} ?>
						</select>
						<p class="description">
							Limit will increase if the user has unused free ads.
						</p>
					</td>
				</tr>
				<tr>
					<th class="bsaLast" scope="row"><label for="user_id">assign to</label></th>
					<td class="bsaLast">
						<select id="user_id" name="user_id">
							<option value="">select user</option>
							<?php if ( get_users( array( 'fields' => array( 'id','display_name' ) ) ) ) {
								foreach ( get_users( array( 'fields' => array( 'id','display_name' ) ) ) as $user ) {
									echo '<option value="'.esc_html( $user->id ).'">' . esc_html( $user->display_name ) . ' (ID: ' . esc_html( $user->id ) . ')' . '</option>';
								}
							} ?>
						</select>
					</td>
				</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" value="Save" class="button button-primary" id="bsa_pro_submit" name="submit">
			</p>
		</form>

	<?php elseif ( isset($_GET['bsa-form']) && $_GET['bsa-form'] == 'give-access' ): ?>

		<?php if ( get_bsa_ads() ): ?>

			<form action="" method="post" class="bsaNewStandardAd">
				<input type="hidden" value="give-access" name="bsaProAction">
				<table class="bsaAdminTable form-table">
					<tbody class="bsaTbody">
					<tr>
						<th colspan="2">
							<h3><span class="dashicons dashicons-exerpt-view"></span> Set access</h3>
						</th>
					</tr>
					<tr>
						<th scope="row"><label for="permissions">Permissions</label></th>
						<td>
							<input type="radio" id="add" name="permissions" value="add" checked>
							<label for="add">assign permissions</label><br>
							<input type="radio" id="remove" name="permissions" value="remove">
							<label for="remove">revoke permissions</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="ad_id">to Ad</label></th>
						<td>
							<select id="ad_id" name="ad_id">
								<option value="">select ad id</option>
								<?php foreach (get_bsa_ads() as $ad):
									echo '<option value="'.esc_html( $ad['id'] ).'">' . esc_html( $ad['id'] ) . (($ad['title'] != '') ? ' ('.$ad['title'].')' : '') . '</option>';
								endforeach; ?>
							</select>
							<div style="display: none;">
								- or -
								<label for="ad_ids">paste few Ad ids</label>
								<input type="text" id="ad_ids" name="ad_ids" value="" placeholder="1,2,3,4 (separated by comma)">
							</div>
						</td>
					</tr>
					<tr>
						<th class="bsaLast" scope="row"><label for="user_id">for</label></th>
						<td class="bsaLast">
							<select id="user_id" name="user_id">
								<option value="">select user</option>
								<?php if ( get_users( array( 'fields' => array( 'id','display_name' ) ) ) ) {
									foreach ( get_users( array( 'fields' => array( 'id','display_name' ) ) ) as $user ) {
										echo '<option value="'.esc_html( $user->id ).'">' . esc_html( $user->display_name ) . ' (ID: ' . esc_html( $user->id ) . ')' . '</option>';
									}
								} ?>
							</select>
						</td>
					</tr>
					</tbody>
				</table>
				<p class="submit">
					<input type="submit" value="Save" class="button button-primary" id="bsa_pro_submit" name="submit">
				</p>
			</form>

		<?php else: ?>

			<div class="updated settings-error" id="setting-error-settings_updated">
				<p><strong>Error!</strong> Ads not exists!</p>
			</div>

		<?php endif; ?>


	<?php else: ?>

		<h3>Users</h3>
		<table class="wp-list-table widefat bsaListTable">
			<thead>
			<tr>
				<th class="manage-column">ID</th>
				<th class="manage-column">User</th>
				<th class="manage-column">Free Ads</th>
				<th class="manage-column">Has access to Ads</th>
				<th class="manage-column">Actions</th>
			</tr>
			</thead>

			<tbody>
			<?php
			$usersPagination = new AdsProPagination();
			if ( is_array($getUsers) && count($getUsers) > 0 && $usersPagination->getUsersPages() && $usersPagination->getUsersPages() != 'not_found') {
				foreach ($usersPagination->getUsersPages() as $key => $entry) {

					if ($key % 2) {
						$alternate = '';
					} else {
						$alternate = 'alternate';
					}
					?>

					<tr class="<?php echo $alternate; ?>">
						<td>
							<?php $user_info = get_userdata($entry['user_id']);
							echo $user_info->ID; ?>
						</td>
						<td>
							<?php
							echo '<strong>' . $user_info->user_login . '</strong>';
							?>
						</td>
						<td>
							<?php echo ( ($entry['free_ads'] > 1) ? $entry['free_ads'] . ' ads' : ( ($entry['free_ads'] == 1) ? $entry['free_ads'] . ' ad' : '-') ) ; ?>
						</td>
						<td>
							<?php $ads = null ?>
							<?php if ( json_decode($entry['ad_ids']) == null ) {
								echo '-';
							} else {
								foreach ( json_decode($entry['ad_ids']) as $ad ) {
									echo '(<strong>' . $ad . '</strong>) ';
								}
							} ?>
						</td>
						<td>
							<a href="<?php echo admin_url('admin.php?page=bsa-pro-sub-menu-users&bsa-form=free-ads') ?>" style="margin-right: 15px">Edit Free Ads</a>
							<a href="<?php echo admin_url('admin.php?page=bsa-pro-sub-menu-users&bsa-form=give-access') ?>">Edit Access to Ads</a>
						</td>
					</tr>

				<?php }

				if ( is_array($getUsers) && count($getUsers) > 40 ): ?>
					<tr>
						<td colspan="5">
							<?php
							if($prev = $usersPagination->getPrev()): ?>
								<a href="<?php echo admin_url('admin.php?page=bsa-pro-sub-menu-users&pagination='.$prev); ?>">< Prev Page</a>
							<?php endif ?>
							<?php if($next = $usersPagination->getNext('users')): ?>
								<a href="<?php echo admin_url('admin.php?page=bsa-pro-sub-menu-users&pagination='.$next); ?>" style="float:right;">Next Page ></a>
							<?php endif ?>
						</td>
					</tr>
				<?php
				endif;

			} else {
				?>

				<tr>
					<td style="text-align: center" colspan="5">
						List empty.
					</td>
				</tr>

			<?php } ?>
			</tbody>
		</table>

	<?php endif; ?>

	<?php endif; ?>

<?php else: // USERS SECTION ?>

	<div style="float:right;">
		<strong><a href="https://1.envato.market/6rkOr" target="_blank">ADS PRO</a></strong> - Version <?php echo get_option('bsa_pro_plugin_version') ?>
	</div>

	<h2>
		<span class="dashicons dashicons-exerpt-view"></span> Your Ads
		<?php if ( $get_free_ads['free_ads'] > 0 ): ?>
			<a href="<?php echo admin_url('admin.php?page=bsa-pro-sub-menu-add-new-ad') ?>" class="add-new-h2">Add new Ad</a>
		<?php endif; ?>
	</h2>

	<h3>Active Ads (<?php echo count($getUserActiveAds) ?>)</h3>
	<table class="wp-list-table widefat bsaListTable">
		<thead>
		<tr>
			<th></th>
			<th style="" class="manage-column post-title page-title column-title">Ad Content</th>
			<th style="" class="manage-column">Buyer</th>
			<th style="" class="manage-column">Stats</th>
			<th style="" class="manage-column">Ad Display Limit</th>
			<th style="" class="manage-column">Order Details</th>
		</tr>
		</thead>

		<tbody>
		<?php
		if (is_array($getUserActiveAds) && count($getUserActiveAds) > 0) {
			foreach ($getUserActiveAds as $key => $entry) {

				if ($key % 2) {
					$alternate = '';
				} else {
					$alternate = 'alternate';
				}
				?>

				<tr class="<?php echo $alternate; ?>">
					<td class="bsaAdminImg">
						<img class="bsaAdminThumb" src="<?php echo ( $entry['img'] != '' ) ? bsa_upload_url(null, $entry['img']) : plugin_dir_url( __DIR__ ).'frontend/img/example.png'; ?>">
					</td>
					<td class="post-title page-title column-title">
						<?php echo ( $entry['title'] != '' ) ? '<strong>'.$entry['title'].'</strong><br>' : ''; ?>
						<?php echo ( $entry['description'] != '' ) ? $entry['description'].'<br>' : ''; ?>
						<?php echo ( $entry['url'] != '' ) ? '<a href="'.$entry['url'].'" target="_blank">'.substr($entry['url'], 0, 50).( strlen($entry['url']) > 50 ? '...</a>' : '</a>' ) : ''; ?><br>
						<?php echo ( $entry['html'] != '' ) ? 'HTML / JS Code' : '' ; ?>
						<div class="row-actions">
							- - - -<br>
							<span class="bsaPaidBtn">
								<a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-add-new-ad&ad_id=<?php echo $entry['id']; ?>">
									Edit
								</a>
							</span>
						</div>
					</td>
					<td>
						<?php echo $entry['buyer_email']; ?>
					</td>
					<td>
						<?php
						$views = bsa_counter($entry['id'], 'view');
						$clicks = bsa_counter($entry['id'], 'click');
						$viewable = bsa_counter($entry['id'], 'viewable'); ?>
						<?php echo bsa_get_trans('user_panel', 'views'); ?> <strong><?php echo ( $views != NULL ) ? $views : 0; ?></strong><br>
						<?php echo bsa_get_trans('user_panel', 'clicks'); ?> <strong><?php echo ( $clicks != NULL ) ? $clicks : 0; ?></strong><br>
						<?php if ( $views != NULL && $clicks != NULL ): ?>
							CTR <strong><?php echo number_format(($clicks / $views) * 100, 2, '.', '').'%'; ?></strong><br>
						<?php endif; ?>
						<a target="_blank" href="<?php echo get_option('bsa_pro_plugin_ordering_form_url') . (( strpos(get_option('bsa_pro_plugin_ordering_form_url'), '?') == TRUE ) ? '&' : '?') ?>bsa_pro_stats=1&bsa_pro_email=<?php echo str_replace('@', '%40', $entry['buyer_email']); ?>&bsa_pro_id=<?php echo $entry['id']; ?>">
							full statistics
						</a><br>
						<?php if ( bsa_get_trans('user_panel', 'viewable') != '' ): ?>
							- - - -<br>
							<?php $minutes = ($viewable > 60 ? $viewable / 60 : 0);
							$seconds = ($minutes - intval($minutes)) * 60; ?>
							<?php echo bsa_get_trans('user_panel', 'viewable'); ?><br> <strong><?php echo intval($minutes); ?> <?php echo bsa_get_trans('user_panel', 'view_min'); ?> <?php echo intval($seconds); ?> <?php echo bsa_get_trans('user_panel', 'view_sec'); ?></strong><br>
						<?php endif; ?>
					</td>
					<td>
						<?php
						if ( $entry['ad_model'] == 'cpd' ) {
							$time = time();
							$limit = $entry['ad_limit'];
							$diff = $limit - $time;
							$limit_value = ( $diff < 86400 /* 1 day in sec */ ) ? ( $diff > 0 ) ? 'less than 1 day' : '0 days' : number_format($diff / 24 / 60 / 60).' days';
							$diffTime = date('d/m/Y (H:i)', time() + $diff); // d M Y (H:i)
						} else {
							$limit_value = ($entry['ad_model'] == 'cpc') ? $entry['ad_limit'].' clicks' : $entry['ad_limit'].' views';
							$diffTime = '';
						}
						?>
						<strong><?php echo $limit_value; ?></strong><br>
						<?php echo ( $entry['ad_model'] == 'cpd' ) ? $diffTime.'<br>' : ''; ?>
					</td>
					<td>
						Ad / Order ID <strong><?php echo $entry['id']; ?></strong><br>
						Billing Model <strong><?php echo strtoupper($entry['ad_model']); ?></strong><br>
                        Earned <strong><?php echo $before.$entry['cost'].$after; ?></strong><br>
						<?php if ( $entry['paid'] == 1 ): ?>
							<span class="bsaColorGreen">Paid</span>
						<?php elseif ( $entry['paid'] == 2 ): ?>
							<span class="bsaColorGreen">Added by Admin</span>
						<?php else: ?>
							<span class="bsaColorRed">Not paid</span>
						<?php endif; ?>
					</td>
				</tr>

			<?php }
		} else {
			?>

			<tr>
				<td style="text-align: center" colspan="7">
					List empty.
				</td>
			</tr>

		<?php } ?>
		</tbody>
	</table>

	<h3>Pending Ads (<?php echo count($getUserPendingAds) . ((count($getUserPendingAds) > 1) ? ' ads' : ' ad') ?> waiting for approval)</h3>
	<table class="wp-list-table widefat bsaListTable">
		<thead>
		<tr>
			<th></th>
			<th style="" class="manage-column post-title page-title column-title">Ad Content</th>
			<th style="" class="manage-column">Buyer</th>
			<th style="" class="manage-column">Stats</th>
			<th style="" class="manage-column">Ad Display Limit</th>
			<th style="" class="manage-column">Order Details</th>
		</tr>
		</thead>

		<tbody>
		<?php
		if (is_array($getUserPendingAds) && count($getUserPendingAds) > 0) {
			foreach ($getUserPendingAds as $key => $entry) {

				if ($key % 2) {
					$alternate = '';
				} else {
					$alternate = 'alternate';
				}
				?>

				<tr class="<?php echo $alternate; ?>">
					<td class="bsaAdminImg">
						<img class="bsaAdminThumb" src="<?php echo ( $entry['img'] != '' ) ? bsa_upload_url(null, $entry['img']) : plugin_dir_url( __DIR__ ).'frontend/img/example.png'; ?>">
					</td>
					<td class="post-title page-title column-title">
						<?php echo ( $entry['title'] != '' ) ? '<strong>'.$entry['title'].'</strong><br>' : ''; ?>
						<?php echo ( $entry['description'] != '' ) ? $entry['description'].'<br>' : ''; ?>
						<?php echo ( $entry['url'] != '' ) ? '<a href="'.$entry['url'].'" target="_blank">'.substr($entry['url'], 0, 50).( strlen($entry['url']) > 50 ? '...</a>' : '</a>' ) : ''; ?><br>
						<?php echo ( $entry['html'] != '' ) ? 'HTML / JS Code' : '' ; ?>
						<div class="row-actions">
							- - - -<br>
							<span class="bsaPaidBtn">
								<a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-add-new-ad&ad_id=<?php echo $entry['id']; ?>">
									Edit
								</a>
							</span>
						</div>
					</td>
					<td>
						<?php echo $entry['buyer_email']; ?>
					</td>
					<td>
						<?php
						$views = bsa_counter($entry['id'], 'view');
						$clicks = bsa_counter($entry['id'], 'click');
						$viewable = bsa_counter($entry['id'], 'viewable'); ?>
						<?php echo bsa_get_trans('user_panel', 'views'); ?> <strong><?php echo ( $views != NULL ) ? $views : 0; ?></strong><br>
						<?php echo bsa_get_trans('user_panel', 'clicks'); ?> <strong><?php echo ( $clicks != NULL ) ? $clicks : 0; ?></strong><br>
						<?php if ( $views != NULL && $clicks != NULL ): ?>
							CTR <strong><?php echo number_format(($clicks / $views) * 100, 2, '.', '').'%'; ?></strong><br>
						<?php endif; ?>
						<a target="_blank" href="<?php echo get_option('bsa_pro_plugin_ordering_form_url') . (( strpos(get_option('bsa_pro_plugin_ordering_form_url'), '?') == TRUE ) ? '&' : '?') ?>bsa_pro_stats=1&bsa_pro_email=<?php echo str_replace('@', '%40', $entry['buyer_email']); ?>&bsa_pro_id=<?php echo $entry['id']; ?>">
							full statistics
						</a><br>
						<?php if ( bsa_get_trans('user_panel', 'viewable') != '' ): ?>
							- - - -<br>
							<?php $minutes = ($viewable > 60 ? $viewable / 60 : 0);
							$seconds = ($minutes - intval($minutes)) * 60; ?>
							<?php echo bsa_get_trans('user_panel', 'viewable'); ?><br> <strong><?php echo intval($minutes); ?> <?php echo bsa_get_trans('user_panel', 'view_min'); ?> <?php echo intval($seconds); ?> <?php echo bsa_get_trans('user_panel', 'view_sec'); ?></strong><br>
						<?php endif; ?>
					</td>
					<td>
						<?php
						if ( $entry['ad_model'] == 'cpd' ) {
							$time = time();
							$limit = $entry['ad_limit'];
							$diff = $limit - $time;
							$limit_value = ( $diff < 86400 /* 1 day in sec */ ) ? ( $diff > 0 ) ? 'less than 1 day' : '0 days' : number_format($diff / 24 / 60 / 60).' days';
							$diffTime = date('d/m/Y (H:i)', time() + $diff); // d M Y (H:i)
						} else {
							$limit_value = ($entry['ad_model'] == 'cpc') ? $entry['ad_limit'].' clicks' : $entry['ad_limit'].' views';
							$diffTime = '';
						}
						?>
						<strong><?php echo $limit_value; ?></strong><br>
						<?php echo ( $entry['ad_model'] == 'cpd' ) ? $diffTime.'<br>' : ''; ?>
					</td>
					<td>
						Ad / Order ID <strong><?php echo $entry['id']; ?></strong><br>
						Billing Model <strong><?php echo strtoupper($entry['ad_model']); ?></strong><br>
                        Earned <strong><?php echo $before.$entry['cost'].$after; ?></strong><br>
						<?php if ( $entry['paid'] == 1 ): ?>
							<span class="bsaColorGreen">Paid</span>
						<?php elseif ( $entry['paid'] == 2 ): ?>
							<span class="bsaColorGreen">Added by Admin</span>
						<?php else: ?>
							<span class="bsaColorRed">Not paid</span>
						<?php endif; ?>
					</td>
				</tr>

			<?php }
		} else {
			?>

			<tr>
				<td style="text-align: center" colspan="7">
					List empty.
				</td>
			</tr>

		<?php } ?>
		</tbody>
	</table>

<?php endif; ?>

<script>
	(function($) {
		"use strict";
		// - start - open page
		let bsaPageContent = $(".wrap");
		let waitingContent = $(".waitingContent");
		$(document).ready(function(){
			bsaPageContent.fadeIn();
			waitingContent.fadeOut();
		});
		// - end - open page
	})(jQuery);
</script>