<?php
class ActionsSearcheverywhere
{ 
     /** Overloading the doActions function : replacing the parent's function with the one below 
      *  @param      parameters  meta datas of the hook (context, etc...) 
      *  @param      object             the object you want to process (an invoice if you are in invoice module, a propale in propale's module, etc...) 
      *  @param      action             current action (if set). Generally create or edit or null 
      *  @return       void 
      */
      
        function printSearchForm($parameters, &$object, &$action, $hookmanager) {
    	
		global $langs,$db;
		
		if (in_array('searchform',explode(':',$parameters['context']))) 
        {
        	
			$langs->load('searcheverywhere@searcheverywhere');
			
			$res = '<div class="menu_titre"> '.img_object($langs->trans('searcheverywhere'),'searcheverywhere@searcheverywhere').' '.$langs->trans('Searcheverywhere').'<br /></div>';
			
			$res.='<form method="post" action="'.dol_buildpath('/searcheverywhere/search.php',1).'">
				<input type="text" size="10" name="keyword" title="'.$langs->trans('Keyword').'" class="flat">
				<input type="submit" value="'.$langs->trans('Go').'" class="button">
				</form>';
						
        	$this->resprints = $res;
		}
		
		return 0;
    }
}