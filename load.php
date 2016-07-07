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

// load.php: redirects to the message reading and also prevents bots from reading the message contents

// SETTINGS:

// PHP error reporting and display (more info: https://secure.php.net/manual/en/errorfunc.configuration.php)
error_reporting(0);

// Server domain names (normal/clearnet and TOR)
define('SERVER_DOMAIN_NORMAL', 'temp.pm');
// TODO TOR domain (remember to add http/https checks later on)

// Redirection wait time in seconds
$redirect_time = '0';

// END OF SETTINGS
//============================================================================================

// Reset some variables
$error = '0';
$redirect_ok = '0';

// Get query string (URL) + decode it
$url = $_SERVER["QUERY_STRING"];
$url = rawurldecode($url);

// Convert/strip some characters just in case
//$url = str_replace('%3A', ':', $url);
//$url = str_replace('%2F', '/', $url);
$url = str_replace('/', '', $url);
$url = str_replace(':', '', $url);
$url = str_replace('<', '', $url);
$url = str_replace('>', '', $url);
$url = str_replace('"', '', $url);
$url = str_replace('javascript', '', $url);
$url = str_replace('JavaScript', '', $url);

// Redirect to main site if there's no URL
if($url == ""){
	header("Location: https://".SERVER_DOMAIN_NORMAL."/");
	exit;
}

// If URL contains all necessary parts it's valid
if(substr_count($url, "-") > "1" && substr_count($url, "-") < "4" && strlen($url) > "4"){

	// Explode URL query parts
	$query_part = explode('-', $url);
	$id = $query_part[0];
	$d = $query_part[1];
	$p = $query_part[2];

	// Convert switch to uppercase
	$query_part[3] = strtoupper($query_part[3]);
	
	// Hidden mode URL
	if($query_part[3] == "H"){
		$url = $id."-".$d."-".$p."-HH";
	}
	// Normal mode URL
	else{
		$url = $id."-".$d."-".$p;
	}

	// Set URL and status: ok
	$url = 'https://'.SERVER_DOMAIN_NORMAL.'/?'.$url;
	$redirect_ok = '1';
	
	// Info texts
	$loading_message = 'Please wait - ';
	//$loading_button_message = 'Click Here If Nothing Happens';
	$loading_button_message = 'Write a New Message';

	// HTTP header for redirecting
	//header('Refresh: '.$redirect_time.'; URL='.$url.'');
}
// If invalid URL -> error (redirect)
else{
	// Reset URL and status: error
	$url = 'https://'.SERVER_DOMAIN_NORMAL.'/';
	$redirect_ok = '0';

	// Info texts
	//$loading_message = 'Error! Invalid URL';
	//$loading_button_message = 'Click Here To Continue';

	// Redirect to main page
	header('Location: '.$url.'');
	exit;
}

//____________________________________________________________________

?>
<!DOCTYPE html>
<html>
  <head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php

	// HTML meta header for redirecting to the message reading page after X seconds
	if($redirect_ok == "1"){
		echo '	<meta http-equiv="refresh" content="'.$redirect_time.'; URL='.$url.'">';
	}
	?>
	<meta name="application-name" content="Temp.PM">
	<meta name="author" content="Temp.PM">
	<meta name="description" content="Temporary Private Message service with encryption, self-destruction and many other security features.">
	<meta name="robots" content="noindex, nofollow">
	
	<title>Temp.PM - Temporary Private Message</title>

	<link rel="shortcut icon" href="favicon.ico" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">   

<style type="text/css">
body {
	background-color: #ECECEC;
	background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC4AAAAXCAAAAABRpyKOAAAB5klEQVR4AU3SSZrEJgwFYO5/UjSiyVlHgq582QCt+i3jp16aKXTSldjSaLPnIQSNUMCTZQBexXDrtFojYnylm6M99Ko4GqeLp5Ka4mgEXa3FRKv3k+1x+nhrkt6ZY172NGosBcoK4qvVpgdHlQJWlcCW7PNP1zqEUakArWXqRiC9IlUZHUaJtKezcsVBsQjVCCHG59mnrKTzgvZPx4kVqXPMSCFNe36T54HNcXnG0wqyJOZRuaHEVwJkaS83tpuv/6eRFl4PEtF9vILbV9qo33eMnmDVz2IYTzie2MLbZxXBqdH89IwL44vlNN5nMi5IN58Tjmw/Da0jg9GyVhdR6ksEG0mzkvwvIeK5iY0XXzbeIgZefyLGPV0HKGdQeCp486L5gRVbW0u999GfTt1Sn6FQl532ArIwuppQq4LwRD6dkc6oxhr36k6LYKS8sMm/T7b+9OGTLggUE5Vk+XLe7DGAzJVt9p+GlxTdTMkzaqVP/ygFrnLCdj9NrNzeLV2oC862rFfgE/N41fn7iNFImX5nfG9QjkCLLPzdn0kjzxzwRGs89QXS7UetiaQ5et2/0+58W2/Lr7XO+eZ3NUIvC/ep6+35coIJ4v0Pjp5pZmuuf3KRRnj+9cc7dlSXp3H09vj8RaD/Anbs2k1HGtFlAAAAAElFTkSuQmCC');
	background-repeat: repeat;
}

<?php 
// Include CSS directly to make page loading more elegant (and avoid issues with TOR)
include('style.min.css')
?>

</style>

  </head>
  <body>

	<?php
		echo '
				<div class="container">
					<div class="panel panel-default panel-background">
						<div class="panel-heading">

<table style="width: 100%; border: 0px;">
	<tr>
		<td style="width: 50%; border: 0px; text-align: left;">

			<a href="'.$url.'" class="sitetitle"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAAe0lEQVR4AWMAAfWQ7Pt+C0E48w6IBmH1EHUGEAQhlar///9jYgYVBihwr/q/4RkmZnCHKWgAqd8DxDtgGGwGQwNMQd7//7f/v0LFIAV5CAWYAF3BbiC8DiEhmDwTIJg8EzBcQNgEKitouPIfC4QHtRSDF0MxVugFlGMAANVoOrjrWwQpAAAAAElFTkSuQmCC" alt="" class="site-logo-img"> 
			
			<b><span style="font-size: 105%;">Temp.PM</span></b><span style="font-size: 95%;">&nbsp;-&nbsp;Temporary Private Message</span></a>

		</td>
		<td style="width: 50%; border: 0px; text-align: right;">
			
			<a href="?hiddenmode" class="btn btn-default btn-xs">&nbsp;Hidden Mode?&nbsp;</a>&nbsp;
			<a href="?blog" class="btn btn-default btn-xs">&nbsp;Blog&nbsp;</a>&nbsp;
			<a href="https://twitter.com/temp_pm" target="_blank" class="btn btn-default btn-xs">&nbsp;Twitter&nbsp;</a>&nbsp;
			<a href="?canary" class="btn btn-default btn-xs">&nbsp;Canary&nbsp;</a>&nbsp;
			<a href="?about" class="btn btn-default btn-xs">&nbsp;About&nbsp;</a>
			
		</td>
	</tr>
</table>
		'; // Copyright text would go here
		?>

			</div>

			<div class="panel-body">

				<div class="alert alert-warning">
					<b>Loading!</b><br />

					<?php

					// Print loading text
					echo $loading_message;
					
					if($redirect_ok == '1'){
						echo ' <a href="'.$url.'" class="yellow">Click here if nothing happens</a>'; 
					}
					?>

				</div>

<div class="spacer">
						<a href="/" class="btn btn-default"> <?php echo $loading_button_message; ?></a>
					</div>
			</div>
		</div>

	<div class="container">
		<div class="row bottom2" style="margin-left: -27px; margin-right: 3px; margin-top: -15px; margin-bottom: -15px;">

			<div class="col-xs-6" style="text-align: left;">
				&copy; temp.pm 2011 &ndash; 2015
			</div>

			<div class="col-xs-6" style="text-align: right;">
				BTC: 1EeprKVy4LkwDr816FEKUEYdux2XBe4FHS
			</div>
		</div>
	</div>

	
  </body>
</html>
