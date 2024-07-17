<?php
	require 'config.php';
	dol_include_once('/core/lib/functions.lib.php');
	dol_include_once('/searcheverywhere/lib/searcheverywhere.lib.php');

	$langs->load('searcheverywhere@searcheverywhere');

	$keyword = GETPOST('keyword');
	if (empty($keyword)) $keyword=GETPOST('sall');
	if (empty($keyword)) $keyword=GETPOST('search_all');

	llxHeader('', $langs->trans('Searcheverywhere'), '', '', 0, 0, array('/searcheverywhere/js/jquery.qtip.min.js')  );
	$head = searcheverywhere_prepare_head($keyword);
	dol_fiche_head($head, 'search', $langs->trans('Searcheverywhere'), 0, 'searcheverywhere@searcheverywhere');
	?>

	<link rel="stylesheet" type="text/css" href="js/jquery.qtip.min.css">
	<script type="text/javascript" src="js/jquery.qtip.min.js"></script>
	<style type="text/css">
		#results {
			position:relative;
			margin-top:15px;
		}

		#results span.loading {
			padding : 20px;
			background-color: #f64f1c;
			border-radius: 10px;
			top:50px;
			left:50px;
			position:relative;
		}

		#results div.result {
			width:300px;
			float:left;
			border-color: #bbb #aaa #aaa;
		    border-style: solid;
		    border-width: 1px;
		    box-shadow: 3px 3px 4px #ddd;
		    margin: 0 10px 14px 0;
		}
		.highlight {
			font-weight: bold;
		}
	</style>

	<input type="text" name="keyword" id="keyword" value="" />
	<input type="button" name="btsearch" id="btsearch" value="<?php print $langs->trans('Search'); ?>" />
	<div id="results"></div>
	<div style="clear:both"></div>
	<script type="text/javascript">
		var url = "<?php echo dol_buildpath('/searcheverywhere/search.php?keyword=', 1) ?>";
		var TSearch = [
			'product',
			'company',
			'contact',
			<?php if(isModEnabled('propal')) echo "'propal',"; ?>
			<?php if(isModEnabled('commande')) echo "'order',"; ?>
			<?php if(isModEnabled('facture')) echo "'invoice',"; ?>
			<?php if(isModEnabled('projet')) echo "'projet','task',"; ?>
			<?php if(isModEnabled('agenda')) echo "'event',"; ?>
			<?php if(isModEnabled('expedition')) echo "'expedition',"; ?>
			<?php if(isModEnabled('fournisseur')) echo "'supplier_order',"; ?>
			<?php if(isModEnabled('of')) echo "'of',"; ?>
			<?php if(isModEnabled('nomenclature')) echo "'nomenclature',"; ?>
			<?php if(isModEnabled('workstationatm')) echo "'workstation',"; ?>
			<?php if(isModEnabled('configurateur')) echo "'configurateur',"; ?>
			<?php if(isModEnabled('fournisseur')) echo "'invoice_supplier',"; ?>
		];

		$(document).ready(function() {
			$("#btsearch").click(function() {
				var keyword = $("#keyword").val();

				$('#results').html("<span class=\"loading\"><?php echo $langs->trans('Loading'); ?>...</span>");
				$('a#search').attr('href', url+keyword);

				for(x in TSearch) {
					$.ajax({
						url : "./script/interface.php"
						,data :{
							get:'search'
							,type:TSearch[x]
							,keyword : keyword
						}

					}).done(function(data) {
						$('#results span.loading').remove();

						$div = $('<div class="result" />');
						$div.append(data);
						$('#results').append($div);
						console.log($('#results div.result'))


						jQuery("#results div.result .classfortooltip").each(function() {
							$(this).qtip({
								content: {
									text: false // Use each elements title attribute
								},
								position: {
									my: 'bottom center',
									at: 'top center',
									target: $(this) // Use the triggering element as the target
								},
								style: {
									classes: 'qtip-bootstrap' // Use the Bootstrap theme
								},
								events: {
									show: function(event, api) {
										// Use the event object to position the tooltip if necessary
									}
								}
							});
						});
					})
				}
			});
			<?php
				if($keyword!='') {
					?>
					$("#keyword").val("<?php echo $keyword; ?>");
					$("#btsearch").click();
					<?php
				}
			?>
		});
	</script>

	<?php
	llxFooter();
