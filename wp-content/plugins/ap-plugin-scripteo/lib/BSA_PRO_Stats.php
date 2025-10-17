<?php
require_once dirname(__FILE__) . '/BSA_PRO_Ordering_form.php'; // require ordering form if stats
$ad_id = (isset($_GET['bsa_pro_id']) && $_GET['bsa_pro_id'] > 0 ? $_GET['bsa_pro_id'] : 0);
$model = new BSA_PRO_Model();
$model->bsaGenerateStats($ad_id);
$statsFrom = (($model->bsaIntervalStats($ad_id, 'from') != null) ? $model->bsaIntervalStats($ad_id, 'from') : 0);
$countClicks = ((bsa_counter($ad_id, 'click') > 0) ? bsa_counter($ad_id, 'click') : 0);
$countViews = ((bsa_counter($ad_id, 'view') > 0) ? bsa_counter($ad_id, 'view') : 0);
echo '
<div class="bsaStatsWrapperBg"></div>
<div class="bsaStatsWrapper" data-ad-id="'.$ad_id.'" data-days="7" data-from="'.$statsFrom[0].'" data-time="'.time().'">
	<div class="bsaStatsWrapperInner">
		<h2>
			<span>'.get_option("bsa_pro_plugin_trans_stats_header").'</span> 
			<small>(<span class="ap-weekly-from">'.date('d/m/Y', time() - (60 * 60 * 24 * 6)).'</span> - <span class="ap-weekly-to">'.date('d/m/Y', time()).'</span>)</small>
			<span class="bsaLoader bsaLoaderStats" style="display: none"></span>
		</h2>
		<div class="bsaStatsButtons">
			<a class="bsaPrevWeek" href="#" onclick="bsaPrevStats()">'.get_option("bsa_pro_plugin_trans_stats_prev_week").'</a>
			<a class="bsaNextWeek" href="#" onclick="bsaNextStats()">'.get_option("bsa_pro_plugin_trans_stats_next_week").'</a>
		</div>
		<div class="bsaStatsChart">
			<div class="bsaSumStats" style="border-left: 4px solid '.get_option('bsa_pro_plugin_'.'stats_clicks_line').';">
				'.get_option("bsa_pro_plugin_trans_stats_clicks").' <strong class="ap-weekly-clicks">0</strong><br>
				'.bsa_get_trans('statistics', 'total').' <strong>'.$countClicks.'</strong>
			</div>
			<div class="bsaSumStats" style="border-left: 4px solid '.get_option('bsa_pro_plugin_'.'stats_views_line').'; margin: 0 4%">
				'.get_option("bsa_pro_plugin_trans_stats_views").' <strong class="ap-weekly-views">0</strong><br>
				'.bsa_get_trans('statistics', 'total').' <strong>'.$countViews.'</strong>
			</div>
			<div class="bsaSumStats" style="border-left: 4px solid grey;">
				'.get_option("bsa_pro_plugin_trans_stats_ctr").' <strong class="ap-weekly-ctr">0.00%</strong><br>
				'.bsa_get_trans('statistics', 'total').' <strong>'.(($countViews > 0) ? number_format(($countClicks / $countViews) * 100, 2)."%" : "-" ).'</strong>
			</div>
		</div>';
    echo '<div class="wp-clearfix"></div>';
$report = plugin_dir_path( __FILE__ ) . 'PDF/reports/ad-'.$ad_id.'.txt';
if ( file_exists( $report ) ) {
echo 	'<div style="text-align: right">
			<span style="clear: both;display: inline-block;">'.bsa_get_trans('statistics', 'full_stats').'</span>
			<a style="margin-left: 10px;" href="' . plugin_dir_url(__FILE__) . 'pdf.php?pdf=' . substr(md5($ad_id . '1'), 1, 11) . '&ad_id=' . $ad_id . '&stats=90" target="_blank">'.bsa_get_trans('statistics', 'last_90').'</a>
			<a style="margin-left: 10px;" href="' . plugin_dir_url(__FILE__) . 'pdf.php?pdf=' . substr(md5($ad_id . '1'), 1, 11) . '&ad_id=' . $ad_id . '&stats=30" target="_blank">'.bsa_get_trans('statistics', 'last_30').'</a>
			<a style="margin-left: 10px;" href="' . plugin_dir_url(__FILE__) . 'pdf.php?pdf=' . substr(md5($ad_id . '1'), 1, 11) . '&ad_id=' . $ad_id . '&stats=7" target="_blank">'.bsa_get_trans('statistics', 'last_7').'</a>
		</div>';
}
echo '	<div class="bsaChart ct-chart"></div>';
$title = get_option("bsa_pro_plugin_trans_stats_clicks");
$title = apply_filters( "bsa-pro-changeTitle", $title, $ad_id);
echo '<h3 class="bsaHeaderClicks">'.$title.'</h3>';
echo '<div class="bsaStatsClicks"></div>';
echo '<span class="bsaStatsClose"></span>
	</div>
</div>'; ?>
<script>
	(function($){
		"use strict";
		let bsaStatsWrapperBg = $(".bsaStatsWrapperBg");
		let bsaStatsWrapper = $(".bsaStatsWrapper");
		let bsaBody = $("body");
		// bsaBody.css({"overflow" : "hidden", "height" : ( bsaBody.hasClass("logged-in") ) ? $( window ).height() - 32 : $( window ).height()});
		bsaStatsWrapper.appendTo(document.body).addClass("animated zoomInDown");
		bsaStatsWrapperBg.appendTo(document.body).addClass("animated zoomInDown");
		bsaInitStatsChart();
		bsaInitClicksList();
		$(document).ready(function() {
			let bsaStatsClose = $(".bsaStatsClose");
			let bsaChartDirect = $(".bsaStatsChart");
			let bsaStatsClicks = $(".bsaStatsClicks");
			bsaChartDirect.css({"max-height" : "300px"});
			bsaStatsClicks.css({"max-height" : "400px"});
			bsaStatsClose.on('click', function () {
				bsaBody.css({"overflow" : "", "height" : ""});
				bsaChartDirect.addClass("animated zoomOut");
				bsaStatsClose.addClass("animated zoomOut");
				bsaStatsClicks.addClass("animated zoomOut");
				setTimeout(function(){
					bsaStatsWrapper.removeClass("zoomInDown").addClass("animated zoomOutUp");
					bsaStatsWrapperBg.removeClass("zoomInDown").addClass("animated zoomOutUp");
				}, 400);
			});
		});
	})(jQuery);
	function bsaInitStatsChart()
	{
		(function($) {
			let bsaStatsWrapper = $(".bsaStatsWrapper");
			let bsaChartDirect = $(".bsaChart");
			let bsaLoader = $(".bsaLoaderStats");
			let bsaPrevWeek = $(".bsaPrevWeek");
			bsaChartDirect.addClass("animated zoomOut");
			bsaLoader.fadeIn(400);
			if ( parseInt(bsaStatsWrapper.attr("data-time")) - parseInt(bsaStatsWrapper.attr("data-days")) * 24 * 60 * 60 < bsaStatsWrapper.attr("data-from") ) {
				bsaPrevWeek.fadeOut();
			} else {
				bsaPrevWeek.fadeIn();
			}
			$.post("<?php echo admin_url("admin-ajax.php") ?>", {action:"bsa_stats_chart_callback",ad_id:bsaStatsWrapper.attr("data-ad-id"),days:bsaStatsWrapper.attr("data-days")}, function(result) {
				bsaChartDirect.removeClass("zoomOut").addClass("animated zoomIn");
				bsaLoader.fadeOut(400);
				let chart = $.parseJSON(result);
				$('.ap-weekly-clicks').html((chart.weekly_clicks > 0 ? chart.weekly_clicks : 0));
				$('.ap-weekly-views').html((chart.weekly_views > 0 ? chart.weekly_views : 0));
				$('.ap-weekly-ctr').html((chart.weekly_views > 0 ? ((chart.weekly_clicks / chart.weekly_views) * 100).toFixed(2) + '%' : '0.00%'));
				$('.ap-weekly-from').html(chart.dateFrom);
				$('.ap-weekly-to').html(chart.dateTo);
				let data = {
					labels: chart.labels,
					series: [
						{
							name: "<?php echo get_option("bsa_pro_plugin_trans_stats_clicks") ?>",
							data: chart.clicks
						},
						{
							name: "<?php echo get_option("bsa_pro_plugin_trans_stats_views") ?>",
							data: chart.views
						}
					]
				};
				let options = {
					height: "200px"
				};
				new Chartist.Line(".ct-chart", data, options);
			});
		})(jQuery);
	}
	function bsaInitClicksList()
	{
		(function($) {
			let bsaStatsWrapper = $(".bsaStatsWrapper");
			let bsaListDirect = $(".bsaStatsClicks");
			let bsaHeaderClicks = $(".bsaHeaderClicks");
			let bsaLoader = $(".bsaLoaderStats");
			bsaListDirect.addClass("animated zoomOut");
			bsaLoader.fadeIn(400);
			$.post("<?php echo admin_url("admin-ajax.php") ?>", {action:"bsa_stats_clicks_callback",ad_id:bsaStatsWrapper.attr("data-ad-id"),days:bsaStatsWrapper.attr("data-days")}, function(result) {
				if ( result === '0' ) {
					bsaHeaderClicks.fadeOut();
				} else {
					bsaHeaderClicks.fadeIn();
					bsaListDirect.html(result).removeClass("zoomOut").addClass("animated zoomIn");
				}
				bsaLoader.fadeOut(400);
			});
		})(jQuery);
	}
	function bsaPrevStats()
	{
		(function($) {
			let bsaStatsWrapper = $(".bsaStatsWrapper");
			let bsaNextWeek = $(".bsaNextWeek");
			let bsaPrevWeek = $(".bsaPrevWeek");
			if ( parseInt(bsaStatsWrapper.attr("data-time")) - parseInt(bsaStatsWrapper.attr("data-days")) * 24 * 60 * 60 < bsaStatsWrapper.attr("data-from") ) {
				bsaPrevWeek.fadeOut();
			} else {
				bsaPrevWeek.fadeIn();
			}
			bsaStatsWrapper.attr( "data-days", (parseInt(bsaStatsWrapper.attr("data-days")) + 7) );
			if ( parseInt(bsaStatsWrapper.attr("data-days")) >= 7 ) {
				bsaNextWeek.fadeIn();
			} else {
				bsaNextWeek.fadeOut();
			}
			bsaInitStatsChart();
			bsaInitClicksList();
		})(jQuery);
	}
	function bsaNextStats()
	{
		(function($) {
			let bsaStatsWrapper = $(".bsaStatsWrapper");
			let bsaNextWeek = $(".bsaNextWeek");
			if ( parseInt(bsaStatsWrapper.attr("data-days")) >= 21 ) {
				bsaNextWeek.fadeIn();
			} else {
				bsaNextWeek.fadeOut();
			}
			bsaStatsWrapper.attr( "data-days", bsaStatsWrapper.attr("data-days") - 7 );
			bsaInitStatsChart();
			bsaInitClicksList();
		})(jQuery);
	}
</script>