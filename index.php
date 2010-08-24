<?php
require_once("roblib.php");
function href ($url,$title=false) { return "<a href='$url'>".($title?$title:$url)."</a>"; }

//~ function MyEscXML ($txt) { return htmlentities($txt); } // �->uuml;
function MyEscXML ($txt) { return strtr($txt,array("ro�er"=>"rosser","�"=>"&ouml;","�"=>"&uuml;","�"=>"ae","�"=>"&Ouml;","�"=>"&Uuml;","�"=>"&Auml;")); } // htmlentities
function MyEsc ($txt) { return $txt; } // htmlentities
//~ function MyEsc ($txt) { return strtr($txt,array("�?"=>"�","ü"=>"�","ö"=>"�")); } // htmlentities
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
font-size:8pt;
}
div.notice {
background-color:#FFFFFF;
border:1px solid #000000;
font-size:8pt;
align:center;
width:400px;
}
</style>
</head>
<body>
<?php


if ($gSeelenID) $gSeelenID = preg_replace('/[^a-zA-Z0-9]/','',$gSeelenID);
$xmlurl = $gSeelenID ? ("http://www.dieverdammten.de/xml/?k=".urlencode($gSeelenID)) : false;

// disclaimer
function MotionTwinNote() {
	?>
	<div class="notice">
	Diese Seite ist kein integraler Bestandteil von <?=href("http://www.dieverdammten.de")?>, sondern lediglich ein externes Fan-Projekt.
	Es wird an keiner Stelle von dir verlangt, deinen Nutzernamen oder Passwort einzugeben. Mit der Eingabe deiner externen ID gilt dieser Umstand als verstanden. 
	Die hier zu sehenden Bilder und Daten sind aus dem Browsergame "Die Verdammten" entnommen und somit Eigentum von Motion Twin. 
	</div>
	<?php
}
function SeelenID_EntryForm () {
	global $gSeelenID;
	if ($gSeelenID) return;
	?> <form action="" method="POST"> Seelen-ID:<input name="SeelenID"> <input type="submit" name="Login" value="Login"> </form> <?php
	PrintFooter(); exit(0);
}


// ***** ***** ***** ***** ***** HEADER

echo "<table><tr><td valign=top>";

	echo "<table><tr><td>";
		echo href("http://verdammt.zwischenwelt.org/"); 
	echo "</td><td>";
		if ($gSeelenID) { ?><form action="" method="POST"><input type="submit" name="LogOut" value="LogOut"></form><?php }
	echo "</td></tr></table>";


	echo "Author: ".href("mailto:ghoulsblade@schattenkind.net","ghoulsblade@schattenkind.net")." ICQ:107677833 (opensource)<br>\n";
	echo "Links:".
		href("http://dvmap.nospace.de/index.php","Karte")." ".
		href("http://emptycookie.de/index.php?id=".($gSeelenID?$gSeelenID:""),"�bersicht")." ".
		href("http://nobbz.de/wiki/","NobbzWiki")." ".
		href("http://forum.der-holle.de/","HolleForum")." ".
		href("http://www.patamap.com/index.php?page=patastats","PataMap")." ".
		href($xmlurl,"XmlStream")." ". 
		"<br>\n";
		
echo "</td><td valign=top>";

	MotionTwinNote();

echo "</td></tr></table>";

SeelenID_EntryForm();


// ***** ***** ***** ***** ***** Load XML


$gDemo = false;
$xmlurl_sample = "sample.xml";
if (isset($_REQUEST["sample"])) { $xmlurl = $xmlurl_sample; $gDemo = true; }
$xmlstr = file_get_contents($xmlurl);
@$xml = simplexml_load_string(MyEscXML($xmlstr));


if (!$xml->data[0]->city[0]["city"] || $xml->status[0]["open"] == "0") {
	echo "<h1>webseite down, zombie-angriff im gange!</h1>\n";
	echo "(lade dummy/demo daten)<br>\n";
	$xmlurl = $xmlurl_sample;
	$xmlstr = file_get_contents($xmlurl);
	$xml = simplexml_load_string(MyEscXML($xmlstr));
}

$icon_url			= $xml->headers[0]["iconurl"];
$icon_url_item		= $xml->headers[0]["iconurl"]."item_";
$city				= $xml->data[0]->city[0];
$icon_url_zombie	= "http://www.dieverdammten.de/gfx/forum/smiley/h_zombie.gif";
//~ $icon_url_attack_in	= "http://data.dieverdammten.de/gfx/forum/smiley/h_zhead.gif";
$icon_url_attack_in	= $icon_url."small_death.gif";
$icon_url_def		= $icon_url."item_shield.gif";

$def = (int)($city->defense[0]["total"]);
$day = (int)$xml->headers[0]->game[0]["days"];

$cityx = $xml->data[0]->city["x"];
$cityy = $xml->data[0]->city["y"];

echo "Stadt=".$city["city"];
echo " Tag:".$day;
echo " ".img($icon_url."small_water.gif","Wasser").":".$city["water"];
if ($gDemo) echo " <b>(demo/offline daten)</b>";
echo "<br>\n";

$zombie_min = (int)($xml->data[0]->estimations[0]->e[0]["min"]);
$zombie_max = (int)($xml->data[0]->estimations[0]->e[0]["max"]);
echo "Sch�tzung:".img($icon_url_zombie,"Zombies")."$zombie_min-$zombie_max -&gt; ".img($icon_url_def,"def")."$def -&gt; ".img($icon_url_attack_in,"tote")."".max(0,$zombie_min-$def)."-".max(0,$zombie_max-$def)."<br>\n";
$stat = array(0,24,50,97,149,215,294,387,489,595,709,831,935,1057,1190,1354,1548,1738,1926,2140,2353,2618,2892,3189,3506,3882,3952,4393,4841,5339,5772,6271,6880,7194,7736,8285,8728,9106,9671,9888,10666,11508,11705,12608,12139,12921,15248,11666);
$zombie_av = isset($stat[$day]) ? $stat[$day] : false;
if ($zombie_av) echo "Statistik:".img($icon_url_zombie,"Zombies")."$zombie_av -&gt; ".img($icon_url_def,"def")."$def -&gt; ".img($icon_url_attack_in,"tote")."".max(0,$zombie_av-$def)."<br>\n";


/*
Unseren Messungen zufolge gab es im Osten ein paar meteorologische Anomalien. 
*/

//~ <news z="232" def="233"><content>bla...</content></news>
//~ <defense base="5" items="16" citizen_guardians="0" citizen_homes="27" upgrades="13" buildings="148" total="225" itemsMul="2"/>


//~ var_dump($o);
//~ echo $xml->data[0]->city[0]->city;
//~ $hordes

//~ echo "iconurl=$icon_url_item<br>\n";

// ***** ***** ***** ***** ***** CITY TABLE START

echo "<table border=1><tr><td valign=top>\n";


// ***** ***** ***** ***** ***** GEB�UDE
//~ echo "Geb�ude:<br>";
foreach ($xml->data[0]->city[0]->building as $building) {
	echo img($icon_url.$building["img"].".gif").$building["name"]."<br>\n";
}


echo "</td><td valign=top>\n";



// ***** ***** ***** ***** ***** BANK
//~ echo "Bank:<br>";
$cats = array();
foreach ($xml->data[0]->bank[0]->item as $item) { 
	$c = $item["count"];
	$cat = (string)$item["cat"];
	$html = (($c>1)?($c."x"):"").img($icon_url_item.$item["img"].".gif",$item["name"]);
	if (!isset($cats[$cat])) $cats[$cat] = array();
	$cats[$cat][] = $html;
}

echo "<table border=0 cellspacing=0 cellpadding=1>\n";
$gCatTransLong = array("Rsc"=>"Rohstoffe","Furniture"=>"Einrichtungsgegenst�nde","Drug"=>"Medikamente","Armor"=>"Verteidigung","Food"=>"Vorr�te","Weapon"=>"Waffen","Misc"=>"Verschiedenes");
$gCatTrans = array("Rsc"=>"Rohstoffe","Furniture"=>"Einrichtung","Drug"=>"Medikamente","Armor"=>"Verteidigung","Food"=>"Vorr�te","Weapon"=>"Waffen","Misc"=>"Verschiedenes");
$cats2 = array();
foreach ($gCatTrans as $k => $v) $cats2[$k] = isset($cats[$k])?$cats[$k]:array();
foreach ($cats as $k => $v) if (!isset($gCatTrans[$k])) $cats2[$k] = $v;
foreach ($cats2 as $k => $arr) echo "<tr><th>".(isset($gCatTrans[$k])?$gCatTrans[$k]:$k).":</th><td align=right>".implode("</td><td align=right>",$arr)."</td></tr>\n";
echo "</table>\n";



// ***** ***** ***** ***** ***** MAP


$map = $xml->data[0]->map[0];
$w = $map["wid"];
$h = $map["hei"];
$gMap = array();
function MapSet ($x,$y,$data) { 
	global $gMap;
	$gMap["$x,$y"] = $data; 
	//~ echo "MapSet($x,$y,nvt=".$data["nvt"].",tag=".$data["tag"].")<br>\n";
}
function Map ($x,$y) { global $gMap; return isset($gMap["$x,$y"])?$gMap["$x,$y"]:false; }
foreach ($map->zone as $zone) MapSet((int)$zone["x"],(int)$zone["y"],$zone);


function TagIconHTML ($tagid) {
	return img("http://data.dieverdammten.de/gfx/icons/tag_".((int)$tagid).".gif");
}

echo "<table border=0 cellspacing=0 cellpadding=0>\n";
for ($y=0;$y<$h;++$y) {
	echo "<tr>";
	for ($x=0;$x<$w;++$x) {
		$data = Map($x,$y);
		$bgimg = "zone_bg.gif";
		$tagimg = "";
		if ($data) {
			$bHasBuilding = isset($data->building);
			$bViewed = ((int)$data["nvt"]) == 0; // nvt : 1/0 (value is 1 was already discovered, but Not Visited Today)
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





echo "</td></tr></table>\n";

// ***** ***** ***** ***** ***** CITY TABLE END

/*
<zone x="0" y="3" nvt="1" tag="5">
<building name="Gepl�nderte Mall" type="5" dig="0">
<![CDATA[Dieser riesige Haufen aus Schutt und Metall war fr�her mal ein hell erleuchtetes Einkaufszentrum, das vor Menschen nur so wimmelte. Das Einzige, was hier noch herumwimmelt, sind W�rmer und anderes Gekreuch und Gefleuch... Du bist jedoch zuversichtlich, hier allerhand n�tzliche Gegenst�nde zu finden.]]>
</building>
</zone>

<zone x="3" y="7" nvt="0" tag="5" danger="2">
<building name="Verfallene Villa" type="4" dig="0">
<![CDATA[Dieses Haus war einmal vor langer Zeit bewohnt. Vielleicht wohnte hier eine gl�ckliche Familie, deren Mitglieder hier sch�ne Momente verbracht haben? Davon ist aber nichts mehr zu sp�ren, im Gegenteil: Staub, Zerst�rung und absolute Trostlosigkeit, wohin du auch blickst. Ab und zu kommt auch mal ein z�hnefletschender Zombie vorbeigestapft.]]>
</building>
</zone>
*/







/*
<map hei="12" wid="12"><zone x="3" y="1" nvt="1" tag="5"/><zone x="4" y="1" nvt="1"/><zone x="5" y="1" nvt="1"/><zone x="6" y="1" nvt="1"/><zone x="7" y="1" nvt="1"/><zone x="2" y="2" nvt="1"/><zone x="3" y="2" nvt="1" tag="5"/>
*/

//~ echo "xml=".htmlspecialchars($xml);

//~ $xmlurl = "sample.xml";
// 



PrintFooter(); 
?>