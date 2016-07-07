<?php
// Replace your-domain-here.com with your own domain!

// Disable PHP error reporting
error_reporting(0);

// Redirect to main page if client is trying to access the error page directly
if ($_SERVER["REQUEST_URI"] == '/error.php') {
	header('Location: http://your-domain-here.com/');
	exit;
}

// Twitter Bootstrap version to use
define('BOOTSTRAP_VERSION', '3.3.5');

// Wipe variables
$error_code = '';
$error_msg_short = '';
$error_msg_long = '';

// Get the error code
$error_code = $_SERVER['REDIRECT_STATUS'].'';

//-----------------

// Error messages:

// 401
if($error_code == '401'){
	$error_msg_short = 'Unauthorized';
	$error_msg_long = '
						<b>Possible causes:</b><br />
						&#9679; Directory listing is disabled<br />
						&#9679; Incorrect login<br />
					';
}
// 403
elseif($error_code == '403'){
	$error_msg_short = 'Forbidden';
	$error_msg_long = '
						<b>Possible causes:</b><br />
						&#9679; We are performing a quick maintenance, try again soon!<br />
						&#9679; Insufficient file permissions<br />
						&#9679; Directory listing has been disabled<br />
					';
}
// 404
elseif($error_code == '404'){
	$error_msg_short = 'File Not Found';
	$error_msg_long = '
						<b>Possible causes:</b><br />
						&#9679; The file has been deleted or moved<br />
						&#9679; There is a typo in the URL<br />
					';
}
// 410
elseif($error_code == '410'){
	$error_msg_short = 'Resource Gone';
	$error_msg_long = 'There was a page here, but forwarding address is missing.<br />';
}
// 500
elseif($error_code == '500'){
	$error_msg_short = 'Internal Server Error';
	$error_msg_long = '
						<b>Possible causes:</b><br />
						&#9679; Invalid .htaccess configuration<br />
						&#9679; Server overload<br />
						&#9679; A script stops with an error<br />
					';
}
// 503
elseif($error_code == '503'){
	$error_msg_short = 'Service Unavailable';
	$error_msg_long = '
						<b>Possible causes:</b><br />
						&#9679; Server overload<br />
						&#9679; Connection refused<br />
					';
}
// 505
elseif($error_code == '505'){
	$error_msg_short = 'HTTP Version Not Supported';
	$error_msg_long = 'The requested HTTP protocol version is not supported.';
}
// 
else{
	$error_code = '';
	$error_msg_short = 'Something went wrong!';
	$error_msg_long = '';
}

//-----------------


//=====================================================================================================
?>

<!DOCTYPE html>
<html>
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Error <?php echo $error_code; ?> - <?php echo $error_msg_short; ?></title>
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/<?php echo BOOTSTRAP_VERSION; ?>/css/bootstrap.min.css" />    
	<link rel="shortcut icon" href="https://your-domain-here.com/favicon_error.ico" />

<style type="text/css">
body {
	background-color: #FAFAFA;
	margin-top: 40px;
	margin-bottom: 20px;
	margin-left: 0px;
	margin-right: 0px;
}
h1, h2, h3, h4 {
	text-align: center;
	font-weight: bold;
}
.main {
	word-wrap: break-word;
	word-break: break-all;
	white-space: nowrap;
	min-width: 300px;
	width: auto;
	display: table;
	padding: 19px 29px 29px;
	margin: 45px auto 20px;
	background-color: #fff;
	border: 1px solid #e5e5e5;
	-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
			border-radius: 5px;
	-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
		-moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
			box-shadow: 0 1px 2px rgba(0,0,0,.05);
}
.title {
	text-align: center;
	font-size: 180%;
	font-weight: bold;
	margin-top: 15px;
}
.content {
	text-align: left;
	display: table;
	margin: 0 auto;
}
a:active, a:focus, a:visited, a:link {
	color: #000000;
	text-decoration: underline;
}
a:hover {
	color: #333333;
	text-decoration: none;
}	
</style>

  </head>
  <body>

	<div class="main">

		<div class="title">
			Error <?php echo $error_code; ?> - <?php echo $error_msg_short; ?>
		</div>

		<?php

		// Print long error message if it's set
				if (!empty($error_msg_long)) {
					echo '<hr />';
					echo '<div class="content">';
					echo $error_msg_long;
					echo '</div>';
				}
		?>

	</div>

  </body>
</html>
