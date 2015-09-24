<?php

	require('../config.php');
	
	dol_include_once('/product/class/product.class.php');
	dol_include_once('/societe/class/societe.class.php');
	dol_include_once('/comm/propal/class/propal.class.php');
	dol_include_once('/projet/class/project.class.php');
	dol_include_once('/projet/class/task.class.php');
	dol_include_once('/comm/action/class/actioncomm.class.php');
	
	$get = GETPOST('get');
	
	switch ($get) {
		case 'search':
			
			_search(GETPOST('type'), GETPOST('keyword'));
					
			break;
		
		default:
			
			break;
	}
	
	
	
function _search($type, $keyword) {
	global $db;
	
	$table = MAIN_DB_PREFIX.$type;
	$objname = ucfirst($type);
	$id_field = 'rowid';
	
	if($type == 'company') {
		$table = MAIN_DB_PREFIX.'societe';
		$objname = 'Societe';
	}
	elseif($type == 'projet') {
		$table = MAIN_DB_PREFIX.'projet';
		$objname = 'Project';
	}
	elseif($type == 'task') {
		$table = MAIN_DB_PREFIX.'projet_task';
	}elseif($type == 'event') {
		$table = MAIN_DB_PREFIX.'actioncomm';
		$objname = 'ActionComm';
		$id_field = 'id';
	}
	
	$res = $db->query('DESCRIBE '.$table);
	
	$sql_where  = '';
	
	while($tbl = $db->fetch_object($res)) {
		$fieldname = $tbl->Field;
		//var_dump($tbl);
		if( strpos($tbl->Type,'varchar') !== false || strpos($tbl->Type,'text') !== false ) {
			$sql_where.=' OR '.$fieldname." LIKE '%".$db->escape($keyword)."%'";	
		}
		else if( strpos($tbl->Type,'int') !== false || strpos($tbl->Type,'double')!== false || strpos($tbl->Type,'float') !== false ) {
			$i_keyword = (double)$keyword;
			if(!empty($i_keyword))$sql_where.=' OR '.$fieldname." = ".$i_keyword;
				
		}
		else{
			$sql_where.=' OR '.$fieldname." = '".$db->escape($keyword)."'";
		}
		
		
	}
	
	
	$sql = 'SELECT '.$id_field.' as rowid  FROM '.$table.' WHERE 0 '.$sql_where.' LIMIT 20 ';
	//print $sql;
	$res = $db->query($sql);
	
	print '<table class="border" width="100%"><tr class="liste_titre"><td>'.$type.'</td></tr>';
	
	if($db->num_rows($res) == 0) {
		print '<td>Pas de résultat</td>';
	}
	else{
		while($obj = $db->fetch_object($res)) {
			
			$o=new $objname($db);
			$o->fetch($obj->rowid);
			
			if(method_exists($o, 'getNomUrl')) {
				print '<tr><td>'.$o->getNomUrl(1).'</td></tr>';
			}  
			
		}
		
	}
	
	
	print '</table>';
	
}
