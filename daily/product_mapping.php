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

$file = file_get_contents($daily_prodmap_url, false, $context);	// load the file over http
if (!strlen($file)){
	exit("error on file fetch\n");
}
echo $file;
$array = json_decode(json_encode(simplexml_load_string($file)), TRUE);

foreach($array[ProductMappings][ProductMapping] as $k => $v){
	$prod_ice_id = (int) pg_escape_string( $v['@attributes'][product_id] );
	$prod_check = mydb_query("select id from product where icecat_id=$prod_ice_id",1);
	// manufacturer missing
	if ($prod_check) {
		$mapping_value = pg_escape_string( $v['@attributes'][prod_id] );
		$mapped_value = pg_escape_string( $v['@attributes'][m_prod_id] );
		$prod_id = (int) $prod_check[id];
		if (strlen($mapping_value)){
			$map_check = mydb_query("select * from mapping where type='sku' and value='$mapping_value'",1);
			if (!$map_check){
				$insert_query = mydb_query("insert into mapping(type,value,mapped_id,mapped_value) values ('sku', '$mapping', $prod_id, '$mapped_value')",1);
			} else {
				debug(1,"mapping found\n");
			} // map check
		} // strlen mapping_value
	} // prodcheck
}// foreach

?>