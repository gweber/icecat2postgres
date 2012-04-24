<?php


include "../config.inc.php";

$debug = 1;
// this file adds aliases of manufacturer to a mapping-table
// TODO: should be run on a daily basis

$context = stream_context_create(array(
	'http' => array(
		'header'  => 
			"Authorization: Basic " . base64_encode("$icecat_user:$icecat_pass") . "\n"
		)
));

$file = file_get_contents($daily_supp_url, false, $context);	// load the file over http
if (!strlen($file)){
	exit("error on file fetch\n");
}

$array = json_decode(json_encode(simplexml_load_string($file)), TRUE);

foreach($array[SupplierMappings][SupplierMapping] as $k => $v){
	$manufacturer_ice_id = (int) pg_escape_string( $v['@attributes'][supplier_id] );
	$manufacturer_name = pg_escape_string( $v['@attributes'][name] );
	$manu_check = mydb_query("select * from manufacturer where icecat_id=$manufacturer_ice_id",1);
	// manufacturer missing
	if (!$manu_check) {
		mydb_query("insert into manufacturer (icecat_id, name) values ($manufacturer_ice_id, '$manufacturer_name' )",1);
		debug(1,"manufacturere $manufacturer_name (#$manufacturer_ice_id) inserted\n");
		$manu_check = mydb_query("select * from manufacturer where icecat_id=$manufacturer_ice_id",1);
	}else {
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
					debug(1,"mapping found\n");
				}
			}
		}

	}
}
?>