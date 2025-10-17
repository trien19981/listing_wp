<?php
$model = new BSA_PRO_Model();
$role = ((bsa_role() == 'admin') ? 'a' : 'u');
$decode_ids = $model->getUserCol(get_current_user_id());
$get_ids = (isset($decode_ids['ad_ids']) ? json_decode($decode_ids['ad_ids']) : false);
$get_free_ads = $model->getUserCol(get_current_user_id(), 'free_ads');

function getAdValue($val) {
	if (isset($_GET['ad_id'])) {
		return bsa_ad($_GET['ad_id'], $val);
	} else {
		if ( isset($_POST[$val]) || isset($_SESSION['bsa_ad_status']) ) {
			if ( isset($_SESSION['bsa_ad_status']) == 'ad_added' ) {
				$_SESSION['bsa_clear_form'] = 'ad_added';
				unset($_SESSION['bsa_ad_status']);
			}
			$status = (isset($_SESSION['bsa_clear_form']) ? $_SESSION['bsa_clear_form'] : '');
			if ( $status == 'ad_added' ) {
				return '';
			} else {
				return stripslashes($_POST[$val]);
			}
		} else {
			return '';
		}
	}
}
function checkedSpaceOpt($optName, $optValue)
{
	if ( isset( $_GET['ad_id'] ) && bsa_ad($_GET['ad_id'], $optName) != '' && in_array($optValue, explode(',', bsa_ad($_GET['ad_id'], $optName))) OR isset($_POST[$optName]) && in_array($optValue, $_POST[$optName]) ) {
		return 'checked';
	} else {
		return null;
    }
}
function checkedAdOpt($optName, $optValue)
{
	if( isset( $_GET['ad_id'] ) && bsa_ad($_GET['ad_id'], $optName) != '' && in_array($optValue, explode(',', bsa_ad($_GET['ad_id'], $optName))) OR isset($_POST[$optName]) && in_array($optValue, $_POST[$optName]) ) {
		echo 'checked="checked"';
	}
}
?>
<h2>
	<?php if ( isset($_GET['ad_id']) ): ?>
		<span class="dashicons dashicons-edit"></span> Edit <strong>Ad ID <?php echo $_GET['ad_id']; ?></strong> added to <strong>Space ID <?php echo getAdValue('space_id'); ?></strong> <small>(<strong><?php echo bsa_ad($_GET['ad_id'], 'ad_model') ?></strong> billing model)</small>
		<?php if ( $role == 'a' ): ?>
		<p><span class="dashicons dashicon-14 dashicons-arrow-left-alt"></span> <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces<?php echo ((getAdValue('space_id')) ? '&space_id='.getAdValue('space_id') : null) ?>">back to <strong>spaces / ads</strong></a></p>
		<?php endif; ?>
	<?php else: ?>
		<span class="dashicons dashicons-plus-alt"></span> Add new Ad
		<?php if ( $role == 'a' ): ?>
			<p><span class="dashicons dashicon-14 dashicons-arrow-left-alt"></span> <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces">back to <strong>spaces / ads</strong></a></p>
		<?php endif; ?>
	<?php endif; ?>
</h2>

<?php if (  isset($_GET['ad_id']) && bsa_ad($_GET['ad_id'], 'id') != NULL && $role == 'a' ||
			!isset($_GET['ad_id']) && $role == 'a' ||
			isset($_GET['ad_id']) && bsa_ad($_GET['ad_id'], 'id') != NULL && is_array($get_ids) && array_search($_GET['ad_id'], $get_ids) !== false && $role == 'u' ||
			!isset($_GET['ad_id']) && $get_free_ads['free_ads'] > 0 && $role == 'u' ):

	if ( $role == 'a' ) { // if admin
		$spaces = (($model->getSpaces('active')) ? $model->getSpaces('active') : NULL);
	} else { // if user
		$spaces = (($model->getSpaces('active', 'html')) ? $model->getSpaces('active', 'html') : NULL);
	}
	$count_ads = NULL;
	$space_verify = NULL;
	if (is_array($spaces))
	{
		foreach ( $spaces as $key => $space ) {
			if ( $role == 'a' ) {
				$count_ads = $model->countAds($space["id"]);
				if ( $model->countAds($space["id"]) < bsa_space($space["id"], 'max_items') ) {
					$space_verify .= (( $key > 0 ) ? ','.$space["id"] : $space["id"]);
				} else {
					$space_verify .= '';
				}
			} else {
				if ( $space['cpc_price'] == 0 && $space['cpm_price'] == 0 && $space['cpd_price'] == 0 ) {
					$space_verify .= '';
				} else {
					$count_ads = $model->countAds($space["id"]);
					if ( $model->countAds($space["id"]) < bsa_space($space["id"], 'max_items') ) {
						$space_verify .= (( $key > 0 ) ? ','.$space["id"] : $space["id"]);
					} else {
						$space_verify .= '';
					}
				}
			}
		}
	}
	$space_verify = (( $space_verify != '') ? explode(',', $space_verify) : FALSE );

	if ( $spaces && $space_verify && !isset($_GET['ad_id']) || $spaces && isset($_GET['ad_id']) && bsa_space(bsa_ad($_GET['ad_id'], 'space_id'), 'max_items') >= $model->countAds(bsa_ad($_GET['ad_id'], 'space_id')) ): ?>
		<form action="" method="post" enctype="multipart/form-data" class="bsaNewAd">
			<?php if ( isset($_GET['ad_id']) ): ?>
				<input type="hidden" value="updateAd" name="bsaProAction">
			<?php else: ?>
				<input type="hidden" value="addNewAd" name="bsaProAction">
			<?php endif; ?>
			<table class="bsaAdminTable form-table">
				<tbody class="bsaTbody">
					<tr>
						<th colspan="2">
							<?php if ( isset($_GET['ad_id']) ): ?>
								<h3><span class="dashicons dashicons-exerpt-view"></span> Edit Ad Content</h3>
							<?php else: ?>
								<h3><span class="dashicons dashicons-exerpt-view"></span> Create new Ad</h3>
							<?php endif; ?>
						</th>
					</tr>
					<?php if ( isset($_GET['ad_id']) ): ?>
						<tr>
							<th scope="row"><label for="bsa_pro_space_ids">Assign to more Ad Spaces (optional)</label></th>
							<td>
                                <?php $similar_ids = $model->getSimilarSpaces(bsa_ad($_GET['ad_id'], 'space_id'), bsa_space(bsa_ad($_GET['ad_id'], 'space_id'), 'template')); ?>
                                <ul>
	                                <?php if ( is_array($similar_ids) ): ?>
		                                <?php foreach ( $similar_ids as $similar_id ): ?>
                                            <li>
                                                <?php if ( checkedSpaceOpt('space_ids', $similar_id['id']) == 'checked' ): ?>
                                                    <label class="selectit">
                                                        <input value="<?= $similar_id['id'] ?>" type="checkbox" name="space_ids[]" checked="checked"> <?= $similar_id['name'] ?>
                                                    </label>
                                                <?php else: ?>
                                                    <label class="selectit hiddenSimilarSpaces" style="display: none">
                                                        <input value="<?= $similar_id['id'] ?>" type="checkbox" name="space_ids[]"> <?= $similar_id['name'] ?>
                                                    </label>
                                                <?php endif; ?>
                                            </li>
		                                <?php endforeach; ?>
                                        <li>
                                            <a href="#" class="showSimilarSpaces">show all similar ad spaces</a>
                                        </li>
                                    <?php else: ?>
                                        No active ad spaces with the same ad template.
	                                <?php endif; ?>
                                </ul>
							</td>
						</tr>
					<?php endif; ?>
					<?php if ( $role == 'a' && bsa_get_opt('admin_settings', 'ad_name') == 'yes' ): ?>
						<tr>
							<th scope="row"><label for="bsa_pro_ad_name">Ad Name (optional) <br>listed in the backend only</label></th>
							<td>
								<input id="bsa_pro_ad_name" name="ad_name" type="text" class="regular-text" value="<?php echo getAdValue('ad_name') ?>">
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<th scope="row"><label for="bsa_pro_buyer_email">E-mail</label></th>
						<td>
							<?php $user_info = get_userdata(get_current_user_id()); ?>
							<?php if ( !isset($_GET['ad_id']) && isset($user_info->user_email) ): ?>
								<input id="bsa_pro_buyer_email" name="buyer_email" type="email" class="regular-text" maxlength="255" value="<?php echo $user_info->user_email; ?>">
							<?php else: ?>
								<input id="bsa_pro_buyer_email" name="buyer_email" type="email" class="regular-text" maxlength="255" value="<?php echo getAdValue('buyer_email'); ?>">
							<?php endif; ?>
							<p class="description">E-mail address is required to generate statistics.</p>
						</td>
					</tr>
					<?php $space_template = null; ?>
					<?php if ( !isset($_GET['ad_id']) ): ?>
					<tr>
						<th scope="row"><label for="bsa_pro_space_id">Choose Space</label></th>
						<td>
							<select id="bsa_pro_space_id" name="space_id" onchange="bsaGetBillingMethods()">
								<?php
								if ( $spaces != NULL ) {
									foreach ( $spaces as $space ) {
										if ( in_array($space['id'], $space_verify) ) {
											if ($role == 'a' || $role == 'u' && $space['template'] != 'html') {
												if ( $space_template == null ) {
													$space_template = $space['template'];
												}
												if ($model->countAds($space["id"]) < bsa_space($space["id"], 'max_items')) {
													echo '<option value="' . $space["id"] . '" ' . ((isset($_POST) && isset($_POST["space_id"]) && $_POST["space_id"] == $space["id"]) ? 'selected="selected"' : "") . '>' . $space["name"] . '</option>';
												} else {
													echo '<option value="" disabled>' . $space["name"] . ' (' . $model->countAds($space["id"]) . '/' . bsa_space($space["id"], 'max_items') . ')' . '</option>';
												}
											}
										}
									}
								}
								?>
							</select> <span class="bsaLoader" style="display:none;"></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label>Billing model <br>(display limit)</label></th>
						<td>
							<h3 style="margin-top:0;">Choose Billing Model and Display Limit <span class="bsaLoader" style="display:none;"></span></h3>
							<div class="bsaGetBillingModels"></div>
						</td>
					</tr>
					<?php endif ?>
					<tr>
						<th scope="row">Live Preview</th>
						<td>
							<?php if ( isset($_GET['ad_id']) ): ?>
								<input id="bsa_pro_space_id" type="hidden" value="<?php echo getAdValue('space_id'); ?>">
								<input id="bsa_pro_ad_id" type="hidden" value="<?php echo $_GET['ad_id']; ?>">
							<?php endif ?>
							<h3 style="margin-top:0;">Ad Live Preview <span class="bsaLoader" style="display:none;"></span></h3>
							<div class="bsaTemplatePreview">
								<div class="bsaTemplatePreviewInner"></div>
							</div>
						</td>
					</tr>
					<tr class="bsa_title_inputs_load" style="display: none">
						<th scope="row"><label for="bsa_pro_title">Title <small>(<span class="bsa_pro_sign_title"><?php echo get_option('bsa_pro_plugin_max_title') ?></span>)</small></label></th>
						<td>
							<input id="bsa_pro_title" name="title" type="text" class="regular-text" maxlength="<?php echo get_option('bsa_pro_plugin_max_title') ?>" value="<?php echo getAdValue('title') ?>">
						</td>
					</tr>
					<tr class="bsa_desc_inputs_load" style="display: none">
						<th scope="row"><label for="bsa_pro_desc">Description <small>(<span class="bsa_pro_sign_desc"><?php echo get_option('bsa_pro_plugin_max_desc') ?></span>)</small></label></th>
						<td>
							<input id="bsa_pro_desc" name="description" type="text" class="regular-text" maxlength="<?php echo get_option('bsa_pro_plugin_max_desc') ?>" value="<?php echo getAdValue('description') ?>">
						</td>
					</tr>
					<tr class="bsa_button_inputs_load" style="display: none">
						<th scope="row"><label for="bsa_pro_button">Action Button <small>(<span class="bsa_pro_sign_button"><?php echo get_option('bsa_pro_plugin_max_button') ?></span>)</small></label></th>
						<td>
							<input id="bsa_pro_button" name="button" type="text" class="regular-text" maxlength="<?php echo get_option('bsa_pro_plugin_max_button') ?>" value="<?php echo getAdValue('button') ?>">
						</td>
					</tr>
					<tr class="bsa_url_inputs_load" style="display: none">
						<th scope="row"><label for="bsa_pro_url">URL <small>(<span class="bsa_pro_sign_url">255</span>)</small></label></th>
						<td>
							<input id="bsa_pro_url" name="url" type="url" class="regular-text" maxlength="255" value="<?php echo getAdValue('url') ?>">
							<p class="bsa_pro_html_desc description" style="display:none;"><strong>Note!</strong> You can use the URL field within clean HTML ads only (you can't use it with AdSense or other external JS codes).</p>
						</td>
					</tr>
					<tr class="bsa_img_inputs_load" style="display: none">
						<th scope="row"><label for="bsa_pro_img">Image</label></th>
						<td>
							<input type="file" id="bsa_pro_img" name="img" onchange="bsaPreviewThumb(this)"><br><br>
							- or -<br><br>
							<label for="bsa_pro_img_url">Full URL of Image (<strong id="upload-btn">use media library</strong>)</label><br>
							<input id="bsa_pro_img_url" name="img_url" type="url" onchange="bsaPreviewThumb(this.value)" class="regular-text" maxlength="1000" value="" placeholder="https://yoursite.com/image.jpg"><br><br>
							<p class="description"><?php echo get_option('bsa_pro_plugin_trans_form_left_thumb'); ?></p>
							<?php if ( isset($_GET['ad_id']) ): ?>
								<p class="description"><strong>Note!</strong> Skip this field if you don't want to change the image.</p><br>
							<?php endif; ?>
						</td>
					</tr>
					<?php $template = ( isset($_GET['ad_id']) ? bsa_space(getAdValue('space_id'), 'template') : $space_template ); ?>
					<?php if ( $template == 'html' ): ?>
						<tr class="bsa_html_inputs_load" style="display: none">
							<th scope="row"><label for="bsa_pro_html">HTML / JS</label></th>
							<td>
								<textarea id="bsa_pro_html" name="html" class="regular-text ltr" rows="14" cols="70"><?php echo getAdValue('html') ?></textarea>
							</td>
						</tr>
					<?php else: ?>
						<tr id="bsa_html_inputs_opened">
							<th scope="row"></th>
							<td><a href="#bsa_html_inputs_opened" class="showAdditionalHTML">+ html / js code</a> (optional)</td>
						</tr>
						<tr id="bsa_html_inputs_open" class="bsa_html_inputs_loaded" style="display: none">
							<th scope="row"><label for="bsa_pro_html">Additional HTML / JS</label></th>
							<td>
								<textarea id="bsa_pro_html" name="html" class="regular-text ltr" rows="14" cols="70"><?php echo getAdValue('html') ?></textarea>
								<p class="description"><strong>Note!</strong> You can optionally add custom HTML / JS Code below the Ad content.</p>
							</td>
						</tr>
					<?php endif; ?>
					<?php if ( isset($_GET['ad_id']) && $role == 'a' ): ?>
						<tr>
							<th colspan="2">
								<h3><span class="dashicons dashicons-plus"></span> Increase / Decrease display Limit</h3>
							</th>
						</tr>
						<tr>
							<?php $diffTime = '';
							if ( bsa_ad($_GET['ad_id'], 'ad_model') == 'cpc' ) {
								$model_type = 'clicks';
								$limit_value = ( bsa_ad($_GET['ad_id'], 'ad_limit') <= 0 ) ? 0 : bsa_ad($_GET['ad_id'], 'ad_limit');
							} elseif ( bsa_ad($_GET['ad_id'], 'ad_model') == 'cpm' ) {
								$model_type = 'views';
								$limit_value = ( bsa_ad($_GET['ad_id'], 'ad_limit') <= 0 ) ? 0 : bsa_ad($_GET['ad_id'], 'ad_limit');
							} else { // if ( bsa_ad($_GET['ad_id'], 'ad_model') == 'cpd' ) // IF CPD BILLING MODEL
								$time = time();
								$limit = bsa_ad($_GET['ad_id'], 'ad_limit');
								$diff = $limit - $time;
								$limit_value = ( $diff < 86400 /* 1 day in sec */ ) ? ( $diff > 0 ) ? 'less than 1' : '0' : number_format($diff / 24 / 60 / 60);
								$diffTime = date('d/m/Y (H:i)', time() + $diff); // d M Y
								$model_type = ( $diff > 86400 || $diff == -0 ) ? 'days' : 'day';
							} ?>
							<th scope="row"><label>Currently display Limit<br>(<?php echo $model_type ?> to finish)</label></th>
							<td>
								<input name="limit" type="text" class="regular-text" placeholder="<?php echo $limit_value ?>" disabled> <em><?php echo $model_type ?></em>
								<p class="description"><?php echo ( bsa_ad($_GET['ad_id'], 'ad_model') == 'cpd' ) ? $diffTime : ''; ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="increase_limit">Change display Limit<br>(add / subtract <?php echo $model_type ?> to currently limit)</label></th>
							<td>
								<input id="increase_limit" name="increase_limit" type="number" class="regular-text" value=""> <em><?php echo $model_type ?></em>
								<p class="description">Skip this field if you don't want to increase / decrease the display limit.</p>
							</td>
						</tr>
					<?php endif; ?>
					<?php do_action( 'bsa-pro-add-input-ads'); ?>
					<tr>
						<th scope="row"><h3 id="showAdvanced" class="showHideAdvanced">more options <span class="dashicons dashicons-ellipsis"></span></h3></th>
						<td></td>
					</tr>
					<?php $dsadasda = ''; ?>
					<?php if ( get_option('bsa_pro_'.$dsadasda.'u'.'pd'.'at'.$dsadasda.'e'.'_s'.'ta'.'t'.$dsadasda.'us') == 'i'.$dsadasda.'n'.'v'.$dsadasda.'a'.'l'.$dsadasda.'i'.$dsadasda.'d' ): ?>
						<tr class="showAdvanced">
							<td colspan="2">
								<div class="bsaLockedContent">
									<strong>Trial Version</strong><br>
									Use purchase code to unlock additional Advanced options.<br><br>
									Paste purchase code in the <a href="<?php echo admin_url() ?>admin.php?page=bsa-pro-sub-menu-opts">settings</a> (Ads Pro > Settings > Purchase Code).<br>
									Where is your purchase code? <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">Learn more</a>.<br><br>
									- or -<br><br>
									<a href="https://1.envato.market/buy-regular-ads-pro-6" target="_blank">Buy now</a> the latest version of Ads Pro.
								</div>
								<img class="bsaLockedImage" alt="Space Advanced Settings" src="<?php echo plugin_dir_url( __DIR__ ) ?>frontend/img/inside-space-preview.png" />
							</td>
						</tr>
					<?php else: ?>
					<?php if ( $role == 'a' && bsa_get_opt('order_form', 'optional_field') == 'yes' ): ?>
						<tr class="showAdvanced">
							<th scope="row"><label for="bsa_pro_optional_field">Additional Information (phone, TIN number, etc.)</label></th>
							<td>
								<input id="bsa_pro_optional_field" name="optional_field" type="text" class="regular-text" value="<?php echo getAdValue('optional_field') ?>">
							</td>
						</tr>
					<?php endif; ?>
					<?php if ( $role == 'a' ): ?>
						<tr class="showAdvanced">
							<th colspan="2">
								<h3><span class="dashicons dashicons-clock"></span> Start / End Dates</h3>
							</th>
						</tr>
						<tr class="showAdvanced">
							<th scope="row">
								<label for="start_date">
									Start Date</label>
							</th>
							<td >
								<?php if ( getAdValue('starts') > time() ): ?>
									<input type="text" class="start_date" id="start_date" name="start_date" value="<?php echo date('Y-m-d', getAdValue('starts')) ?>" placeholder="select date" />
									<input type="time" min="00:00" max="23:59" class="start_time" id="start_date" name="start_time" value="<?php echo date('H:i', getAdValue('starts')) ?>" placeholder="time format: 15:30" />
								<?php else: ?>
									<input type="text" class="start_date" id="start_date" name="start_date" value="<?php echo getAdValue('start_date') ?>" placeholder="select date" />
									<input type="time" min="00:00" max="23:59" class="start_time" id="start_date" name="start_time" value="<?php echo getAdValue('start_time') ?>" placeholder="time format: 15:30" />
								<?php endif; ?>
							</td>
						</tr>
						<tr class="showAdvanced">
							<th scope="row">
								<label for="end_date">
									End<br><br>
									<span style="font-weight:normal;">current server time: <br><strong><?php echo date('Y-m-d H:i'); ?></strong></span>
								</label>
							</th>
							<td>
								<?php if ( getAdValue('ends') > 0 ): ?>
									<input type="text" class="end_date" id="end_date" name="end_date" value="<?php echo date('Y-m-d', getAdValue('ends')) ?>" placeholder="select date" />
									<input type="time" min="00:00" max="23:59" class="end_time" id="end_date" name="end_time" value="<?php echo date('H:i', getAdValue('ends')) ?>" placeholder="time format: 15:30" />
								<?php else: ?>
									<input type="text" class="end_date" id="end_date" name="end_date" value="<?php echo getAdValue('end_date') ?>" placeholder="select date" />
									<input type="time" min="00:00" max="23:59" class="end_time" id="end_date" name="end_time" value="<?php echo getAdValue('end_time') ?>" placeholder="time format: 15:30" />
								<?php endif; ?>
								<br><br>
								<p><strong>Note!</strong><br>End Date should be greater than Start Date.</p>
							</td>
						</tr>
						<tr class="showAdvanced">
							<th colspan="2">
								<h3><span class="dashicons dashicons-admin-site"></span> Geo-Target Ad</h3>
							</th>
						</tr>
						<tr class="showAdvanced" id="bsaShowGeo" style="padding-top: 25px;">
							<th scope="row">Show in specific <br>Countries</th>
							<td>
								<div style="max-width: 500px;">
									<div class="inside">
										<div id="taxonomy-category" class="categorydiv">
											<ul id="category-tabs" class="category-tabs">
												<li class="tabs bsaProTabCountry" data-tab="bsaShowCountries"><a href="#bsaShowGeo">Show in Countries</a></li>
												<li class="bsaProTabCountry" data-tab="bsaAdvanced"><a href="#bsaShowGeo">Advanced</a></li>
											</ul>

											<div id="bsaAdvanced" class="bsaAdvanced tabs-panel" style="display: none;">
												<ul id="inside-advanced" data-wp-lists="list:countries" class="countrieschecklist form-no-clear">
													<li>
														<strong>Note!</strong><br>
														We recommend really careful use of advanced options. Implemented rules can really limit your Ads.
														Remember that Internet Providers don't always return your actual position (it all depends on their central internet point).<br><br>
                                                        Use "all:" parameter to group zip-codes, example: <strong>all:NY101</strong> makes Ad visible to zip-codes from NY10100 to NY10199.
													</li>
													<li class="bsaProSpecificItem">
														<div style="margin-bottom: 10px"><br><strong>Show</strong> in regions, cities or zip-codes</div>
														<input type="hidden" name="show_in_advanced" class="show_in_advanced" value="<?php echo getAdValue('show_in_advanced') ?>" />
														<input type="text" class="regular-text code spaceChips tagfield" id="show_in_advanced" name="show_in_advanced"
															   value="<?php echo getAdValue('show_in_advanced') ?>" />
													</li>
												</ul>
											</div>

											<div id="bsaShowCountries" class="bsaShowCountries tabs-panel" style="display: block;">
												<ul id="inside-show-countries" data-wp-lists="list:countries" class="countrieschecklist form-no-clear">
													<h4>Selected</h4>
													<div class="checkedC"></div>
													<h4>Unselected</h4>
													<div class="uncheckedC">
														<?php
														$countryCodes = bsa_get_country_codes();
														if ($countryCodes) {
															foreach($countryCodes as $coutry) {
																?>
																<li class="bsaProSpecificItem bsaCheckItem-C<?php echo $coutry['Code']; ?>">
																	<label class="selectit"><input value="<?php echo $coutry['Code']; ?>" class="bsaCheckItem" section="C" itemId="C<?php echo $coutry['Code']; ?>" type="checkbox" name="show_in_country[]" <?php checkedAdOpt('show_in_country', $coutry['Code']); ?>>
																		<?php echo $coutry['Name']; ?></label>
																</li>
																<?php
															}
														}
														?>
													</div>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
						<?php $ipDetails = bsa_get_user_geo_data(); ?>
						<?php if ( isset($ipDetails['query']) && $ipDetails['query'] != '' ): ?>
						<tr id="bsa_geo_details_opened" class="showAdvanced">
							<th scope="row"></th>
							<td><a href="#bsa_geo_details_opened" class="showGeoDetails">see your location details</a></td>
						</tr>
						<tr id="bsa_geo_details_open" style="display: none">
							<th scope="row">IP: <?php echo $ipDetails['query']; ?></th>
							<td>
								<?php if ( isset($ipDetails['countryCode']) ): ?>
									County: <strong><?php echo $ipDetails['countryCode']; ?></strong><br>
								<?php endif;?>
								<?php if ( isset($ipDetails['regionName']) ): ?>
									Region name: <strong><?php echo $ipDetails['regionName']; ?></strong><br>
								<?php endif;?>
								<?php if ( isset($ipDetails['city']) ): ?>
									City: <strong><?php echo $ipDetails['city']; ?></strong><br>
								<?php endif;?>
								<?php if ( isset($ipDetails['zip']) ): ?>
									Zip-code: <strong><?php echo $ipDetails['zip']; ?></strong><br>
								<?php endif;?>
							</td>
						</tr>
						<?php endif; ?>
					<?php endif; ?>
					<tr class="showAdvanced">
						<th colspan="2">
							<h3><span class="dashicons dashicons-chart-bar"></span> Capping Limit</h3>
						</th>
					</tr>
					<tr class="showAdvanced">
						<th class="bsaLast" scope="row"><label for="bsa_pro_capping">Number of impressions per User / Session</label></th>
						<td class="bsaLast">
							<input id="bsa_pro_capping" name="capping" type="text" class="regular-text" maxlength="3" value="<?php echo getAdValue('capping') ?>">
						</td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
			<input class="bsa_inputs_required" name="inputs_required" type="hidden" value="">
			<p class="submit">
				<input type="submit" value="Save Ad" class="button button-primary" id="bsa_pro_submit" name="submit">
			</p>
		</form>
	<?php else: ?>

		<div class="updated settings-error" id="setting-error-settings_updated">
			<p><strong>Ad Spaces are fully or doesn't exists!</strong> Go <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-add-new-space">here</a> to add new Ad Space.</p>
		</div>

	<?php endif; ?>

<?php else: ?>

	<div class="updated settings-error" id="setting-error-settings_updated">
		<p><strong>Error!</strong> Ad doesn't exists or you can't manage this section!</p>
	</div>

<?php endif; ?>

<style>
	<?php
	if ( isset($spaces) && is_array($spaces) ){
        foreach ( $spaces as $space ) {
			$size = explode('--', str_replace('block-', '', $space['template']));
			$width = (isset($size[0]) ? $size[0] : 0);
			$height = (isset($size[1]) ? $size[1] : 0);
			if ( isset($space['template']) && $width > 0 && $height > 0 ) { ?>
    .bsaTemplatePreview .bsa-<?php echo $space['template']; ?> {
        width: <?php echo $width; ?>px;
        height: <?php echo $height; ?>px;
    }
    <?php }
	}
}
?>
</style>
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

		// additional html
		$('.showAdditionalHTML').on('click', function() {
			$('#bsa_html_inputs_open').toggle();
		});

		// geo-target tabs
		$('.bsaProTabCountry').on('click', function() {
			let clicked = $(this).attr('data-tab');
			$('.bsaProTabCountry').removeClass('tabs');
			$(this).addClass('tabs');
			if ( clicked === 'bsaShowCountries' ) {
				$('.bsaShowCountries').show();
				$('.bsaAdvanced').hide();
			} else {
				$('.bsaAdvanced').show();
				$('.bsaShowCountries').hide();
			}
		});

		// location details
		$('.showGeoDetails').on('click', function() {
			$('#bsa_geo_details_open').toggle();
		});

		// chips
		$('.spaceChips').tagsInput({
			'height':'140px',
			'width':'97%',
			'interactive':true,
			'defaultText':'new',
			'onAddTag':function(tag){
				$('.' + $(this).attr('id')).val($(this).val());
			},
			'onRemoveTag':function(tag){
				$('.' + $(this).attr('id')).val($(this).val());
			},
			'removeWithBackspace':true
		});

		let frame;
		let attachment;
		// Build the choose from library frame.
		$('#upload-btn').on('click', function () {
			if (frame) {
				frame.open();
				return;
			}
			// Create the media frame.
			frame = wp.media.frames.meta_image_frame = wp.media({
				// Tell the modal to show only images.
				library: {
					type: 'image'
				},
			});
			// When an image is selected, run a callback.
			frame.on('select', function () {
				// Grab the selected attachment.
				attachment = frame.state().get('selection').first().toJSON();
				$('#bsa_pro_img_url').val(attachment.url);
				$(".bsaProItemInner__img").css({"background-image" : "url(" + attachment.url + ")"});
				let tmpImg = new Image();
				tmpImg.src = attachment.url; // or document.images[i].src;
			});
			frame.open();
		});

		// selected / unselected checkbox
		(function($){
			"use strict";
			$(document).ready(function(){
				let bsaCheckItem = $( '.bsaCheckItem' );
				bsaCheckItem.on('change', function() {
					if ( $( this ).is(":checked") ) {
						$( '.bsaCheckItem-' + $(this).attr('itemId') ).appendTo( ".checked" + $(this).attr('section') );
					} else {
						$( '.bsaCheckItem-' + $(this).attr('itemId') ).prependTo( ".unchecked" + $(this).attr('section') );
					}
				});
				bsaCheckItem.each( function(){
					if ( $( this ).is(":checked") ) {
						$( '.bsaCheckItem-' + $(this).attr('itemId') ).appendTo( ".checked" + $(this).attr('section') );
					} else {
						$( '.bsaCheckItem-' + $(this).attr('itemId') ).appendTo( ".unchecked" + $(this).attr('section') );
					}
				});
			});
		})(jQuery);

		let inputTitle = $("#bsa_pro_title");
		let inputDesc = $("#bsa_pro_desc");
		let inputButton = $("#bsa_pro_button");
		let inputUrl = $("#bsa_pro_url");
		let inputHtml = $("#bsa_pro_html");
		inputTitle.on("keyup",function() { bsaPreviewInput("title"); });
		inputDesc.on("keyup",function() { bsaPreviewInput("desc"); });
		inputButton.on("keyup",function() { bsaPreviewInput("button"); });
		inputUrl.on("keyup",function() { bsaPreviewInput("url"); });
		inputHtml.on("keyup",function() { bsaPreviewInput("html"); });

		bsaTemplatePreview();
		let sid = $("#bsa_pro_space_id");
		sid.on("change",function() {
			bsaGetBillingMethods();
			bsaTemplatePreview();
			$(".bsaUrlSpaceId").html($("#bsa_pro_space_id").val());
		});
		sid.trigger("change");

		$(document).ready(function() {
			$('.start_date, .end_date').datepicker({
				dateFormat : 'yy-mm-dd',
				beforeShow: function(input, inst) {
					$('#ui-datepicker-div').addClass('bsaProCalendar');
				}
			});
			$('.showSimilarSpaces').on('click', function() {
				$('.hiddenSimilarSpaces').show();
                $(this).hide();
			});
			$('.showHideAdvanced').on('click', function() {
				window.location = window.location + '#showAdvanced';
				$('.showAdvanced').toggle('fade');
			});
			if ( window.location.hash === '#showAdvanced' || window.location.hash === '#bsaShowGeo' ) {
				$('.showAdvanced').toggle('fade');
			}
		});
	})(jQuery);

	function bsaGetBillingMethods()
	{
		(function($) {
			let getBillingModels = $(".bsaGetBillingModels");
			let bsaLoader = $(".bsaLoader");

			getBillingModels.slideUp();
			bsaLoader.fadeIn(400);
			setTimeout(function(){
				$.post(ajaxurl, {action:"bsa_get_billing_models_callback",bsa_space_id:$("#bsa_pro_space_id").val(),bsa_pro_admin:1}, function(result) {

					getBillingModels.html(result).slideDown();
					bsaLoader.fadeOut(400);

				});
			}, 1100);
		})(jQuery);
	}

	function bsaTemplatePreview()
	{
		(function($) {
			let bsaTemplatePreviewInner = $(".bsaTemplatePreviewInner");
			let bsaLoader = $(".bsaLoader");

			bsaTemplatePreviewInner.slideUp(400);
			bsaLoader.fadeIn(400);
			setTimeout(function(){
				$.post("<?php echo admin_url("admin-ajax.php") ?>", {action:"bsa_preview_callback",bsa_space_id:$("#bsa_pro_space_id").val(),bsa_ad_id:$("#bsa_pro_ad_id").val()}, function(result) {

					bsaTemplatePreviewInner.html(result).slideDown(400);

					bsaGetRequiredInputs();
					let inputTitle = $("#bsa_pro_title");
					let inputDesc = $("#bsa_pro_desc");
					let inputButton = $("#bsa_pro_button");
					let inputUrl = $("#bsa_pro_url");
					let inputHtml = $("#bsa_pro_html");
					if ( inputTitle.val().length > 0 ) { bsaPreviewInput("title"); }
					if ( inputDesc.val().length > 0 ) { bsaPreviewInput("desc"); }
					if ( inputButton.val().length > 0 ) { bsaPreviewInput("button"); }
					if ( inputUrl.val().length > 0 ) { bsaPreviewInput("url"); }
					if ( inputHtml.val().length > 0 ) { bsaPreviewInput("html"); }

					bsaLoader.fadeOut(400);

				});
			}, 1100);
		})(jQuery);
	}

	function bsaGetRequiredInputs()
	{
		(function($) {
			$.post(ajaxurl, {action:"bsa_required_inputs_callback",bsa_space_id:$("#bsa_pro_space_id").val(),bsa_get_required_inputs:1}, function(result) {
				$(".bsa_inputs_required").val($.trim(result));

				if ( result.indexOf('title') !== -1 ) { // show if title required
					$(".bsa_title_inputs_load").fadeIn();
				} else {
					$(".bsa_title_inputs_load").fadeOut();
				}
				if ( result.indexOf('desc') !== -1 ) { // show if description required
					$(".bsa_desc_inputs_load").fadeIn();
				} else {
					$(".bsa_desc_inputs_load").fadeOut();
				}
				if ( result.indexOf('button') !== -1 ) { // show if button required
					$(".bsa_button_inputs_load").fadeIn();
				} else {
					$(".bsa_button_inputs_load").fadeOut();
				}
				if ( result.indexOf('url') !== -1 ) { // show if url required
					$(".bsa_url_inputs_load").fadeIn();
				} else {
					$(".bsa_url_inputs_load").fadeOut();
				}
				if ( result.indexOf('img') !== -1 ) { // show if img required
					$(".bsa_img_inputs_load").fadeIn();
				} else {
					$(".bsa_img_inputs_load").fadeOut();
				}
				if ( result.indexOf('html') !== -1 ) { // show if html required
					$(".bsa_html_inputs_load").fadeIn();
					// show html notice
					$('.bsa_pro_html_desc').fadeIn();
				} else {
					$(".bsa_html_inputs_load").fadeOut();
					// hide html notice
					$('.bsa_pro_html_desc').fadeOut();
				}
			});
		})(jQuery);
	}

	function bsaPreviewInput(inputName)
	{
		(function($){
			"use strict";
			let input = $("#bsa_pro_" + inputName);
			let sign = $(".bsa_pro_sign_" + inputName);
			let limit = input.attr("maxLength");
			let bsaProContainerExample = $(".bsaProContainerExample");
			let exampleTitle = "<?php echo get_option("bsa_pro_plugin_trans_form_left_eg_title"); ?>";
			let exampleDesc = "<?php echo get_option("bsa_pro_plugin_trans_form_left_eg_desc"); ?>";
			let exampleButton = "<?php echo get_option("bsa_pro_plugin_trans_form_left_eg_button"); ?>";
			let exampleUrl = "<?php echo get_option("bsa_pro_plugin_trans_form_left_eg_url"); ?>";
			let exampleHTML = "HTML / JS Code";

			sign.text(limit - input.val().length);

			input.on('keyup', function() {
				if (input.val().length > limit) {
					input.val($(this).val().substring(0, limit));
				}
			});

			if (input.val().length > 0) {
				if ( inputName === "title" ) {
					bsaProContainerExample.find(".bsaProItemInner__" + inputName).html(input.val());
				} else if ( inputName === "desc" ) {
					bsaProContainerExample.find(".bsaProItemInner__" + inputName).html(input.val());
				} else if ( inputName === "button" ) {
					bsaProContainerExample.find(".bsaProItemInner__" + inputName).html(input.val());
				} else if ( inputName === "url" ) {
					let url_protocol = (input.val().search("https") > 0 ? 'https://' : 'http://');
					bsaProContainerExample.find(".bsaProItemInner__" + inputName).html(url_protocol + input.val().replace("http://","").replace("https://","").replace("www.","").split(/[/?#]/)[0]);
				} else if ( inputName === "html" ) {
					<?php if ( get_option('bsa_pro_plugin_'.'html_preview') == 'no' || get_option('bsa_pro_plugin_'.'html_preview') == NULL ): ?>
					bsaProContainerExample.find(".bsaProItemInner__" + inputName).html(input.val());
					<?php endif; ?>
				}
			} else {
				if ( inputName === "title" ) {
					bsaProContainerExample.find(".bsaProItemInner__" + inputName).html(exampleTitle);
				} else if ( inputName === "desc" ) {
					bsaProContainerExample.find(".bsaProItemInner__" + inputName).html(exampleDesc);
				} else if ( inputName === "button" ) {
					bsaProContainerExample.find(".bsaProItemInner__" + inputName).html(exampleButton);
				} else if ( inputName === "url" ) {
					let url_protocol = (exampleUrl.search("https") > 0 ? 'https://' : 'http://');
					bsaProContainerExample.find(".bsaProItemInner__" + inputName).html(url_protocol + exampleUrl.replace("http://","").replace("https://","").replace("www.","").split(/[/?#]/)[0]);
				} else if ( inputName === "html" ) {
					if ( bsaProContainerExample.find(".bsaProHTML .bsaProItemInner__" + inputName).length ) {
						bsaProContainerExample.find(".bsaProHTML .bsaProItemInner__" + inputName).html(exampleHTML);
					} else {
						bsaProContainerExample.find(".bsaProItemInner__" + inputName).html('');
					}
				}
			}
		})(jQuery);
	}

	function bsaPreviewThumb(input)
	{
		(function($) {
			if (input.files && input.files[0]) {
				let reader = new FileReader();
				reader.onload = function (e) {
					$(".bsaProItemInner__img").css({"background-image" : "url(" + e.target.result + ")"});
				};
				reader.readAsDataURL(input.files[0]);
			} else {
				if ( input !== '' ) {
					$(".bsaProItemInner__img").css({"background-image" : "url(" + input + ")"});
				}
			}
		})(jQuery);
	}
</script>