<?php
$space_id = (isset($_GET['space_id']) && $_GET['space_id'] > 0 ? $_GET['space_id'] : null);
function getSpaceValue($val) {
	$ret = NULL;
	$ret = apply_filters( "bsa-pro-getspacevalue", $ret, ((isset($_GET['space_id'])) ? $_GET['space_id'] : null), $val);
	if($ret!=NULL) {
		return $ret;
	}
	if (isset($_GET['space_id'])) {
		if ( $val == 'cpc_contract_1' or $val == 'cpc_contract_2' or $val == 'cpc_contract_3' or
		     $val == 'cpm_contract_1' or $val == 'cpm_contract_2' or $val == 'cpm_contract_3' or
		     $val == 'cpd_contract_1' or $val == 'cpd_contract_2' or $val == 'cpd_contract_3') {
			if ( bsa_space($_GET['space_id'], $val) == '' or bsa_space($_GET['space_id'], $val) == 0 ) {
				if ( isset($_POST[$val]) ) {
					return $_POST[$val];
				} else {
					return '0';
				}
			} else {
				if ( isset($_POST[$val]) ) {
					return $_POST[$val];
				} else {
					return bsa_space($_GET['space_id'], $val);
				}
			}
		} else {
			if ( isset($_POST[$val]) ) {
				return $_POST[$val];
			} else {
				return bsa_space($_GET['space_id'], $val);
			}
		}
	} else {
		if ( isset($_POST[$val]) || isset($_SESSION['bsa_space_status']) ) {
			if ( isset($_SESSION['bsa_space_status']) && $_SESSION['bsa_space_status'] == 'space_added' ) {
				$_SESSION['bsa_clear_form'] = 'space_added';
				unset($_SESSION['bsa_space_status']);
			}
			$status = (isset($_SESSION['bsa_clear_form']) ? $_SESSION['bsa_clear_form'] : '');
			if ( $status == 'space_added' ) {
				return '';
			} else {
				return $_POST[$val];
			}
		} else {
			return '';
		}
	}
}

function selectedSpaceOpt($optName, $optValue)
{
	if ( $optName == 'show_ads' || $optName == 'show_close_btn' || $optName == 'close_ads' ) {
		if ( isset( $_GET['space_id'] ) || isset( $_POST['show_ads'] ) && isset( $_POST['show_close_btn'] )&& isset( $_POST['close_ads'] ) ) {
			if ( isset( $_GET['space_id'] ) ) {
				$action = explode(',', (bsa_space($_GET['space_id'], 'close_action') != null ? bsa_space($_GET['space_id'], 'close_action') : '0,0,0'));
			} else {
				$action = explode(',', ($_POST['show_ads'] > 0 ? $_POST['show_ads'] : '0').','.($_POST['show_close_btn'] > 0 ? $_POST['show_close_btn'] : '0').','.($_POST['close_ads'] > 0 ? $_POST['close_ads'] : '0'));
			}
			if ( $optName == 'show_ads' ) {
				if ( isset($action[0]) && $action[0] == $optValue ) {
					echo 'selected="selected"';
				}
			} elseif ( $optName == 'show_close_btn' ) {
				if ( isset($action[1]) && $action[1] == $optValue ) {
					echo 'selected="selected"';
				}
			} elseif ( $optName == 'close_ads' ) {
				if ( isset($action[2]) && $action[2] == $optValue ) {
					echo 'selected="selected"';
				}
			}
		}
	} else {
		if ( isset( $_GET['space_id'] ) && bsa_space($_GET['space_id'], $optName) == $optValue || isset($_POST[$optName]) && $_POST[$optName] == $optValue ) {
			echo 'selected="selected"';
		}
	}
}

function checkedSpaceOpt($optName, $optValue)
{
	if ( $optName == 'hide_for_id' && isset( $_GET['space_id'] ) && $_GET['space_id'] != '' ) {
		$getIds = json_decode(bsa_space($_GET['space_id'], 'advanced_opt'));
		if( isset($getIds->hide_for_id) && in_array($optValue, explode(',', $getIds->hide_for_id)) OR isset($_POST['hide_for_id']) && in_array($optValue, $_POST['hide_for_id']) ) {
			echo 'checked="checked"';
		}
	} else {
		if( isset( $_GET['space_id'] ) && bsa_space($_GET['space_id'], $optName) != '' && in_array($optValue, explode(',', bsa_space($_GET['space_id'], $optName))) OR isset($_POST[$optName]) && in_array($optValue, $_POST[$optName]) ) {
			echo 'checked="checked"';
		}
	}
}
?>
<h2>
	<?php if ( isset($_GET['space_id']) ): ?>
        <span class="dashicons dashicons-edit"></span> Edit <strong>Space ID <?php echo $_GET['space_id']; ?></strong>
        <p><span class="dashicons dashicon-14 dashicons-arrow-left-alt"></span> <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces<?php echo ((isset( $_GET['space_id'] )) ? '&space_id='.$_GET['space_id'] : null) ?>">back to <strong>spaces / ads</strong></a></p>
	<?php else: ?>
        <span class="dashicons dashicons-plus-alt"></span> Add new Space
        <p><span class="dashicons dashicon-14 dashicons-arrow-left-alt"></span> <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-spaces">back to <strong>spaces / ads</strong></a></p>
	<?php endif; ?>
</h2>

<?php if ( isset($_GET['space_id']) && bsa_space($_GET['space_id'], 'id') != NULL || !isset($_GET['space_id']) ): ?>

    <form action="" method="post" enctype="multipart/form-data">
		<?php if ( isset($_GET['space_id']) ): ?>
            <input type="hidden" value="updateSpace" name="bsaProAction">
		<?php else: ?>
            <input type="hidden" value="addNewSpace" name="bsaProAction">
		<?php endif; ?>
        <table class="bsaAdminTable bsaSpaces form-table">
            <tbody class="bsaTbody">
            <tr>
                <th colspan="2">
                    <h3><span class="dashicons dashicons-welcome-widgets-menus"></span> Space Settings</h3>
                </th>
            </tr>
            <tr>
                <th scope="row"><label for="bsa_pro_status">Status</label></th>
                <td>
                    <select id="bsa_pro_status" name="status">
                        <option value="active" <?php selectedSpaceOpt('status', 'active'); ?>>active</option>
                        <option value="inactive" <?php selectedSpaceOpt('status', 'inactive'); ?>>inactive</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bsa_pro_name">Space Name</label><p class="description">(shown in the order form)</p></th>
                <td>
                    <input type="text" class="regular-text code" maxlength="255" value="<?php echo stripslashes(getSpaceValue('name')) ?>"
                           id="bsa_pro_name" name="name" placeholder="Sidebar Ad Section">
                </td>
            </tr>
			<?php $sdadsa = ''; ?>
			<?php $dsadasd = ( get_option('b'.$sdadsa.'sa_p'.$sdadsa.'ro_u'.'pd'.'at'.$sdadsa.'e'.'_s'.'ta'.'tus') == 'i'.$sdadsa.'n'.'v'.$sdadsa.'a'.'l'.$sdadsa.'i'.$sdadsa.'d' ? 'bsaLockedSection' : '' ) ?>
            <tr class="<?php echo $dsadasd ?>">
                <th scope="row"><label for="bsa_pro_title">Space Title</label><p class="description">(shown in the space)</p></th>
                <td>
                    <input type="text" class="regular-text code" maxlength="255" value="<?php echo stripslashes(getSpaceValue('title')) ?>"
                           id="bsa_pro_title" name="title" placeholder="Featured section">
                </td>
            </tr>
            <tr class="<?php echo $dsadasd ?>">
                <th scope="row"><label for="bsa_pro_add_new">Space Button</label><p class="description">(shown in the space)</p></th>
                <td>
                    <input type="text" class="regular-text code" maxlength="255" value="<?php echo stripslashes(getSpaceValue('add_new')) ?>"
                           id="bsa_pro_add_new" name="add_new" placeholder="add advertising here">
                </td>
            </tr>
            <tr class="<?php echo $dsadasd ?>">
                <th scope="row"><label for="bsa_pro_cpc_price">Billing Models</label><p class="description">(shown in the order form)</p></th>
                <td>
                    <p class="description"><strong>Note!</strong><br>
                        Fill price field only for the 1st contract because prices for other contracts will be generated in automatically (containing discount).<br>
                        Enter 0 into all price fields if you want to hide Ad Space in the Order Form.</p>

                    <div class="billing-col">
                        <h3>CPC - Cost per Click</h3>

                        <label for="bsa_pro_cpc_price" class="billing-label"><strong>CPC Price</strong> (contract 1st)</label>
						<?php if ( get_option('bsa_pro_plugin_'.'symbol_position') == 'before' ): echo get_option('bsa_pro_plugin_'.'currency_symbol'); endif; ?>
                        <input type="text" class="regular-text code billing-input" id="bsa_pro_cpc_price" name="cpc_price"
                               maxlength="10" value="<?php echo ($space_id && bsa_space($space_id, 'cpc_price') > 0 ? bsa_number_format(bsa_space($space_id, 'cpc_price')) : 0); ?>" placeholder="<?= bsa_number_format(0.00) ?>">
						<?php if ( get_option('bsa_pro_plugin_'.'symbol_position') == 'after' ): echo get_option('bsa_pro_plugin_'.'currency_symbol'); endif; ?>
                        <br>

                        <label for="bsa_pro_cpc_contract_1" class="billing-label"><strong>Contract 1st</strong> (target clicks)</label>
                        <input type="number" class="regular-text code billing-input" id="bsa_pro_cpc_contract_1" name="cpc_contract_1"
                               maxlength="10" value="<?php echo getSpaceValue('cpc_contract_1') ?>" placeholder="10"> clicks
                        <br>

                        <label for="bsa_pro_cpc_contract_2" class="billing-label"><strong>Contract 2nd</strong> (target clicks)</label>
                        <input type="number" class="regular-text code billing-input" id="bsa_pro_cpc_contract_2" name="cpc_contract_2"
                               maxlength="10" value="<?php echo getSpaceValue('cpc_contract_2') ?>" placeholder="100"> clicks
                        <br>

                        <label for="bsa_pro_cpc_contract_3" class="billing-label"><strong>Contract 3rd</strong> (target clicks)</label>
                        <input type="number" class="regular-text code billing-input" id="bsa_pro_cpc_contract_3" name="cpc_contract_3"
                               maxlength="10" value="<?php echo getSpaceValue('cpc_contract_3') ?>" placeholder="1000"> clicks

						<?php do_action( 'bsa-pro-addcontract', 'cpc' ); ?>
                    </div>

                    <div class="billing-col">
                        <h3>CPM - Cost per Mille (Views)</h3>

                        <label for="bsa_pro_cpm_price" class="billing-label"><strong>CPM Price</strong> (contract 1st)</label>
						<?php if ( get_option('bsa_pro_plugin_'.'symbol_position') == 'before' ): echo get_option('bsa_pro_plugin_'.'currency_symbol'); endif; ?>
                        <input type="text" class="regular-text code billing-input" id="bsa_pro_cpm_price" name="cpm_price"
                               maxlength="10" value="<?php echo ($space_id && bsa_space($space_id, 'cpm_price') > 0 ? bsa_number_format(bsa_space($space_id, 'cpm_price')) : 0); ?>" placeholder="<?= bsa_number_format(0.00) ?>">
						<?php if ( get_option('bsa_pro_plugin_'.'symbol_position') == 'after' ): echo get_option('bsa_pro_plugin_'.'currency_symbol'); endif; ?>
                        <br>

                        <label for="bsa_pro_cpm_contract_1" class="billing-label"><strong>Contract 1st</strong> (target views)</label>
                        <input type="number" class="regular-text code billing-input" id="bsa_pro_cpm_contract_1" name="cpm_contract_1"
                               maxlength="10" value="<?php echo getSpaceValue('cpm_contract_1') ?>" placeholder="1000"> views
                        <br>

                        <label for="bsa_pro_cpm_contract_2" class="billing-label"><strong>Contract 2nd</strong> (target views)</label>
                        <input type="number" class="regular-text code billing-input" id="bsa_pro_cpm_contract_2" name="cpm_contract_2"
                               maxlength="10" value="<?php echo getSpaceValue('cpm_contract_2') ?>" placeholder="10000"> views
                        <br>

                        <label for="bsa_pro_cpm_contract_3" class="billing-label"><strong>Contract 3rd</strong> (target views)</label>
                        <input type="number" class="regular-text code billing-input" id="bsa_pro_cpm_contract_3" name="cpm_contract_3"
                               maxlength="10" value="<?php echo getSpaceValue('cpm_contract_3') ?>" placeholder="100000"> views

						<?php do_action( 'bsa-pro-addcontract', 'cpm' ); ?>
                    </div>

                    <div class="billing-col">
                        <h3>CPD - Cost per Days</h3>

                        <label for="bsa_pro_cpd_price" class="billing-label"><strong>CPD Price</strong> (contract 1st)</label>
						<?php if ( get_option('bsa_pro_plugin_'.'symbol_position') == 'before' ): echo get_option('bsa_pro_plugin_'.'currency_symbol'); endif; ?>
                        <input type="text" class="regular-text code billing-input" id="bsa_pro_cpd_price" name="cpd_price"
                               maxlength="10" value="<?php echo ($space_id && bsa_space($space_id, 'cpd_price') > 0 ? bsa_number_format(bsa_space($space_id, 'cpd_price')) : 0); ?>" placeholder="<?= bsa_number_format(0.00) ?>">
						<?php if ( get_option('bsa_pro_plugin_'.'symbol_position') == 'after' ): echo get_option('bsa_pro_plugin_'.'currency_symbol'); endif; ?>
                        <br>

                        <label for="bsa_pro_cpd_contract_1" class="billing-label"><strong>Contract 1st</strong> (target days)</label>
                        <input type="number" class="regular-text code billing-input" id="bsa_pro_cpd_contract_1" name="cpd_contract_1"
                               maxlength="10" value="<?php echo getSpaceValue('cpd_contract_1') ?>" placeholder="30"> days
                        <br>

                        <label for="bsa_pro_cpd_contract_2" class="billing-label"><strong>Contract 2nd</strong> (target days)</label>
                        <input type="number" class="regular-text code billing-input" id="bsa_pro_cpd_contract_2" name="cpd_contract_2"
                               maxlength="10" value="<?php echo getSpaceValue('cpd_contract_2') ?>" placeholder="60"> days
                        <br>

                        <label for="bsa_pro_cpd_contract_3" class="billing-label"><strong>Contract 3rd</strong> (target days)</label>
                        <input type="number" class="regular-text code billing-input" id="bsa_pro_cpd_contract_3" name="cpd_contract_3"
                               maxlength="10" value="<?php echo getSpaceValue('cpd_contract_3') ?>" placeholder="90"> days

						<?php do_action( 'bsa-pro-addcontract', 'cpd' ); ?>
                    </div>
					<?php do_action( 'bsa-pro-addbilling' ); ?>
                </td>
            </tr>
            <tr class="<?php echo $dsadasd ?>">
                <th scope="row">
                    <label for="bsa_pro_discount_2">Discount (<strong>2nd</strong> contract)</label>
                </th>
                <td>
                    <input type="number" class="regular-text code" id="bsa_pro_discount_2" name="discount_2"
                           maxlength="2" value="<?php echo getSpaceValue('discount_2') ?>" placeholder="10"> <em>%</em>
                </td>
            </tr>
            <tr class="<?php echo $dsadasd ?>">
                <th scope="row">
                    <label for="bsa_pro_discount_3">Discount (<strong>3rd</strong> contract)</label>
                </th>
                <td>
                    <input type="number" class="regular-text code" id="bsa_pro_discount_3" name="discount_3"
                           maxlength="2" value="<?php echo getSpaceValue('discount_3') ?>" placeholder="25"> <em>%</em>
                </td>
            </tr>
			<?php $arhts = ''; ?>
			<?php if ( get_option('b'.$arhts.'sa_p'.$arhts.'ro_u'.'pd'.'at'.$arhts.'e'.'_s'.'ta'.'tus') == 'i'.$arhts.'n'.'v'.$arhts.'a'.'l'.$arhts.'i'.$arhts.'d' ): ?>
                <tr>
                    <td colspan="2">
                        <div class="bsaLockedContent">
                            <strong>Trial Version</strong><br>
                            Use purchase code to unlock CPC, CPM, CPD Billing Models.<br><br>
                            Paste purchase code in the <a href="<?php echo admin_url() ?>admin.php?page=bsa-pro-sub-menu-opts">settings</a> (Ads Pro > Settings > Purchase Code).<br>
                            Where is your purchase code? <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">Learn more</a>.<br><br>
                            - or -<br><br>
                            <a href="https://1.envato.market/buy-regular-ads-pro-6" target="_blank">Buy now</a> the latest version of Ads Pro.
                        </div>
                    </td>
                </tr>
			<?php endif; ?>
            <tr>
                <th colspan="2">
                    <h3><span class="dashicons dashicons-admin-appearance"></span> Space Layout Settings</h3>
                </th>
            </tr>
            <tr>
                <th scope="row">
                    <label for="bsa_pro_grid_system">Grid System</label><br><br>
                    <img loading="lazy" class="gutterPreview gridGutter" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/with-gutter.png'; ?>" alt="grid with gutter" style="<?php echo ( getSpaceValue('grid_system') == 'bsaGridGutter' || !isset($_GET['space_id']) ? 'display:block;' : 'display:none;'); ?>"/>
                    <img loading="lazy" class="gutterPreview bsaGridGutVer" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/ver-gutter.png'; ?>" alt="grid with vertical gutter" style="<?php echo ( getSpaceValue('grid_system') == 'bsaGridGutVer' ? 'display:block;' : 'display:none;'); ?>"/>
                    <img loading="lazy" class="gutterPreview bsaGridGutHor" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/hor-gutter.png'; ?>" alt="grid with horizontal gutter" style="<?php echo ( getSpaceValue('grid_system') == 'bsaGridGutHor' ? 'display:block;' : 'display:none;'); ?>"/>
                    <img loading="lazy" class="gutterPreview bsaGridNoGutter" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/without-gutter.png'; ?>" alt="grid without gutter" style="<?php echo ( getSpaceValue('grid_system') == 'bsaGridNoGutter' ? 'display:block;' : 'display:none;'); ?>"/>
                </th>
                <td class="bsaLast">
                    <fieldset>
                        <br><br>
                        <label title="grid with gutter between ads"><input type="radio" class="gutterButton" data-preview="gridGutter" <?php echo (getSpaceValue('grid_system') == 'bsaGridGutter' || !isset($_GET['space_id']) ? 'checked="checked"' : ''); ?> value="bsaGridGutter" name="grid_system">grid <strong>with Gutter between Ads</strong></label><br>
                        <label title="grid with vertical gutter between ads"><input type="radio" class="gutterButton" data-preview="bsaGridGutVer" <?php echo (getSpaceValue('grid_system') == 'bsaGridGutVer' ? 'checked="checked"' : ''); ?> value="bsaGridGutVer" name="grid_system">grid <strong>with Vertical Gutter between Ads</strong></label><br>
                        <label title="grid with horizontal gutter between ads"><input type="radio" class="gutterButton" data-preview="bsaGridGutHor" <?php echo (getSpaceValue('grid_system') == 'bsaGridGutHor' ? 'checked="checked"' : ''); ?> value="bsaGridGutHor" name="grid_system">grid <strong>with Horizontal Gutter between Ads</strong></label><br>
                        <label title="grid without gutter between ads"><input type="radio" class="gutterButton" data-preview="bsaGridNoGutter" <?php echo (getSpaceValue('grid_system') == 'bsaGridNoGutter' ? 'checked="checked"' : ''); ?> value="bsaGridNoGutter" name="grid_system">grid <strong>without Gutter between Ads</strong></label><br>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bsa_pro_template">Template</label></th>
                <td>
					<?php if ( isset($_GET['space_id']) ): ?>
						<?php $model = new BSA_PRO_Model(); ?>
						<?php $assignedAds = $model->getAssignedAds($_GET['space_id']); ?>
					<?php endif; ?>
					<?php if ( isset($_GET['space_id']) && is_array($assignedAds) && count($assignedAds) > 0 ): ?>
                        <div>
                            <strong>Note!</strong> It's not possible to change the ad template, because external ads are assigned to the ad space.<br><br>
							<?php foreach ( $assignedAds as $key => $ad ): ?>
								<?= ($key > 0 ? ' | ' : 'Assigned Ads: ') ?>ID: <strong><?= $ad['id'] ?></strong> (<a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-add-new-ad&ad_id=<?= $ad['id']; ?>" target="_blank">edit ad</a>)
							<?php endforeach; ?>
                        </div>
                        <input id="bsa_pro_template" type="hidden" name="template" value="<?php echo getSpaceValue('template') ?>">
					<?php else: ?>
                        <select id="bsa_pro_template" name="template" onchange="bsaGetTemplate()">
							<?php
							$styles = array();
							$templates = glob(plugin_dir_path( __FILE__ )."../frontend/template/*");
							foreach ( $templates as $file ) {
								$files = $file;
								$styles = explode('/',$files);
								$style = array_reverse($styles);
								$name = explode('.', $style[0]);
								if ($name[0] != 'asset') {
									$dsghghf = ( get_option('b'.'sa'.'_pr'.'o_u'.'pd'.'ate_s'.'tatu'.'s') == 'i'.'n'.'va'.'li'.'d' && !in_array($name[0], array('default', 'block-125--125', 'block-468--60')) ? true : false );
									?>
                                    <option value="<?php echo $name[0]; ?>" <?php selectedSpaceOpt('template', $name[0]); ?> <?php echo ($dsghghf == true ? 'disabled' : '' ) ?>> <?php echo ucfirst ( str_replace('-',' ',$name[0]) ); ?> <?php echo ($dsghghf == true ? '(full version)' : '') ?></option>
									<?php
								}
							}

							?>
                        </select>
					<?php endif; ?>

                    <h3>Ad Live Preview <span class="bsaLoader" style="display:none;"></span></h3>
                    <div class="bsaTemplatePreview">
                        <div class="bsaTemplatePreviewInner"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bsa_pro_display_type">Display Type</label></th>
                <td>
					<?php $tdflsd = ( get_option('bsa_pro'.'_'.'update'.'_status') == 'invalid' ? 'disabled' : '' ); ?>
                    <select id="bsa_pro_display_type" name="display_type">
                        <option value="default" <?php selectedSpaceOpt('display_type', 'default'); ?>>default</option>
                        <option value="carousel_slide" <?php selectedSpaceOpt('display_type', 'carousel_slide'); ?> <?php echo $tdflsd ?>>carousel - slide <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                        <option value="carousel_fade" <?php selectedSpaceOpt('display_type', 'carousel_fade'); ?> <?php echo $tdflsd ?>>carousel - fade <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                        <option value="top_scroll_bar" <?php selectedSpaceOpt('display_type', 'top_scroll_bar'); ?> <?php echo $tdflsd ?>>top scroll bar <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                        <option value="bottom_scroll_bar" <?php selectedSpaceOpt('display_type', 'bottom_scroll_bar'); ?> <?php echo $tdflsd ?>>bottom scroll bar <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                        <option value="floating-bottom-right" <?php selectedSpaceOpt('display_type', 'floating-bottom-right'); ?> <?php echo $tdflsd ?>>floating - bottom right <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                        <option value="floating-bottom-left" <?php selectedSpaceOpt('display_type', 'floating-bottom-left'); ?> <?php echo $tdflsd ?>>floating - bottom left <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                        <option value="floating-top-right" <?php selectedSpaceOpt('display_type', 'floating-top-right'); ?> <?php echo $tdflsd ?>>floating - top right <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                        <option value="floating-top-left" <?php selectedSpaceOpt('display_type', 'floating-top-left'); ?> <?php echo $tdflsd ?>>floating - top left <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                        <option value="popup" <?php selectedSpaceOpt('display_type', 'popup'); ?> <?php echo $tdflsd ?>>pop-up <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                        <option value="popup_2" <?php selectedSpaceOpt('display_type', 'popup_2'); ?> <?php echo $tdflsd ?>>pop-up + opacity (70%) <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                        <option value="corner" <?php selectedSpaceOpt('display_type', 'corner'); ?> <?php echo $tdflsd ?>>corner peel <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                        <option value="layer" <?php selectedSpaceOpt('display_type', 'layer'); ?> <?php echo $tdflsd ?>>layer <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                        <option value="background" <?php selectedSpaceOpt('display_type', 'background'); ?> <?php echo $tdflsd ?>>background <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                        <option value="exit_popup" <?php selectedSpaceOpt('display_type', 'exit_popup'); ?> <?php echo $tdflsd ?>>exit pop-up <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                        <option value="exit_popup_2" <?php selectedSpaceOpt('display_type', 'exit_popup_2'); ?> <?php echo $tdflsd ?>>exit pop-up + opacity (70%) <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                        <option value="link" <?php selectedSpaceOpt('display_type', 'link'); ?> <?php echo $tdflsd ?>>hover link <?php echo ($tdflsd == 'disabled' ? '(full'.' version)' : '') ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    Show Ads Randomly or Statically<br><br>
                    <img loading="lazy" class="gridPreview allGrid" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/all-in-grid.png'; ?>" alt="all in grid statically" style="<?php echo ( getSpaceValue('random') == 0 || !isset($_GET['space_id']) ? 'display:block;' : 'display:none;'); ?>"/>
                    <img loading="lazy" class="gridPreview 1row" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/1-row.png'; ?>" alt="one row statically" style="<?php echo ( getSpaceValue('random') == 8 ? 'display:block;' : 'display:none;'); ?>"/>
                    <img loading="lazy" class="gridPreview 1column" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/1-column.png'; ?>" alt="one column statically" style="<?php echo ( getSpaceValue('random') == 9 ? 'display:block;' : 'display:none;'); ?>"/>
                    <img loading="lazy" class="gridPreview 2grid" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/2-grid.png'; ?>" alt="two rows / columns statically" style="<?php echo ( getSpaceValue('random') == 4 ? 'display:block;' : 'display:none;'); ?>"/>
                    <img loading="lazy" class="gridPreview 3grid" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/3-grid.png'; ?>" alt="three rows / columns statically" style="<?php echo ( getSpaceValue('random') == 5 ? 'display:block;' : 'display:none;'); ?>"/>
                    <img loading="lazy" class="gridPreview 1rowRandom" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/1-row-random.png'; ?>" alt="one row randomly" style="<?php echo ( getSpaceValue('random') == 1 ? 'display:block;' : 'display:none;'); ?>"/>
                    <img loading="lazy" class="gridPreview 1columnRandom" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/1-column-random.png'; ?>" alt="one column randomly" style="<?php echo ( getSpaceValue('random') == 2 ? 'display:block;' : 'display:none;'); ?>"/>
                    <img loading="lazy" class="gridPreview 2gridRandom" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/2-grid-random.png'; ?>" alt="two rows / columns randomly" style="<?php echo ( getSpaceValue('random') == 6 ? 'display:block;' : 'display:none;'); ?>"/>
                    <img loading="lazy" class="gridPreview 3gridRandom" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/3-grid-random.png'; ?>" alt="three rows / columns randomly" style="<?php echo ( getSpaceValue('random') == 7 ? 'display:block;' : 'display:none;'); ?>"/>
                    <img loading="lazy" class="gridPreview allGridRandom" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/all-in-grid-random.png'; ?>" alt="all in grid randomly" style="<?php echo ( getSpaceValue('random') == 3 ? 'display:block;' : 'display:none;'); ?>"/>
                </th>
                <td>
                    <fieldset>
                        <label title="show ALL ads statically"><input type="radio" class="gridButton" data-preview="allGrid" <?php echo (getSpaceValue('random') == 0 || !isset($_GET['space_id']) ? 'checked="checked"' : ''); ?> value="0" name="random">show <strong>ALL ads statically</strong></label><br>
                        <label title="show ads statically in 1 row"><input type="radio" class="gridButton" data-preview="1row" <?php echo (getSpaceValue('random') == 8 ? 'checked="checked"' : ''); ?> value="8" name="random">show ads <strong>statically in 1 row</strong></label><br>
                        <label title="show ads statically in 1 column"><input type="radio" class="gridButton" data-preview="1column" <?php echo (getSpaceValue('random') == 9 ? 'checked="checked"' : ''); ?> value="9" name="random">show ads <strong>statically in 1 column</strong></label><br>
                        <label title="show ads statically in 2 rows / columns"><input type="radio" class="gridButton" data-preview="2grid" <?php echo (getSpaceValue('random') == 4 ? 'checked="checked"' : ''); ?> value="4" name="random">show ads <strong>statically in 2 rows / columns</strong></label><br>
                        <label title="show ads statically in 3 rows / columns"><input type="radio" class="gridButton" data-preview="3grid" <?php echo (getSpaceValue('random') == 5 ? 'checked="checked"' : ''); ?> value="5" name="random">show ads <strong>statically in 3 rows / columns</strong></label><br>
                        <label title="show ads randomly in 1 row"><input type="radio" class="gridButton" data-preview="1rowRandom" <?php echo (getSpaceValue('random') == 1 ? 'checked="checked"' : ''); ?> value="1" name="random">show ads <strong>randomly in 1 row</strong></label><br>
                        <label title="show ads randomly in 1 column"><input type="radio" class="gridButton" data-preview="1columnRandom" <?php echo (getSpaceValue('random') == 2 ? 'checked="checked"' : ''); ?> value="2" name="random">show ads <strong>randomly in 1 column</strong></label><br>
                        <label title="show ads randomly in 2 rows / columns"><input type="radio" class="gridButton" data-preview="2gridRandom" <?php echo (getSpaceValue('random') == 6 ? 'checked="checked"' : ''); ?> value="6" name="random">show ads <strong>randomly in 2 rows / columns</strong></label><br>
                        <label title="show ads randomly in 3 rows / columns"><input type="radio" class="gridButton" data-preview="3gridRandom" <?php echo (getSpaceValue('random') == 7 ? 'checked="checked"' : ''); ?> value="7" name="random">show ads <strong>randomly in 3 rows / columns</strong></label><br>
                        <label title="show ALL ads randomly"><input type="radio" class="gridButton" data-preview="allGridRandom" <?php echo (getSpaceValue('random') == 3 ? 'checked="checked"' : ''); ?> value="3" name="random">show <strong>ALL ads randomly</strong></label><br>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bsa_pro_max_items">Maximum Ads in Space</label></th>
                <td>
                    <select id="bsa_pro_max_items" name="max_items">
						<?php
						for ($i = 1; $i <= 96; $i++) {
							echo $i;
							?>
                            <option value="<?php echo $i; ?>" <?php selectedSpaceOpt('max_items', $i); ?>> <?php echo $i; ?> ad<?php if($i != 1) { echo 's'; } ?></option>
							<?php
						}
						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bsa_pro_col_per_row">Number of Ads per one <br>Row or Column</label></th>
                <td>
                    <select id="bsa_pro_col_per_row" name="col_per_row">
						<?php

						for ($i = 1; $i <= 12; $i++) {
							echo $i;
							if ( $i <= 4 || $i == 8 || $i == 12 ) {
								?>
                                <option value="<?php echo $i; ?>" class="colsRowsOpt" <?php selectedSpaceOpt('col_per_row', $i); ?>> <?php echo $i; ?> <?php if($i == 1) { echo 'ad in row / column'; } else { echo 'ads in rows / columns'; } ?></option>
								<?php
							}
						}

						?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bsa_pro_animation">Ads Animation if visible</label></th>
                <td>
                    <select id="bsa_pro_animation" name="animation">
                        <option value="none" <?php selectedSpaceOpt('animation', 'none'); ?>>none</option>
                        <optgroup label="Attention Seekers">
                            <option value="bounce" <?php selectedSpaceOpt('animation', 'bounce'); ?>>bounce</option>
                            <option value="flash" <?php selectedSpaceOpt('animation', 'flash'); ?>>flash</option>
                            <option value="pulse" <?php selectedSpaceOpt('animation', 'pulse'); ?>>pulse</option>
                            <option value="rubberBand" <?php selectedSpaceOpt('animation', 'rubberBand'); ?>>rubberBand</option>
                            <option value="shake" <?php selectedSpaceOpt('animation', 'shake'); ?>>shake</option>
                            <option value="swing" <?php selectedSpaceOpt('animation', 'swing'); ?>>swing</option>
                            <option value="tada" <?php selectedSpaceOpt('animation', 'tada'); ?>>tada</option>
                            <option value="wobble" <?php selectedSpaceOpt('animation', 'wobble'); ?>>wobble</option>
                        </optgroup>

                        <optgroup label="Bouncing Entrances">
                            <option value="bounceIn" <?php selectedSpaceOpt('animation', 'bounceIn'); ?>>bounceIn</option>
                            <option value="bounceInDown" <?php selectedSpaceOpt('animation', 'bounceInDown'); ?>>bounceInDown</option>
                            <option value="bounceInLeft" <?php selectedSpaceOpt('animation', 'bounceInLeft'); ?>>bounceInLeft</option>
                            <option value="bounceInRight" <?php selectedSpaceOpt('animation', 'bounceInRight'); ?>>bounceInRight</option>
                            <option value="bounceInUp" <?php selectedSpaceOpt('animation', 'bounceInUp'); ?>>bounceInUp</option>
                        </optgroup>

                        <optgroup label="Fading Entrances">
                            <option value="fadeIn" <?php selectedSpaceOpt('animation', 'fadeIn'); ?>>fadeIn</option>
                            <option value="fadeInDown" <?php selectedSpaceOpt('animation', 'fadeInDown'); ?>>fadeInDown</option>
                            <option value="fadeInDownBig" <?php selectedSpaceOpt('animation', 'fadeInDownBig'); ?>>fadeInDownBig</option>
                            <option value="fadeInLeft" <?php selectedSpaceOpt('animation', 'fadeInLeft'); ?>>fadeInLeft</option>
                            <option value="fadeInLeftBig" <?php selectedSpaceOpt('animation', 'fadeInLeftBig'); ?>>fadeInLeftBig</option>
                            <option value="fadeInRight" <?php selectedSpaceOpt('animation', 'fadeInRight'); ?>>fadeInRight</option>
                            <option value="fadeInRightBig" <?php selectedSpaceOpt('animation', 'fadeInRightBig'); ?>>fadeInRightBig</option>
                            <option value="fadeInUp" <?php selectedSpaceOpt('animation', 'fadeInUp'); ?>>fadeInUp</option>
                            <option value="fadeInUpBig" <?php selectedSpaceOpt('animation', 'fadeInUpBig'); ?>>fadeInUpBig</option>
                        </optgroup>

                        <optgroup label="Flippers">
                            <option value="flip" <?php selectedSpaceOpt('animation', 'flip'); ?>>flip</option>
                            <option value="flipInX" <?php selectedSpaceOpt('animation', 'flipInX'); ?>>flipInX</option>
                            <option value="flipInY" <?php selectedSpaceOpt('animation', 'flipInY'); ?>>flipInY</option>
                        </optgroup>

                        <optgroup label="Lightspeed">
                            <option value="lightSpeedIn" <?php selectedSpaceOpt('animation', 'lightSpeedIn'); ?>>lightSpeedIn</option>
                        </optgroup>

                        <optgroup label="Rotating Entrances">
                            <option value="rotateIn" <?php selectedSpaceOpt('animation', 'rotateIn'); ?>>rotateIn</option>
                            <option value="rotateInDownLeft" <?php selectedSpaceOpt('animation', 'rotateInDownLeft'); ?>>rotateInDownLeft</option>
                            <option value="rotateInDownRight" <?php selectedSpaceOpt('animation', 'rotateInDownRight'); ?>>rotateInDownRight</option>
                            <option value="rotateInUpLeft" <?php selectedSpaceOpt('animation', 'rotateInUpLeft'); ?>>rotateInUpLeft</option>
                            <option value="rotateInUpRight" <?php selectedSpaceOpt('animation', 'rotateInUpRight'); ?>>rotateInUpRight</option>
                        </optgroup>

                        <optgroup label="Specials">
                            <option value="hinge" <?php selectedSpaceOpt('animation', 'hinge'); ?>>hinge</option>
                            <option value="rollIn" <?php selectedSpaceOpt('animation', 'rollIn'); ?>>rollIn</option>
                        </optgroup>

                        <optgroup label="Zoom Entrances">
                            <option value="zoomIn" <?php selectedSpaceOpt('animation', 'zoomIn'); ?>>zoomIn</option>
                            <option value="zoomInDown" <?php selectedSpaceOpt('animation', 'zoomInDown'); ?>>zoomInDown</option>
                            <option value="zoomInLeft" <?php selectedSpaceOpt('animation', 'zoomInLeft'); ?>>zoomInLeft</option>
                            <option value="zoomInRight" <?php selectedSpaceOpt('animation', 'zoomInRight'); ?>>zoomInRight</option>
                            <option value="zoomInUp" <?php selectedSpaceOpt('animation', 'zoomInUp'); ?>>zoomInUp</option>
                        </optgroup>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><h3 class="showHideAdvanced">more options <span class="dashicons dashicons-ellipsis"></span></h3></th>
                <td></td>
            </tr>
			<?php $arhts = ''; ?>
			<?php if ( get_option('b'.$arhts.'sa_p'.$arhts.'ro_u'.'pd'.'at'.$arhts.'e'.'_s'.'ta'.'tus') == 'i'.$arhts.'n'.'v'.$arhts.'a'.'l'.$arhts.'i'.$arhts.'d' ): ?>
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
                        <img loading="lazy" class="bsaLockedImage" alt="Space Advanced Settings" src="<?php echo plugin_dir_url( __DIR__ ) ?>frontend/img/main-space-preview.png" />
                    </td>
                </tr>
			<?php else: ?>
                <tr class="showAdvanced">
                    <th scope="row"><label for="bsa_pro_devices">Show on specific devices</label></th>
                    <td>
                        <ul>
                            <li class="bsaProSpecificDevice">
                                <label class="selectit">
                                    <input value="mobile" type="checkbox" name="devices[]" <?php checkedSpaceOpt('devices', 'mobile'); ?>><br><br>
                                    <img loading="lazy" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/icon-mobile.png'; ?>"/><br>
                                    Mobile
                                </label>
                            </li>
                            <li class="bsaProSpecificDevice">
                                <label class="selectit">
                                    <input value="tablet" type="checkbox" name="devices[]" <?php checkedSpaceOpt('devices', 'tablet'); ?>><br><br>
                                    <img loading="lazy" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/icon-tablet.png'; ?>"/><br>
                                    Tablet
                                </label>
                            </li>
                            <li class="bsaProSpecificDevice">
                                <label class="selectit">
                                    <input value="desktop" type="checkbox" name="devices[]" <?php checkedSpaceOpt('devices', 'desktop'); ?>><br><br>
                                    <img loading="lazy" src="<?php echo plugin_dir_url( __DIR__ ).'frontend/img/icon-desktop.png'; ?>"/><br>
                                    Desktop
                                </label>
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr class="showAdvanced">
                    <th scope="row"><label for="bsa_pro_show_ads">Show Ad Space after X seconds</label></th>
                    <td>
                        <select id="bsa_pro_show_ads" name="show_ads">
							<?php

							for ($i = 0; $i <= 250; $i++) {
								echo $i;
								if ( $i == 0 || $i >= 2 && $i <= 10 || $i == 15 || $i == 20 || $i == 25 || $i == 30 || $i == 40 || $i == 50 || $i == 75 || $i == 100 || $i == 125 || $i == 150 || $i == 200 || $i == 250 ) {
									?>
                                    <option value="<?php echo $i; ?>" <?php selectedSpaceOpt('show_ads', $i); ?>> <?php echo ($i > 0 ? $i : ''); ?> <?php echo ($i > 0 ? ($i > 1 ? 'seconds' : 'second') : 'none' ); ?></option>
									<?php
								}
							}

							?>
                        </select>
                        <p class="description">You can use it for <strong>default, carousel, scroll bar, corner peel</strong> and <strong>pop-up</strong> display types.</p>
                    </td>
                </tr>
                <tr class="showAdvanced">
                    <th scope="row"><label for="bsa_pro_show_close_btn">Show Close Button after X seconds</label></th>
                    <td>
                        <select id="bsa_pro_show_close_btn" name="show_close_btn">
							<?php for ($i = 0; $i <= 60; $i++) {
								echo $i;
								if ( $i == 0 || $i >= 2 && $i <= 10 || $i == 15 || $i == 20 || $i == 25 || $i == 30 || $i == 40 || $i == 50 || $i == 60 ) {
									?>
                                    <option value="<?php echo $i; ?>" <?php selectedSpaceOpt('show_close_btn', $i); ?>> <?php echo ($i > 0 ? $i : ''); ?> <?php echo ($i > 0 ? ($i > 1 ? 'seconds' : 'second') : 'none' ); ?></option>
									<?php
								}
							} ?>
                            <option value="1000" <?php selectedSpaceOpt('show_close_btn', 1000); ?>> never</option>
                        </select>
                        <p class="description">You can use it for <strong>scroll bar, floating, layer</strong> and <strong>pop-up</strong> display types.</p>
                    </td>
                </tr>
                <tr class="showAdvanced">
                    <th scope="row"><label for="bsa_pro_close_ads">Close Ad Space after X seconds</label></th>
                    <td>
                        <select id="bsa_pro_close_ads" name="close_ads">
							<?php
							for ($i = 0; $i <= 250; $i++) {
								echo $i;
								if ( $i == 0 || $i >= 5 && $i <= 10 || $i == 15 || $i == 20 || $i == 25 || $i == 30 || $i == 40 || $i == 50 || $i == 75 || $i == 100 || $i == 125 || $i == 150 || $i == 200 || $i == 250 ) {
									?>
                                    <option value="<?php echo $i; ?>" <?php selectedSpaceOpt('close_ads', $i); ?>> <?php echo ($i > 0 ? $i : ''); ?> <?php echo ($i > 0 ? ($i > 1 ? 'seconds' : 'second') : 'none' ); ?></option>
									<?php
								}
							}
							?>
                        </select>
                        <p class="description">You can use it for <strong>default, carousel, scroll bar, corner peel,</strong> and <strong>pop-up</strong> display types.</p>
                    </td>
                </tr>
                <tr id="bsaShowPagePost" class="showAdvanced" style="padding-top: 25px;">
                    <th class="bsaLast" scope="row"><strong>Hide</strong> for specific <br>Posts / Pages / Custom Types or Taxonomies</th>
                    <td class="bsaLast">
                        <div style="max-width: 500px;">
                            <div class="inside">
                                <div id="taxonomy-category" class="categorydiv">
                                    <ul id="category-tabs" class="category-tabs">
                                        <li class="tabs bsaProTabPP" data-tab="bsaAllPages"><a href="#bsaShowPagePost">Select Pages</a></li>
                                        <li class="bsaProTabPP" data-tab="bsaAllPosts"><a href="#bsaShowPagePost">Select Posts</a></li>
                                        <li class="bsaProTabPP" data-tab="bsaAllHideCustoms"><a href="#bsaShowPagePost">Custom Types or Taxonomies</a></li>
                                    </ul>

                                    <div class="bsaAllHideCustoms tabs-panel" style="display: none;">
                                        <br><strong>Note!</strong><br>
                                        We recommend really careful use of advanced options. Implemented rules can really limit your Ads.
                                        Remember to paste exact the <strong>CPT slug or Taxonomies</strong>.
										<?php $getAdvanced = ( isset($_GET['space_id']) && $_GET['space_id'] > 0 ? json_decode(bsa_space($_GET['space_id'], 'advanced_opt')) : null); ?>
                                        <input type="hidden" name="hide_customs" class="spaceHideCustoms" value="<?php echo (isset($getAdvanced->hide_customs) ? $getAdvanced->hide_customs : '') ?>" />
                                        <input type="text" id="spaceHideCustoms" class="spaceChips tagfield" value="<?php echo (isset($getAdvanced->hide_customs) ? $getAdvanced->hide_customs : '') ?>" placeholder=""/>
                                    </div>

									<?php $ajaxLimit = 200; ?>
									<?php $count_posts = wp_count_posts(); ?>
									<?php $count_pages = wp_count_posts('page'); ?>
                                    <div class="bsaAllPosts tabs-panel" style="display: none;">
                                        <ul id="inside-list-posts" class="categorychecklist form-no-clear">
                                            <h4>Selected</h4>
                                            <div class="checkedPO"></div>
                                            <h4>Unselected</h4>
                                            <div class="uncheckedPO" offset="0" count="<?php echo $count_posts->publish ?>">
												<?php
												if ( is_multisite() ) {

													// Current Site
													$current = get_current_site();

													// All Sites
													$blogs = json_decode(json_encode(get_sites()), true);
													foreach ( $blogs as $blog ) {

														// switch to the blog
														switch_to_blog( $blog['blog_id'] );

														// get only selected entry
														$getEntryIds = null;
														if ( isset($_GET['space_id']) ) {
															$getIds = json_decode(bsa_space($_GET['space_id'], 'advanced_opt'));
															if ( isset($getIds->hide_for_id) ) {
																foreach ( explode(',', $getIds->hide_for_id) as $getId ) {
																	if ( substr($getId, 0, 1) == $blog['blog_id'] ) {
																		$getEntryIds[] = substr($getId, 1);
																	}
																}
															}
														}

														// get args
														if ( $count_posts->publish <= $ajaxLimit || $getEntryIds != null ) {
															if ( $count_posts->publish <= $ajaxLimit ) { $getEntryIds = array(); }
															$args = array('include' => $getEntryIds, 'posts_per_page' => $ajaxLimit);
															$allPosts = get_posts($args);
															if ($allPosts) {
																foreach ($allPosts as $post) {
																	?>
                                                                    <li class="bsaProSpecificItem bsaCheckItem-PO<?php echo $post->ID; ?>-<?php echo $blog['blog_id']; ?>">
                                                                        <label class="selectit"><input
                                                                                    value="<?php echo $blog['blog_id']; ?><?php echo $post->ID; ?>"
                                                                                    class="bsaCheckItem" section="PO"
                                                                                    itemId="PO<?php echo $post->ID; ?>-<?php echo $blog['blog_id']; ?>"
                                                                                    type="checkbox"
                                                                                    name="hide_for_id[]" <?php checkedSpaceOpt('hide_for_id', $blog['blog_id'] . $post->ID); ?>>
																			<?php echo $post->post_title; ?> (site
                                                                            id: <?php echo $blog['blog_id']; ?>)</label>
                                                                    </li>
																	<?php
																}
															}
														}

													}

													// return to the current site
													switch_to_blog( $current->id );

												} else {

													// get only selected posts
													$getEntryIds = null;
													if ( isset($_GET['space_id']) ) {
														$getIds = json_decode(bsa_space($_GET['space_id'], 'advanced_opt'));
														if ( isset($getIds->hide_for_id) && $getIds->hide_for_id != '' ) {
															foreach ( explode(',', $getIds->hide_for_id) as $getId ) {
																$getEntryIds[] = $getId;
															}
														}
													}

													// get args
													if ( $count_posts->publish <= $ajaxLimit || $getEntryIds != null ) {
														if ( $count_posts->publish <= $ajaxLimit ) { $getEntryIds = array(); }
														$args = array('include' => $getEntryIds, 'posts_per_page' => $ajaxLimit);
														$allPosts = get_posts($args);
														if ($allPosts) {
															foreach ($allPosts as $post) {
																?>
                                                                <li class="bsaProSpecificItem bsaCheckItem-PO<?php echo $post->ID; ?>">
                                                                    <label class="selectit"><input
                                                                                value="<?php echo $post->ID; ?>"
                                                                                class="bsaCheckItem" section="PO"
                                                                                itemId="PO<?php echo $post->ID; ?>"
                                                                                type="checkbox"
                                                                                name="hide_for_id[]" <?php checkedSpaceOpt('hide_for_id', $post->ID); ?>>
																		<?php echo $post->post_title; ?></label>
                                                                </li>
																<?php
															}
														}
													}

												}

												if ( $count_posts->publish > $ajaxLimit ) {
													?>
                                                    <a href="#bsaShowPagePost" class="bsaLinkPO" onclick="bsaGetUnselected('posts', 'PO', <?php echo $ajaxLimit; ?>)">show unselected posts</a> <span class="bsaLoader bsaLoaderPO" style="display:none;"></span>
													<?php
												}
												?>
                                            </div>
                                        </ul>
                                    </div>

                                    <div class="bsaAllPages tabs-panel" style="display: block;">
                                        <ul id="inside-list-pages" data-wp-lists="list:page" class="pagechecklist form-no-clear">
                                            <h4>Selected</h4>
                                            <div class="checkedPA"></div>
                                            <h4>Unselected</h4>
                                            <div class="uncheckedPA" offset="0" count="<?php echo $count_pages->publish ?>">
												<?php
												if ( is_multisite() ) {

													// Current Site
													$current = get_current_site();

													// All Sites
													$blogs = json_decode(json_encode(get_sites()), true);
													foreach ( $blogs as $blog ) {

														// switch to the blog
														switch_to_blog( $blog['blog_id'] );

														// get only selected entry
														$getEntryIds = null;
														if ( isset($_GET['space_id']) ) {
															$getIds = json_decode(bsa_space($_GET['space_id'], 'advanced_opt'));
															if ( isset($getIds->hide_for_id) && $getIds->hide_for_id != '' ) {
																foreach ( explode(',', $getIds->hide_for_id) as $getId ) {
																	if ( substr($getId, 0, 1) == $blog['blog_id'] ) {
																		$getEntryIds[] = substr($getId, 1);
																	}
																}
															}
														}

														// get args
														if ( $count_pages->publish <= $ajaxLimit || $getEntryIds != null ) {
															if ( $count_pages->publish <= $ajaxLimit ) { $getEntryIds = array(); }
															$args = array('include' => $getEntryIds, 'number' => $ajaxLimit);
															$allPosts = get_pages($args);
															if ($allPosts) {
																foreach ($allPosts as $post) {
																	?>
                                                                    <li class="bsaProSpecificItem bsaCheckItem-PA<?php echo $post->ID; ?>-<?php echo $blog['blog_id']; ?>">
                                                                        <label class="selectit"><input
                                                                                    value="<?php echo $blog['blog_id']; ?><?php echo $post->ID; ?>"
                                                                                    class="bsaCheckItem" section="PA"
                                                                                    itemId="PA<?php echo $post->ID; ?>-<?php echo $blog['blog_id']; ?>"
                                                                                    type="checkbox"
                                                                                    name="hide_for_id[]" <?php checkedSpaceOpt('hide_for_id', $blog['blog_id'] . $post->ID); ?>>
																			<?php echo $post->post_title; ?> (site
                                                                            id: <?php echo $blog['blog_id']; ?>)</label>
                                                                    </li>
																	<?php
																}
															}
														}

													}

													// return to the current site
													switch_to_blog( $current->id );

												} else {

													// get only selected posts
													$getEntryIds = null;
													if ( isset($_GET['space_id']) ) {
														$getIds = json_decode(bsa_space($_GET['space_id'], 'advanced_opt'));
														if ( isset($getIds->hide_for_id) && $getIds->hide_for_id != '' ) {
															foreach ( explode(',', $getIds->hide_for_id) as $getId ) {
																$getEntryIds[] = $getId;
															}
														}
													}

													// get args
													if ( $count_pages->publish <= $ajaxLimit || $getEntryIds != null ) {
														if ( $count_pages->publish <= $ajaxLimit ) { $getEntryIds = array(); }
														$args = array('include' => $getEntryIds, 'number' => $ajaxLimit);
														$allPosts = get_pages($args);
														if ($allPosts) {
															foreach ($allPosts as $post) {
																?>
                                                                <li class="bsaProSpecificItem bsaCheckItem-PA<?php echo $post->ID; ?>">
                                                                    <label class="selectit"><input
                                                                                value="<?php echo $post->ID; ?>"
                                                                                class="bsaCheckItem" section="PA"
                                                                                itemId="PA<?php echo $post->ID; ?>"
                                                                                type="checkbox"
                                                                                name="hide_for_id[]" <?php checkedSpaceOpt('hide_for_id', $post->ID); ?>>
																		<?php echo $post->post_title; ?></label>
                                                                </li>
																<?php
															}
														}
													}

												}

												if ( $count_pages->publish > $ajaxLimit ) {
													?>
                                                    <a href="#bsaShowPagePost" class="bsaLinkPA" onclick="bsaGetUnselected('pages', 'PA', <?php echo $ajaxLimit; ?>)">show unselected pages</a> <span class="bsaLoader bsaLoaderPA" style="display:none;"></span>
													<?php
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
                <tr id="bsaShowCatTag" class="showAdvanced" style="padding-top: 25px;">
                    <th class="bsaLast" scope="row">Show for specific <br>Categories / Tags / Custom Types or Taxonomies</th>
                    <td class="bsaLast">
                        <div style="max-width: 500px;">
                            <div class="inside">
                                <div id="taxonomy-category" class="categorydiv">
                                    <ul id="category-tabs" class="category-tabs">
                                        <li class="tabs bsaProTab" data-tab="bsaAllCategories"><a href="#bsaShowCatTag">Select Categories</a></li>
                                        <li class="bsaProTab" data-tab="bsaAllTags"><a href="#bsaShowCatTag">Select Tags</a></li>
                                        <li class="bsaProTab" data-tab="bsaAllShowCustoms"><a href="#bsaShowCatTag">Custom Types or Taxonomies</a></li>
                                    </ul>

                                    <div class="bsaAllShowCustoms tabs-panel" style="display: none;">
                                        <br><strong>Note!</strong><br>
                                        We recommend really careful use of advanced options. Implemented rules can really limit your Ads.
                                        Remember to paste exact the <strong>CPT slug or Taxonomies</strong>.
                                        <input type="hidden" name="show_customs" class="spaceShowCustoms" value="<?php echo (isset($getAdvanced->show_customs) ? $getAdvanced->show_customs : '') ?>" />
                                        <input type="text" id="spaceShowCustoms" class="spaceChips tagfield" value="<?php echo (isset($getAdvanced->show_customs) ? $getAdvanced->show_customs : '') ?>" placeholder=""/>
                                    </div>

									<?php $count_tags = count(get_tags()); ?>
                                    <div class="bsaAllTags tabs-panel" style="display: none;">
                                        <ul id="inside-list-tags" class="categorychecklist form-no-clear">
                                            <h4>Selected</h4>
                                            <div class="checkedT"></div>
                                            <h4>Unselected</h4>
                                            <div class="uncheckedT" offset="0" count="<?php echo $count_tags ?>">
												<?php
												if ( is_multisite() ) {

													// Current Site
													$current = get_current_site();

													// All Sites
													$blogs = json_decode(json_encode(get_sites()), true);

													foreach ( $blogs as $blog ) {

														// switch to the blog
														switch_to_blog( $blog['blog_id'] );

														// get only selected tags
														$getEntryIds = null;
														if ( isset($_GET['space_id']) ) {
															$getIds = bsa_space($_GET['space_id'], 'has_tags');
															if ( isset($getIds) && $getIds != '' ) {
																foreach ( explode(',', $getIds) as $getId ) {
																	$getId = get_term_by('name', $getId, 'post_tag');
																	if ( isset( $getId->term_id ) ) {
																		$getEntryIds[] = $getId->term_id;
																	}
																}
															}
														}

														if ( $count_tags <= $ajaxLimit || $getEntryIds != null && $getEntryIds[0] != null ) {
															if ( $count_tags <= $ajaxLimit ) { $getEntryIds = array(); }
															$args = array( 'taxonomy' => 'post_tag', 'include' => $getEntryIds, 'number' => $ajaxLimit );
															$posttags = get_terms($args);
															if ( !is_wp_error( $posttags ) ) {
																if ( is_array($posttags) ) {
																	foreach ($posttags as $key => $tag) {
																		?>
                                                                        <li class="bsaProSpecificItem bsaCheckItem-T<?php echo $key; ?>-<?php echo $blog['blog_id']; ?>">
                                                                            <label class="selectit"><input
                                                                                        value="<?php echo $tag->name; ?>"
                                                                                        class="bsaCheckItem" section="T"
                                                                                        itemId="T<?php echo $key; ?>-<?php echo $blog['blog_id']; ?>"
                                                                                        type="checkbox"
                                                                                        name="space_tags[]" <?php checkedSpaceOpt('has_tags', $tag->name); ?>>
																				<?php echo $tag->name; ?> (site
                                                                                id: <?php echo $blog['blog_id']; ?>)</label>
                                                                        </li>
																		<?php
																	}
																} else { echo "No tags found."; }
															}
														}

													}

													// return to the current site
													switch_to_blog( $current->id );

												} else {

													// get only selected tags
													$getEntryIds = null;
													if ( isset($_GET['space_id']) ) {
														$getIds = bsa_space($_GET['space_id'], 'has_tags');
														if ( isset($getIds) && $getIds != '' ) {
															foreach ( explode(',', $getIds) as $getId ) {
																$getId = get_term_by('name', $getId, 'post_tag');
																if ( isset($getId->term_id) ) {
																	$getEntryIds[] = $getId->term_id;
																}
															}
														}
													}

													if ( $count_tags <= $ajaxLimit || $getEntryIds != null && $getEntryIds[0] != null ) {
														if ( $count_tags <= $ajaxLimit ) { $getEntryIds = array(); }
														$args = array( 'taxonomy' => 'post_tag', 'include' => $getEntryIds, 'number' => $ajaxLimit );
														$posttags = get_terms($args);
														if ( !is_wp_error( $posttags ) ) {
															if ( is_array($posttags) ) {
																foreach ($posttags as $key => $tag) {
																	?>
                                                                    <li class="bsaProSpecificItem bsaCheckItem-T<?php echo $key; ?>">
                                                                        <label class="selectit"><input
                                                                                    value="<?php echo $tag->name; ?>"
                                                                                    class="bsaCheckItem" section="T"
                                                                                    itemId="T<?php echo $key; ?>" type="checkbox"
                                                                                    name="space_tags[]" <?php checkedSpaceOpt('has_tags', $tag->name); ?>>
																			<?php echo $tag->name; ?></label>
                                                                    </li>
																	<?php
																}
															} else { echo "No tags found."; }
														}
													}

												}

												if ( $count_tags > $ajaxLimit ) {
													?>
                                                    <a href="#bsaShowCatTag" class="bsaLinkT" onclick="bsaGetUnselected('tags', 'T', <?php echo $ajaxLimit; ?>)">show unselected tags</a> <span class="bsaLoader bsaLoaderT" style="display:none;"></span>
													<?php
												}
												?>
                                            </div>
                                        </ul>
                                    </div>

									<?php $count_categories = count(get_categories()) ?>
                                    <div class="bsaAllCategories tabs-panel" style="display: block;">
                                        <ul id="inside-list-categories" data-wp-lists="list:category" class="categorychecklist form-no-clear">
                                            <h4>Selected</h4>
                                            <div class="checkedCT"></div>
                                            <h4>Unselected</h4>
                                            <div class="uncheckedCT" offset="0" count="<?php echo $count_categories ?>">
												<?php
												if ( is_multisite() ) {

													// Current Site
													$current = get_current_site();

													// All Sites
													$blogs = json_decode(json_encode(get_sites()), true);

													foreach ( $blogs as $blog ) {

														// switch to the blog
														switch_to_blog( $blog['blog_id'] );

														// get only selected tags
														$getEntryIds = null;
														if ( isset($_GET['space_id']) ) {
															$getIds = bsa_space($_GET['space_id'], 'in_categories');
															if ( isset($getIds) && $getIds != '' ) {
																foreach ( explode(',', $getIds) as $getId ) {
																	$getEntryIds[] = $getId;
																}
															}
														}

														if ( $count_categories <= $ajaxLimit || $getEntryIds != null && $getEntryIds[0] != null ) {
															if ( $count_categories <= $ajaxLimit ) { $getEntryIds = array(); }
															$args = array( 'taxonomy' => 'category', 'include' => $getEntryIds, 'number' => $ajaxLimit );
															$postcategories = get_terms($args);
															if ( !is_wp_error( $postcategories ) ) {
																if ( is_array($postcategories) ) {
																	foreach ($postcategories as $postcategory) {
																		?>
                                                                        <li class="bsaProSpecificItem bsaCheckItem-CT<?php echo $postcategory->term_id; ?>-<?php echo $blog['blog_id']; ?>">
                                                                            <label class="selectit"><input
                                                                                        value="<?php echo $postcategory->term_id; ?>"
                                                                                        class="bsaCheckItem" section="CT"
                                                                                        itemId="CT<?php echo $postcategory->term_id; ?>-<?php echo $blog['blog_id']; ?>"
                                                                                        type="checkbox"
                                                                                        name="space_categories[]" <?php checkedSpaceOpt('in_categories', $postcategory->term_id); ?>>
																				<?php echo $postcategory->name; ?> (site
                                                                                id: <?php echo $blog['blog_id']; ?>)</label>
                                                                        </li>
																		<?php
																	}
																} else { echo "No categories found."; }
															}
														}

													}

													// return to the current site
													switch_to_blog( $current->id );

												} else {

													// get only selected tags
													$getEntryIds = null;
													if ( isset($_GET['space_id']) ) {
														$getIds = bsa_space($_GET['space_id'], 'in_categories');
														if ( isset($getIds) && $getIds != '' ) {
															foreach ( explode(',', $getIds) as $getId ) {
																$getEntryIds[] = $getId;
															}
														}
													}

													if ( $count_categories <= $ajaxLimit || $getEntryIds != null && $getEntryIds[0] != null ) {
														if ($count_categories <= $ajaxLimit) { $getEntryIds = array(); }
														$args = array( 'taxonomy' => 'category', 'include' => $getEntryIds, 'number' => $ajaxLimit );
														$postcategories = get_terms($args);
														if ( !is_wp_error( $postcategories ) ) {
															if ( is_array($postcategories) ) {
																foreach ($postcategories as $postcategory) {
																	?>
                                                                    <li class="bsaProSpecificItem bsaCheckItem-CT<?php echo $postcategory->term_id; ?>">
                                                                        <label class="selectit"><input
                                                                                    value="<?php echo $postcategory->term_id; ?>"
                                                                                    class="bsaCheckItem" section="CT"
                                                                                    itemId="CT<?php echo $postcategory->term_id; ?>"
                                                                                    type="checkbox"
                                                                                    name="space_categories[]" <?php checkedSpaceOpt('in_categories', $postcategory->term_id); ?>>
																			<?php echo $postcategory->name; ?></label>
                                                                    </li>
																	<?php
																}
															} else { echo "No categories found."; }
														}
													}

												}

												if ( $count_categories > $ajaxLimit ) {
													?>
                                                    <a href="#bsaShowCatTag" class="bsaLinkCT" onclick="bsaGetUnselected('categories', 'CT', <?php echo $ajaxLimit; ?>)">show unselected categories</a> <span class="bsaLoader bsaLoaderCT" style="display:none;"></span>
													<?php
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
                <tr id="bsaShowGeo" class="showAdvanced" style="padding-top: 25px;">
                    <th class="bsaLast" scope="row">Show / Hide in specific <br>Countries</th>
                    <td class="bsaLast">
                        <div style="max-width: 500px;">
                            <div class="inside">
                                <div id="taxonomy-category" class="categorydiv">
                                    <ul id="category-tabs" class="category-tabs">
                                        <li class="tabs bsaProTabCountry" data-tab="bsaShowCountries"><a href="#bsaShowGeo">Show in Countries</a></li>
                                        <li class="bsaProTabCountry" data-tab="bsaHideCountries"><a href="#bsaShowGeo">Hide in Countries</a></li>
                                        <li class="bsaProTabCountry" data-tab="bsaAdvanced"><a href="#bsaShowGeo">Advanced</a></li>
                                    </ul>

                                    <div id="bsaAdvanced" class="bsaAdvanced tabs-panel" style="display: none;">
                                        <ul id="inside-advanced" data-wp-lists="list:countries" class="countrieschecklist form-no-clear">
                                            <li>
                                                <strong>Note!</strong><br>
                                                We recommend really careful use of advanced options. Implemented rules can really limit your Ads.
                                                Remember that Internet Providers don't always return your actual position (it all depends on their central internet point).
                                            </li>
                                            <li class="bsaProSpecificItem">
                                                <div style="margin-bottom: 10px"><br><strong>Show</strong> in regions, cities or zip-codes</div>
                                                <input type="hidden" name="show_in_advanced" class="show_in_advanced" value="<?php echo getSpaceValue('show_in_advanced') ?>" />
                                                <input type="text" class="regular-text code spaceChips tagfield" id="show_in_advanced" name="show_in_advanced"
                                                       value="<?php echo getSpaceValue('show_in_advanced') ?>" />
                                            </li>
                                            <li class="bsaProSpecificItem">
                                                <div style="margin: 10px 0"><strong>Hide</strong> in regions, cities or zip-codes</div>
                                                <input type="hidden" name="hide_in_advanced" class="hide_in_advanced" value="<?php echo getSpaceValue('hide_in_advanced') ?>" />
                                                <input type="text" class="regular-text code spaceChips tagfield" id="hide_in_advanced"
                                                       value="<?php echo getSpaceValue('hide_in_advanced') ?>" />
                                            </li>
                                        </ul>
                                    </div>

                                    <div id="bsaHideCountries" class="bsaHideCountries tabs-panel" style="display: none;">
                                        <ul id="inside-hide-countries" data-wp-lists="list:countries" class="countrieschecklist form-no-clear">
                                            <h4>Selected</h4>
                                            <div class="checkedHC"></div>
                                            <h4>Unselected</h4>
                                            <div class="uncheckedHC">
												<?php
												$postcategories = bsa_get_country_codes();
												if ($postcategories) {
													foreach($postcategories as $postcategory) {
														?>
                                                        <li class="bsaProSpecificItem bsaCheckItem-HC<?php echo $postcategory['Code']; ?>">
                                                            <label class="selectit"><input value="<?php echo $postcategory['Code']; ?>" class="bsaCheckItem" section="HC" itemId="HC<?php echo $postcategory['Code']; ?>" type="checkbox" name="hide_in_country[]" <?php checkedSpaceOpt('hide_in_country', $postcategory['Code']); ?>>
																<?php echo $postcategory['Name']; ?></label>
                                                        </li>
														<?php
													}
												}
												?>
                                            </div>
                                        </ul>
                                    </div>

                                    <div id="bsaShowCountries" class="bsaShowCountries tabs-panel" style="display: block;">
                                        <ul id="inside-show-countries" data-wp-lists="list:countries" class="countrieschecklist form-no-clear">
                                            <h4>Selected</h4>
                                            <div class="checkedC"></div>
                                            <h4>Unselected</h4>
                                            <div class="uncheckedC">
												<?php
												$postcategories = bsa_get_country_codes();
												if ($postcategories) {
													foreach($postcategories as $postcategory) {
														?>
                                                        <li class="bsaProSpecificItem bsaCheckItem-C<?php echo $postcategory['Code']; ?>">
                                                            <label class="selectit"><input value="<?php echo $postcategory['Code']; ?>" class="bsaCheckItem" section="C" itemId="C<?php echo $postcategory['Code']; ?>" type="checkbox" name="show_in_country[]" <?php checkedSpaceOpt('show_in_country', $postcategory['Code']); ?>>
																<?php echo $postcategory['Name']; ?></label>
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
				<?php if ( get_option('bsa_pro_plugin_calendar') == 'yes' ): ?>
                    <tr class="showAdvanced">
                        <th scope="row"><label for="bsa_pro_unavailable_dates">Unavailable Dates in Calendar</label></th>
                        <td>
                            <input type="text" class="regular-text code" maxlength="1000" value="<?php echo getSpaceValue('unavailable_dates') ?>"
                                   id="bsa_pro_unavailable_dates" name="unavailable_dates" placeholder="2015-10-17,2015-10-21">
                            <p class="description"><strong>Example</strong> 2015-10-17,2015-10-21,2015-10-24</p>
                        </td>
                    </tr>
				<?php endif; ?>
                <tr class="showAdvanced">
                    <th>Customization</th>
                    <td>
                        <div id="postbox-container-1" class="postbox-container">
                            <div id="side-sortables" class="meta-box-sortables ui-sortable" style="">
                                <div id="bsaSpaceCustomization" class="postbox closed">
                                    <p class="hndle ui-sortable-handle bsaSpaceCustomization" style="margin: 0; padding: 10px; cursor: pointer;">
                                        Options <span class="dashicons dashicons-arrow-down" style="float: right;"></span>
                                    </p>
                                    <div class="inside">
                                        <table>
                                            <tbody>
                                            <tr>
                                                <th scope="row"><label for="bsa_pro_font">Google Font</label></th>
                                                <td>
                                                    <input type="text" class="regular-text code" value="<?php echo str_replace("\\'", "", getSpaceValue('font')) ?>" id="bsa_pro_font" name="font">
                                                    <p class="description">
                                                        Example: <strong>font-family: 'Open Sans', sans-serif;</strong><br>
                                                        Choose from 650+ fonts available here <a href="https://www.google.com/fonts" target="_blank">https://www.google.com/fonts</a>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="bsa_pro_font_url">Google Font URL</label></th>
                                                <td>
                                                    <input type="text" class="regular-text code" value="<?php echo getSpaceValue('font_url') ?>" id="bsa_pro_font_url" name="font_url">
                                                    <p class="description">Example: <strong>@import url(http://fonts.googleapis.com/css?family=Open+Sans);</strong></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="bsa_pro_header_bg">Header Background</label></th>
                                                <td>
                                                    <input id="bsa_pro_header_bg"
                                                           name="header_bg"
                                                           value="<?php echo getSpaceValue('header_bg') ?>"
                                                           data-default-color="#FFFFFF" type="text" class="bsaColorPicker">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="bsa_pro_header_color">Header Color</label></th>
                                                <td>
                                                    <input id="bsa_pro_header_color"
                                                           name="header_color"
                                                           value="<?php echo getSpaceValue('header_color') ?>"
                                                           data-default-color="#000000" type="text" class="bsaColorPicker">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="bsa_pro_link_color">Header Link Color</label></th>
                                                <td>
                                                    <input id="bsa_pro_link_color"
                                                           name="link_color"
                                                           value="<?php echo getSpaceValue('link_color') ?>"
                                                           data-default-color="#000000" type="text" class="bsaColorPicker">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="bsa_pro_ads_bg">Ads Whole Section Background</label></th>
                                                <td>
                                                    <input id="bsa_pro_ads_bg"
                                                           name="ads_bg"
                                                           value="<?php echo getSpaceValue('ads_bg') ?>"
                                                           data-default-color="#f5f5f5" type="text" class="bsaColorPicker">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="bsa_pro_ad_bg">Ad Background</label></th>
                                                <td>
                                                    <input id="bsa_pro_ad_bg"
                                                           name="ad_bg"
                                                           value="<?php echo getSpaceValue('ad_bg') ?>"
                                                           data-default-color="#f5f5f5" type="text" class="bsaColorPicker">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="bsa_pro_ad_title_color">Ad Title Color</label></th>
                                                <td>
                                                    <input id="bsa_pro_ad_title_color"
                                                           name="ad_title_color"
                                                           value="<?php echo getSpaceValue('ad_title_color') ?>"
                                                           data-default-color="#000000" type="text" class="bsaColorPicker">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="bsa_pro_ad_desc_color">Ad Description Color</label></th>
                                                <td>
                                                    <input id="bsa_pro_ad_desc_color"
                                                           name="ad_desc_color"
                                                           value="<?php echo getSpaceValue('ad_desc_color') ?>"
                                                           data-default-color="#000000" type="text" class="bsaColorPicker">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="bsa_pro_ad_url_color">Ad URL Color</label></th>
                                                <td>
                                                    <input id="bsa_pro_ad_url_color"
                                                           name="ad_url_color"
                                                           value="<?php echo getSpaceValue('ad_url_color') ?>"
                                                           data-default-color="#000000" type="text" class="bsaColorPicker">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="bsa_pro_ad_extra_color_1">Ad Extra Color 1</label></th>
                                                <td>
                                                    <input id="bsa_pro_ad_extra_color_1"
                                                           name="ad_extra_color_1"
                                                           value="<?php echo getSpaceValue('ad_extra_color_1') ?>"
                                                           data-default-color="#FFFFFF" type="text" class="bsaColorPicker">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="bsa_pro_ad_extra_color_2">Ad Extra Color 2</label></th>
                                                <td>
                                                    <input id="bsa_pro_ad_extra_color_2"
                                                           name="ad_extra_color_2"
                                                           value="<?php echo getSpaceValue('ad_extra_color_2') ?>"
                                                           data-default-color="#444444" type="text" class="bsaColorPicker">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
			<?php endif; ?>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" value="Save Space" class="button button-primary" id="bsa_pro_submit" name="bsa_pro_submit">
        </p>
    </form>
    <style>
        <?php
		if ( isset($templates) && is_array($templates) ) {
			foreach ( $templates as $file ) {
				$styles = explode('/', $file);
				$style = array_reverse($styles);
				$name = explode('.', $style[0]);
				$size = explode('--', str_replace('block-', '', $name[0]));
				$width = (isset($size[0]) ? $size[0] : 0);
				$height = (isset($size[1]) ? $size[1] : 0);
				if ( $width > 0 && $height > 0 ) { ?>
        .bsaTemplatePreview .bsa-<?php echo $name[0]; ?> {
            width: <?php echo $width; ?>px;
            height: <?php echo $height; ?>px;
        }
        <?php }
	}
}
?>
    </style>

<?php else: ?>

    <div class="updated settings-error" id="setting-error-settings_updated">
        <p><strong>Error!</strong> Space does not exist!</p>
    </div>

<?php endif; ?>

<script>
    (function($){
        "use strict";
        // - start - open page
        let bsaPageContent = $(".wrap");
        let waitingContent = $(".waitingContent");
        $(document).ready(function(){
            bsaPageContent.fadeIn();
            waitingContent.fadeOut();
        });
        // - end - open page

        bsaGetTemplate();
        $(document).ready(function(){
            $('.bsaColorPicker').wpColorPicker();
            bsaGetTemplate();

            let bsaAnimation = $("#bsa_pro_animation");
            let bsaTemplatePreviewInner = $(".bsaTemplatePreviewInner");
            bsaAnimation.on('change',function() {
                bsaTemplatePreviewInner.addClass(('animated ' + bsaAnimation.val()));
                setTimeout(function(){
                    bsaTemplatePreviewInner.removeClass().addClass('bsaTemplatePreviewInner');
                }, 1500);
            });
            bsaAnimation.trigger('change');
        });

        // gutter preview
        $('.gutterButton').on('click', function() {
            let gutterPreview = $(this).attr('data-preview');
            $('.gutterPreview').hide();
            $('.' + gutterPreview).show();
        });

        // grid preview
        $('.gridButton').on('click', function() {
            let gridPreview = $(this).attr('data-preview');
            $('.gridPreview').hide();
            $('.' + gridPreview).show();
        });

        // post & pages tabs
        $('.bsaProTabPP').on('click', function() {
            let clicked = $(this).attr('data-tab');
            $('.bsaProTabPP').removeClass('tabs');
            $(this).addClass('tabs');
            if ( clicked === 'bsaAllPosts' ) {
                $('.bsaAllPosts').show();
                $('.bsaAllPages').hide();
                $('.bsaAllHideCustoms').hide();
            } else if ( clicked === 'bsaAllPages' ) {
                $('.bsaAllPages').show();
                $('.bsaAllPosts').hide();
                $('.bsaAllHideCustoms').hide();
            } else {
                $('.bsaAllHideCustoms').show();
                $('.bsaAllPosts').hide();
                $('.bsaAllPages').hide();
            }
        });

        // categories & tags tabs
        $('.bsaProTab').on('click', function() {
            let clicked = $(this).attr('data-tab');
            $('.bsaProTab').removeClass('tabs');
            $(this).addClass('tabs');
            if ( clicked === 'bsaAllCategories' ) {
                $('.bsaAllCategories').show();
                $('.bsaAllTags').hide();
                $('.bsaAllShowCustoms').hide();
            } else if ( clicked === 'bsaAllTags' ) {
                $('.bsaAllTags').show();
                $('.bsaAllCategories').hide();
                $('.bsaAllShowCustoms').hide();
            } else {
                $('.bsaAllShowCustoms').show();
                $('.bsaAllCategories').hide();
                $('.bsaAllTags').hide();
            }
        });

        // geo-target tabs
        $('.bsaProTabCountry').on('click', function() {
            let clicked = $(this).attr('data-tab');
            $('.bsaProTabCountry').removeClass('tabs');
            $(this).addClass('tabs');
            if ( clicked === 'bsaShowCountries' ) {
                $('.bsaShowCountries').show();
                $('.bsaHideCountries').hide();
                $('.bsaAdvanced').hide();
            } else if ( clicked === 'bsaHideCountries' ) {
                $('.bsaHideCountries').show();
                $('.bsaShowCountries').hide();
                $('.bsaAdvanced').hide();
            } else {
                $('.bsaAdvanced').show();
                $('.bsaShowCountries').hide();
                $('.bsaHideCountries').hide();
            }
        });

        // location details
        $('.showGeoDetails').on('click', function() {
            $('#bsa_geo_details_open').toggle();
        });

        // change display type
        function changeDisplayOptions(type)
        {
            let colsRows = $('.colsRowsOpt');
            let gridButton = $('.gridButton');
            // reset all filters
            colsRows.attr('disabled', false);
            gridButton.attr('disabled', false);
            if ( 'carousel_slide,carousel_fade,top_scroll_bar,bottom_scroll_bar'.indexOf(type) >= 0 ) {
                let disableGridOptions = '1column,2grid,3grid,1columnRandom,2gridRandom,3gridRandom';
                gridButton.each( function(){
                    let gridSelected = $( this ).attr('data-preview');
                    if ( disableGridOptions.indexOf(gridSelected) >= 0 ) {
                        $(this).attr('disabled', true);
                    }
                });
            } else if ( 'corner,layer,background,link'.indexOf(type) >= 0 ) {
                colsRows.each( function(){
                    if ( $( this ).val() >= 2 ) {
                        $(this).attr("disabled", true);
                    }
                });
                let disableGridOptions = 'allGrid,1column,2grid,3grid,1columnRandom,2gridRandom,3gridRandom,allGridRandom';
                gridButton.each( function(){
                    let gridSelected = $( this ).attr('data-preview');
                    if ( disableGridOptions.indexOf(gridSelected) >= 0 ) {
                        $(this).attr('disabled', true);
                    }
                    // if ( gridSelected === '2grid' || gridSelected === '2gridRandom' ) {
                    //     colsRows.each( function(){
                    //         if ( $( this ).val() <= 2 ) {
                    //             $(this).attr("disabled", true);
                    //         }
                    //     });
                    // }
                });
            }
        }
        $(document).ready(function(){
            let displayType = $('#bsa_pro_display_type');
            changeDisplayOptions(displayType.val());
        });
        let displayType = $('#bsa_pro_display_type');
        displayType.on('change', function() {
            changeDisplayOptions($(this).val());
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

        $('.bsaSpaceCustomization').on('click', function() {
            let bsaSpaceCustomization = $('#bsaSpaceCustomization');
            let arrow = $('#bsaSpaceCustomization span.dashicons');
            if ( bsaSpaceCustomization.hasClass('closed') ) {
                bsaSpaceCustomization.removeClass('closed');
                arrow.removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
            } else {
                bsaSpaceCustomization.addClass('closed');
                arrow.removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
            }
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

        $(document).ready(function() {
            $('.showHideAdvanced').on('click', function() {
                window.location = window.location + '#showAdvanced';
                $('.showAdvanced').toggle('fade');
            });
            if ( window.location.hash === '#showAdvanced' || window.location.hash === '#bsaShowPagePost' || window.location.hash === '#bsaShowCatTag' || window.location.hash === '#bsaShowGeo' ) {
                $('.showAdvanced').toggle('fade');
            }
        });
    })(jQuery);

    function bsaGetTemplate()
    {
        (function($) {
            let bsaTemplatePreviewInner = $('.bsaTemplatePreviewInner');
            let bsaLoader = $('.bsaLoader');

            bsaTemplatePreviewInner.slideUp(400);
            bsaLoader.fadeIn(400);
            setTimeout(function(){
                $.post(ajaxurl, {action:'bsa_preview_callback',bsa_template:$("#bsa_pro_template").val()}, function(result) {
                    bsaTemplatePreviewInner.html(result).slideDown(400);
                    bsaLoader.fadeOut(400);
                });
            }, 1100);
        })(jQuery);
    }

    function bsaGetUnselected(type, short, ajaxLimit)
    {
        (function($) {
            let bsaUnchecked = $('.unchecked' + short);
            let countUnchecked = $('.unchecked' + short + ' > .bsaProSpecificItem').length;
            let countChecked = $('.checked' + short + ' > .bsaProSpecificItem').length;
            let bsaLink = $('.bsaLink' + short);
            let bsaLoader = $('.bsaLoader' + short);
            let bsaCount = bsaUnchecked.attr( "count" );
            bsaLoader.fadeIn(400);
            setTimeout(function(){
                $.post(ajaxurl, {action:'bsa_unselected',type:type,space_id:<?php echo isset($_GET['space_id']) ? $_GET['space_id'] : 0 ?>,bsa_offset:countUnchecked,ajax_limit:ajaxLimit}, function(result) {
                    bsaUnchecked.attr( "offset", countUnchecked).prepend(result);
                    if ( countChecked + countUnchecked >= bsaCount ) {
                        bsaLink.fadeOut(400);
                    }
                    bsaLoader.fadeOut(400);
                });
            }, 1100);
        })(jQuery);
    }
</script>
