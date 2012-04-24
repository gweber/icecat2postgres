<?php


include "../config.inc.php";

$debug = 7;
// this file adds aliases of manufacturer to a mapping-table
// TODO: should be run on a daily basis

$file = file_get_contents("/data/icecat/initial/supplier_mapping.xml");

$array = json_decode(json_encode(simplexml_load_string($file)), TRUE);

foreach($array[SupplierMappings][SupplierMapping] as $k => $v){
	$manufacturer_ice_id = (int) pg_escape_string( $v['@attributes'][supplier_id] );
	$manu_check = mydb_query("select * from manufacturer where icecat_id=$manufacturer_ice_id",1);
	if ($manu_check) {
		$manu_id = (int) $manu_check[id];
		
		if ($v['@attributes']){
			$v[Symbol] = array('key' => trim($v[Symbol]) );
		}
		foreach ($v[Symbol] as $key => $value){
			$mapping = pg_escape_string(trim($value));
			if (strlen($mapping)){
				$map_check = mydb_query("select * from mapping where type='manufacturer' and value='$mapping'",1);
				if (!$map_check){
					$insert_query = mydb_query("insert into mapping(type,value,mapped_id) values ('manufacturer', '$mapping', $manu_id)",1);
				} else {
					echo "mapping found\n";
				}
			}
		}

	}
}
?>