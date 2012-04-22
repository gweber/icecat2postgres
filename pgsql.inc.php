<?php

$dbconn = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpassword");

function mydb_query($sql, $override=0){
	global $debug;
	debug(4, $sql."\n");
	if (! $debug  & 2 || $override){
		$do_q = pg_query($sql);	// let the cow fly
		if (pg_affected_rows($do_q)){ // insert update delete
			if (pg_num_rows($do_q)){ // select
				$do_f = pg_fetch_array($do_q);	// fetch a row ... maybe get the full result and return the array one time
				return $do_f;
			} else {
				//return pg_insert_id($do_q);	// return the insert id 
			}
		} else {
			return;
		}
	} else {
		debug(2, "blocked sql\n");
	}
} // mydb_query

// getsid returns an sid-id from sid_seq (hail to postgres)
function getsid($override=1){
	$sid = mydb_query("select nextval('sid_seq')",$override);
	return (int) $sid[0];
} // getsid

// gettid returns a new tid from tid-seq
function gettid($override=1){
	$tid = mydb_query("select nextval('tid_seq')",$override);
	return (int) $tid[0];
} // gettid
?>
