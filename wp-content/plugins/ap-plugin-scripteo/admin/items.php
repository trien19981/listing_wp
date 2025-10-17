<?php
if (isset($_GET['bsa_pro_action'])) {
	$getParam = $_GET['bsa_pro_action'];
} else {
	$getParam = NULL;
}

//echo get_num_queries().'queries in '.timer_stop(1).' seconds.';
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

// active order by column
function get_order_ads($sid, $column)
{
	if ( isset($sid) && bsa_space($sid, 'order_ads') == $column ) {
		return 'bsaOrderActive';
	} else {
		return null;
	}
}

$model = new BSA_PRO_Model();
$get_spaces = $model->getSpaces('both');
$first_space = (isset($get_spaces[0]['id']) ? $get_spaces[0]['id'] : 0);
if ( isset($_GET['space_id']) && $_GET['space_id'] != NULL && $_GET['space_id'] != '' ) {
	$space_id = $_GET['space_id'];
} elseif ( $get_spaces != NULL ) {
	$space_id = $first_space;
} else {
	$space_id = 0;
}

$getActiveAds 	= $model->getActiveAds($space_id, intval(bsa_space($space_id, 'max_items') + 20), 'admin');
$getPendingAds 	= $model->getPendingAds('pending_ads', $space_id);
$getNotPaidAds 	= $model->getNotPaidAds($space_id, 40);
$getBlockedAds 	= $model->getBlockedAds($space_id, 40);
$getArchiveAds 	= $model->getArchiveAds($space_id, 40);
$getStats 		= $model->getStats($space_id);
?>

<div class="bsaActionNotice bsaSortableNotice" style="display:none">
    Changes have been saved.
</div>

<h2>
    <span class="dashicons dashicons-welcome-widgets-menus"></span> Manage Ad Spaces and Ads
</h2>

<?php if ( isset($_GET['space_id']) && bsa_space($_GET['space_id'], 'id') != NULL || !isset($_GET['space_id']) ): ?>

	<?php if ( isset($get_spaces) ): ?>

        <h2 class="nav-tab-wrapper">
			<?php if ( bsa_get_opt('admin_settings', 'selection') == 'select' ): ?>
                <label for="bsa_pro_space_select">Select Space</label>
                <select id="bsa_pro_space_select" name="space_select">
					<?php foreach ( $get_spaces as $space ): ?>
                        <option value="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces&space_id=<?php echo $space['id']; ?>" <?php if ( isset($space) && $first_space == $space['id'] && !isset($_GET['space_id']) OR isset($_GET['space_id']) && $_GET['space_id'] == $space['id'] ) { echo 'selected="selected"'; } else { echo ''; } ?>>
							<?php echo $space['name']; ?> <?php echo ($space['status'] == 'active') ? '<small>(<span class="bsaGreen">active</span>)</small>' : '<small>(<span class="bsaRed">inactive</span>)</small>' ?>
                        </option>
					<?php endforeach; ?>
                </select>
			<?php else: ?>
				<?php foreach ( $get_spaces as $space ): ?>
                    <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces&space_id=<?php echo $space['id']; ?>"
                       class="nav-tab <?php if ( isset($space) && $first_space == $space['id'] && !isset($_GET['space_id']) OR isset($_GET['space_id']) && $_GET['space_id'] == $space['id'] ) { echo 'nav-tab-active'; } else { echo ''; } ?>">
						<?php echo $space['name']; ?> <?php echo ($space['status'] == 'active') ? '<small>(<span class="bsaGreen">active</span>)</small>' : '<small>(<span class="bsaRed">inactive</span>)</small>' ?>
                    </a>
				<?php endforeach; ?>
			<?php endif; ?>
            <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-add-new-space" class="nav-tab" style="float: right;margin-top: 0;"><span class="bsaGreen">+</span> Add new Space</a>
        </h2>

        <div class="bsaSpaceFilter wp-filter">
            <ul class="filter-links">
                <li class="bsaSpaceShortCode">
					<?php if( $space_id && bsa_space($space_id, 'status') == 'removed' ) {
						?><span style="display: inline-block;padding: 8px 0;"><strong>Note!</strong> This space was removed and you cannot use it!</span><?php
					} else {
						?>Use this Ad Space by shortcode <input class="bsaSpaceShortCodeInner" type="text" value="[bsa_pro_ad_space id=<?php echo $space_id; ?>]" placeholder=""><?php
					} ?>
                </li>
				<?php if ( $get_spaces && bsa_space($space_id, 'status') != 'removed'): ?>
                    <li class="addNewSpace">
                        <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-add-new-space&space_id=<?php echo $space_id; ?>" class="current currentBlue">Edit Space</a>
                        <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-add-new-ad" class="current currentGreen">Add new Ad</a>
                        <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces&space_id=<?php echo $space_id; ?>&remove_action=1&remove_confirm=1" class="current currentRed removeConfirm">Remove Space</a>
                    </li>
				<?php endif; ?>
            </ul>
        </div>

		<?php $model->getAdminAction();
		if ($model->validationBlocked()) {
			echo '
            <div class="updated settings-error" id="setting-error-settings_updated">
                <p><div class="bsaLoader"></div><strong>Ad has been blocked.</strong></p>
            </div>';
		} elseif ($model->validationUnblocked()) {
			echo '
            <div class="updated settings-error" id="setting-error-settings_updated">
                <p><div class="bsaLoader"></div><strong>Ad has been unblocked.</strong></p>
            </div>';
		} elseif ($model->validationRemoved()) {
			echo '
            <div class="updated settings-error" id="setting-error-settings_updated">
                <p><div class="bsaLoader"></div><strong>Ad has been removed.</strong></p>
            </div>';
		} elseif ($model->validationPaid()) {
			echo '
            <div class="updated settings-error" id="setting-error-settings_updated">
                <p><div class="bsaLoader"></div><strong>Ad has been marked as paid.</strong></p>
            </div>';
		} ?>

		<?php
		if( isset($_GET['remove_action']) && !isset($_GET['remove_confirm']) ) {
			echo '
            <div class="updated settings-error" id="setting-error-settings_updated">
                <p><strong>Confirm remove action!</strong> Yes, I <a href="'.admin_url().'admin.php?page=bsa-pro-sub-menu-spaces&space_id='.$_GET['space_id'].'&remove_action=1&remove_confirm=1">want</a> to delete Space ID <strong>'.$_GET['space_id'].'</strong>.</p>
            </div>';
		}
		?>

        <div class="spaceDetailsContainer">
            <strong>Space details</strong>
            <a class="spaceDetailsButton spaceDetailsShow">show</a>
            <a class="spaceDetailsButton spaceDetailsHide" style="display:none;">hide</a>
            <div class="spaceDetails" style="display:none;">
				<?php $space = bsa_space($space_id); ?>
				<?php $space_advanced = json_decode($space["advanced_opt"], true); ?>
                <div class="spaceDetailsCol">
                    <strong>General</strong><br>
					<?php if ( $space['title'] != '' ): ?>
                        Title: <strong><?= $space['title']; ?></strong><br>
					<?php endif; ?>
                    Template: <strong><?php echo ucfirst ( str_replace('-',' ',$space['template']) ); ?></strong><br>
                    Display type: <strong><?php echo ucfirst ( str_replace('_',' ', str_replace('-',' ', $space['display_type'])) ); ?></strong><br>
                    Max Ads: <strong><?= $space['max_items']; ?></strong><br>
                    Ads in Cols / Rows: <strong><?= $space['col_per_row']; ?></strong>
					<?php if ( $space['animation'] != '' && $space['animation'] != 'none' ): ?>
                        <br>Animation: <strong class="spaceEnabled">enabled</strong>
					<?php endif; ?>
					<?php if ( $space['close_action'] != '0,0,0' ): ?>
                        <br>Delays: <strong class="spaceEnabled">enabled</strong>
					<?php endif; ?>
                </div>
                <div class="spaceDetailsCol spaceDetailsBilling">
                    <strong>Billing Models</strong><br>
                    <div class="">
						<?php if ( $space['cpc_price'] > 0 && $space['cpc_contract_1'] > 0 ): ?>
                            CPC from <strong><?= $before.bsa_number_format($space['cpc_price']).$after; ?></strong><br>
							<?php if ( $space['cpc_contract_2'] > 0 ): ?>
                                (<?= $space['cpc_contract_1']; ?> - <?= ( $space['cpc_contract_3'] > $space['cpc_contract_2'] ? $space['cpc_contract_3'] : $space['cpc_contract_2']); ?> clicks)<br>
							<?php else: ?>
                                (<?= $space['cpc_contract_1']; ?> clicks)<br>
							<?php endif; ?>
						<?php else: ?>
                            CPC model <strong class="spaceDisabled">disabled</strong><br>
						<?php endif; ?>
						<?php if ( $space['cpm_price'] > 0 && $space['cpm_contract_1'] > 0 ): ?>
                            CPM from <strong><?= $before.bsa_number_format($space['cpm_price']).$after; ?></strong><br>
							<?php if ( $space['cpm_contract_2'] > 0 ): ?>
                                (<?= $space['cpm_contract_1']; ?> - <?= ( $space['cpm_contract_3'] > $space['cpm_contract_2'] ? $space['cpm_contract_3'] : $space['cpm_contract_2']); ?> views)<br>
							<?php else: ?>
                                (<?= $space['cpm_contract_1']; ?> clicks)<br>
							<?php endif; ?>
						<?php else: ?>
                            CPM model <strong class="spaceDisabled">disabled</strong><br>
						<?php endif; ?>
						<?php if ( $space['cpd_price'] > 0 && $space['cpd_contract_1'] > 0 ): ?>
                            CPD from <strong><?= $before.bsa_number_format($space['cpd_price']).$after; ?></strong><br>
							<?php if ( $space['cpd_contract_2'] > 0 ): ?>
                                (<?= $space['cpd_contract_1']; ?> - <?= ( $space['cpd_contract_3'] > $space['cpd_contract_2'] ? $space['cpd_contract_3'] : $space['cpd_contract_2']); ?> days)<br>
							<?php else: ?>
                                (<?= $space['cpd_contract_1']; ?> clicks)<br>
							<?php endif; ?>
						<?php else: ?>
                            CPD model <strong class="spaceDisabled">disabled</strong><br>
						<?php endif; ?>
						<?php if ( $space['discount_2'] > 0 ): ?>
                            Discounts: <strong><?= ($space['discount_3'] > 0 ? $space['discount_2'].'%, '.$space['discount_3'].'%' : $space['discount_2'].'%'); ?></strong><br>
						<?php elseif ( $space['discount_3'] > 0 ): ?>
                            Discounts: <strong><?= $space['discount_3'].'%'; ?></strong><br>
						<?php else: ?>
                            Discounts <strong class="spaceDisabled">disabled</strong>
						<?php endif; ?>
                    </div>
                </div>
                <div class="spaceDetailsCol">
                    <strong>Filters</strong><br>
                    Devices: <strong><?= ($space['devices'] != '' ? str_replace(',',', ', $space['devices']) : 'mobile, tablet, desktop'); ?></strong><br>
					<?php if ( isset($space_advanced['hide_for_id']) && $space_advanced['hide_for_id'] != '' ): ?>
                        Hide for Posts / Pages: <strong class="spaceEnabled">enabled</strong><br>
					<?php endif; ?>
					<?php if ( isset($space_advanced['hide_customs']) && $space_advanced['hide_customs'] != '' ): ?>
                        Hide for Customs: <strong class="spaceEnabled">enabled</strong><br>
					<?php endif; ?>
					<?php if ( $space['in_categories'] != '' ): ?>
                        Show for Categories: <strong class="spaceEnabled">enabled</strong><br>
					<?php endif; ?>
					<?php if ( $space['has_tags'] != '' ): ?>
                        Show for Tags: <strong class="spaceEnabled">enabled</strong><br>
					<?php endif; ?>
					<?php if ( isset($space_advanced['show_customs']) && $space_advanced['show_customs'] != '' ): ?>
                        Show for Customs: <strong class="spaceEnabled">enabled</strong><br>
					<?php endif; ?>
					<?php if ( $space['show_in_country'] != '' ): ?>
                        Show in Countries: <strong class="spaceEnabled">enabled</strong><br>
					<?php endif; ?>
					<?php if ( $space['hide_in_country'] != '' ): ?>
                        Hide in Countries: <strong class="spaceEnabled">enabled</strong><br>
					<?php endif; ?>
					<?php if ( $space['show_in_advanced'] != '' ): ?>
                        Show in Locations: <strong class="spaceEnabled">enabled</strong><br>
					<?php endif; ?>
					<?php if ( $space['show_in_advanced'] != '' ): ?>
                        Hide in Locations: <strong class="spaceEnabled">enabled</strong>
					<?php endif; ?>
                </div>
            </div>
        </div>

        <div class="wp-clearfix"></div>

        <h3 style="margin-bottom: 10px;">Active Ads (<?php echo (is_array($getActiveAds) ? count($getActiveAds) : 0); ?>)</h3>
        <div style="margin:10px 0 20px;">order by
			<?php if (bsa_space($space_id, 'order_ads') == 'id_up'): ?>
                <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces&space_id=<?php echo $space_id; ?>&order_ads=id" class="bsaOrderBy <?php echo get_order_ads($space_id, 'id_up'); ?>"><span class="dashicons dashicons-editor-ol"></span> id <span class="dashicons dashicons-arrow-up"></span></a>
			<?php else: ?>
                <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces&space_id=<?php echo $space_id; ?>&order_ads=id_up" class="bsaOrderBy <?php echo get_order_ads($space_id, 'id'); ?>"><span class="dashicons dashicons-editor-ol"></span> id <span class="dashicons dashicons-arrow-down"></span></a>
			<?php endif; ?>
			<?php if (bsa_space($space_id, 'order_ads') == 'ad_limit_up'): ?>
                <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces&space_id=<?php echo $space_id; ?>&order_ads=ad_limit" class="bsaOrderBy <?php echo get_order_ads($space_id, 'ad_limit_up'); ?>"><span class="dashicons dashicons-clock"></span> display limit <span class="dashicons dashicons-arrow-up"></span></a>
			<?php else: ?>
                <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces&space_id=<?php echo $space_id; ?>&order_ads=ad_limit_up" class="bsaOrderBy <?php echo get_order_ads($space_id, 'ad_limit'); ?>"><span class="dashicons dashicons-clock"></span> display limit <span class="dashicons dashicons-arrow-down"></span></a>
			<?php endif; ?>
            <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces&space_id=<?php echo $space_id; ?>&order_ads=priority" class="bsaOrderBy <?php echo get_order_ads($space_id, 'priority'); ?>"><img src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/icon-drag-16.png'; ?>" /> priority</a>
			<?php if (bsa_space($space_id, 'order_ads') == 'cost_up'): ?>
                <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces&space_id=<?php echo $space_id; ?>&order_ads=cost" class="bsaOrderBy <?php echo get_order_ads($space_id, 'cost_up'); ?>"><span class="dashicons dashicons-vault"></span> cost <span class="dashicons dashicons-arrow-up"></span></a>
			<?php else: ?>
                <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces&space_id=<?php echo $space_id; ?>&order_ads=cost_up" class="bsaOrderBy <?php echo get_order_ads($space_id, 'cost'); ?>"><span class="dashicons dashicons-vault"></span> cost <span class="dashicons dashicons-arrow-down"></span></a>
			<?php endif; ?>
        </div>
        <table class="wp-list-table widefat bsaListTable">
            <thead>
            <tr>
                <th></th>
                <th style="" class="manage-column post-title page-title column-title">Ad Content</th>
                <th style="" class="manage-column">Buyer</th>
                <th style="" class="manage-column">Stats</th>
                <th style="" class="manage-column">Display Limit</th>
                <th style="" class="manage-column">Order Details</th>
            </tr>
            </thead>

            <tbody id="bsaSortable" class="<?php echo ( (get_order_ads($space_id, 'priority')) ? 'bsaSortableOn' : null ); ?>">
			<?php
			if (is_array($getActiveAds) && count($getActiveAds) > 0) {
				foreach ($getActiveAds as $key => $entry) {

					if ($key % 2) {
						$alternate = '';
					} else {
						$alternate = 'alternate';
					}
					?>

                    <tr class="<?php echo $alternate; ?>" id="<?php echo $entry['id']; ?>">
                        <td class="bsaAdminImg">
                            <img class="bsaAdminThumb" src="<?php echo ( $entry['img'] != '' ) ? bsa_upload_url(null, $entry['img']) : plugin_dir_url( __DIR__ ).'frontend/img/example.png'; ?>">
                        </td>
                        <td class="post-title page-title column-title">
							<?php echo (isset($entry['ad_name']) && $entry['ad_name'] != '') ? '<span class="bsaAdName">'.$entry['ad_name'].'</span>' : null; ?>
							<?php echo ( $entry['title'] != '' ) ? '<strong>'.$entry['title'].'</strong>' : ''; ?>
							<?php echo ( $entry['description'] != '' ) ? $entry['description'].'<br>' : ''; ?>
							<?php echo ( $entry['url'] != '' ) ? '<a href="'.$entry['url'].'" target="_blank">'.substr($entry['url'], 0, 50).( strlen($entry['url']) > 50 ? '...</a>' : '</a>' ) : ''; ?><br>
							<?php echo ( $entry['html'] != '' ) ? 'HTML / JS Code' : '' ; ?>
                            <div class="row-actions">
                                - - - -<br>
                                <form action="" method="post">
                                    <input type="hidden" value="block" name="bsaProAction">
                                    <input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
                                    <span class="bsaProActionBtn bsaBlockBtn">
                                        <input type="submit" value="Disable" id="submit" name="submit">
                                    </span>
                                </form>
                                |
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
							$views = (isset($getStats[$entry['id']]['views'])  ? $getStats[$entry['id']]['views'] : 0);
							$clicks = (isset($getStats[$entry['id']]['clicks'])  ? $getStats[$entry['id']]['clicks'] : 0);
							$viewable = (isset($getStats[$entry['id']]['viewable'])  ? $getStats[$entry['id']]['viewable'] : 0); ?>
                            Views <strong><?php echo ( $views != NULL ) ? $views : 0; ?></strong><br>
                            Clicks <strong><?php echo ( $clicks != NULL ) ? $clicks : 0; ?></strong><br>
							<?php if ( $views != NULL && $clicks != NULL ): ?>
                                CTR <strong><?php echo number_format(($clicks / $views) * 100, 2, '.', '').'%'; ?></strong><br>
							<?php endif; ?>
                            <a target="_blank" href="<?php echo get_option('bsa_pro_plugin_ordering_form_url') . (( strpos(get_option('bsa_pro_plugin_ordering_form_url'), '?') == TRUE ) ? '&' : '?') ?>bsa_pro_stats=1&bsa_pro_email=<?php echo str_replace('@', '%40', $entry['buyer_email']); ?>&bsa_pro_id=<?php echo $entry['id']; ?>">
                                full statistics
                            </a><br>
							<?php if ( bsa_get_trans('statistics', 'viewable') != '' ): ?>
                                - - - -<br>
								<?php $minutes = ($viewable > 60 ? $viewable / 60 : 0);
								$seconds = ($minutes - intval($minutes)) * 60; ?>
								<?php echo bsa_get_trans('statistics', 'viewable'); ?><br> <strong><?php echo intval($minutes); ?> <?php echo bsa_get_trans('statistics', 'view_min'); ?> <?php echo intval($seconds); ?> <?php echo bsa_get_trans('statistics', 'view_sec'); ?></strong><br>
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
							$limit_value = apply_filters( "bsa-pro-limitValue", $limit_value, $entry);
							?>
                            <strong><?php echo $limit_value; ?></strong><br>
							<?php echo ( $entry['ad_model'] == 'cpd' ) ? $diffTime.'<br>' : ''; ?>
							<?php if ( isset($entry['starts']) && $entry['starts'] > time() || isset($entry['ends']) && $entry['ends'] > time() ): ?>
                                - - - -<br>
							<?php endif; ?>
							<?php if ( isset($entry['starts']) && $entry['starts'] > time() ): ?>
                                Start Date<br>
                                <strong><?php echo date('d/m/Y (H:i)', $entry['starts']); ?></strong><br>
							<?php endif; ?>
							<?php if ( isset($entry['ends']) && $entry['ends'] > time() ): ?>
                                Ends<br>
                                <strong><?php echo date('d/m/Y (H:i)', $entry['ends']); ?></strong><br>
							<?php endif; ?>
							<?php if ( isset($entry['show_in_country']) && $entry['show_in_country'] != '' || isset($entry['show_in_advanced']) && $entry['show_in_advanced'] != '' ): ?>
                                - - - -<br>
                                Geotarget:
								<?php if ( isset($entry['show_in_country']) && $entry['show_in_country'] != '' ): ?>
                                    <strong><?php echo ( strlen($entry['show_in_country']) > 10 ? substr($entry['show_in_country'], 0, 10).'.. ' : $entry['show_in_country'].' ' ); ?></strong>
								<?php endif; ?>
                                <?php if ( isset($entry['show_in_advanced']) && $entry['show_in_advanced'] != '' ): ?>
                                    <strong><?php echo ( strlen($entry['show_in_advanced']) > 10 ? substr($entry['show_in_advanced'], 0, 10).'..' : $entry['show_in_advanced'] ); ?></strong>
                                <?php endif; ?><br>
							<?php endif; ?>
							<?php if ( isset($entry['capping']) && $entry['capping'] > 0 ): ?>
                                - - - -<br>
                                Capping: <strong><?php echo $entry['capping'] ?></strong><br>
							<?php endif; ?>
							<?php $scheduleTask = $model->getPendingTask($entry['id'], 'ad'); ?>
							<?php if ( isset($scheduleTask) && isset($scheduleTask['start_time']) && $scheduleTask['start_time'] > time() ): ?>
                                - - - -<br>
                                <span class="dashicons dashicons-clock"></span> Scheduled on <?php echo date('d/m/Y (H:i)', $scheduleTask['start_time']); ?>
							<?php endif; ?>
                        </td>
                        <td>
                            Ad / Order ID <strong><?php echo $entry['id']; ?></strong><br>
                            Billing Model <strong><?php echo strtoupper($entry['ad_model']); ?></strong><br>
                            Earned <strong><?php echo $before . bsa_number_format($entry['cost']) . $after; ?></strong><br>
							<?php if ( $entry['paid'] == 1 ): ?>
                                <span class="bsaColorGreen">Paid</span>
							<?php elseif ( $entry['paid'] == 2 ): ?>
                                <span class="bsaColorGreen">Added by Admin</span>
							<?php else: ?>
                                <span class="bsaColorRed">Not paid</span>
							<?php endif; ?><br>
							<?php echo ( $entry['optional_field'] != '' ) ? '- - - -<br>'.$entry['optional_field'] : '' ; ?>
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

        <h3>Pending Ads</h3>
        <table class="wp-list-table widefat bsaListTable">
            <thead>
            <tr>
                <th></th>
                <th style="" class="manage-column post-title page-title column-title">Ad Content</th>
                <th style="" class="manage-column">Buyer</th>
                <th style="" class="manage-column">Stats</th>
                <th style="" class="manage-column">Display Limit</th>
                <th style="" class="manage-column">Order Details</th>
            </tr>
            </thead>

            <tbody>
			<?php
			if (is_array($getPendingAds) && count($getPendingAds) > 0) {
				foreach ($getPendingAds as $key => $entry) {

					if ($key % 2) {
						$alternate = '';
					} else {
						$alternate = 'alternate';
					}
					?>

                    <tr class="<?php echo $alternate; ?>">
                        <td class="bsaAdminImg">
                            <img class="bsaAdminThumb"
                                 src="<?php echo ($entry['img'] != '') ? bsa_upload_url(null, $entry['img']) : plugin_dir_url( __DIR__ ).'frontend/img/example.png'; ?>">
                        </td>
                        <td class="post-title page-title column-title">
							<?php echo (isset($entry['ad_name']) && $entry['ad_name'] != '') ? '<span class="bsaAdName">'.$entry['ad_name'].'</span>' : null; ?>
							<?php echo ( $entry['title'] != '' ) ? '<strong>'.$entry['title'].'</strong>' : ''; ?>
							<?php echo ( $entry['description'] != '' ) ? $entry['description'].'<br>' : ''; ?>
							<?php echo ( $entry['url'] != '' ) ? '<a href="'.$entry['url'].'" target="_blank">'.substr($entry['url'], 0, 50).( strlen($entry['url']) > 50 ? '...</a>' : '</a>' ) : ''; ?><br>
							<?php echo ( $entry['html'] != '' ) ? 'HTML / JS Code' : '' ; ?>
                            <div class="row-actions">
                                - - - -<br>
                                <form action="" method="post">
                                    <input type="hidden" value="block" name="bsaProAction">
                                    <input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
                                    <span class="bsaProActionBtn bsaBlockBtn"><input type="submit" value="Disable" id="submitBlock" name="submit"></span>
                                </form>
                                |
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
							$views = (isset($getStats[$entry['id']]['views'])  ? $getStats[$entry['id']]['views'] : 0);
							$clicks = (isset($getStats[$entry['id']]['clicks'])  ? $getStats[$entry['id']]['clicks'] : 0);
							$viewable = (isset($getStats[$entry['id']]['viewable'])  ? $getStats[$entry['id']]['viewable'] : 0); ?>
                            Views <strong><?php echo ( $views != NULL ) ? $views : 0; ?></strong><br>
                            Clicks <strong><?php echo ( $clicks != NULL ) ? $clicks : 0; ?></strong><br>
							<?php if ($views != NULL && $clicks != NULL): ?>
                                CTR <strong><?php echo number_format(($clicks / $views) * 100, 2, '.', '') . '%'; ?></strong><br>
							<?php endif; ?>
                            <a target="_blank" href="<?php echo get_option('bsa_pro_plugin_ordering_form_url') . (( strpos(get_option('bsa_pro_plugin_ordering_form_url'), '?') == TRUE ) ? '&' : '?') ?>bsa_pro_stats=1&bsa_pro_email=<?php echo str_replace('@', '%40', $entry['buyer_email']); ?>&bsa_pro_id=<?php echo $entry['id']; ?>">
                                full statistics
                            </a><br>
							<?php if ( bsa_get_trans('statistics', 'viewable') != '' ): ?>
                                - - - -<br>
								<?php $minutes = ($viewable > 60 ? $viewable / 60 : 0);
								$seconds = ($minutes - intval($minutes)) * 60; ?>
								<?php echo bsa_get_trans('statistics', 'viewable'); ?><br> <strong><?php echo intval($minutes); ?> <?php echo bsa_get_trans('statistics', 'view_min'); ?> <?php echo intval($seconds); ?> <?php echo bsa_get_trans('statistics', 'view_sec'); ?></strong><br>
							<?php endif; ?>
                        </td>
                        <td>
							<?php
							if ($entry['ad_model'] == 'cpd') {
								$time = time();
								$limit = $entry['ad_limit'];
								$diff = $limit - $time;
								$limit_value = ($diff < 86400 /* 1 day in sec */) ? ($diff > 0) ? 'less than 1 day' : '0 days' : number_format($diff / 24 / 60 / 60) . ' days';
								$diffTime = date('d/m/Y (H:i)', time() + $diff); // d M Y (H:i)
							} else {
								$limit_value = ($entry['ad_model'] == 'cpc') ? $entry['ad_limit'] . ' clicks' : $entry['ad_limit'] . ' views';
								$diffTime = '';
							}
							?>
                            <strong><?php echo $limit_value; ?></strong><br>
							<?php echo ( $entry['ad_model'] == 'cpd' ) ? $diffTime.'<br>' : ''; ?>
							<?php if ( isset($entry['starts']) && $entry['starts'] > time() || isset($entry['ends']) && $entry['ends'] > time() ): ?>
                                - - - -<br>
							<?php endif; ?>
							<?php if ( isset($entry['starts']) && $entry['starts'] > time() ): ?>
                                Start Date<br>
                                <strong><?php echo date('d/m/Y (H:i)', $entry['starts']); ?></strong><br>
							<?php endif; ?>
							<?php if ( isset($entry['ends']) && $entry['ends'] > time() ): ?>
                                Ends<br>
                                <strong><?php echo date('d/m/Y (H:i)', $entry['ends']); ?></strong><br>
							<?php endif; ?>
							<?php if ( isset($entry['show_in_country']) && $entry['show_in_country'] != '' || isset($entry['show_in_advanced']) && $entry['show_in_advanced'] != '' ): ?>
                                - - - -<br>
                                Geotarget:
								<?php if ( isset($entry['show_in_country']) && $entry['show_in_country'] != '' ): ?>
                                    <strong><?php echo ( strlen($entry['show_in_country']) > 10 ? substr($entry['show_in_country'], 0, 10).'.. ' : $entry['show_in_country'].' ' ); ?></strong>
								<?php endif; ?>
								<?php if ( isset($entry['show_in_advanced']) && $entry['show_in_advanced'] != '' ): ?>
                                    <strong><?php echo ( strlen($entry['show_in_advanced']) > 10 ? substr($entry['show_in_advanced'], 0, 10).'..' : $entry['show_in_advanced'] ); ?></strong>
								<?php endif; ?><br>
							<?php endif; ?>
							<?php if ( isset($entry['capping']) && $entry['capping'] > 0 ): ?>
                                - - - -<br>
                                Capping: <strong><?php echo $entry['capping'] ?></strong><br>
							<?php endif; ?>
							<?php $scheduleTask = $model->getPendingTask($entry['id'], 'ad'); ?>
							<?php if ( isset($scheduleTask) && isset($scheduleTask['start_time']) && $scheduleTask['start_time'] > time() ): ?>
                                - - - -<br><span class="dashicons dashicons-clock"></span> Scheduled on <?php echo date('d/m/Y (H:i)', $scheduleTask['start_time']); ?>
							<?php endif; ?>
                        </td>
                        <td>
                            Space ID <strong><?php echo $entry['space_id']; ?></strong><br>
                            Ad / Order ID <strong><?php echo $entry['id']; ?></strong><br>
                            Billing Model <strong><?php echo strtoupper($entry['ad_model']); ?></strong><br>
                            Earned <strong><?php echo $before . bsa_number_format($entry['cost']) . $after; ?></strong><br>
							<?php if ( $entry['paid'] == 1 ): ?>
                                <span class="bsaColorGreen">Paid</span>
							<?php elseif ( $entry['paid'] == 2 ): ?>
                                <span class="bsaColorGreen">Added by Admin</span>
							<?php else: ?>
                                <span class="bsaColorRed">Not paid</span>
							<?php endif; ?><br>
							<?php echo ( $entry['optional_field'] != '' ) ? '- - - -<br>'.$entry['optional_field'] : '' ; ?>
                        </td>
                    </tr>

					<?php
				}
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

        <h3>Not Paid Ads</h3>
        <table class="wp-list-table widefat bsaListTable">
            <thead>
            <tr>
                <th></th>
                <th style="" class="manage-column post-title page-title column-title">Ad Content</th>
                <th style="" class="manage-column">Buyer</th>
                <th style="" class="manage-column">Stats</th>
                <th style="" class="manage-column">Display Limit</th>
                <th style="" class="manage-column">Order Details</th>
            </tr>
            </thead>

            <tbody>
			<?php
			if (is_array($getNotPaidAds) && count($getNotPaidAds) > 0) {
				foreach ($getNotPaidAds as $key => $entry) {

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
							<?php echo (isset($entry['ad_name']) && $entry['ad_name'] != '') ? '<span class="bsaAdName">'.$entry['ad_name'].'</span>' : null; ?>
							<?php echo ( $entry['title'] != '' ) ? '<strong>'.$entry['title'].'</strong>' : ''; ?>
							<?php echo ( $entry['description'] != '' ) ? $entry['description'].'<br>' : ''; ?>
							<?php echo ( $entry['url'] != '' ) ? '<a href="'.$entry['url'].'" target="_blank">'.substr($entry['url'], 0, 50).( strlen($entry['url']) > 50 ? '...</a>' : '</a>' ) : ''; ?><br>
							<?php echo ( $entry['html'] != '' ) ? 'HTML / JS Code' : '' ; ?>
                            <div class="row-actions">
                                - - - -<br>
                                <form action="" method="post">
                                    <input type="hidden" value="paid" name="bsaProAction">
                                    <input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
                                    <span class="bsaProActionBtn bsaPaidBtn"><input type="submit" value="Mark as paid" id="submitPaid" name="submit"></span>
                                </form>
                                |
                                <form action="" method="post">
                                    <input type="hidden" value="block" name="bsaProAction">
                                    <input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
                                    <span class="bsaProActionBtn bsaBlockBtn"><input type="submit" value="Disable" id="submitBlock" name="submit"></span>
                                </form>
                                |
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
							$views = (isset($getStats[$entry['id']]['views'])  ? $getStats[$entry['id']]['views'] : 0);
							$clicks = (isset($getStats[$entry['id']]['clicks'])  ? $getStats[$entry['id']]['clicks'] : 0);
							$viewable = (isset($getStats[$entry['id']]['viewable'])  ? $getStats[$entry['id']]['viewable'] : 0); ?>
                            Views <strong><?php echo ( $views != NULL ) ? $views : 0; ?></strong><br>
                            Clicks <strong><?php echo ( $clicks != NULL ) ? $clicks : 0; ?></strong><br>
							<?php if ( $views != NULL && $clicks != NULL ): ?>
                                CTR <strong><?php echo number_format(($clicks / $views) * 100, 2, '.', '').'%'; ?></strong><br>
							<?php endif; ?>
                            <a target="_blank" href="<?php echo get_option('bsa_pro_plugin_ordering_form_url') . (( strpos(get_option('bsa_pro_plugin_ordering_form_url'), '?') == TRUE ) ? '&' : '?') ?>bsa_pro_stats=1&bsa_pro_email=<?php echo str_replace('@', '%40', $entry['buyer_email']); ?>&bsa_pro_id=<?php echo $entry['id']; ?>">
                                full statistics
                            </a><br>
							<?php if ( bsa_get_trans('statistics', 'viewable') != '' ): ?>
                                - - - -<br>
								<?php $minutes = ($viewable > 60 ? $viewable / 60 : 0);
								$seconds = ($minutes - intval($minutes)) * 60; ?>
								<?php echo bsa_get_trans('statistics', 'viewable'); ?><br> <strong><?php echo intval($minutes); ?> <?php echo bsa_get_trans('statistics', 'view_min'); ?> <?php echo intval($seconds); ?> <?php echo bsa_get_trans('statistics', 'view_sec'); ?></strong><br>
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
                            <br>
							<?php echo ( $entry['ad_model'] == 'cpd' ) ? $diffTime.'<br>' : ''; ?>
							<?php if ( isset($entry['starts']) && $entry['starts'] > time() || isset($entry['ends']) && $entry['ends'] > time() ): ?>
                                - - - -<br>
							<?php endif; ?>
							<?php if ( isset($entry['starts']) && $entry['starts'] > time() ): ?>
                                Start Date<br>
                                <strong><?php echo date('d/m/Y (H:i)', $entry['starts']); ?></strong><br>
							<?php endif; ?>
							<?php if ( isset($entry['ends']) && $entry['ends'] > time() ): ?>
                                Ends<br>
                                <strong><?php echo date('d/m/Y (H:i)', $entry['ends']); ?></strong><br>
							<?php endif; ?>
							<?php if ( isset($entry['show_in_country']) && $entry['show_in_country'] != '' || isset($entry['show_in_advanced']) && $entry['show_in_advanced'] != '' ): ?>
                                - - - -<br>
                                Geotarget:
								<?php if ( isset($entry['show_in_country']) && $entry['show_in_country'] != '' ): ?>
                                    <strong><?php echo ( strlen($entry['show_in_country']) > 10 ? substr($entry['show_in_country'], 0, 10).'.. ' : $entry['show_in_country'].' ' ); ?></strong>
								<?php endif; ?>
								<?php if ( isset($entry['show_in_advanced']) && $entry['show_in_advanced'] != '' ): ?>
                                    <strong><?php echo ( strlen($entry['show_in_advanced']) > 10 ? substr($entry['show_in_advanced'], 0, 10).'..' : $entry['show_in_advanced'] ); ?></strong>
								<?php endif; ?><br>
							<?php endif; ?>
							<?php if ( isset($entry['capping']) && $entry['capping'] > 0 ): ?>
                                - - - -<br>
                                Capping: <strong><?php echo $entry['capping'] ?></strong><br>
							<?php endif; ?>
							<?php $scheduleTask = $model->getPendingTask($entry['id'], 'ad'); ?>
							<?php if ( isset($scheduleTask) && isset($scheduleTask['start_time']) && $scheduleTask['start_time'] > time() ): ?>
                                - - - -<br><span class="dashicons dashicons-clock"></span> Scheduled on <?php echo date('d/m/Y (H:i)', $scheduleTask['start_time']); ?>
							<?php endif; ?>
                        </td>
                        <td>
                            Ad / Order ID <strong><?php echo $entry['id']; ?></strong><br>
                            Billing Model <strong><?php echo strtoupper($entry['ad_model']); ?></strong><br>
                            Earned <strong><?php echo $before . bsa_number_format($entry['cost']) . $after; ?></strong><br>
							<?php if ( $entry['paid'] == 1 ): ?>
                                <span class="bsaColorGreen">Paid</span>
							<?php elseif ( $entry['paid'] == 2 ): ?>
                                <span class="bsaColorGreen">Added by Admin</span>
							<?php else: ?>
                                <span class="bsaColorRed">Not paid</span>
							<?php endif; ?><br>
							<?php echo ( $entry['optional_field'] != '' ) ? '- - - -<br>'.$entry['optional_field'] : '' ; ?>
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

        <h3>Disabled Ads</h3>
        <table class="wp-list-table widefat bsaListTable">
            <thead>
            <tr>
                <th></th>
                <th style="" class="manage-column post-title page-title column-title">Ad Content</th>
                <th style="" class="manage-column">Buyer</th>
                <th style="" class="manage-column">Stats</th>
                <th style="" class="manage-column">Display Limit</th>
                <th style="" class="manage-column">Order Details</th>
            </tr>
            </thead>

            <tbody>
			<?php
			if (is_array($getBlockedAds) && count($getBlockedAds) > 0) {
				foreach ($getBlockedAds as $key => $entry) {

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
							<?php echo (isset($entry['ad_name']) && $entry['ad_name'] != '') ? '<span class="bsaAdName">'.$entry['ad_name'].'</span>' : null; ?>
							<?php echo ( $entry['title'] != '' ) ? '<strong>'.$entry['title'].'</strong>' : ''; ?>
							<?php echo ( $entry['description'] != '' ) ? $entry['description'].'<br>' : ''; ?>
							<?php echo ( $entry['url'] != '' ) ? '<a href="'.$entry['url'].'" target="_blank">'.substr($entry['url'], 0, 50).( strlen($entry['url']) > 50 ? '...</a>' : '</a>' ) : ''; ?><br>
							<?php echo ( $entry['html'] != '' ) ? 'HTML / JS Code' : '' ; ?>
                            <div class="row-actions">
                                - - - -<br>
                                <form action="" method="post">
                                    <input type="hidden" value="unblock" name="bsaProAction">
                                    <input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
                                    <span class="bsaProActionBtn bsaPaidBtn"><input type="submit" value="Enable" id="submitBlock" name="submit"></span>
                                </form>
                                |
                                <span class="bsaPaidBtn">
                                    <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-add-new-ad&ad_id=<?php echo $entry['id']; ?>">
                                        Edit
                                    </a>
                                </span>
                                |
                                <form action="" method="post">
                                    <input type="hidden" value="remove" name="bsaProAction">
                                    <input type="hidden" value="<?php echo $entry['id']; ?>" name="orderId">
                                    <span class="bsaProActionBtn bsaBlockBtn"><input type="submit" value="Remove" id="submitBlock" name="submit"></span>
                                </form>
                            </div>
                        </td>
                        <td>
							<?php echo $entry['buyer_email']; ?>
                        </td>
                        <td>
							<?php
							$views = (isset($getStats[$entry['id']]['views'])  ? $getStats[$entry['id']]['views'] : 0);
							$clicks = (isset($getStats[$entry['id']]['clicks'])  ? $getStats[$entry['id']]['clicks'] : 0);
							$viewable = (isset($getStats[$entry['id']]['viewable'])  ? $getStats[$entry['id']]['viewable'] : 0); ?>
                            Views <strong><?php echo ( $views != NULL ) ? $views : 0; ?></strong><br>
                            Clicks <strong><?php echo ( $clicks != NULL ) ? $clicks : 0; ?></strong><br>
							<?php if ( $views != NULL && $clicks != NULL ): ?>
                                CTR <strong><?php echo number_format(($clicks / $views) * 100, 2, '.', '').'%'; ?></strong><br>
							<?php endif; ?>
                            <a target="_blank" href="<?php echo get_option('bsa_pro_plugin_ordering_form_url') . (( strpos(get_option('bsa_pro_plugin_ordering_form_url'), '?') == TRUE ) ? '&' : '?') ?>bsa_pro_stats=1&bsa_pro_email=<?php echo str_replace('@', '%40', $entry['buyer_email']); ?>&bsa_pro_id=<?php echo $entry['id']; ?>">
                                full statistics
                            </a><br>
							<?php if ( bsa_get_trans('statistics', 'viewable') != '' ): ?>
                                - - - -<br>
								<?php $minutes = ($viewable > 60 ? $viewable / 60 : 0);
								$seconds = ($minutes - intval($minutes)) * 60; ?>
								<?php echo bsa_get_trans('statistics', 'viewable'); ?><br> <strong><?php echo intval($minutes); ?> <?php echo bsa_get_trans('statistics', 'view_min'); ?> <?php echo intval($seconds); ?> <?php echo bsa_get_trans('statistics', 'view_sec'); ?></strong><br>
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
							<?php if ( isset($entry['starts']) && $entry['starts'] > time() || isset($entry['ends']) && $entry['ends'] > time() ): ?>
                                - - - -<br>
							<?php endif; ?>
							<?php if ( isset($entry['starts']) && $entry['starts'] > time() ): ?>
                                Start Date<br>
                                <strong><?php echo date('d/m/Y (H:i)', $entry['starts']); ?></strong><br>
							<?php endif; ?>
							<?php if ( isset($entry['ends']) && $entry['ends'] > time() ): ?>
                                Ends<br>
                                <strong><?php echo date('d/m/Y (H:i)', $entry['ends']); ?></strong><br>
							<?php endif; ?>
							<?php if ( isset($entry['show_in_country']) && $entry['show_in_country'] != '' || isset($entry['show_in_advanced']) && $entry['show_in_advanced'] != '' ): ?>
                                - - - -<br>
                                Geotarget:
								<?php if ( isset($entry['show_in_country']) && $entry['show_in_country'] != '' ): ?>
                                    <strong><?php echo ( strlen($entry['show_in_country']) > 10 ? substr($entry['show_in_country'], 0, 10).'.. ' : $entry['show_in_country'].' ' ); ?></strong>
								<?php endif; ?>
								<?php if ( isset($entry['show_in_advanced']) && $entry['show_in_advanced'] != '' ): ?>
                                    <strong><?php echo ( strlen($entry['show_in_advanced']) > 10 ? substr($entry['show_in_advanced'], 0, 10).'..' : $entry['show_in_advanced'] ); ?></strong>
								<?php endif; ?><br>
							<?php endif; ?>
							<?php if ( isset($entry['capping']) && $entry['capping'] > 0 ): ?>
                                - - - -<br>
                                Capping: <strong><?php echo $entry['capping'] ?></strong><br>
							<?php endif; ?>
							<?php $scheduleTask = $model->getPendingTask($entry['id'], 'ad'); ?>
							<?php if ( isset($scheduleTask) && isset($scheduleTask['start_time']) && $scheduleTask['start_time'] > time() ): ?>
                                - - - -<br><span class="dashicons dashicons-clock"></span> Scheduled on <?php echo date('d/m/Y (H:i)', $scheduleTask['start_time']); ?>
							<?php endif; ?>
                        </td>
                        <td>
                            Ad / Order ID <strong><?php echo $entry['id']; ?></strong><br>
                            Billing Model <strong><?php echo strtoupper($entry['ad_model']); ?></strong><br>
                            Earned <strong><?php echo $before . bsa_number_format($entry['cost']) . $after; ?></strong><br>
							<?php if ( $entry['paid'] == 1 ): ?>
                                <span class="bsaColorGreen">Paid</span>
							<?php elseif ( $entry['paid'] == 2 ): ?>
                                <span class="bsaColorGreen">Added by Admin</span>
							<?php else: ?>
                                <span class="bsaColorRed">Not paid</span>
							<?php endif; ?><br>
							<?php echo ( $entry['optional_field'] != '' ) ? '- - - -<br>'.$entry['optional_field'] : '' ; ?>
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

        <h3>Archived Ads</h3>
        <table class="wp-list-table widefat bsaListTable">
            <thead>
            <tr>
                <th></th>
                <th style="" class="manage-column post-title page-title column-title">Ad Content</th>
                <th style="" class="manage-column">Buyer</th>
                <th style="" class="manage-column">Stats</th>
                <th style="" class="manage-column">Display Limit</th>
                <th style="" class="manage-column">Order Details</th>
            </tr>
            </thead>

            <tbody>
			<?php
			if (is_array($getArchiveAds) && count($getArchiveAds) > 0) {
				foreach ($getArchiveAds as $key => $entry) {

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
							<?php echo (isset($entry['ad_name']) && $entry['ad_name'] != '') ? '<span class="bsaAdName">'.$entry['ad_name'].'</span>' : null; ?>
							<?php echo ( $entry['title'] != '' ) ? '<strong>'.$entry['title'].'</strong>' : ''; ?>
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
							$views = (isset($getStats[$entry['id']]['views'])  ? $getStats[$entry['id']]['views'] : 0);
							$clicks = (isset($getStats[$entry['id']]['clicks'])  ? $getStats[$entry['id']]['clicks'] : 0);
							$viewable = (isset($getStats[$entry['id']]['viewable'])  ? $getStats[$entry['id']]['viewable'] : 0); ?>
                            Views <strong><?php echo ( $views != NULL ) ? $views : 0; ?></strong><br>
                            Clicks <strong><?php echo ( $clicks != NULL ) ? $clicks : 0; ?></strong><br>
							<?php if ( $views != NULL && $clicks != NULL ): ?>
                                CTR <strong><?php echo number_format(($clicks / $views) * 100, 2, '.', '').'%'; ?></strong><br>
							<?php endif; ?>
                            <a target="_blank" href="<?php echo get_option('bsa_pro_plugin_ordering_form_url') . (( strpos(get_option('bsa_pro_plugin_ordering_form_url'), '?') == TRUE ) ? '&' : '?') ?>bsa_pro_stats=1&bsa_pro_email=<?php echo str_replace('@', '%40', $entry['buyer_email']); ?>&bsa_pro_id=<?php echo $entry['id']; ?>">
                                full statistics
                            </a><br>
							<?php if ( bsa_get_trans('statistics', 'viewable') != '' ): ?>
                                - - - -<br>
								<?php $minutes = ($viewable > 60 ? $viewable / 60 : 0);
								$seconds = ($minutes - intval($minutes)) * 60; ?>
								<?php echo bsa_get_trans('statistics', 'viewable'); ?><br> <strong><?php echo intval($minutes); ?> <?php echo bsa_get_trans('statistics', 'view_min'); ?> <?php echo intval($seconds); ?> <?php echo bsa_get_trans('statistics', 'view_sec'); ?></strong><br>
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
							<?php if ( isset($entry['starts']) && $entry['starts'] > time() || isset($entry['ends']) && $entry['ends'] > 0 ): ?>
                                - - - -<br>
							<?php endif; ?>
							<?php if ( isset($entry['starts']) && $entry['starts'] > time() ): ?>
                                Start Date<br>
                                <strong><?php echo date('d/m/Y (H:i)', $entry['starts']); ?></strong><br>
							<?php endif; ?>
							<?php if ( isset($entry['ends']) && $entry['ends'] > 0 ): ?>
                                Ended on<br>
                                <strong><?php echo date('d/m/Y (H:i)', $entry['ends']); ?></strong><br>
							<?php endif; ?>
							<?php if ( isset($entry['show_in_country']) && $entry['show_in_country'] != '' || isset($entry['show_in_advanced']) && $entry['show_in_advanced'] != '' ): ?>
                                - - - -<br>
                                Geotarget:
								<?php if ( isset($entry['show_in_country']) && $entry['show_in_country'] != '' ): ?>
                                    <strong><?php echo ( strlen($entry['show_in_country']) > 10 ? substr($entry['show_in_country'], 0, 10).'.. ' : $entry['show_in_country'].' ' ); ?></strong>
								<?php endif; ?>
								<?php if ( isset($entry['show_in_advanced']) && $entry['show_in_advanced'] != '' ): ?>
                                    <strong><?php echo ( strlen($entry['show_in_advanced']) > 10 ? substr($entry['show_in_advanced'], 0, 10).'..' : $entry['show_in_advanced'] ); ?></strong>
								<?php endif; ?><br>
							<?php endif; ?>
							<?php if ( isset($entry['capping']) && $entry['capping'] > 0 ): ?>
                                - - - -<br>
                                Capping: <strong><?php echo $entry['capping'] ?></strong><br>
							<?php endif; ?>
							<?php $scheduleTask = $model->getPendingTask($entry['id'], 'ad'); ?>
							<?php if ( isset($scheduleTask) && isset($scheduleTask['start_time']) && $scheduleTask['start_time'] > time() ): ?>
                                - - - -<br><span class="dashicons dashicons-clock"></span> Scheduled on <?php echo date('d/m/Y (H:i)', $scheduleTask['start_time']); ?>
							<?php endif; ?>
                        </td>
                        <td>
                            Ad / Order ID <strong><?php echo $entry['id']; ?></strong><br>
                            Billing Model <strong><?php echo strtoupper($entry['ad_model']); ?></strong><br>
                            Earned <strong><?php echo $before . bsa_number_format($entry['cost']) . $after; ?></strong><br>
							<?php if ( $entry['paid'] == 1 ): ?>
                                <span class="bsaColorGreen">Paid</span>
							<?php elseif ( $entry['paid'] == 2 ): ?>
                                <span class="bsaColorGreen">Added by Admin</span>
							<?php else: ?>
                                <span class="bsaColorRed">Not paid</span>
							<?php endif; ?><br>
							<?php echo ( $entry['optional_field'] != '' ) ? '- - - -<br>'.$entry['optional_field'] : '' ; ?>
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

	<?php elseif ( $space_id == 0 ): ?>

        <div class="updated settings-error" id="setting-error-settings_updated">
            <p><strong>Spaces not exists!</strong> Go <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-add-new-space">here</a> to add first space.</p>
        </div>

	<?php else: ?>

        <div class="updated settings-error" id="setting-error-settings_updated">
            <p><strong>Error!</strong> Space does not exist! Go <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces">back</a>.</p>
        </div>

	<?php endif; ?>

<?php else: ?>

    <div class="updated settings-error" id="setting-error-settings_updated">
        <p><strong>Error!</strong> Space does not exist! Go <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces">back</a>.</p>
    </div>

<?php endif; ?>

<script>
    (function($) {
        // - start - open page
        let bsaPageContent = $(".wrap");
        let waitingContent = $(".waitingContent");
        $(document).ready(function(){
            bsaPageContent.fadeIn();
            waitingContent.fadeOut();
        });
        // - end - open page

        let spaceDetailsShow = $('.spaceDetailsShow');
        let spaceDetailsHide = $('.spaceDetailsHide');
        let spaceDetails = $('.spaceDetails');
        spaceDetailsShow.on('click', function(e){
            spaceDetails.show();
            $(this).hide();
            spaceDetailsHide.show();
        });
        spaceDetailsHide.on('click', function(e){
            spaceDetails.hide();
            $(this).hide();
            spaceDetailsShow.show();
        });

        $( document ).ready(function() {
			<?php if ( get_order_ads($space_id, 'priority') ): ?>
            let sortList = $('#bsaSortable');
            sortList.sortable({
                stop : function(event, ui){
                    let getOrder = $(this).sortable('toArray');
                    $.post(ajaxurl, {action:'bsa_sortable_callback',bsa_order:getOrder}, function(result) {
                        let bsaSortableNotice = $('.bsaSortableNotice');
                        bsaSortableNotice.fadeIn();
                        setTimeout(function(){
                            bsaSortableNotice.fadeOut();
                        }, 2000);
                    });
                }
            });
            sortList.disableSelection();
			<?php endif; ?>

			<?php if ($model->validationBlocked() or $model->validationUnblocked() or $model->validationPaid() or $model->validationRemoved()) { ?>
            let bsaValidationAlert = $('#setting-error-settings_updated');
            bsaValidationAlert.fadeIn(100);
            setTimeout(function(){
                bsaValidationAlert.fadeOut(100);
                bsaItemsWrap.fadeOut(100);
            }, 2000);
            setTimeout(function(){
                window.location = document.location.href;
            }, 2000);
			<?php } ?>

			<?php if ( bsa_get_opt('admin_settings', 'selection') == 'select' ): ?>
            $('#bsa_pro_space_select').change(function(){ // select redirection
                window.location.href = $(this).val();
            });
			<?php endif; ?>
        });
    })(jQuery);
</script>