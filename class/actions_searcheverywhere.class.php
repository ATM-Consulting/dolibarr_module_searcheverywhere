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
		
		if (in_array('searchform',explode(':',$parameters['context'])) && DOL_VERSION <= 3.8) 
        {
        	
			$langs->load('searcheverywhere@searcheverywhere');
			
			$res = '';
			
			$res.='<form method="post" action="'.dol_buildpath('/searcheverywhere/search.php',1).'">';
			$res.= '<div class="menu_titre menu_titre_search"><label for="sew_keyword"><a class="vsmenu" href="'.dol_buildpath('/searcheverywhere/search.php',1).'">'.img_object($langs->trans('searcheverywhere'),'searcheverywhere@searcheverywhere').' '.$langs->trans('Searcheverywhere').'</a></label></div>';
			$res.= '	<input type="text" size="10" name="keyword" title="'.$langs->trans('Keyword').'" class="flat" id="sew_keyword" /><input type="submit" value="'.$langs->trans('Go').'" class="button" style="padding-top: 4px; padding-bottom: 4px; padding-left: 6px; padding-right: 6px">
				</form>';
						
			$this->resprints = $res;
		}
		
		return 0;
    }
	
	function addSearchEntry($parameters, &$object, &$action, $hookmanager) {
		global $langs;
		
		if (in_array('searchform',explode(':',$parameters['context'])) && DOL_VERSION > 3.8) {
			$search_boxvalue = $parameters['search_boxvalue'];
			
			$this->results = array(
				'searcheverywhere' => array(
					'img'=>'object_searcheverywhere'
					,'label'=>$langs->trans("searcheverywhere", $search_boxvalue)
					,'text'=>img_picto('','object_searcheverywhere').' '.$langs->trans("searcheverywhere", $search_boxvalue)
					,'url'=>dol_buildpath('/searcheverywhere/search.php',1).'?keyword='.urlencode($search_boxvalue)
				)
			);
		}
		
		return 0;
	}
}