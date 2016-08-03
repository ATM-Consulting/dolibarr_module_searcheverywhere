<?php

	require('../config.php');
	
	dol_include_once('/product/class/product.class.php');
	dol_include_once('/societe/class/societe.class.php');
	dol_include_once('/comm/propal/class/propal.class.php');
	dol_include_once('/projet/class/project.class.php');
	dol_include_once('/projet/class/task.class.php');
	dol_include_once('/comm/action/class/actioncomm.class.php');
	dol_include_once('/compta/facture/class/facture.class.php');
	dol_include_once('/commande/class/commande.class.php');
	dol_include_once('/expedition/class/expedition.class.php');
	
	$langs->load('searcheverywhere@searcheverywhere');
	
	$get = GETPOST('get');
	
	switch ($get) {
		case 'search':
			
			_search(GETPOST('type'), GETPOST('keyword'));
					
			break;
		
		default:
			
			break;
	}
	
	
	
function _search($type, $keyword) {
	global $db, $conf, $langs;
	
	$table = MAIN_DB_PREFIX.$type;
	$objname = ucfirst($type);
	$id_field = 'rowid';
	$complete_label = true;
	$show_find_field = false;
	$sql_join = '1';
	
	if($type == 'company') {
		$table = MAIN_DB_PREFIX.'societe';
		$objname = 'Societe';
		$complete_label = false;
	}
	elseif($type == 'projet') {
		$table = MAIN_DB_PREFIX.'projet';
		$objname = 'Project';
	}
	elseif($type == 'task') {
		$table = MAIN_DB_PREFIX.'projet_task';
		
	}
	elseif($type == 'event') {
		$table = MAIN_DB_PREFIX.'actioncomm';
		$objname = 'ActionComm';
		$id_field = 'id';
		$complete_label = false;
	}
	elseif($type == 'order') {
		$table = MAIN_DB_PREFIX.'commande';
		$objname = 'Commande';
		$complete_label = false;
	}
	elseif($type == 'invoice') {
		$table = array(MAIN_DB_PREFIX.'facture',MAIN_DB_PREFIX.'facture_extrafields');
		$objname = 'Facture';
		$complete_label = false;
		$sql_join = MAIN_DB_PREFIX.'facture.rowid = '.MAIN_DB_PREFIX.'facture_extrafields.fk_object';
		$id_field = MAIN_DB_PREFIX.'facture.rowid';
	}
	elseif($type == 'contact') {
		$table = MAIN_DB_PREFIX.'socpeople';
		
		$complete_label = false;
	}
	
	$table=(Array)$table;
	
	$sql_where = ' 0 ';
	$sql_fields = '';
	
	foreach($table as $table1) {
		
		$res = $db->query('DESCRIBE '.$table1);
		
		while($tbl = $db->fetch_object($res)) {
			$fieldname = $tbl->Field;
			//var_dump($tbl);
			$sql_fields .=','. $table1.'.'.$fieldname.' as '.$table1.'_'.$fieldname;
			
			if( strpos($tbl->Type,'varchar') !== false || strpos($tbl->Type,'text') !== false ) {
				$sql_where.=' OR '.$table1.'.'.$fieldname." LIKE '%".$db->escape($keyword)."%'";	
			}
			else if( strpos($tbl->Type,'int') !== false || strpos($tbl->Type,'double')!== false || strpos($tbl->Type,'float') !== false ) {
				$i_keyword = (double)$keyword;
				if(!empty($i_keyword))$sql_where.=' OR '.$table1.'.'.$fieldname." = ".$i_keyword;
					
			}
			else if( strpos($tbl->Type,'date') !== false ) {
				$sql_where.=' OR '.$table1.'.'.$fieldname." LIKE '".$db->escape($keyword)."%'";	
			}
			
			else{
				$sql_where.=' OR '.$table1.'.'.$fieldname." = '".$db->escape($keyword)."'";
			}
			
			
		}
		
		
	}
	
	
    $sql = 'SELECT '.$id_field.' as rowid '.$sql_fields.'  FROM '.$table.' WHERE (0 '.$sql_where.') ';
    if(!empty($conf->global->SEARCHEVERYWHERE_SEARCH_ONLY_IN_ENTITY)) $sql.= 'AND '.$table.'.entity = '.$conf->entity.' ';
    $sql.= 'LIMIT 20 ';

	//print $sql;
	$res = $db->query($sql);
	
	$nb_results = $db->num_rows($res);
	print '<table class="border" width="100%"><tr class="liste_titre"><td colspan="2">'.$langs->trans( ucfirst($objname) ).' <span class="badge">'.$nb_results.'</span></td></tr>';
	
	if($nb_results == 0) {
		print '<td colspan="2">Pas de résultat</td>';
	}
	else{
		while($obj = $db->fetch_object($res)) {
		
			$o=new $objname($db);
			$o->fetch($obj->rowid);
			
			$label = '';
			if($complete_label) {
				if(empty($label) && !empty( $o->label )) $label = $o->label;
				else if(empty($label) && !empty( $o->name )) $label = $o->name;
				else if(empty($label) && !empty( $o->nom )) $label = $o->nom;
				else if(empty($label) && !empty( $o->title )) $label = $o->title;
				else if(empty($label) && !empty( $o->ref )) $label = $o->ref;
				else if(empty($label) && !empty( $o->id)) $label = $o->id;
			}
			
			if(method_exists($o, 'getNomUrl')) {
				$label = $o->getNomUrl(1).' '.$label;
			}
			
			if(method_exists($o, 'getLibStatut')) {
				$statut = $o->getLibStatut(3);
				
			}  
			
			$desc = '';
			
			if($show_find_field) {
				foreach($o as $k=>$v) {
					if(is_string($v) && preg_match("/" . $keyword . "/", $v)) {
						$desc .= '<br />'.$k.' : '.preg_replace("/" . $keyword . "/", "<span class='highlight'>" . $keyword . "</span>", $v);	
					}
					
					
				}
				
			}
			
			print '<tr>
				<td>'.$label.$desc.'</td>
				<td align="right">'.$statut.'</td>
			</tr>';
			
		}
		
	}
	
	
	print '</table>';
	
}
