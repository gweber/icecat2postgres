<?php 
# debug: 		0	try to be quiet
#			1	show only some strings (does sql, no show off it)
#			2	turn sql off
#			4	show sql queries

$debug = 7;

function debug ($level, $message){
	global $debug,$id;
	if ($debug & $level ){
		echo "debug[$level|$id]: $message";
	}
}

function echocolor($text,$color="normal",$back=0) { 
  $colors = array('light_red'  => "[1;31m", 'light_green' => "[1;32m", 'yellow'     => "[1;33m", 
                  'light_blue' => "[1;34m", 'magenta'     => "[1;35m", 'light_cyan' => "[1;36m", 
                  'white'      => "[1;37m", 'normal'      => "[0m",    'black'      => "[0;30m", 
                  'red'        => "[0;31m", 'green'       => "[0;32m", 'brown'      => "[0;33m", 
                  'blue'       => "[0;34m", 'cyan'        => "[0;36m", 'bold'       => "[1m", 
                  'underscore' => "[4m",    'reverse'     => "[7m" ); 
  $out = $colors["$color"]; 
  $ech = chr(27)."$out"."$text".chr(27)."[0m"; 
  if($back)   { 
    return $ech; 
  }  else { 
    echo $ech; 
  } 
}

?>