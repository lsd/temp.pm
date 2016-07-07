<?php

//===================================================================================================================
/*
 	Temp.PM - Temporary Private Message
	-----------------------------------

 	Contact: temp.pm (at) riseup (dot) net  /  temp.pm (at) protonmail (dot) com
 	Web: https://temp.pm
	License: Do what you want but keep it free and open source and include original authors info/contact details!
*/
//===================================================================================================================

// PHP error reporting and display (more info: https://secure.php.net/manual/en/errorfunc.configuration.php)
error_reporting(0);
//display_errors(0);

// BEGIN SETTINGS:

// Version number and release date
define('VERSION', '3.7.1 (11.1.2016)');

// Server domain names (normal/clearnet and TOR)
define('SERVER_DOMAIN_NORMAL', 'temp.pm');
define('SERVER_DOMAIN_TOR', 'temppm7ynp557fn7.onion');

// BitCoin donation address
define('BTC_DONATE', '1tempAitLUWwgNGhiJjAxXegv61tXd6x2');

// Path for message files, password files and bruteforce counters (absolute path, no trailing slash!)
define('MESSAGE_PATH_ABSOLUTE', '/path/to/temp/txt');

// Path for message statistics files (absolute path, no trailing slash!)
define('STATS_PATH_ABSOLUTE', '/path/to/temp/txt/stats');

// ____________________

// Announcement(s) that will be printed on all pages
//$announcement_msg[0] = '<b>Hurray!</b> - We have a new server!';
//$announcement_msg[0] = '<b>Announcement</b> - TOR related issues should now be fixed. <a href="?about#contact" class="blue">Contact us</a> if you are still having issues.';
//$announcement_msg[1] = '<b>New feature!</b> - Optional password: require a password before the message can be read.';
//$announcement_msg[2] = '';
//$announcement_msg[3] = '';

// Use this to add some vertical spacing if you use multiple lines:
// <img src="transparent.png" alt="" style="height: 25px; width: 1px;">
// ____________________


// Announcement background colour. Options: blue, green, yellow, red (defaults to blue if empty)
$announcement_colour = 'blue';

// Maintenance mode enabled (disables the site if enabled)
define('MAINTENANCE_MODE_ENABLED', '0');

// Message creation enabled
define('MESSAGE_CREATION_ENABLED', '1');

// Message reading enabled
define('MESSAGE_READING_ENABLED', '1');

// Messages with custom password enabled
define('MESSAGE_CUSTOM_PASS_ENABLED', '1');

// Custom password max length
define('CUSTOM_PASS_MAX_LENGTH', '64');

// Random string generator min string length (must be 8 or more)
$filename_min_length = '24';
$pass_min_length = '24';

// Random string generator max string length (must be 8 or more)
$filename_max_length = '24';
$pass_max_length = '24';

// Secure delete parameters for messages (shred with 7-pass + unlink by default)
define('MESSAGE_DELETE_PARAMETERS', 'shred -f -u -n 7');

// Delete parameters for bruteforce counters and custom password files (rm by default)
define('NORMAL_DELETE_PARAMETERS', 'rm -f');

// Bruteforce protection: after how many failed attempts the message will be destroyed (must be above 0)
define('BRUTEFORCE_THRESHOLD', '3');

// Max message length in bits (default 33554432 = 4 MB)
define('MESSAGE_MAX_LENGTH', '33554432');

// Default error message
define('DEFAULT_ERROR_MESSAGE', 'The message has either been read/expired/deleted or this URL is invalid.');

// END OF SETTINGS
//============================================================================================

// TODO this code is not currently in use:
/*
// Check if the visitor is accessing the site through TOR and set the domain accordingly
if(gethostbyname(ReverseIPOctets($_SERVER['REMOTE_ADDR']).".".$_SERVER['SERVER_PORT'].".".ReverseIPOctets($_SERVER['SERVER_ADDR']).".ip-port.exitlist.torproject.org")=="127.0.0.2") {
	$http = "http://";
	$server_domain = SERVER_DOMAIN_TOR;
	$is_tor = "1";
}
else{
	$http = "https://";
	$server_domain = SERVER_DOMAIN_NORMAL;
	$is_tor = "0";
}

function ReverseIPOctets($inputip){
	$ipoc = explode(".",$inputip);
	return $ipoc[3].".".$ipoc[2].".".$ipoc[1].".".$ipoc[0];
}
*/

// TODO these are a temporary solution before implementing TOR service
$http = 'https://';
$server_domain = SERVER_DOMAIN_NORMAL;

//_________________________________________________________

// Random string length setting check (minimum = 8)
if ($filename_min_length < '8') {
		$filename_min_length = '8';
}

if ($pass_min_length < '8') {
		$pass_min_length = '8';
}

if ($filename_max_length < '8') {
		$filename_max_length = '8';
}

if ($pass_max_length < '8') {
		$pass_max_length = '8';
}

//_________________________________________________________

// Annoucement color settings

// Blue = alert-info
if ($announcement_colour == "blue") {
		$announcement_colour = 'alert-info';
}
// Green = alert-success
elseif ($announcement_colour == "green") {
		$announcement_colour = 'alert-success';
}
// Yellow = alert-warning
elseif ($announcement_colour == "yellow") {
		$announcement_colour = 'alert-warning';
}
// Red = alert-danger
elseif ($announcement_colour == "red") {
		$announcement_colour = 'alert-danger';
}
// Otherwise defaults to blue
else {
		$announcement_colour = 'alert-info';
}

//_________________________________________________________

// Reset some variables
$message_exists = '1';
$stat_file_exists = '1';
$user_agent_block_now = '0';
$new_messages_1d = '0';
$new_messages_7d = '0';
$new_messages_30d = '0';
$new_messages_60d = '0';
$page_hits_1d = '0';
$is_tor = '0';

$note = '';
$ttl = '';
$random = '';
$link = '';
$cpass_create = '';
$cpass_read = '';
$id = '';
$d = '';
$p = '';

// POSTs
$note = rtrim($_POST['note']); // Strip empty characters from the end of the message
$ttl = substr($_POST['ttl'], 0, 3); // Limit to 3 characters max
$random = $_POST['random'];

// GETs
//$link = $_GET['link']; // TODO not in use

//_________________________________________________________

// Limit optional password length and a "dirty fix" for whitespaces in the passwords
$cpass_create = substr($_POST['cpass_create'], 0, CUSTOM_PASS_MAX_LENGTH);
$cpass_read = substr($_POST['cpass_read'], 0, CUSTOM_PASS_MAX_LENGTH);
$cpass_create = preg_replace('/\s+/', '__', $cpass_create); 
$cpass_read = preg_replace('/\s+/', '__', $cpass_read);

//_________________________________________________________

// Relative path to this script file from www-root (example: /index.php)
$relative_path_to_script = $_SERVER['SCRIPT_NAME'];
//$relative_path_to_script = basename(__FILE__); // = index.php (without the last slash that is needed)

// Strip index.php from the path
if($relative_path_to_script == "/index.php"){
		$relative_path_to_script = "/";
}

// Get query string content and explode it to the correct variables
$query = $_SERVER["QUERY_STRING"];
$query_part = explode('-', $query);
$id = $query_part[0];
$d = $query_part[1];
$p = $query_part[2];

// Validate message ID, TTL and password variables
if (preg_match('/[^A-Za-z0-9]/', $id)) {
		$id = 'error';
}

if (preg_match('/[^A-Za-z0-9]/', $d)) {
		$d = 'error';
}

if (preg_match('/[^A-Za-z0-9]/', $p)) {
		$p = 'error';
}

// Get the first 2 characters from the last query part and convert it to uppercase
$mode_query = substr(strtoupper($query_part[3]), 0, 2);

//_________________________________________________________

// Mode query/switch redirects (load.php)

// Normal mode (remove the '-N' part to prevent redirect loop)
if($mode_query == 'N' && !empty($id) && !empty($d) && !empty($p)){
	header("Location: https://".SERVER_DOMAIN_NORMAL."/load.php?".$id."-".$d."-".$p);
	exit();
}

// Hidden mode
if($mode_query == 'H' && !empty($id) && !empty($d) && !empty($p)){
	header("Location: https://".SERVER_DOMAIN_NORMAL."/load.php?".$id."-".$d."-".$p."-H");
	exit();
}

//_________________________________________________________

// Prevent form resubmits by redirecting to main page
// TODO improve this somehow (with sessions?), this still allows first (= 1) resubmit
if(isset($_POST['create']) && !empty($note) && !empty($random) && MESSAGE_CREATION_ENABLED == '1' && !file_exists(STATS_PATH_ABSOLUTE."/random/$random")){
	header("Location: " . $http . $server_domain . $relative_path_to_script);
	exit();
}

//_________________________________________________________

// TODO this code is not in use:
/*
// Invalid external link (extenal links have not been implemented because of brs breaks the href link)
if(substr($query, 0, 4) == 'link' && strlen($link) < '1'){	
	header("Location: " . $http . $server_domain . $relative_path_to_script);
	exit();
}
*/

//_________________________________________________________

// Block message reading for some user agents (because of link previews etc.) to prevent premature message deletion

// Get user agent + convert to uppercase
$user_agent = strtoupper($_SERVER['HTTP_USER_AGENT']);

// User agents blocklist
$user_agent_blocklist = array('FACEBOOK', 'TELEGRAMBOT', 'TWITTER', 'YOURLS', 'BITLYBOT', 'ROGERBOT', 'READABILITY.COM', 'DFTBA', 'DFT.BA', 'SLURP', 'BAIDU', 'BOT');

// Check if user agent matches the blocklist
if (!empty($user_agent)) {

	foreach($user_agent_blocklist as $ua_list) {

		if (strstr($user_agent, $ua_list)){
			$user_agent_block_now = "1";
		}
	}
}

//_________________________________________________________

// Get currently used domain
//$host1 = $_SERVER["SERVER_NAME"];
$host2 = $_SERVER['HTTP_HOST'];

//_________________________________________________________

// Force HTTPS/SSL
if($_SERVER["HTTPS"] != "on" && $is_tor == "0"){
	header("Location: https://" . SERVER_DOMAIN_NORMAL . $_SERVER["REQUEST_URI"]);
	exit();
}

// Force specific domain (normal/clearnet)
if($host2 != SERVER_DOMAIN_NORMAL && $is_tor == "0"){
	header("Location: https://" . SERVER_DOMAIN_NORMAL . $_SERVER["REQUEST_URI"]);
	exit();
}

// TODO this code is not in use:
/*
// Force specific domain (TOR)
if($host2 != SERVER_DOMAIN_TOR && $is_tor == "1" && $host2 != SERVER_DOMAIN_NORMAL){
	header("Location: http://" . SERVER_DOMAIN_TOR . $_SERVER["REQUEST_URI"]);
	exit();
}
*/

//_________________________________________________________

// Server URL for messages (HTTP/HTTPS + domain name):	
$server_url = "$http"."$server_domain";

//_________________________________________________________

// TODO add check that the message has other content besides spaces and empty lines (strip \n \r \t (and space?) + check if contains something after that = ok)
// $string = preg_replace('/\s+/', '', $string);
// preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string);

// TODO this code is not in use:
/*
// Check that message contains a-z, A-Z or 0-9
if(preg_match('/[A-Za-z]/', $note) || preg_match('/[0-9]/', $note)){
	$message_is_valid = "1";
}
else{
	$message_is_valid = "0";
}
*/

//_________________________________________________________

// TODO this code is not in use:
// JavaScript code for the 1 hour countdown timer
/*
$js_timer = '
	<script type="text/javascript">
	window.onload=WindowLoad;
	function WindowLoad(event) {
		jsCountDown("jsCountDown", "3600");
	}

	var _countDowncontainer=0;
	var _currentSeconds=0;
	function jsCountDown(strContainerID, initialValue) {
		_countDowncontainer = document.getElementById(strContainerID);
		if (!_countDowncontainer) {
			return;
		}
		SetCountdownText(initialValue);
		window.setTimeout("CountDownTick()", 1000);
	}
	function CountDownTick() {
		if (_currentSeconds <= 0) {
			return;
		}
		SetCountdownText(_currentSeconds-1);
		window.setTimeout("CountDownTick()", 1000);
	}
	function SetCountdownText(seconds) {
		_currentSeconds = seconds;
		var minutes=parseInt(seconds/60);
		seconds = (seconds%60);
		var hours=parseInt(minutes/60);
		minutes = (minutes%60);
		var strText = AddZero(hours) + ":" + AddZero(minutes) + ":" + AddZero(seconds);
		_countDowncontainer.innerHTML = strText;
	}
	function AddZero(num) {
		return ((num >= 0)&&(num < 10))?"0"+num:num+"";
	}
	</script>
';
*/

//_________________________________________________________

// Random string generation for page hits and form resubmit prevention
$hit_file = generateRandom("64");

// Create a new file for every page hit
touch(STATS_PATH_ABSOLUTE."/hits/$hit_file");

//============================================================================================

// Function: Generate random string with specified length (Uses numbers and uppercase and lowercase alphabets)
function generateRandom($length) {
	$generated = '';
	for ($i=0;$i<=$length;$i++) {
		$chr = '';
		switch (mt_rand(1,3)) {
			case 1:
				$chr = chr(mt_rand(48,57));
			break;
			
			case 2:
				$chr = chr(mt_rand(65,90));
			break;
			
			case 3:
			$chr = chr(mt_rand(97,122));
		}
	  $generated.=$chr;
	}	

  return $generated;
}

//_____________

// Function: Generate salt (or IV) using mcrypt. Default length is 128 bits (16 bytes)
function generateSalt($bytes = '16'){
	return mcrypt_create_iv($bytes, MCRYPT_RAND);
}

//_____________

// Function: Decrypt message (AES-256-CBC, output: base64, IV: 128 bits)
function encryptData($openssl_data, $openssl_pass, $openssl_iv){
	$method = 'AES-256-CBC';

	if(strlen($openssl_iv) > '128'){
		$openssl_iv = substr($openssl_iv, 0, 128);
	}

	return trim(openssl_encrypt($openssl_data, $method, $openssl_pass, FALSE, $openssl_iv));
}

//_____________

// Function: Encrypt message (AES-256-CBC, output: base64, IV: 128 bits)
function decryptData($openssl_data, $openssl_pass, $openssl_iv){
	$method = 'AES-256-CBC';

	if(strlen($openssl_iv) > '128'){
		$openssl_iv = substr($openssl_iv, 0, 128);
	}

	return trim(openssl_decrypt($openssl_data, $method, $openssl_pass, FALSE, $openssl_iv));
}

//_____________

// TODO this code is not in use:
// Function: Convert URLs to links to the external website warning page
// TODO fix relative_path_to_script (isn't passed in the function "header")
/*
function convertURLs($s) {
	return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="https://temp.pm'.$relative_path_to_script.'?link=$1" class="url-ext" target="_blank">$1</a>', $s);
}
*/

//_____________

// Function: Ping server / check online status
function pingServer($host, $port, $timeout){
	$starttime = microtime(true);
	$file      = fsockopen ($host, $port, $errno, $errstr, $timeout);
	$stoptime  = microtime(true);
	$status    = 0;
	 
	// Server is down
	if(!$file){
		$status = -1;
	}
	// Otherwise print response time in milliseconds
	else{
		fclose($file);
		$status = ($stoptime - $starttime) * 1000;
		$status = floor($status);
	}
	return $status;
}

//_____________

// Function: Server uptime
function getServerUptime(){
  //if(PHP_OS == "Linux") {
    $uptime = @file_get_contents("/proc/uptime");
    //if ($uptime !== false) {

	$uptime = explode(" ",$uptime);
	$uptime = $uptime[0];
	$days = explode(".",(($uptime % 31556926) / 86400));
	$hours = explode(".",((($uptime % 31556926) % 86400) / 3600));
	$minutes = explode(".",(((($uptime % 31556926) % 86400) % 3600) / 60));

	$time = '';	

	// Days
	if($days[0] == '0'){
		$time = '';
	}
	elseif($days[0] == '1'){
		$time = "$days[0] day";
	}
	elseif($days[0] > '1'){
		$time = "$days[0] days";
	}

	// Hours
	if($hours[0] == '0'){
		$time = $time;
	}
	elseif($hours[0] == '1'){
		$time = $time.", $hours[0] hour";
	}
	elseif($hours[0] > '1'){
		$time = $time.", $hours[0] hours";
	}

	// Minutes
	if($minutes[0] == '0'){
		$time = $time;
	}
	elseif($minutes[0] == '1'){
		$time = $time.", $minutes[0] min";
	}
	elseif($minutes[0] > '1'){
		$time = $time.", $minutes[0] mins";
	}

	// Fix leading commas (when uptime is less than 1 day)
	if(substr($time, 0, 2) == ', '){
		$time = substr($time, 2, (strlen($time)-2));
	}

  return $time;
}

//_____________

// Function: System load average
function getSystemLoad(){
	$load_source = substr(strrchr(shell_exec("uptime"),":"),1);
	$load = array_map("trim",explode(",",$load_source));
	$load = $load[0].', '.$load[1].', '.$load[2];

	return $load;
}

//_____________

// Function: CPU usage percentage
function getCPUusage(){
	exec('ps -aux', $processes);
	
	foreach($processes as $process){
		
		$cols = split(' ', ereg_replace(' +', ' ', $process));
		
		if(strpos($cols[2], '.') > -1){
			$cpu_usage += floatval($cols[2]);
			$cpu_usage = round($cpu_usage, 0);
		}
	}

	// Fix if over 100% (TODO not sure why it does this?)
	if ($cpu_usage > '100') {
			$cpu_usage = '100';
	}

	return $cpu_usage.'%';
}

//_____________

// Function: memory usage percentage
function getMemUsage(){
	// Resets
	$total = 0;
	$free = 0;
	$cached = 0;

	// Open file handle
	$fh = fopen('/proc/meminfo','r');

	// Loop
	while($line = fgets($fh)) {
		$pieces = array();

		// Total memory
		if(preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)){
			$total = $pieces[1];
		}

		// Free memory
		if(preg_match('/^MemFree:\s+(\d+)\skB$/', $line, $pieces)){
			$free = $pieces[1];
		}

		// Cached memory 
		if(preg_match('/^Cached:\s+(\d+)\skB$/', $line, $pieces)){
			$cached = $pieces[1];
		}

		// Break loop when both are set
		if(!empty($total) && !empty($free) && !empty($cached)){
			break;
		}
	}
	// Close file handle (/proc/meminfo)
	fclose($fh);

	// Convert all values from kB to GB and round to 2 decimal places
	$total = ($total / 1024 / 1024);
	$total = round($total, 2);
	
	$free = ($free / 1024 / 1024);
	$free = round($free, 2);
	
	$cached = ($cached / 1024 / 1024);
	$cached = round($cached, 2);
	
	// Fix if there is something weird with memory usage (TODO not sure why it does this?)
	if($cached > $free){
		$usage_percentage = round(((($total - $cached)/$total)*100), 0);
	}
	elseif($cached < $free){
		$usage_percentage = round(((($total - $free)/$total)*100), 0);
	}

	// Fix if over 100% (TODO not sure why it does this?)
	if ($usage_percentage > '100') {
			$usage_percentage = '100';
	}

	return $usage_percentage.'%';
}

// HTTP header for page refresh (wipe) after 1 hour when reading a message or after creating a message
if (!empty($id) && !empty($d) && !empty($p) || $query == "create" || isset($_POST['create'])) {
	header('Refresh: 3600; URL=https://'.SERVER_DOMAIN_NORMAL.'/');
}

// HTTP header for main page refresh (wipe) after 23 hours
elseif ($query == "") {
	header('Refresh: 82800; URL=https://'.SERVER_DOMAIN_NORMAL.'/');
}


//============================================================================================
?>
<!DOCTYPE html>
<html>
  <head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<?php

	// HTML meta header for page refresh (wipe) after 1 hour when reading a message or after creating a message
	if (!empty($id) && !empty($d) && !empty($p) || $query == "create" || isset($_POST['create'])) {

			echo '	<meta http-equiv="refresh" content="3600; url=https://'.SERVER_DOMAIN_NORMAL.'/">
					<meta name="robots" content="noindex, follow">
					<meta name="robots" content="noarchive, nosnippet">
';

	}
	// Refresh (wipe) the main page after 23 hours
	elseif ($query == "") {

			echo '	<meta http-equiv="refresh" content="82800">
					<meta name="robots" content="index, follow">
';

	// No refresh for other/normal pages
	}
	else {
			echo '	<meta name="robots" content="index, follow">';
	}
?>
	<meta name="application-name" content="Temp.PM">
	<meta name="author" content="Temp.PM">
	<meta name="description" content="Temporary Private Message service with encryption, self-destruction and many other security features.">
	
	<title>Temp.PM - Temporary Private Message</title>
	
	<link rel="shortcut icon" href="favicon.ico" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">   

<style type="text/css">
<?php 
// Include CSS directly to make page loading more elegant (and avoid issues with TOR)
include('style.min.css')
?>
</style>

</head>

  <body onload="document.getElementById('note').reset();">
		
	<div class="container">
		<div class="panel panel-default panel-background">
			<div class="panel-heading">

				<table style="width: 100%; border: 0px;">
					<tr>
						<td style="width: 50%; border: 0px; text-align: left;">

							<a href="<?php echo $relative_path_to_script;?>" class="sitetitle"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAAe0lEQVR4AWMAAfWQ7Pt+C0E48w6IBmH1EHUGEAQhlar///9jYgYVBihwr/q/4RkmZnCHKWgAqd8DxDtgGGwGQwNMQd7//7f/v0LFIAV5CAWYAF3BbiC8DiEhmDwTIJg8EzBcQNgEKitouPIfC4QHtRSDF0MxVugFlGMAANVoOrjrWwQpAAAAAElFTkSuQmCC" alt="" class="site-logo-img"/> 

							<b><span style="font-size: 105%;">Temp.PM</span></b><span style="font-size: 95%;">&nbsp;-&nbsp;Temporary Private Message</span></a>

						</td>

						<td style="width: 50%; border: 0px; text-align: right;">

							<a href="?hiddenmode" class="btn btn-default btn-xs">&nbsp;Hidden Mode?&nbsp;</a>&nbsp;
							<?php if (file_exists('blog.txt')) { echo '<a href="?blog" class="btn btn-default btn-xs">&nbsp;Blog&nbsp;</a>&nbsp;'; } ?>
							<a href="https://twitter.com/temp_pm" target="_blank" class="btn btn-default btn-xs">&nbsp;Twitter&nbsp;</a>&nbsp;
							<?php if (file_exists('canary.txt')) { echo '<a href="?canary" class="btn btn-default btn-xs">&nbsp;Canary&nbsp;</a>&nbsp;'; } ?>
							<a href="?about" class="btn btn-default btn-xs">&nbsp;About&nbsp;</a>
						</td>
					</tr>
				</table>

			</div>
			<div class="panel-body">

				<?php
				// Print announcement messages
				if(!empty($announcement_msg)){
						
					// Loop
					for($x=0; $x<count($announcement_msg); $x++){
							
						// Check that there is some content -> print
						if(!empty($announcement_msg[$x])){
							echo '
								<div class="alert '.$announcement_colour.'">
									'.$announcement_msg[$x].'
								</div>
							';
						} // end: if (announcement has content)
					} // end: for loop
				} // end: if (announcement is not empty)

//============================================================================================
					
					// Hidden mode info page
					if($query == "hiddenmode" && file_exists('hiddenmode.txt')){

						// Get hidden mode info from file
						$hiddenmode_content = trim(file_get_contents('hiddenmode.txt', FILE_USE_INCLUDE_PATH));

						// Convert spaces to <br />
						$hiddenmode_content = nl2br($hiddenmode_content);

						// Print contents
						if(!empty($hiddenmode_content)){

							// Print the news
							echo '
								<div class="well">
									<div style="text-align: justify;">
										'.$hiddenmode_content.'
									</div>
								</div>
							';

						}
						// Otherwise print error message
						else{
							echo '
								<div class="alert alert-danger">
									<b>Error!</b><br />
									Page content not found! Sorry for the inconvenience. Please try again later.
								</div>
							';
						}

						// Footer button
						echo '
							<div class="spacer">
								<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write a New Message</a>
							</div>
						';
					}
//============================================================================================
					
					// Blog page
					elseif($query == "blog" && file_exists('blog.txt')){

						// Print blog page info
						echo '
							<div class="well">
								<div style="text-align: justify;">
									<b>Blog</b><br />
									Here you\'ll find information about the latest changes on Temp.PM, usually in more detail than on our <a href="https://twitter.com/temp_pm" target="_blank">Twitter page</a>.<br />
								</div>
							</div>
						';

						// Get blog content from file
						$blog_content = trim(file_get_contents('blog.txt', FILE_USE_INCLUDE_PATH));

						// Fix: remove first br
						//$blog_content = substr($blog_content, 0, strlen($blog_content));
						
						// Convert spaces to <br />
						$blog_content = nl2br($blog_content);

						//$blog_content = str_replace('(START)', '<div class="well"><div style="text-align: justify;">', $blog_content);
						//$blog_content = str_replace('(STOP)', '</div></div>', $blog_content);

						// Print contents
						if(!empty($blog_content)){

							// Print the blog
							echo $blog_content;

						}
						// Otherwise print error message
						else{
							echo '
								<div class="alert alert-danger">
									<b>Error!</b><br />
									Page content not found! Sorry for the inconvenience. Please try again later.
								</div>
							';
						}

						// Print footer button
						echo '
							<div class="spacer">
								<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write a New Message</a>
							</div>
						';
					}
//============================================================================================

					// Canary page
					elseif($query == "canary" && file_exists('canary.txt')){

						// Print page info
						echo '
							<div class="well">
								<div style="text-align: justify;">
									<b>Canary</b><br />
									<a href="https://www.eff.org/deeplinks/2014/04/warrant-canary-faq" target="_blank">A warrant canary</a> is a colloquial term for a regularly published statement that a service provider has not received legal process that it would be prohibited from saying it had received.<br /><br />

The statement can be found below or you can use the <a href="https://temp.pm/canary.txt" target="_blank">plain text version</a>. Verify the signature with our <a href="https://temp.pm/pgp.asc">PGP key</a>.<br />
								</div>
							</div>

							<div class="well">
								<div style="text-align: justify;">
						';

						// Get content from file
						$canary_content = file_get_contents('canary.txt', FILE_USE_INCLUDE_PATH);

						// Check if content exists
						if (!empty($canary_content)) {

							// Trim and convert spaces to <br />
							$canary_content = trim(nl2br($canary_content));

							// Print content
							echo $canary_content;

						}
						// Otherwise print error message
						else {
							echo '
								<div class="alert alert-danger">
									<b>Error!</b><br />
									Page content not found! Sorry for the inconvenience. Please try again later.
								</div>
							';
						}

						// Print footer + footer button
						echo '
							</div>
								</div>

							<div class="spacer">
								<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write a New Message</a>
							</div>
						';
					}
//============================================================================================

					// External links
					elseif(substr($query, 0, 4) == 'link' && !empty($link)){	

						// Print the information about the external link
						echo '
							<div class="alert alert-warning">
								<b>Link to external website</b><br />
								<div style="text-align: justify;">
									&bullet; The purpose of this page is to inform you about the dangers of external websites.<br />
									&bullet; Be aware that the external website may compromise your privacy/security or be harmful to your computer.<br />
									&bullet; To hide your browser\'s referer information, copy the URL below and paste it to your address bar.<br />
									&bullet; If you just wish to proceed normally, click on the link below.<br />
								</div>
							</div>
						';

						// // Encode some characters to HTML equivalens (prevents using HTML in messages)
						$link = htmlentities($link, ENT_QUOTES | ENT_HTML401); 
						
						// Print the link						
						echo '
							<div class="panel panel-default panel-message1">
		  						<div class="panel-body panel-message2">
							';

						// If URL is valid -> print link with href
						if(filter_var($link, FILTER_VALIDATE_URL)){
							echo '<a href="'.$link.'" class="url-ext">'.$link.'</a>';
						}
						// Otherwise (URL is invalid) -> print link without href
						else {
							echo $link;
						}
						echo '
								</div>
							</div>
							';
							
						// Footer button
						echo '
							<div class="spacer">
								<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write a New Message</a>
							</div>
						';
					}
//============================================================================================

					// About page
					elseif($query == "about"){
						
						// Get the message counts
						$count = file_get_contents(STATS_PATH_ABSOLUTE."/waiting.txt");
						$new_messages_1d = file_get_contents(STATS_PATH_ABSOLUTE."/1.txt");
						$new_messages_7d = file_get_contents(STATS_PATH_ABSOLUTE."/7.txt");
						$new_messages_30d = file_get_contents(STATS_PATH_ABSOLUTE."/30.txt");
						$page_hits_1d = file_get_contents(STATS_PATH_ABSOLUTE."/hits.txt");

						// OLD:
						// It uses strong encryption, secure deletion and the message URLs are protected against bruteforce attacks. You can also use your own custom password for additional protection.						

						// Print about page content
						echo '
							<div class="well">
							<b>About</b><br />
							<div style="text-align: justify;">
							Temp.PM (formely known as ParaNote) stands for Temporary Private Message. You can create messages which will self-destruct after being read or after a timer expires if they won\'t be read in time. We have a strong emphasis on security and privacy.<br /><br />
							Only those who have the complete message URL can decrypt/read the message which means that we can\'t give decrypted messages to third-parties or even to our own admins, even if we wanted to!<br /><br />
						
							Compared to many other similar services and projects - Temp.PM is actively developed, it has many security features that others don\'t have and it\'s compatible with almost every web browser and device.<br />
							</div>
							</div>
						
							<div class="well">
								<b>Features</b><br />
								&bullet; Double encryption with 256-bit AES (message content + file system)<br />
								&bullet; Secure deletion with 7-pass shred<br />
								&bullet; Forced HTTPS/TLS connection<br />
								&bullet; Self-destruction timer<br />
								&bullet; Bruteforce protection<br />
								&bullet; Optional custom password protection<br />
								&bullet; Dedicated server located in Europe<br />
								&bullet; Free &amp; open source<br />
								&bullet; No swap, no logs, no tracking, no backups, no ads!<br />
							</div>
						';

			/*

						echo '
							<div class="well">
								<b>Alternative domains</b><br />
							You can also use these domains. They all just redirect to temp.pm domain but might be easier to remember.<br />

								<div style="width: auto;">

									<div style="float: left; text-align: left; white-space: nowrap; width: 95px;">
										<input type="text" class="form-control form-url-domain" name="tmp_pm_url" onClick="this.setSelectionRange(0, 9999);" autocomplete="off" value="tmp.pm">
									</div>

									<div style="float: left; text-align: left; white-space: nowrap; width: 95px;">
										<input type="text" class="form-control form-url-domain" name="crypt_pm_url" onClick="this.setSelectionRange(0, 9999);" autocomplete="off" value="crypt.pm">
									</div>

									<div style="float: left; text-align: left; white-space: nowrap; width: 95px;">
										<input type="text" class="form-control form-url-domain" name="dont_re_url" onClick="this.setSelectionRange(0, 9999);" autocomplete="off" value="dont.re">
									</div>
						  
								</div>

								<br style="clear: left;" />

							</div>
						';
			*/

						/*
						// Print TOR hidden service address (TODO disabled temporarily)
						echo '
						<div class="well">
							<b>TOR address</b><br />
						Currently this just redirects to the clearnet address.
						<br />
						<input type="text" class="form-control form-url-bottom" name="tor" onClick="this.setSelectionRange(0, 9999);" autocomplete="off" value="http://'.SERVER_DOMAIN_TOR.'">
						<br />
						</div>';
						*/

echo '
	<div class="row">
		<div class="col-md-6">
	';
						// Print message stats and page hits (if stats path is found)
						if(file_exists(STATS_PATH_ABSOLUTE)){

							echo '
							<div class="well">
							<b>Statistics</b><br />
							Updated every 15 minutes.<br /><br />

							<table style="width: auto; border: 0px; white-space: nowrap;">
							';

							// Unread messages
							echo '
							<tr>
								<td style="padding-right: 5px;">&bullet; Unread / waiting:</td>
								<td>'.$count.'</td>
							</tr>
							';
						
							// Written in 1 / 7 / 30 days
							echo '
							<tr>
								<td style="padding-right: 5px;">&bullet; Written in 1 / 7 / 30 days:</td>
								<td>'.$new_messages_1d.' / '.$new_messages_7d.' / '.$new_messages_30d.'</td>
							</tr>
							';


							// Print page hits in the past 1 day
							echo '
								<tr>
									<td style="padding-right: 5px;">&bullet; Page hits in 24 hours:</td>
									<td>'.$page_hits_1d.'</td>
								</tr>
							';

							// Spacing to make both stats divs as tall
							//echo '<tr><td><br /></td></tr>';

						// End of message stats table
						echo '
							</table>
							</div>
						';
					}
					
		echo '</div>';
	echo '<div class="col-md-6">';

						// Server status
						echo '
							<div class="well">
							<b>Server status</b><br />
							Updated on page load.<br /><br />
							
							<table style="width: auto; border: 0px; white-space: nowrap;">
						';

				/*
						// Uptime
						echo '
							<tr>
								<td style="padding-right: 5px;">&bullet; Uptime:</td>
								<td>'.getServerUptime().'</td>
							</tr>
						';
				*/

						// Load avg.
						echo '
							<tr>
								<td style="padding-right: 5px;">&bullet; Load:</td>
								<td>'.getSystemLoad().'</td>
							</tr>
						';
						
						// CPU usage
						echo '
							<tr>
								<td style="padding-right: 5px;">&bullet; CPU usage:</td>
								<td>'.getCPUusage().'</td>
							</tr>
						';

						// Memory usage
						echo '
							<tr>
								<td style="padding-right: 5px;">&bullet; Memory usage:</td>
								<td>'.getMemUsage().'</td>
							</tr>
						';

					/*
						// Server date & time
						echo '
							<tr>
								<td style="padding-right: 5px;">&bullet; Date &amp; time:</td>
								<td>'.date('j.n.Y - H:i').'</td>
							</tr>
						';
					*/
						
						/*
						// Check file system
						if(file_exists(MESSAGE_PATH_ABSOLUTE) && file_exists(STATS_PATH_ABSOLUTE)){
							$filesystem_status = 'OK';
						}
						else{
							$filesystem_status = 'Offline';
						}
						
						echo '
							<tr>
								<td style="padding-right: 5px;">&bullet; File system:</td>
								<td>'.$filesystem_status.'</td>
							</tr>
						';	
						*/
						
				/*	
						// Check TOR hidden service status
						if(pingServer('127.0.0.1', '9050', '1') != '-1'){
							$tor_status = 'Ok';
						}
						else{
							$tor_status = 'Offline';
						}
					

						// Check clearnet/HTTPS status
						if(pingServer('127.0.0.1', '443', '1') != '-1'){
							$clearnet_status = 'Ok';
						}
						else{
							$clearnet_status = 'Offline';
						}
							
						echo '
							<tr>
								<td style="padding-right: 5px;">&bullet; Clearnet:</td>
								<td>'.$clearnet_status.'</td>
							</tr>
						';

						echo '
							<tr>
								<td style="padding-right: 5px;">&bullet; TOR:</td>
								<td>'.$tor_status.'</td>
							</tr>
						';

				*/

						// End of server stats table
						echo '
							</table>
							</div>
							';
echo '
		</div>
	</div>
	';

						// Contact
						echo '
							<div class="well" id="contact">
								<b>Contact admins</b><br />
						';

		// TODO not in use anymore (was a good script though, the noscript option doesn't work with html code though sadly (was using img src with image of the email address)
		/*
								<script type="text/javascript" language="javascript">
									// Email obfuscator script 2.1 by Tim Williams, University of Arizona
									// Random encryption key feature by Andrew Moulden, Site Engineering Ltd
									// This code is freeware provided these four comment lines remain intact
									// A wizard to generate this code is at http://www.jottings.com/obfuscator/
									{ coded = "f9Ni.iN@pJj9Bi.d9f"
									  key = "hlrA95QT4t8FzawCHgoVpMmqDX3f7vPxsbSLnEiRNWuY0U1ZeGj6cydOJIk2BK"
									  shift=coded.length
									  link=""
									  for (i=0; i<coded.length; i++) {
									    if (key.indexOf(coded.charAt(i))==-1) {
									      ltr = coded.charAt(i)
									      link += (ltr)
									    }
									    else {     
									      ltr = (key.indexOf(coded.charAt(i))-shift+key.length) % key.length
									      link += (key.charAt(ltr))
									    }
									  }
									  document.write(""+link+"")
									}
								</script>

								<noscript>You need to enable JavaScript to view the contact info.</noscript>
		*/
					// Print contact email with HTML entities (converted via https://mothereff.in/html-entities)
					echo '

&#x74;&#x65;&#x6D;&#x70;&#x2E;&#x70;&#x6D;&#x20;&#x28;&#x61;&#x74;&#x29;&#x20;&#x72;&#x69;&#x73;&#x65;&#x75;&#x70;&#x20;&#x28;&#x64;&#x6F;&#x74;&#x29;&#x20;&#x6E;&#x65;&#x74;<br />
&#x74;&#x65;&#x6D;&#x70;&#x2E;&#x70;&#x6D;&#x20;&#x28;&#x61;&#x74;&#x29;&#x20;&#x70;&#x72;&#x6F;&#x74;&#x6F;&#x6E;&#x6D;&#x61;&#x69;&#x6C;&#x20;&#x28;&#x64;&#x6F;&#x74;&#x29;&#x20;&#x63;&#x6F;&#x6D;

						';

					// Print PGP key link
					echo '
								<br /><br />
								Our <a href="https://temp.pm/pgp.asc">PGP key</a> can be used for both addresses above.<br />

							</div>
						';

						// Donations
						echo '
						
							<div class="well">
								<span id="donate"><b>Donations</b></span><br />
									Temp.PM is 100% free to use but of course it costs us money to run it, so donations of any size are appreciated.<br /><br />

									BitCoins can be sent to the address that is shown in the page footer. QR code can be found <a href="'.BTC_DONATE.'.png">here</a>.<br /><br />

									Email us if you wish to use some other payment method.<br />
							</div>
						';
						
						// Print version & source code stuff //(if the files exist)
						if(file_exists('Temp.PM-source_code.zip')){
							echo '
								<div class="well">
							
								<b>Version & source code</b><br />
								Site is currently running on v'.VERSION.'. <br /><br />

								Git repository will be set up some day. In the mean time, the source code can be downloaded <a href="https://temp.pm/Temp.PM-source_code.zip">here</a> but it may not always be the most recent version, email us to make sure.<br />
								</div>
							';
						}

						echo '
							<div class="spacer">
								<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write a New Message</a>
							</div>
						';
//============================================================================================
					}
					// Error: File system is NOT mounted OR message reading is disabled from settings OR when mcrypt/openssl/shred/rm is NOT installed/working

					elseif(!file_exists(MESSAGE_PATH_ABSOLUTE) || !exec("shred --help") || !exec("rm --help") || !function_exists('openssl_encrypt') || !function_exists('openssl_decrypt') || !function_exists('mcrypt_create_iv')){
						echo '
							<div class="alert alert-danger">
								<b>Error!</b><br />
								File system is not mounted. If this error persists, please <a href="?about#contact">contact the admins.</a>
							</div>
						';
						
						echo '
							<div class="spacer">
								<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write a New Message</a>
							</div>
						';
//============================================================================================
					}
					// Error: Facebook (or some other service) trying to read the message

					elseif($user_agent_block_now == '1' && $query != "" && $query != "about" && $query != "canary" && $query != "blog" && $query != "hiddenmode"){

						echo '
							<div class="alert alert-warning">
								<b>Blocked!</b><br />
								For some reason you were detected as a bot. Try to open this link with a different web browser.
							</div>
						';
						
						echo '
							<div class="spacer">
								<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write a New Message</a>
							</div>
						';
//============================================================================================
					}
					// Error: Message is too long

					elseif(isset($_POST['create']) && strlen($note) > MESSAGE_MAX_LENGTH && MESSAGE_CREATION_ENABLED == "1"){

						echo '
							<div class="alert alert-danger">
								<b>Error!</b><br />
								Your message is too long! Max length is '.MESSAGE_MAX_LENGTH.' characters.
							</div>
						';
						
						echo '
							<div class="spacer">
								<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write a New Message</a>
							</div>
						';
//============================================================================================
					}
					// Error: Message not found or it has already been read

					elseif(!empty($d) && !empty($id) && !empty($p) && !file_exists(MESSAGE_PATH_ABSOLUTE."/$d/$id".".counter")){	
					
						echo '
							<div class="alert alert-danger">
								<b>Error!</b><br />
								'.DEFAULT_ERROR_MESSAGE.'
							</div>
						';

						echo '
							<div class="spacer">
								<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write a New Message</a>
							</div>
						';
						
//============================================================================================
					}
/*
					// Error: Message reading has been disabled

					elseif(!empty($d) && !empty($id) && !empty($p) && MESSAGE_READING_ENABLED != "1"){		
				
						echo '
							<div class="alert alert-danger">
								<b>Error!</b><br />
								Message reading has been disabled due to maintenance! Sorry for the inconvenience. Please try again later.
							</div>
						';

						echo '
							<div class="spacer">
								<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write a New Message</a>
							</div>
						';
					}
*/

//============================================================================================

/*
					// TODO
					// Error: Trying to read your own message that was just created
					}
					elseif(!empty($d) && !empty($id) && !empty($p) && $cpass_read == "d41d8cd98f00b204e9800998ecf8427e" && file_exists(MESSAGE_PATH_ABSOLUTE."/$d/$id".".counter") && !file_exists(MESSAGE_PATH_ABSOLUTE."/$d/$id".".pass") && MESSAGE_READING_ENABLED == "1" && $time_error == "1"){
						
						echo '
							<div class="alert alert-danger">
								<b>Error!</b><br />
								You just created this message! Wait 15 seconds and open the link again if you really need to read or destroy it.
							</div>
						';

						echo '
							<div class="spacer">
								<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write a New Message</a>
							</div>
						';
					}
*/

//============================================================================================

					// Read message (both types)

					elseif(!empty($d) && !empty($id) && !empty($p) > "" && file_exists(MESSAGE_PATH_ABSOLUTE."/$d/$id".".counter") && MESSAGE_READING_ENABLED == "1"){

						// Get the bruteforce counter value
						$message_counter_file = MESSAGE_PATH_ABSOLUTE."/"."$d"."/"."$id".".counter";
						$hits = file_get_contents($message_counter_file);
						
						// Delete message and bruteforce counter file if bruteforce threshold is exceeded (if not using custom password)
						if(!file_exists(MESSAGE_PATH_ABSOLUTE."/$d/$id".".pass")){
							if($hits >= BRUTEFORCE_THRESHOLD){

								// Run deletions in background process
								Proc_Close (Proc_Open (MESSAGE_DELETE_PARAMETERS." ".MESSAGE_PATH_ABSOLUTE."/$d/$id &", Array (), $foo));
								Proc_Close (Proc_Open (NORMAL_DELETE_PARAMETERS." $message_counter_file &", Array (), $foo));
								Proc_Close (Proc_Open (NORMAL_DELETE_PARAMETERS." ".MESSAGE_PATH_ABSOLUTE."/$d/$id".".iv &", Array (), $foo));
								Proc_Close (Proc_Open (NORMAL_DELETE_PARAMETERS." ".MESSAGE_PATH_ABSOLUTE."/$d/$id".".debug"." &", Array (), $foo)); // TODO debug
							}
							// Otherwise increment the bruteforce counter
							else{
								$hits++;
								file_put_contents($message_counter_file, $hits, LOCK_EX);
							}
						}
						
						// Get message file contents (encrypted data)
						$read_note = file_get_contents(MESSAGE_PATH_ABSOLUTE."/$d/$id");

						// Get IV from file (for decryption) and base64 decode it
						if(file_exists(MESSAGE_PATH_ABSOLUTE."/$d/$id".".iv")){
							$iv = base64_decode(file_get_contents(MESSAGE_PATH_ABSOLUTE."/$d/$id".".iv"));
						}
						
						// Decrypt message content
						if(file_exists(MESSAGE_PATH_ABSOLUTE."/$d/$id".".pass") && !empty($cpass_read)){

							// Decrypt message
							$decrypted_note = decryptData($read_note, "$p"."$cpass_read", $iv);

							// Wipe variables
							$read_note = '';
							$iv = '';

							// Get the decryption check part of the note
							$decrypt_check_string = "$p"."$cpass_read";
							$decrypted_note_end_for_checking = substr($decrypted_note, (strlen($decrypt_check_string) * -1));
							
						}
						elseif(!file_exists(MESSAGE_PATH_ABSOLUTE."/$d/$id".".pass")){

							// Decrypt message
							$decrypted_note = decryptData($read_note, $p, $iv);

							// Wipe variables
							$read_note = '';
							$iv = '';
						
							// Get the decryption check part of the note
							$decrypt_check_string = $p;
							$decrypted_note_end_for_checking = substr($decrypted_note, (strlen($decrypt_check_string) * -1));
						}
						
						
						// If decryption is successful -> print message TODO poista 2015
						if($decrypted_note_end_for_checking == $decrypt_check_string && !empty($decrypted_note_end_for_checking) && !empty($decrypt_check_string)){
							
							// Run deletions in background process
							Proc_Close (Proc_Open (MESSAGE_DELETE_PARAMETERS." ".MESSAGE_PATH_ABSOLUTE."/$d/$id &", Array (), $foo));
							Proc_Close (Proc_Open (NORMAL_DELETE_PARAMETERS." $message_counter_file &", Array (), $foo));
							Proc_Close (Proc_Open (NORMAL_DELETE_PARAMETERS." ".MESSAGE_PATH_ABSOLUTE."/$d/$id".".pass"." &", Array (), $foo));
							Proc_Close (Proc_Open (NORMAL_DELETE_PARAMETERS." ".MESSAGE_PATH_ABSOLUTE."/$d/$id".".iv"." &", Array (), $foo));
							Proc_Close (Proc_Open (NORMAL_DELETE_PARAMETERS." ".MESSAGE_PATH_ABSOLUTE."/$d/$id".".debug"." &", Array (), $foo)); // TODO debug
							
							// Strip the decryption check code from the note
							$decrypted_note = substr($decrypted_note, 0, (strlen($decrypt_check_string) * -1));

							/*
							// Redirect to error page (use sessions: redirect_to_page = error_read_general) TODO
							else{
								
							}
							*/

							// Print countdown timer (JavaScript)
							echo $js_timer;

//_______________________

						// Kill mode: delete message (don't show it, just print info message)
						if ($mode_query == 'K' || $mode_query == 'D') {
							
							// Wipe the message variable
							$decrypted_message = '';

							// Print delete successful message
							echo '
								<div class="alert alert-success">
									<b>Message deleted successfully!</b><br />
									&bullet; It has now been securely deleted, as you requested.<br />
								</div>
							';

						} else {
//_______________________

								echo '
									<div class="alert alert-warning">
										<b>Attention!</b><br />
										<div style="text-align: justify;">
											&bullet; This message has now been securely deleted. You can\'t open the URL again or refresh this page!<br />
											&bullet; If you need to save the message contents somewhere, please make sure you use appropriate encryption.<br />
								';

								// Get the wipe time/date for non-JS users (TODO somehow this requires 3 hours instead of 1 ??? time zone thing???)
								$wipe_date = time() + (60 * 60 * 4);
								$wipe_date = DateTime::createFromFormat('U', $wipe_date);
								//$wipe_date = $wipe_date->format('j.n.Y - H:i');
								$wipe_date = $wipe_date->format('H:i A');

								echo '&bullet; The contents of this page will disappear in 1 hour. Based on server time it will happen at '.$wipe_date.'.';

					/*
								// Print the countdown timer (no JavaScript)
								echo '
									<noscript>&bullet; The contents of this page will disappear in 1 hour. Based on server time it will happen at '.$wipe_date.'.</noscript>
								';
							
								// Print the countdown timer (JavaScript)
								echo "<script>
										document.write('&bullet; The contents of this page will disappear in 1 hour. Time left: <span id=\"jsCountDown\"></span>');
									</script>

										</div>
									</div>
								";

					*/

								echo '
										</div>
									</div>';

						/*
								// Convert URLs to links to the external website warning page only when hidden mode is not being used
								if($mode_query != "HH"){
									$decrypted_note = convertURLs($decrypted_note); // TODO fix when using links like: "https://temp.pm/<br /> "
								}
						*/

								// If hidden mode is enabled -> convert the message
								if($mode_query == "HH"){

									// Doesn't work with http:// content TODO 
									//$decrypted_note = preg_replace('([a-zA-Z.,!?0-9]+(?![^<]*>))', '<span class="hidden-msg">$0</span>', $decrypted_note);

									// Fix line breaks (part 1/2)
									$decrypted_note = str_replace('<br />', '<br>', $decrypted_note);

									// Replace spaces and line breaks with spans
									$decrypted_note = str_replace(' ', '</span> <span class="hidden-msg">', $decrypted_note);
									$decrypted_note = str_replace('<br>', '</span><br><span class="hidden-msg">', $decrypted_note);

									// Add spans to both ends of the string
									$decrypted_note = '<span class="hidden-msg">'.$decrypted_note.'</span>';

									// Fix line breaks (part 2/2)
									$decrypted_note = str_replace('<br>', '<br />', $decrypted_note);

									// Print hidden mode info
									echo '
										<div class="alert alert-warning">
											<b>Hidden mode is enabled!</b><br />
											<div style="text-align: justify;">
												&bullet; Hover your mouse pointer over the black bars to reveal the content.<br />
												&bullet; When using a mobile device, you may need to select and copy the black bars to a text editor app instead.<br />
												&bullet; In some text editors, you may also need to change the text colour or text background colour.<br />
											</div>
										</div>
									';

								}
							
								// Print the decrypted message content
								echo '
									<div class="panel panel-default panel-message1">
		  								<div class="panel-body panel-message2">
											'.$decrypted_note.'

										</div>
									</div>
								';

								// Wipe the decrypted message variable
								$decrypted_note = '';

								// TODO include debugger if it's enabled
								if (file_exists(MESSAGE_PATH_ABSOLUTE."/$d/$id".".debug")) {
										include('debug_debug.php');
								}

							} // end: kill mode (and hidden mode + normal mode as well)

						/*
							// Reset variable to allow random generation below
							$stat_file_exists = '1';
	
							// Generate random filename for stats and make sure it doesn't exist
							while($stat_file_exists == "1"){
	
								// Generate random 64bit string
								$stats_file = generateRandom("64");
	
								// Filename already exists -> new one will be generated
								if(file_exists(STATS_PATH_ABSOLUTE."/read/30/$stats_file") || strlen($stats_file) < "64"){
									$stat_file_exists = "1";
								}
								else{
									// Stat file name is unique
									$stat_file_exists = "0";
		
									// Create stats files
									touch(STATS_PATH_ABSOLUTE."/read/1/$stats_file");
									touch(STATS_PATH_ABSOLUTE."/read/7/$stats_file");
									touch(STATS_PATH_ABSOLUTE."/read/30/$stats_file");
								} // end of else (filename already exists -> new one will be generated)
							} // end of while stat_file_exists == 1
						*/						

							// Print footer
							echo '
								<div class="spacer">
									<a href="'.$relative_path_to_script.'" class="btn btn-default"> Clear This Page / Write a New Message</a>
								</div>
								';	
						
						} // end: decryption is successful
						// _______________________
						
						// Incorrect password (normal or custom):
						else{
							
							// Incorrect custom password
							
							if(file_exists(MESSAGE_PATH_ABSOLUTE."/$d/$id".".pass")){
							
								// If custom password has been entered	
								if(!empty($cpass_read)){
								
									// Get the bruteforce counter value
									$message_counter_file = MESSAGE_PATH_ABSOLUTE."/"."$d"."/"."$id".".counter";
									$hits = file_get_contents($message_counter_file);
						
									// Check the threshold
									if($hits >= BRUTEFORCE_THRESHOLD){

										// Run deletions in background process
										Proc_Close (Proc_Open (MESSAGE_DELETE_PARAMETERS." ".MESSAGE_PATH_ABSOLUTE."/$d/$id &", Array (), $foo));
										Proc_Close (Proc_Open (NORMAL_DELETE_PARAMETERS." $message_counter_file &", Array (), $foo));
										Proc_Close (Proc_Open (NORMAL_DELETE_PARAMETERS." ".MESSAGE_PATH_ABSOLUTE."/$d/$id".".pass"." &", Array (), $foo));
										Proc_Close (Proc_Open (NORMAL_DELETE_PARAMETERS." ".MESSAGE_PATH_ABSOLUTE."/$d/$id".".iv"." &", Array (), $foo));
										Proc_Close (Proc_Open (NORMAL_DELETE_PARAMETERS." ".MESSAGE_PATH_ABSOLUTE."/$d/$id".".debug"." &", Array (), $foo)); // TODO debug
									}
									// Otherwise increment the bruteforce counter
									else{
										$hits++;
										file_put_contents($message_counter_file, $hits, LOCK_EX);
									}
								
									// Print error message
									echo '
									<div class="alert alert-danger">
										<b>Error!</b><br />
										Incorrect password. Please try again.
									</div>
										';	
								} // end of if (cpass has been entered)
								
								
								// Print custom password form
								echo '
									<div class="alert alert-warning">
										<b>Password required!</b><br />
						
											&bullet; This message is password protected.<br />
											&bullet; Self-destruction will occur after '.BRUTEFORCE_THRESHOLD.' failed attempts.<br />
									</div>

									<form action="'.$relative_path_to_script.'?'.$query.'" method="post" id="cpass-form" autocomplete="off">

										<div class="spacer">
											<input type="password" style="width: 150px !important;" name="cpass_read" class="form-control" maxlength="'.CUSTOM_PASS_MAX_LENGTH.'" placeholder="Password" autofocus="autofocus" autocomplete="off" required>
										</div>

										<div class="spacer">
											<input type="submit" class="btn btn-default" name="cpass_submit" value="Unlock">
										</div>
								
									</form>
								';
									
							} // end of if (.pass file exists)
							// Incorrect password (non-custom password)
							else{
							
								echo '
									<div class="alert alert-danger">
										<b>Error!</b><br />
										'.DEFAULT_ERROR_MESSAGE.'
									</div>
								';

								echo '
									<div class="spacer">
										<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write a New Message</a>
									</div>
								';
							} // end of else (incorrect non-custom pass)
							
						} // end of else (incorrect pass, both types)
						
//============================================================================================
					}
/*
					// Error: Message creation failed due to page refresh/resubmit (on message URL page)

					elseif(isset($_POST['create']) && !empty($note) && !empty($random) && MESSAGE_CREATION_ENABLED == '1' && !file_exists(STATS_PATH_ABSOLUTE."/random/$random")){

						echo '
							<div class="alert alert-danger">
								<b>Error!</b><br />
								Message creation failed! Please click on the button below and rewrite the message. Sorry for the inconvenience.<br />
							</div>
						';
						
						echo '
							<div class="spacer">
								<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write a New Message</a>
							</div>
						';

//============================================================================================
					}
*/
					// Create a new message

					elseif(isset($_POST['create']) && !empty($note) && !empty($random) && MESSAGE_CREATION_ENABLED == '1' && file_exists(STATS_PATH_ABSOLUTE."/random/$random")){ 
					
					// Validate TTL value (defaults to 3 days)
					if (preg_match('/[^A-Za-z0-9]/', $ttl) or empty($ttl)) {
							$ttl = '3';
					}
					
					// Encode some characters to HTML equivalens (prevents using HTML in messages)
					$note = htmlentities($note, ENT_QUOTES | ENT_HTML401);

					// Convert line breaks
					$note = nl2br($note);
					
					// Randomize message ID and password lengths (if needed)
					if($filename_min_length == $filename_max_length) {
						$file_random_len = $filename_max_length;
					}
					else {
						$file_random_len = mt_rand($filename_min_length, $filename_max_length);
					}

					if($pass_min_length == $pass_max_length) {
						$pass_random_len = $pass_max_length;
					}
					else {
						$pass_random_len = mt_rand($pass_min_length, $pass_max_length);
					}
					
					// Generate random password
					$pass = generateRandom($pass_random_len);

					// Generate IV (for encryption)
					$iv = generateSalt(16);

					// Add password inside the encrypted message (for decryption verification purposes) and encrypt content
					//$note = "$note"."$decrypt_check_string"; // TODO poista 2015
					$note = "$note"."$pass"."$cpass_create";
					$message_encrypted = encryptData($note, "$pass"."$cpass_create", $iv);
					
					// Wipe note variable (just in case)
					$note = '';

					// Base64 encode IV
					$iv = base64_encode($iv);
					
					// Generate random filename and make sure it doesn't exist
					while($message_exists == '1'){
						
						// Generate random filename
						$file = generateRandom($file_random_len);
						
						// Filename already exists -> new one will be generated
						if(file_exists(MESSAGE_PATH_ABSOLUTE."/$ttl/$file") || file_exists(MESSAGE_PATH_ABSOLUTE."/$ttl/$file".".counter") || file_exists(MESSAGE_PATH_ABSOLUTE."/$ttl/$file".".pass") || file_exists(MESSAGE_PATH_ABSOLUTE."/$ttl/$file".".iv")){
							
							$message_exists = '1';
						}
						else{
							// Exit the while loop
							$message_exists = '0';
							
							// Message file path
							$message_file = MESSAGE_PATH_ABSOLUTE."/$ttl/$file";
							$message_counter_file = "$message_file".".counter";

							// IV file path
							$iv_file = "$message_file".".iv";
							
							// Write message to file
							file_put_contents($message_file, $message_encrypted, LOCK_EX);

							// Write IV to file
							file_put_contents($iv_file, $iv, LOCK_EX);
							
							// Create empty bruteforce protection counter
							file_put_contents($message_counter_file, '0', LOCK_EX);
							
							// Create custom pass check file
							if(!empty($cpass_create)){
								touch(MESSAGE_PATH_ABSOLUTE."/$ttl/$file".".pass");
							}

							// Delete the random file (in background process) to prevent resubmits
							$random_file = STATS_PATH_ABSOLUTE."/random/"."$random";
							Proc_Close (Proc_Open (NORMAL_DELETE_PARAMETERS." $random_file &", Array (), $foo));

							// Wipe POST contents to prevent resubmits TODOJEA CHECKCHECK CHECK CHECK
							unset($_POST);
							$_POST = array();
							
						} // end: else (proceed with saving)
					} // end: while note_exists == 1 (filename already exists -> new one will be generated)

//***********************************************
//***********************************************
					// Print message URLs etc.
						echo '
							<div class="alert alert-success">
								<b>Message was created successfully!</b><br />

								<div style="text-align: justify;">
									&bullet; Copy the URL below and send it to the recipient.<br />
									&bullet; The message will self-destruct after being read or after the timer expires if the message hasn\'t been read in time.<br />
									&bullet; In case you need to delete the message you just wrote, use the corresponding button.<br />
						';

						// Get the wipe time/date for non-JS users (TODO somehow this requires 3 hours instead of 1 ??? time zone thing???)
						$wipe_date = time() + (60 * 60 * 4);
						$wipe_date = DateTime::createFromFormat('U', $wipe_date);
						//$wipe_date = $wipe_date->format('j.n.Y - H:i');
						$wipe_date = $wipe_date->format('H:i A');

						echo '&bullet; The contents of this page will disappear in 1 hour. Based on server time it will happen at '.$wipe_date.'.';

				/*
						// Print countdown timer (JavaScript)
						echo $js_timer;

						// Print the countdown timer (no JavaScript)
						echo '
							<noscript>&bullet; The contents of this page will disappear in 1 hour. Based on server time it will happen at '.$wipe_date.'.</noscript>
						';
							
						// Print the countdown timer (JavaScript)
						echo "<script>
								document.write('&bullet; The contents of this page will disappear in 1 hour. Time left: <span id=\"jsCountDown\"></span>');
							</script>
						";

				*/

						echo '
								</div>
								
							</div>

							<div class="well">
						';


// Set clearnet URL to variable
						$message_url_to_print = "https://".SERVER_DOMAIN_NORMAL."$relative_path_to_script"."?".$file."-".$ttl."-".$pass;

						// Add mode query/switch to message URL
						$message_url_to_print_clearnet = "$message_url_to_print"."-N";

						// Print clearnet message URL
						echo '<b>URL</b>
								<input type="text" class="form-control form-url-normal" name="noteurl1" onClick="this.setSelectionRange(0, 9999);" autocomplete="off" value="'.$message_url_to_print_clearnet.'">
						';

						// Using TOR?
						if($is_tor == "1"){

							// Set TOR URL to variable
							$message_url_to_print_tor = "http://".SERVER_DOMAIN_TOR."$relative_path_to_script"."?".$file."-".$ttl."-".$pass;

							// Add mode query/switch to message URL
							$message_url_to_print_tor = "$message_url_to_print_tor"."-N";					

							//echo '</div>';
							
/*
// TOR disabeld temporarily
							// Print TOR URL
							echo '<div class="well">
							
									<b>URL (TOR users only)</b>
									<input type="text" class="form-control form-url-normal" name="noteurl2" onClick="this.setSelectionRange(0, 9999);" autocomplete="off" value="'.$message_url_to_print_tor.'">';
*/
						} // end: using TOR?
						
echo '</div>';
						
						echo '
							<div class="spacer">
								<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write Another Message</a> &nbsp;
						<a href="'.$message_url_to_print.'-K" class="btn btn-default"> Delete This Message</a>
							</div>
						';

						// Generate random filename for stats and make sure it doesn't exist
						while($stat_file_exists == "1"){
	
							// Generation (based on random 64bit string + UNIX date -> hash to SHA256)
							$stats_file = generateRandom("64");
	
							// Filename already exists -> new one will be generated
							if(file_exists(STATS_PATH_ABSOLUTE."/30/$stats_file") || strlen($stats_file) < "64"){
								$stat_file_exists = "1";
							}
							else{
								// Stat file name is unique
								$stat_file_exists = "0";
		
								// Create stats files
								touch(STATS_PATH_ABSOLUTE."/1/$stats_file");
								touch(STATS_PATH_ABSOLUTE."/7/$stats_file");
								touch(STATS_PATH_ABSOLUTE."/30/$stats_file");
							} // end of else (filename already exists -> new one will be generated)
						} // end of while stat_file_exists == 1
												
						// Wipe variables
						$id = "";
						$d = "";
						$p = "";
						
						$file = "";
						$ttl = "";
						$pass = "";
						
						$stats_file = "";
						$message_url_to_print = "";
						$message_url_to_print_tor = "";

//***********************************************
//***********************************************


					// Header/session thing (to prevent form resubmits)
					//header("Location: "."$server_url"."$relative_path_to_script"."?create");
					//exit();	
					}
//============================================================================================
					// TODOSESSIONFIX related thing (TODO not needed ???)
					// Redirect to main page if ?create is being used
					elseif($query == "create"){
							header("Location: "."$server_url"."$relative_path_to_script");
							exit();
//============================================================================================
					}
					// Error: Message not found or it has already been read

					elseif(!empty($d) && !empty($id) && !empty($p) && !file_exists(MESSAGE_PATH_ABSOLUTE."/$d/$id".".counter")){
					
							echo '
								<div class="alert alert-danger">
									<b>Error!</b><br />
									'.DEFAULT_ERROR_MESSAGE.'
								</div>
							';
						
							echo '
								<div class="spacer">
									<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write a New Message</a>
								</div>
							';
//============================================================================================

					}
					// Error: Maintenance mode enabled

					elseif(MAINTENANCE_MODE_ENABLED != "0" || MESSAGE_READING_ENABLED != "1" || MESSAGE_CREATION_ENABLED != "1"){
						
						// Title (if needed)
						//	<b>We\'ll be back soon!</b><br />

						echo '
							<div class="alert alert-warning">
								We are performing some maintenance. Sorry for the inconvenience. Please try again later.
							</div>
						';
						
						echo '
							<div class="spacer">
								<a href="'.$relative_path_to_script.'" class="btn btn-default"> Write a New Message</a>
							</div>
						';
//============================================================================================
					}
					// Main page
					else{
					?>
					
					<form action="<?php echo $relative_path_to_script; ?>" method="post" id="message-form" autocomplete="off">

					<div class="well">
						<b>How to use this?</b><br />
						1. Write a message<br />
						2. Set the timer. It will trigger the auto self-destruction if the message won't be read in time<br />
						3. Click "Create Message"<br />
						4. Copy the URL that will be generated for you and send it to the message recipient<br />
					</div>
						
					<textarea name="note" id="note" class="form-control form-message" rows="8" maxlength="<?php echo MESSAGE_MAX_LENGTH; ?>" required="required" autofocus="autofocus" style="margin-bottom: 20px;"></textarea>

					<div class="spacer">
						<input type="submit" class="btn btn-default" name="create" id="create" value="Create Message">
					</div>

					<div class="spacer">
						<select name="ttl" class="form-control">

							<optgroup label="Minutes">
								<option value="15m">15 minutes</option>
								<option value="30m">30 minutes</option>
								<option value="45m">45 minutes</option>
							</optgroup>

							<optgroup label="Hours">
								<option value="1h">1 hour</option>
								<option value="6h">6 hours</option>
								<option value="12h">12 hours</option>
							</optgroup>

							<optgroup label="Days">
								<option value="1">1 day</option>
								<option value="3" selected>3 days</option>
								<option value="7">7 days</option>
							</optgroup>

							<optgroup label="Months">
								<option value="30">1 month</option>
								<option value="60">2 months</option>
							</optgroup>

						</select>
					</div>

					<?php

					// Form id using the random hit counter
					touch(STATS_PATH_ABSOLUTE."/random/$hit_file");
					echo '<input type="hidden" name="random" value="'.$hit_file.'">';

					// Show custom password input if it's enabled
					if(MESSAGE_CUSTOM_PASS_ENABLED == "1"){
						echo '
							<div class="spacer">
								<input type="password" class="form-control" style="width: 150px !important;" name="cpass_create" autocomplete="off" maxlength="'.CUSTOM_PASS_MAX_LENGTH.'" placeholder="Password (optional)">
							</div>
						';
					}
					?>
					
					</form>
					
					<?php
					} // end of main page
//============================================================================================
					?>
					
				</div>
		</div>
	</div>

	<div class="container">
		<div class="row bottom2" style="margin-left: -12px; margin-right: -12px; margin-top: -15px; margin-bottom: -15px;">
			<div class="col-xs-6" style="text-align: left;">

				<?php
				// Print copyright
				echo '&copy; temp.pm 2011 &ndash; '.date('Y');
				?>

			</div>
			<div class="col-xs-6" style="text-align: right;">

				<?php
				// Print BTC donation address
				echo 'BTC: '.BTC_DONATE;
				?>

			</div>
		</div>
	</div>

  </body>
</html>
