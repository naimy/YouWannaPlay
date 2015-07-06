<?php
error_reporting(0);
@set_time_limit(3);

$lines_array = file("options/options.txt");

$search_string = "backgroundAnimate";
foreach($lines_array as $line) {
    if(strpos($line, $search_string) !== false) {
        list(, $new_str) = explode("=", $line);
    
    }
}
$backgroundOption = trim($new_str);

$search_string = "playMusic";
foreach($lines_array as $line) {
    if(strpos($line, $search_string) !== false) {
        list(, $new_str) = explode("=", $line);
    
    }
}
$playMusic = trim($new_str);

$search_string = "defaultBackground";
foreach($lines_array as $line) {
    if(strpos($line, $search_string) !== false) {
        list(, $new_str) = explode("=", $line);
    
    }
}
$defaultBackground = trim($new_str);

$search_string = "useBackground";
foreach($lines_array as $line) {
    if(strpos($line, $search_string) !== false) {
        list(, $new_str) = explode("=", $line);
    
    }
}
$useBackground = trim($new_str);


$search_string = "customBackgroundColor";
foreach($lines_array as $line) {
    if(strpos($line, $search_string) !== false) {
        list(, $new_str) = explode("=", $line);
    
    }
}
$customBackgroundColor = trim($new_str);

$search_string = "customBackgroundURL";
foreach($lines_array as $line) {
    if(strpos($line, $search_string) !== false) {
        list(, $new_str) = explode("=", $line);
    
    }
}
$customBackgroundURL = trim($new_str);

$search_string = "API";
foreach($lines_array as $line) {
    if(strpos($line, $search_string) !== false) {
        list(, $new_str) = explode("=", $line);
    
    }
}
$API = trim($new_str);


$search_string = "customColorRed";
foreach($lines_array as $line) {
    if(strpos($line, $search_string) !== false) {
        list(, $new_str) = explode("=", $line);
    
    }
}
$customColorRed = trim($new_str);

$search_string = "customColorGreen";
foreach($lines_array as $line) {
    if(strpos($line, $search_string) !== false) {
        list(, $new_str) = explode("=", $line);
    
    }
}
$customColorGreen = trim($new_str);

$search_string = "customColorBlue";
foreach($lines_array as $line) {
    if(strpos($line, $search_string) !== false) {
        list(, $new_str) = explode("=", $line);
    
    }
}
$customColorBlue = trim($new_str);

$search_string = "customColorAlphaTransparency";
foreach($lines_array as $line) {
    if(strpos($line, $search_string) !== false) {
        list(, $new_str) = explode("=", $line);
    
    }
}
$customColorAlphaTransparency = trim($new_str);


$search_string = "customFontColor";
foreach($lines_array as $line) {
    if(strpos($line, $search_string) !== false) {
        list(, $new_str) = explode("=", $line);
    
    }
}
$customFontColor = trim($new_str);

$search_string = "useRules";
foreach($lines_array as $line) {
    if(strpos($line, $search_string) !== false) {
        list(, $new_str) = explode("=", $line);
    
    }
}
$useRules = trim($new_str);

$search_string = "useAbout";
foreach($lines_array as $line) {
    if(strpos($line, $search_string) !== false) {
        list(, $new_str) = explode("=", $line);
    
    }
}
$useAbout = trim($new_str);






$data = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$API.'&steamids='.$_GET['steamid'];


$f = file_get_contents($data);
$arr = json_decode($f, true);

$plname = $arr['response']['players'][0]['personaname'];

$avatar = $arr['response']['players'][0]['avatarfull'];
    

$lines_array = file("options/rules.txt");

$search_string = "rule";
foreach($lines_array as $line) {
	$rules[] .= substr($line, 5);
}


$mapname = $_GET[mapname];

$lines_array = file("options/gamemode.txt");

foreach($lines_array as $line) {
	$gamemode = $line;
}

$lines_array = file("options/ServerName.txt");

foreach($lines_array as $line) {
	$servername = $line;
}

$lines_array = file("options/about.txt");

foreach($lines_array as $line) {
	$about = $line;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>LOADING SCREEN</title>
<link href="style/style.css" rel="stylesheet" type="text/css"/>  
<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>  
<script src="js/JS.js"></script>
<script src="js/bigText.js"></script>

</head>


<style>

body
{
	color:<?php echo $customFontColor ?>;
}

#avatar
{
	background-color:rgba(<?php echo $customColorRed ?>, <?php echo $customColorGreen ?>, <?php echo $customColorBlue ?>, <?php echo $customColorAlphaTransparency ?>);
}

#ServerDetails
{
	background-color:rgba(<?php echo $customColorRed ?>, <?php echo $customColorGreen ?>, <?php echo $customColorBlue ?>, <?php echo $customColorAlphaTransparency ?>);
}

#Rules
{
	background-color:rgba(<?php echo $customColorRed ?>, <?php echo $customColorGreen ?>, <?php echo $customColorBlue ?>, <?php echo $customColorAlphaTransparency ?>);
}

#About
{
	background-color:rgba(<?php echo $customColorRed ?>, <?php echo $customColorGreen ?>, <?php echo $customColorBlue ?>, <?php echo $customColorAlphaTransparency ?>);
}




</style>
<body style="background:<?php echo $customBackgroundColor ?>; background-image:url(<?php echo $customBackgroundURL ?>);">


<?php 


if (strcmp($backgroundOption, 'yes')!== 0 && strcmp($useBackground, 'yes') === 0)
{
	
	if (strcmp($defaultBackground, 'blue') === 0)
	{
		echo '<div id="background1" style="opacity:1;"></div>';
	}
	if (strcmp($defaultBackground, 'green') === 0)
	{
		echo '<div id="background2" style="opacity:1;"></div>';
	}
	if (strcmp($defaultBackground, 'yellow') === 0)
	{
		echo '<div id="background3" style="opacity:1;"></div>';
	}
	if (strcmp($defaultBackground, 'red') === 0)
	{
		echo '<div id="background4" style="opacity:1;"></div>';
	}
	if (strcmp($defaultBackground, 'orange') === 0)
	{
		echo '<div id="background5" style="opacity:1;"></div>';
	}
	if (strcmp($defaultBackground, 'black') === 0)
	{
		echo '<div id="background6" style="opacity:1;"></div>';
	}
}else
{
	if (strcmp($useBackground, 'yes') === 0)
	{
		
		echo '<script src="js/backgroundAnimate.js"></script>';
		echo '
		<div id="background1"></div>
		<div id="background2"></div>
		<div id="background3"></div>
		<div id="background4"></div>
		<div id="background5"></div>
		<div id="background6"></div>
		';
	}
}
?>


<div id="avatar">
	<div class="image" style="background-image:url(<?php echo $avatar ?>)"></div>
    <div class="name"><span id="playerName"><?php echo $plname ?></span></div>
   	<script>
        jQuery(document).ready(function($) {
        	$('#playerName').bigtext();
        });
     </script>
</div>

<div id="ServerDetails">

	<div class="item"><div class="name"><?php echo $servername ?></div>
    </div>
    
    <div class="item"><div class="gameMode">GAME MODE</div>
    <div class="detail"><?php echo $gamemode ?></div>
    </div>
    
    <div class="item"><div class="map">MAP</div>
    <div class="detail"><?php echo $mapname ?></div>
    </div>

</div>




<?php

if (strcmp($useRules, 'yes') === 0)
{
	echo '
	<div id="Rules">
	<div class="name">RULES</div>	
	';
	 $currentRule = 1;
	
	foreach ($rules as &$value) {
	echo '<div class="item"><div class="number">'. $currentRule .'</div>'. $value .'</div>';
	$currentRule += 1;
	}
	echo '</div>';
}

?>

<?php

if (strcmp($useAbout, 'yes') === 0)
{
	echo '
	<div id="About">
	<div class="name">ABOUT</div>	
	
	<div class="aboutText"> '.$about.' </div>
	
	</div>
	';
}

?>


  



<?php if (strcmp($playMusic, 'yes') === 0)
{
	echo '<embed loop="true" src="music/music.mp3" hidden="true" type="audio/mpeg"></embed>';
}
?>



</body>
</html>