<?php

//============================================================================================

// SETTINGS:

// Absolute path to the statistics files (NO trailing slash!)
$stats_path = '/path/for/messages/stats';

//============================================================================================

// GET + error reset etc.
$d = $_GET['d'];
$errors = "0";
$interval = strtotime('-30 days');

// String max length to 4 chars (semi "error check")
$d = substr($d, 0, 6);

// Set the correct deletion interval based on the above
if($d == "1"){
	$interval = strtotime('-24 hours');
}
elseif($d == "7"){
	$interval = strtotime('-7 days');
}
elseif($d == "30"){
	$interval = strtotime('-30 days');
}
elseif($d == "hits"){
	$interval = strtotime('-24 hours');
}
elseif($d == "random"){
	$interval = strtotime('-24 hours');
}
elseif($d == "r1"){
	$interval = strtotime('-24 hours');
	$d = "read/1";
}
elseif($d == "r7"){
	$interval = strtotime('-7 days');
	$d = "read/7";
}
elseif($d == "r30"){
	$interval = strtotime('-30 days');
	$d = "read/30";
}
else{
	exit;
	//$interval = strtotime('-30 days');
}

// Absolute path to the stats files
$dir = "$stats_path"."/$d/";

// Process the stats file directory
foreach (glob($dir."*") as $file){

	// Trim filename/path
	$file = trim($file);

	// Check if file is older (or equal) than the set interval
   	if(filemtime($file) <= $interval){
		Proc_Close (Proc_Open ("rm -f $file", Array (), $foo));

		//echo "Deleted: $file<br />";
	}
}

echo '0';

?>
