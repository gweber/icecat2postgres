<?php


include "../config.inc.php";

$i = 0;
$file = "/data/icecat/initial/SuppliersList.xml";

$fh = fopen($file,'r');
while ($buf = fgets($fh)){
	if ($i++<6){
		continue;
	}
	$xmlarr = json_decode(json_encode(simplexml_load_string($buf)),true );
	$xmlarr = $xmlarr['@attributes'];
	if (!$xmlarr[LogoPic]){
		$xmlarr[LogoPic] = '';
	}
	$sql = "insert into manufacturer 
		(icecat_id,		name, 	Logo) values 
		($xmlarr[ID], 	'$xmlarr[Name]', '$xmlarr[LogoPic]')";
	echo $sql;
	$res = pg_query($dbconn, $sql);
	if ($res){
		echo pg_last_oid($res);
		echo "$xmlarr[ID] inserted";
	}
	$wait = fgets(STDIN);
}

?>
