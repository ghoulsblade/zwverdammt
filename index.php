<?php
require_once("roblib.php");
function href ($url,$title=false) { return "<a href='$url'>".($title?$title:$url)."</a>"; }

//~ function MyEscXML ($txt) { return htmlentities($txt); } // ö->uuml;
function MyEscXML ($txt) { return strtr($txt,array("roßer"=>"rosser","ö"=>"&ouml;","ü"=>"&uuml;","ä"=>"ae","Ö"=>"&Ouml;","Ü"=>"&Uuml;","Ä"=>"&Auml;")); } // htmlentities
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
if ($gSeelenID) $gSeelenID = preg_replace('/[^a-zA-Z0-9]/','',$gSeelenID);

echo "Author: EMail:".href("mailto:ghoulsblade@schattenkind.net","ghoulsblade@schattenkind.net")." ICQ:107677833 (wer SourceCode mag einfach melden)<br>\n";
echo "Links:".
	href("http://dvmap.nospace.de/index.php","VerdammteKarte")." ".
	href("http://emptycookie.de/index.php?id=".($gSeelenID?$gSeelenID:""),"VerdammteÜbersicht")." ".
	href("http://nobbz.de/wiki/","NobbzWiki")." ".
	href("http://forum.der-holle.de/","HolleForum")." ".
	href("http://www.patamap.com/index.php?page=patastats","PataMap")." ".
	"<br>\n";


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
echo "Zombies=$zombie_min-$zombie_max, def=$def, durchkommen=".max(0,$zombie_min-$def)."-".max(0,$zombie_max-$def)."<br>\n";
//~ var_dump($o);
//~ echo $xml->data[0]->city[0]->city;
//~ $hordes

$iconurl = $xml->headers[0]["iconurl"]."item_";
//~ echo "iconurl=$iconurl<br>\n";

echo "Bank:<br>";
$cats = array();
foreach ($xml->data[0]->bank[0]->item as $item) { 
	$c = $item["count"];
	$cat = (string)$item["cat"];
	$html = (($c>1)?($c."x"):"").img($iconurl.$item["img"].".gif",$item["name"]);
	if (!isset($cats[$cat])) $cats[$cat] = array();
	$cats[$cat][] = $html;
}

echo "<table border=1 cellspacing=0 cellpadding=0>\n";
foreach ($cats as $k => $arr) echo "<tr><th>".$k."</th><td align=right>".implode("</td><td align=right>",$arr)."</td></tr>\n";
echo "</table>\n";

$map = $xml->data[0]->map[0];
$w = $map["wid"];
$h = $map["hei"];
echo "Map:w=$w,h=$h<br>\n";
$gMap = array();
function MapSet ($x,$y,$data) { 
	global $gMap;
	$gMap["$x,$y"] = $data; 
	//~ echo "MapSet($x,$y,nvt=".$data["nvt"].",tag=".$data["tag"].")<br>\n";
}
function Map ($x,$y) { global $gMap; return isset($gMap["$x,$y"])?$gMap["$x,$y"]:false; }
foreach ($map->zone as $zone) MapSet((int)$zone["x"],(int)$zone["y"],$zone);

/*
<zone x="0" y="3" nvt="1" tag="5">
<building name="Geplünderte Mall" type="5" dig="0">
<![CDATA[Dieser riesige Haufen aus Schutt und Metall war früher mal ein hell erleuchtetes Einkaufszentrum, das vor Menschen nur so wimmelte. Das Einzige, was hier noch herumwimmelt, sind Würmer und anderes Gekreuch und Gefleuch... Du bist jedoch zuversichtlich, hier allerhand nützliche Gegenstände zu finden.]]>
</building>
</zone>

<zone x="3" y="7" nvt="0" tag="5" danger="2">
<building name="Verfallene Villa" type="4" dig="0">
<![CDATA[Dieses Haus war einmal vor langer Zeit bewohnt. Vielleicht wohnte hier eine glückliche Familie, deren Mitglieder hier schöne Momente verbracht haben? Davon ist aber nichts mehr zu spüren, im Gegenteil: Staub, Zerstörung und absolute Trostlosigkeit, wohin du auch blickst. Ab und zu kommt auch mal ein zähnefletschender Zombie vorbeigestapft.]]>
</building>
</zone>
*/


$cityx = $xml->data[0]->city["x"];
$cityy = $xml->data[0]->city["y"];

// nvt : 1/0 (value is 1 was already discovered, but Not Visited Today)

function TagIconHTML ($tagid) {
	return img("http://data.dieverdammten.de/gfx/icons/tag_".((int)$tagid).".gif");
}

echo "<table border=1 cellspacing=0 cellpadding=0>\n";
for ($y=0;$y<$h;++$y) {
	echo "<tr>";
	for ($x=0;$x<$w;++$x) {
		$data = Map($x,$y);
		$bgimg = "zone_bg.gif";
		$tagimg = "";
		if ($data) {
			$bHasBuilding = isset($data->building);
			$bViewed = ((int)$data["nvt"]) == 0;
			$bgimg = $bViewed ? "zone.gif" : "zone_nv.gif";
			if ($bHasBuilding) $bgimg = $bViewed ? "ruin.gif" : "ruin_nv.gif";
			if ($data["danger"] == 1) $bgimg = "zone_d1.gif";
			if ($data["danger"] == 2) $bgimg = "zone_d2.gif";
			if ($data["danger"] >= 3) $bgimg = "zone_d3.gif";
			if ($data["tag"]) $tagimg = TagIconHTML($data["tag"]);
		}
		if ($x == $cityx && $y == $cityy) $bgimg = "city.gif";
		
		$bgimg = "background='images/map/$bgimg'";
		
		$style = ""; // "bgcolor=green"
		echo "<td $style $bgimg width=20 height=20>".$tagimg."</td>";
		//~ echo "<td width=16 height=16>".($data?("nvt=".$data["nvt"].",tag=".$data["tag"]):"")."</td>";
		//~ echo "<td width=16 height=16>".($data?"x":"")."</td>";
	}
	echo "</tr>\n";
}
echo "</table>\n";



/*
<map hei="12" wid="12"><zone x="3" y="1" nvt="1" tag="5"/><zone x="4" y="1" nvt="1"/><zone x="5" y="1" nvt="1"/><zone x="6" y="1" nvt="1"/><zone x="7" y="1" nvt="1"/><zone x="2" y="2" nvt="1"/><zone x="3" y="2" nvt="1" tag="5"/>
*/

//~ echo "xml=".htmlspecialchars($xml);

//~ $xmlurl = "sample.xml";
// 



PrintFooter(); 
?>