<?php
$model			= new BSA_PRO_Model();
$get_ads		= $model->getAds();
$get_ad_A 		= (isset($_POST['ad_a_id']) ? $_POST['ad_a_id'] : null);
$get_ad_B 		= (isset($_POST['ad_b_id']) ? $_POST['ad_b_id'] : null);
$ifIssetForm 	= ((isset($_POST['bsaProAction']) && $_POST['bsaProAction'] == 'ab-tests' && isset($_POST['ad_a_id']) && isset($_POST['ad_b_id'])) ? true : false);
$wersdadp 		= '';
?>
<h2>
	<span class="dashicons dashicons-chart-line"></span> A/B Tests
	<?php if ( $ifIssetForm && get_option('bsa_pro_u' . $wersdadp . 'pd' . 'at' . $wersdadp . 'e' . '_s' . $wersdadp . 'ta' . 'tus') != 'i' . $wersdadp . 'n' . 'v' . $wersdadp . 'a' . 'l' . $wersdadp . 'i' . $wersdadp . 'd'): ?>
		<p><span class="dashicons dashicon-14 dashicons-arrow-left-alt"></span> <a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-ab-tests">back to the <strong>form</strong></a></p>
	<?php endif; ?>
</h2>

<?php $kojass = ''; ?>
<?php if ( get_option('b'.'sa_pr'.'o_u'.'pd'.'at'.$kojass.'e'.'_s'.$kojass.'ta'.'tus') == 'i'.$kojass.'n'.'v'.$kojass.'a'.'l'.$kojass.'i'.$kojass.'d' ): ?>

	<tr class="showAdvanced">
		<td colspan="2">
			<div class="bsaLockedContent bsaLockedWhite">
				<strong>Trial Version</strong><br>
				Use purchase code to unlock A/B Tests and other features.<br><br>
				Paste purchase code in the <a href="<?php echo admin_url() ?>admin.php?page=bsa-pro-sub-menu-opts">settings</a> (Ads Pro > Settings > Purchase Code).<br>
				Where is your purchase code? <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">Learn more</a>.<br><br>
				- or -<br><br>
				<a href="https://1.envato.market/buy-regular-ads-pro-6" target="_blank">Buy now</a> the latest version of Ads Pro.
			</div>
		</td>
	</tr>

<?php else: ?>

	<?php if ( $ifIssetForm ):
		$viewsA 	= ( bsa_counter($get_ad_A, 'view') != NULL ) ? bsa_counter($get_ad_A, 'view') : 0;
		$viewsB 	= ( bsa_counter($get_ad_B, 'view') != NULL ) ? bsa_counter($get_ad_B, 'view') : 0;

		$clicksA 	= ( bsa_counter($get_ad_A, 'click') != NULL ) ? bsa_counter($get_ad_A, 'click') : 0;
		$clicksB 	= ( bsa_counter($get_ad_B, 'click') != NULL ) ? bsa_counter($get_ad_B, 'click') : 0;

		$trafficA 	= ( $viewsA > 0 || $viewsB > 0 ? number_format($viewsA / ($viewsA + $viewsB) * 100, 2, '.', '') : 0);
		$trafficB 	= ( $viewsA > 0 || $viewsB > 0 ? number_format($viewsB / ($viewsA + $viewsB) * 100, 2, '.', '') : 0);

		$ctrA		= ($viewsA > 0 ? number_format(($clicksA / $viewsA) * 100, 2, '.', '') : number_format(0, 2, '.', ''));
		$ctrB		= ($viewsB > 0 ? number_format(($clicksB / $viewsB) * 100, 2, '.', '') : number_format(0, 2, '.', ''));
		?>

		<div class="bsaCompareContainer">

			<div class="bsaCompare bsaCompareA <?php echo ( $ctrA >= $ctrB ? 'bsaCompareWinner' : null); ?>">

				<div class="bsaCompareSignature">A</div>

				<div class="bsaCompareAdId">Ad ID: <strong><?php echo $get_ad_A; ?></strong></div>
				<div class="bsaCompareSpaceId">Space ID: <strong><?php echo bsa_ad($get_ad_A, 'space_id'); ?></strong></div>
				<div class="bsaCompareTemplate">Template: <strong><?php echo bsa_space(bsa_ad($get_ad_A, 'space_id'), 'template'); ?></strong></div>
				<div class="bsaCompareWeight">Traffic Weight: <strong><?php echo $trafficA.'%'; ?></strong></div>

				<div class="bsaCompareCTR"><div class="bsaCompareCTRInner"><strong><?php echo $ctrA.'%'; ?></strong><br>CTR</div></div>

				<div class="bsaCompareViews"><span><?php echo $viewsA; ?></span> Views</div>
				<div class="bsaCompareClicks"><span><?php echo $clicksA; ?></span> Clicks</div>

			</div>

			<div class="bsaCompare bsaCompareB <?php echo ( $ctrA <= $ctrB ? 'bsaCompareWinner' : null); ?>">

				<div class="bsaCompareSignature">B</div>

				<div class="bsaCompareAdId">Ad ID: <strong><?php echo $get_ad_B; ?></strong></div>
				<div class="bsaCompareSpaceId">Space ID: <strong><?php echo bsa_ad($get_ad_B, 'space_id'); ?></strong></div>
				<div class="bsaCompareTemplate">Template: <strong><?php echo bsa_space(bsa_ad($get_ad_B, 'space_id'), 'template'); ?></strong></div>
				<div class="bsaCompareWeight">Traffic Weight: <strong><?php echo $trafficB.'%'; ?></strong></div>

				<div class="bsaCompareCTR"><div class="bsaCompareCTRInner"><strong><?php echo $ctrB.'%'; ?></strong><br>CTR</div></div>

				<div class="bsaCompareViews"><span><?php echo $viewsB; ?></span> Views</div>
				<div class="bsaCompareClicks"><span><?php echo $clicksB; ?></span> Clicks</div>

			</div>

		</div>

	<?php else: ?>

		<form action="" method="post" class="bsaNewStandardAd">
			<input type="hidden" value="ab-tests" name="bsaProAction">
			<table class="bsaAdminTable form-table">
				<tbody class="bsaTbody">
				<tr>
					<th colspan="2">
						<h3><span class="dashicons dashicons-image-flip-horizontal"></span> Compare 2 different Ads</h3>
					</th>
				</tr>
				<tr>
					<th scope="row"><label for="ad_a_id">Select Ad A</label></th>
					<td>
						<select id="ad_a_id" name="ad_a_id">
							<?php if (is_array($get_ads)) {
								foreach ($get_ads as $entry):
									echo '<option value="'.esc_html( $entry['id'] ).'">Ad ID: ' . esc_html( $entry['id'] ) . (($entry['title'] != '') ? ' - '.$entry['title'] : null) . esc_html( $entry['id'] ) . (($entry['space_id'] != '') ? ' (space id: '.$entry['space_id'].')' : null) . '</option>';
								endforeach;
							} ?>
						</select>
					</td>
				</tr>
				<tr>
					<th class="bsaLast" scope="row"><label for="ad_b_id">Select Ad B</label></th>
					<td class="bsaLast">
						<select id="ad_b_id" name="ad_b_id">
							<?php if (is_array($get_ads)) {
								foreach ($get_ads as $entry):
									echo '<option value="'.esc_html( $entry['id'] ).'">Ad ID: ' . esc_html( $entry['id'] ) . (($entry['title'] != '') ? ' - '.$entry['title'] : null) . (($entry['space_id'] != '') ? ' (space id: '.$entry['space_id'].')' : null) . '</option>';
								endforeach;
							} ?>
						</select>
					</td>
				</tr>

				</tbody>
			</table>
			<p class="submit">
				<input type="submit" value="Compare now!" class="button button-primary" id="bsa_pro_submit" name="submit">
			</p>
		</form>

	<?php endif; ?>

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