<?php

	require 'config.php';
	
	llxHeader('', 'SearchEveryWhere', '', '', 0, 0, array('/searcheverywhere/js/jquery.tile.min.js')  );

	?>
	<style type="text/css">
		#results {
			position:relative;
			
		}
		
		#results span.loading {
			padding : 20px;
			background-color: #f64f1c;
			border-radius: 10px;
			top:50px;
			left:50px;
		}
		
		#results div.result {
			
			width:300px; 
			float:left;
			
			border-color: #bbb #aaa #aaa;
		    border-style: solid;
		    border-width: 1px;
		    box-shadow: 3px 3px 4px #ddd;
		    margin: 0 5px 14px;
		   
		   
			
		}
		.highlight {
			font-weight: bold;
		}
	</style>
	<div class="tabBar">
		<input type="text" name="keyword" id="keyword" value="" />
		<input type="button" name="btseach" id="btseach" value="Rechercher" />
		
		<div id="results">
			
		</div>
		<div style="clear:both"></div>
	</div>
	<script type="text/javascript">
	
		var TSearch = ['product','company','propal','projet','task','event'];
	
		$(document).ready(function() {
			
			$("#btseach").click(function() {
				
				var keyword = $("#keyword").val();
				$('#results').html("<span class=\"loading\">Chargement...</span>");
				
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
						
						$('#results div.result').tile();
						
					})
					
					
				}
				
				
			});
			
			<?php
				if(GETPOST('keyword')!='') {
					?>
					$("#keyword").val("<?php echo GETPOST('keyword') ?>");
					$("#btseach").click();
					<?php
				}			
			
			?>
			
		});
	</script>


	<?php
	
	
	llxFooter();

?>


