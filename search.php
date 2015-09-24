<?php

	require 'config.php';
	
	llxHeader();

	?>
	<input type="text" name="keyword" id="keyword" value="" />
	<input type="button" name="btseach" id="btseach" value="Rechercher" />
	
	<div id="results">
		
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
						
						$div = $('<div style="float:left; width:300px;" />');
						$div.append(data);
						
						$('#results').append($div);
						
						
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


