<?php
// this script checks, if files exists, and sets the status to 10,  
// otherwise tries to download, if that fails, sets status to 2

@include "../config.inc.php";

$context = stream_context_create(array(
	'http' => array(
		'header'  => "Authorization: Basic " . base64_encode("$icecat_user:$icecat_pass") . "\n".
		"Accept-Encoding: gzip\n" 
	)
));

while(1){

$query = pg_query("select icecat_id from product where status=5 order by icecat_id desc limit 1");
if ($rows = pg_num_rows($query)){
	//echo "$rows found\n";
	while ($fetch = pg_fetch_array($query)){
		$id = $fetch[0];

		if ( file_exists( $xmlstorage . $id .".xml") ) {	// is file already in fetch_data?
			echo "file " . $xmlstorage . $id .".xml locally known\n";
			$updq = pg_query("update product set status = 11 where icecat_id=$id");
		} else if ( file_exists( $xmlstorage . $id .".xml.gz") ){
			echo "file " . $xmlstorage . $id .".xml.gz locally known\n";
			$updq = pg_query("update product set status = 11 where icecat_id=$id");
		} else {	
			$readurl = $iceurl . $id .".xml";
			echo "read => ";
			$file_str = file_get_contents($readurl,false,$context);	// load the file over http
			if ($file_str) {	// read was successful
				file_put_contents($xmlstorage . $id .".xml.gz", $file_str);
				echo strlen($file_str) ." bytes fetched and $id.xml.gz written\n";
				$updq = pg_query("update product set status = 10 where icecat_id=$id");	
			} else {
				echo "no result from fetch\n";
				$updq = pg_query("update product set status = 2 where icecat_id=$id");
			}
		}
		$wait = fgets(STDIN);
	}
} else {
	exit("done\n");
}

} // while (1)
?>
