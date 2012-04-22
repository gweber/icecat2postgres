<?php


include "../config.inc.php";


$file = file_get_contents("/data/icecat/initial/LanguageList.xml");

$array = json_decode(json_encode(simplexml_load_string($file)), TRUE);

foreach($array[Response][LanguageList][Language] as $k => $v){
	$langid = pg_escape_string( $v['@attributes'][ID] );
	$langcode = pg_escape_string( $v['@attributes'][Code] );
	$langshortcode = pg_escape_string( $v['@attributes'][ShortCode] );
	echo $v['@attributes'];
	$language_query = mydb_query("select language_id,sid from language where language_id=$langid",1);
	if (!$language_query) {
		$sid = getsid();
		mydb_query("insert into language (language_id, sid, code, short_code) values ( $langid, $sid, '$langcode', '$langshortcode' )",1);
		
	} else {
		$sid = $language_query[1];
	}
	foreach ($v[Name] as $key => $value){
		$name_id = $value['@attributes'][ID];
		$name_value = pg_escape_string($value['@attributes'][Value]);
		$name_langid = $value['@attributes'][langid];
		$voc_check = mydb_query("select * from vocabulary where vocabulary_id=$name_id",1);
		if (!$voc_check){
			$insert_query = mydb_query("insert into vocabulary (vocabulary_id, sid, langid, value) values ($name_id, $sid, $name_langid, '$name_value')",1);
		}
	}
}
?>