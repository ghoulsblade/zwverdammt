<?php
require_once("roblib.php");
function href ($url,$title=false) { return "<a href='$url'>".($title?$title:$url)."</a>"; }

//~ function MyEscXML ($txt) { return htmlentities($txt); } // ö->uuml;
function MyEscXML ($txt) { return strtr($txt,array("roßer"=>"rosser","ö"=>"&ouml;","ü"=>"&uuml;","ä"=>"&auml;","Ö"=>"&Ouml;","Ü"=>"&Uuml;","Ä"=>"&Auml;")); } // htmlentities
function MyEsc ($txt) { return $txt; } // htmlentities
//~ function MyEsc ($txt) { return strtr($txt,array("Ã?"=>"ß","Ã¼"=>"ü","Ã¶"=>"ö")); } // htmlentities
function img ($url,$title=false) { $title = $title?(MyEsc($title)):$title; return "<img src='$url' ".($title?("alt='$title' title='$title'"):"")."/>"; }

$gSeelenID = isset($_COOKIE["SeelenID"]) ? $_COOKIE["SeelenID"] : false;

if (isset($_REQUEST["LogOut"])) {
	setcookie ("SeelenID", "", time() - 3600);
	//~ echo "logout<br>";
	$gSeelenID = false;
} elseif (isset($_REQUEST["Login"])) {
	setcookie ("SeelenID", $_REQUEST["SeelenID"], time() + 30*24*3600);
	//~ echo "login:".$_REQUEST["SeelenID"]."<br>";
	$gSeelenID = $_REQUEST["SeelenID"];
}

function PrintFooter () { ?></body></html><?php }

// htmlspecialchars

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<title>Verdammt</title>
<style type="text/css">
body {
font-family:Arial;
color: #000000;
}
div.notice {
background-color:#FFFFFF;
border:1px solid #000000;
font-size:8pt;
align:center;
width:600px;
}
</style>
</head>
<body>
<?php

// disclaimer
?>
<div class="notice">
Diese Seite ist kein integraler Bestandteil von <?=href("http://www.dieverdammten.de")?>, sondern lediglich ein externes Fan-Projekt.
Es wird an keiner Stelle von dir verlangt, deinen Nutzernamen oder Passwort einzugeben. Mit der Eingabe deiner externen ID gilt dieser Umstand als verstanden. 
Die hier zu sehenden Bilder und Daten sind aus dem Browsergame "Die Verdammten" entnommen und somit Eigentum von Motion Twin. 
</div>
<?php

// header
echo href("http://verdammt.zwischenwelt.org/");
if ($gSeelenID) { ?><form action="" method="POST"><input type="submit" name="LogOut" value="LogOut"></form><?php }
echo "Author: EMail:".href("mailto:ghoulsblade@schattenkind.net","ghoulsblade@schattenkind.net")." ICQ:107677833 (wer SourceCode mag einfach melden)<br>\n";
echo "<br>\n";

// seelenid
if (!$gSeelenID) {
	?>
	<form action="" method="POST">
	Seelen-ID:<input name="SeelenID">
	<input type="submit" name="Login" value="Login">
	</form>
	<?php
	PrintFooter(); exit(0);
}

$gSeelenID = preg_replace('/[^a-zA-Z0-9]/','',$gSeelenID);

$xmlurl = "http://www.dieverdammten.de/xml/?k=".urlencode($gSeelenID);
echo "xmlurl=".href(htmlspecialchars($xmlurl))."<br>\n";
$xmlurl_sample = "sample.xml";
if (isset($_REQUEST["sample"])) { $xmlurl = $xmlurl_sample; echo "<h1>DEMO DATEN</h1>\n"; }
$xmlstr = file_get_contents($xmlurl);
@$xml = simplexml_load_string(MyEscXML($xmlstr));


if (!$xml->data[0]->city[0]["city"] || $xml->status[0]["open"] == "0") {
	echo "<h1>webseite down, zombie-angriff im gange!</h1>\n";
	echo "(lade dummy/demo daten)<br>\n";
	$xmlurl = $xmlurl_sample;
	$xmlstr = file_get_contents($xmlurl);
	$xml = simplexml_load_string(MyEscXML($xmlstr));
}

$city = $xml->data[0]->city[0];
echo "Stadt=".$city["city"]."<br>\n";
$def = (int)($city->defense[0]["total"]);

$zombie_min = (int)($xml->data[0]->estimations[0]->e[0]["min"]);
$zombie_max = (int)($xml->data[0]->estimations[0]->e[0]["max"]);
echo "defense=$def, zombie_min=$zombie_min, zombie_max=$zombie_max, durchkommen=".max(0,$zombie_min-$def)."-".max(0,$zombie_max-$def)."<br>\n";
//~ var_dump($o);
//~ echo $xml->data[0]->city[0]->city;
//~ $hordes

$iconurl = $xml->headers[0]["iconurl"]."item_";
//~ echo "iconurl=$iconurl<br>\n";

echo "Bank:<br>";
foreach ($xml->data[0]->bank[0]->item as $item) { 
	$c = $item["count"];
	echo (($c>1)?($c."x"):"").img($iconurl.$item["img"].".gif",$item["name"])."&nbsp;\n"; 
}

//~ echo "xml=".htmlspecialchars($xml);

//~ $xmlurl = "sample.xml";
// 



PrintFooter(); 
?>