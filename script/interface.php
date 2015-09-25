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
	$complete_label = true;
	$show_find_field = false;
	
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
		
	}elseif($type == 'event') {
		$table = MAIN_DB_PREFIX.'actioncomm';
		$objname = 'ActionComm';
		$id_field = 'id';
		$complete_label = false;
	}
	
	$res = $db->query('DESCRIBE '.$table);
	
	$sql_where  = $sql_fields = '';
	
	while($tbl = $db->fetch_object($res)) {
		$fieldname = $tbl->Field;
		//var_dump($tbl);
		$sql_fields .=','. $fieldname;
		
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
	
	
	$sql = 'SELECT '.$id_field.' as rowid '.$sql_fields.'  FROM '.$table.' WHERE 0 '.$sql_where.' LIMIT 20 ';
	//print $sql;
	$res = $db->query($sql);
	
	print '<table class="border" width="100%"><tr class="liste_titre"><td>'.$type.'</td></tr>';
	
	if($db->num_rows($res) == 0) {
		print '<td>Pas de résultat</td>';
	}
	else{
		while($obj = $db->fetch_object($res)) {
		
			$desc = '';
			
			if($show_find_field) {
				foreach($obj as $k=>$v) {
					if(preg_match("/" . $keyword . "/", $v)) {
						$desc .= '<br />'.$k.' : '.preg_replace("/" . $keyword . "/", "<span class='highlight'>" . $keyword . "</span>", $v);	
					}
					
					
				}
				
			}
			
			$o=new $objname($db);
			$o->fetch($obj->rowid);
			
			$label = '';
			if($complete_label) {
				if(empty($label) && !empty( $o->label )) $label = $o->label;
				else if(empty($label) && !empty( $o->name )) $label = $o->name;
				else if(empty($label) && !empty( $o->nom )) $label = $o->nom;
				else if(empty($label) && !empty( $o->title )) $label = $o->title;
			}
			
			if(method_exists($o, 'getNomUrl')) {
				$label = $o->getNomUrl(1).' '.$label;
			}  
			
			print '<tr><td>'.$label.$desc.'</td></tr>';
			
		}
		
	}
	
	
	print '</table>';
	
}
