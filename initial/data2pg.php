<?php


include "../pgsql.inc.php";

$i = 0;
$values = '';
// get the index file from icecat, and store it local
$file = "/data/icecat/initial/icecat.files.index.xml";

// the xml file is too large to handle it completely by simplexml, so read each line
$fh = fopen($file,'r');
while ($buf = fgets($fh)){
	// skip first lines
	if ($i++ <= 5){
		continue;
	}
	// there should be a <file in string
	if (strpos($buf,"<file")) { 
		// the parser fails on additional information, so add a closinf </file
		if (!simplexml_load_string($buf)){
			$buf .= "</file>";
		}
		// still buggy? skip
		if (!simplexml_load_string($buf)){
			continue;
		}
		$xmlarr = json_decode(json_encode(simplexml_load_string($buf)),true );
		$xmlarr = $xmlarr['@attributes'];
		$values .= "(	$xmlarr[Product_ID], " .
					"	'" . pg_escape_string($xmlarr[Model_Name]) ."' , ".
					"	$xmlarr[Supplier_id], ".
					"	'" . pg_escape_string($xmlarr[Prod_ID]) ."', ".
					"	$xmlarr[Catid], ".
					"	'" . pg_escape_string($xmlarr[HighPic]) ."', ".
					"	$xmlarr[HighPicSize], ".
					"	5, ".		// marked for fetch
					"	'" . pg_escape_string($xmlarr[Quality]) ."', ".
					"	$xmlarr[Product_View] ".
					")";
		// insert every 100 rows
		if ( !($i%100) ){
			$res = pg_query($dbconn, "insert into product 
				(	icecat_id,  name, manufacturer_id, manufacturer_sku, category_id,
	    			high_pic, high_pic_size, status, quality, topseller ) values " . $values);
			// reset the values 
			$values = '';
			echo "$i done\n";
			// wait for user to check, strg+d to detach this			
			$wait = fgets(STDIN);
		} else {
			$values .= ", ";
		}
	}
}
fclose($fh);

if ($values){
	$res = pg_query($dbconn, "insert into product 
				(	icecat_id,  name, manufacturer_id, manufacturer_sku, category_id,
	    			high_pic, high_pic_size, status, quality, topseller ) values " . $values);
}
?>
