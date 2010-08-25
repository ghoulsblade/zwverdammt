<?php
require_once("defines.php");
require_once("roblib.php");
function href ($url,$title=false) { return "<a href='$url'>".($title?$title:$url)."</a>"; }

//~ function MyEscXML ($txt) { return htmlentities($txt); } // ö->uuml;
function MyEscXML ($txt) { 
	//~ return utf8_decode($txt);
	return strtr($txt,array("roßer"=>"rosser","ö"=>"&ouml;","ü"=>"&uuml;","ä"=>"ae","Ö"=>"&Ouml;","Ü"=>"&Uuml;","Ä"=>"&Auml;")); 
		//~ wegen den umlauten ein tip: utf8_decode 
} // htmlentities
function MyEsc ($txt) { return utf8_decode($txt); } // htmlentities
function MyEscHTML ($txt) { return utf8_decode($txt); } // htmlentities
//~ function MyEsc ($txt) { return strtr($txt,array("Ã?"=>"ß","Ã¼"=>"ü","Ã¶"=>"ö")); } // htmlentities
function img ($url,$title=false,$special="") { $title = $title?(MyEsc($title)):$title; return "<img $special src='$url' ".($title?("alt='$title' title='$title'"):"")."/>"; }

$gShowAvatars = false;

$gSeelenID = isset($_COOKIE["SeelenID"]) ? $_COOKIE["SeelenID"] : false;
$gUseSampleData = isset($_REQUEST["sample"]);
if ($gUseSampleData) $gSeelenID = "abcdefghijklmnopqrstuvwxyz";

if (isset($_REQUEST["LogOut"])) {
	setcookie ("SeelenID", "", time() - 3600);
	//~ echo "logout<br>";
	$gSeelenID = false;
} elseif (isset($_REQUEST["Login"])) {
	setcookie ("SeelenID", $_REQUEST["SeelenID"], time() + 30*24*3600);
	//~ echo "login:".$_REQUEST["SeelenID"]."<br>";
	$gSeelenID = $_REQUEST["SeelenID"];
}

session_start(); // -> man kann $_SESSION benutzen


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
	echo href("?sample=1","(Vorschau ohne SeelenID)")."<br>\n";
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
		href("http://emptycookie.de/index.php?id=".($gSeelenID?$gSeelenID:""),"Übersicht")." ".
		href("http://nobbz.de/wiki/","NobbzWiki")." ".
		href("http://forum.der-holle.de/","HolleForum")." ".
		href("http://chat.mibbit.com/?channel=%23dieverdammten&server=irc.mibbit.net","Chat")." ".
		href("http://www.patamap.com/index.php?page=patastats","PataMap")." ".
		href($xmlurl,"XmlStream")." ". 
		"<br>\n";
		// http://verdammt.mnutz.de/  (baldwin)
		
echo "</td><td valign=top>";

	MotionTwinNote();

echo "</td></tr></table>";

SeelenID_EntryForm();


// ***** ***** ***** ***** ***** Load XML

$gStoreXML = true;
$gDemo = false;
$xmlurl_sample = "sample.xml";
if ($gUseSampleData) { $xmlurl = $xmlurl_sample; $gDemo = true; $gStoreXML = false; }
$xmlstr = file_get_contents($xmlurl);
@$xml = simplexml_load_string(MyEscXML($xmlstr));


if (!$xml->data[0]->city[0]["city"] || $xml->status[0]["open"] == "0") {
	echo "<h1>Webseite down, Zombie-Angriff im Gange!</h1>\n";
	echo "(lade dummy/demo daten)<br>\n";
	$xmlurl = $xmlurl_sample;
	$xmlstr = file_get_contents($xmlurl);
	$xml = simplexml_load_string(MyEscXML($xmlstr));
	$gStoreXML = false;
}

$icon_url			= $xml->headers[0]["iconurl"];
$icon_url_item		= $xml->headers[0]["iconurl"]."item_";
$avatar_url			= $xml->headers[0]["avatarurl"];
$city				= $xml->data[0]->city[0];
$icon_url_zombie	= "http://www.dieverdammten.de/gfx/forum/smiley/h_zombie.gif";
//~ $icon_url_attack_in	= "http://data.dieverdammten.de/gfx/forum/smiley/h_zhead.gif";
$icon_url_attack_in	= $icon_url."small_death.gif";
$icon_url_death		= $icon_url."small_death.gif";
$icon_url_def		= $icon_url."item_shield.gif";

$def = (int)($city->defense[0]["total"]);
$day = (int)$xml->headers[0]->game[0]["days"];
$gGameDay = $day;
$gGameID = (int)$xml->headers[0]->game[0]["id"];

$cityx = $xml->data[0]->city["x"];
$cityy = $xml->data[0]->city["y"];
$gCityX = $cityx;
$gCityY = $cityy;

$buerger_draussen = 0;
$buerger_alive = 0;
$gCitizens = $xml->data[0]->citizens[0]->citizen;
foreach ($gCitizens as $citizen) { 
	if ($citizen["dead"] == "0") ++$buerger_alive;
	if ((int)$citizen["x"] == $cityx && (int)$citizen["y"] == $cityy) {} else { ++$buerger_draussen; }
}



if ($gStoreXML) {
	$o = false;
	$o->seelenid = (string)$gSeelenID;
	$o->time = time();
	$o->gameid = $gGameID;
	$o->cityname = (string)$city["city"];
	$o->day = (int)$day;
	$o->xml = $xmlstr;
	sql("INSERT INTO xml SET ".obj2sql($o));
}

echo "Stadt=".$city["city"];
echo " Tag=".$day;
echo " ".img($icon_url."small_water.gif","Wasser").":".$city["water"];
echo " &Uuml;berlebende=".$buerger_alive;
echo " draussen=".$buerger_draussen;
if ($gDemo) echo " <b>(demo/offline daten)</b>";
echo "<br>\n";

$e = $xml->data[0]->estimations[0]->e[0];
$zombie_min = (int)($e["min"]);
$zombie_max = (int)($e["max"]);
$bEstMax = ($e["maxed"]!="0"); // schon maximale qualität ?
echo "Schätzung".($bEstMax?"(gut)":"(<b>schlecht</b>)").":".img($icon_url_zombie,"Zombies")."$zombie_min-$zombie_max -&gt; ".img($icon_url_def,"def")."$def -&gt; ".img($icon_url_attack_in,"tote")."".max(0,$zombie_min-$def)."-".max(0,$zombie_max-$def)."<br>\n";
$stat = array(0,24,50,97,149,215,294,387,489,595,709,831,935,1057,1190,1354,1548,1738,1926,2140,2353,2618,2892,3189,3506,3882,3952,4393,4841,5339,5772,6271,6880,7194,7736,8285,8728,9106,9671,9888,10666,11508,11705,12608,12139,12921,15248,11666);
$zombie_av = isset($stat[$day]) ? $stat[$day] : false;
if ($zombie_av) echo "Statistik:".img($icon_url_zombie,"Zombies")."$zombie_av -&gt; ".img($icon_url_def,"def")."$def -&gt; ".img($icon_url_attack_in,"tote")."".max(0,$zombie_av-$def)."<br>\n";
if (!$bEstMax) echo "<b>Hilf mit die Schätzung im Wachturm zu verbessern!</b><br>\n";

$gBuildingDone = array();
$gUpgrades = array();
foreach ($xml->data[0]->upgrades[0]->up as $upgrade) $gUpgrades[(string)$upgrade["name"]] = (int)$upgrade["level"];
foreach ($xml->data[0]->city[0]->building as $building) $gBuildingDone[(string)$building["name"]] = true;

function GetBuildingLevel ($bname) { // -1= not build, 0=built but no upgrade, >1 = upgrade level 
	global $gBuildingDone,$gUpgrades;
	if (!isset($gBuildingDone[$bname])) return -1;
	if (!isset($gUpgrades[$bname])) return 0;
	return $gUpgrades[$bname];
}

function CheckBuilding ($bname,$minlevel,$text,$pre="den/die") { 
	if (GetBuildingLevel($bname) < 0) { echo "Hilf mit ".$pre." <b>$bname</b> zu bauen: ".$text."<br>\n"; return false; }
	if (GetBuildingLevel($bname) < $minlevel) { echo "Hilf mit ".$pre." <b>$bname</b> als <b>Verbesserung des Tages</b> zu wählen: ".$text."<br>\n"; return false; }
	return true;
}
if (CheckBuilding("Werkstatt",0,"wird benötigt um BaumStümpfe, MetallTrümmer und viele andere Sachen umzuwandeln","die")) {
	CheckBuilding("Wachturm",0,"wird benötigt um den Forschungsturm zu bauen","den");
	CheckBuilding("Forschungsturm",2,"sorgt dafür dass sich leere Felder, auf denen man sonst nur BaumStümpfe und MetallTrümmer findet wieder regenerieren","den");
}
if ($gGameDay == 1) { echo ("bau dein Feldbett zu einem Zelt aus, aber NICHT zu einer Baracke, Holzbretter werden dringend für die Werkstatt benötigt")."<br>\n"; }



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



// ***** ***** ***** ***** ***** BÜRGER

$gDefIcon = array();
$gDefIcon[1] = $icon_url."upgrade_tent.gif";
$gDefIcon[3] = $icon_url."upgrade_house1.gif";


// job="collec" job="basic"
echo "<table border=1 cellpadding=0 cellspacing=0>\n";
foreach ($xml->data[0]->citizens[0]->citizen as $citizen) { 
	if ($citizen["dead"] != "0") continue;
	$x = (int)$citizen["x"]; $rx = $x - $cityx;
	$y = (int)$citizen["y"]; $ry = $y - $cityy;
	$bIsHome = ($x == $cityx && $y == $cityy);
	$bHeld = $citizen["hero"] != "0";
	$basedef = (int)$citizen["baseDef"];
	echo "<tr>";
	if ($gShowAvatars) echo "<td>".img($avatar_url.$citizen["avatar"],null,"style='width:90px; height:30px;'")."</td>";
	echo "<td>".MyEscHTML($citizen["name"])."</td>";
	echo "<td>".$basedef.($bHeld?"+2":"").img($icon_url_def).(isset($gDefIcon[$basedef])?img($gDefIcon[$basedef]):"").(($day==1 && $basedef > 1)?"<b>VERSCHWENDER!</b>":"")."</td>";
	echo "<td ".($bIsHome?"":"bgcolor=orange").">".($bIsHome?(img("images/map/city.gif")):("$rx,$ry"))."</td>";
	echo "</tr>\n";
	
	// normalo http://data.dieverdammten.de/gfx/icons/item_basic_suit.gif
	// hero http://data.dieverdammten.de/gfx/icons/small_hero.gif
	// buddler http://data.dieverdammten.de/gfx/icons/item_pelle.gif
	// aufklaerer http://data.dieverdammten.de/gfx/icons/item_vest_on.gif
	// kaempfer http://www.dieverdammten.de/gfx/icons/item_shield.gif
}
echo "</table>\n";



// <citizen dead="0" hero="0" name="Baldwin" avatar="hordes/e/b/a11743a1_9061.jpg" x="4" y="4" id="9061" ban="0" job="basic" out="0" baseDef="3">Rohstoffe bunkern für die Stadt.</citizen>


echo "</td><td valign=top>\n";

// ***** ***** ***** ***** ***** GEBÄUDE
foreach ($xml->data[0]->city[0]->building as $building) {
	echo img($icon_url.$building["img"].".gif").MyEscHTML($building["name"])."<br>\n";
}
// ***** ***** ***** ***** ***** upgrades

echo "<br>\n";
$icon_upgrade_url = "http://data.dieverdammten.de/gfx/icons/item_electro.gif";
foreach ($xml->data[0]->upgrades[0]->up as $upgrade) {
	echo img($icon_upgrade_url,"Verbesserung").$upgrade["level"]." ".MyEscHTML($upgrade["name"])."<br>\n"; // $upgrade["buildingId"]
}


// ***** ***** ***** ***** ***** TOTE
echo "<table border=1 cellpadding=0 cellspacing=0>\n";
$icon_msg_url = "http://data.dieverdammten.de/gfx/forum/smiley/h_chat.gif";
$icon_warning_url = "http://data.dieverdammten.de/gfx/icons/small_warning.gif";
$arr = array();
foreach ($xml->data[0]->cadavers[0]->cadaver as $cadaver) $arr[] = $cadaver;
function cmp_cadaver ($a,$b) { $a = (int)$a["day"]; $b = (int)$b["day"]; if ($a == $b) return 0; return ($a > $b) ? -1 : 1; }
usort($arr,"cmp_cadaver");
foreach ($arr as $cadaver) {
	$msg = $cadaver->msg;
	
	// <cadaver name="Aerox" dtype="1" id="11174" day="4">
	echo "<tr>";
	$cleanup = $cadaver->cleanup[0];
	$cleanup_txt = ($cleanup["user"] != "")?("entsorgt von ".htmlspecialchars($cleanup["user"])." : ".$cleanup["type"]):"";
	if ($cadaver["day"] == $gGameDay-1) {
		if ($cleanup && $cleanup["user"] != "") 
				echo "<td>".img($icon_url_death,$cleanup_txt)."</td>";
		else	echo "<td>".img($icon_warning_url,"LEICHE ENTSORGEN! SONST STEHT SIE ALS ZOMBIE WIEDER AUF!")."</td>";
	} else {
		echo "<td>".img($icon_url_death,$cleanup_txt)."</td>";
	}
	echo "<td>".(($msg && $msg != "")?img($icon_msg_url,htmlspecialchars($msg)):"")."</td>";
	echo "<td>".MyEscHTML($cadaver["name"])."</td>";
	echo "<td>Tag".$cadaver["day"]."</td>";
	echo "</tr>\n";
}
echo "</table>\n";



echo "</td><td valign=top>\n";



// ***** ***** ***** ***** ***** BANK
//~ echo "Bank:<br>";
$cats = array();
foreach ($xml->data[0]->bank[0]->item as $item) { 
	$c = (int)$item["count"];
	$b = (int)$item["broken"];
	$cat = (string)$item["cat"];
	$html = (($c>1)?($c."x"):"").img($icon_url_item.$item["img"].".gif",$item["name"]).(($b>0)?"($b kaputt)":"");
	if (!isset($cats[$cat])) $cats[$cat] = array();
	$cats[$cat][] = $html;
}

echo "<table border=0 cellspacing=0 cellpadding=1>\n";
$gCatTransLong = array("Rsc"=>"Rohstoffe","Furniture"=>"Einrichtungsgegenstände","Drug"=>"Medikamente","Armor"=>"Verteidigung","Food"=>"Vorräte","Weapon"=>"Waffen","Misc"=>"Verschiedenes");
$gCatTrans = array("Rsc"=>"Rohstoffe","Furniture"=>"Einrichtung","Drug"=>"Medikamente","Armor"=>"Verteidigung","Food"=>"Vorräte","Weapon"=>"Waffen","Misc"=>"Verschiedenes");
$cats2 = array();
foreach ($gCatTrans as $k => $v) $cats2[$k] = isset($cats[$k])?$cats[$k]:array();
foreach ($cats as $k => $v) if (!isset($gCatTrans[$k])) $cats2[$k] = $v;
foreach ($cats2 as $k => $arr) echo "<tr><th>".(isset($gCatTrans[$k])?$gCatTrans[$k]:$k).":</th><td align=right>".implode("</td><td align=right>",$arr)."</td></tr>\n";
echo "</table>\n";

// TODO : KAPUTTE MARKIEREN!! broken=1 <item name="Großer trockener Stock" count="3" id="15" cat="Weapon" img="staff" broken="1"/>


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


function TagIconURL ($tagid) { return "http://data.dieverdammten.de/gfx/icons/tag_".((int)$tagid).".gif"; }
function GetMapToolTip ($x,$y) { 
	$txt = "";
	global $gCitizens;
	foreach ($gCitizens as $citizen) { 
		if ($citizen["dead"] == "0" && (int)$citizen["x"] == $x && (int)$citizen["y"] == $y) ;
	}
	return $txt;
}
function MapGetSpecial ($x,$y) {
	global $gCityX,$gCityY,$gGameID;
	$rx = $x-$gCityX;
	$ry = $gCityY-$y;
	if ($gGameID == 826) {
		if ($rx ==  0 && $ry ==  1) return img("images/map/dot_leer.gif","($rx/$ry) ?? Zombies, Feld leer");
		if ($rx == -1 && $ry ==  0) return img("images/map/dot_leer.gif","($rx/$ry) ?? Zombies, Feld leer");
		if ($rx ==  1 && $ry ==  0) return img("images/map/dot_leer.gif","($rx/$ry) ?? Zombies, Feld leer");
		
		if ($rx ==  0 && $ry == -1) return img("images/map/dot_leer.gif","($rx/$ry) 2  Zombies, Feld leer");
		if ($rx == -1 && $ry == -1) return img("images/map/dot_leer.gif","($rx/$ry) ?? Zombies, Feld leer");
		if ($rx ==  1 && $ry == -1) return img("images/map/dot_leer.gif","($rx/$ry) ?? Zombies, Feld leer");
		
		if ($rx ==  0 && $ry == -2) return img("images/map/dot_leer.gif","($rx/$ry) 4  Zombies, Feld leer");
		if ($rx == -1 && $ry == -2) return img("images/map/dot_voll.gif","($rx/$ry) ?? Zombies, Feld REGENERIERT! GRABEN!");
		if ($rx ==  1 && $ry == -2) return img("images/map/dot_voll.gif","($rx/$ry) ?? Zombies, (Feld wird bis morgen ausgegraben)");
		
		if ($rx ==  0 && $ry == -3) return img("images/map/dot_leer.gif","($rx/$ry) 5  Zombies, Feld leer");
		if ($rx ==  1 && $ry == -3) return img("images/map/dot_leer.gif","($rx/$ry) 4  Zombies, Feld leer");
		if ($rx == -1 && $ry == -3) return img("images/map/dot_voll.gif","($rx/$ry) ?? Zombies, Feld REGENERIERT! GRABEN!");
		if ($rx ==  2 && $ry == -3) return img("images/map/dot_leer.gif","($rx/$ry) ?? Zombies, Feld leer");
		
		if ($rx ==  1 && $ry == -4) return img("images/map/dot_voll.gif","($rx/$ry) 6  Zombies, (Feld wird bis morgen ausgegraben)");
		if ($rx ==  0 && $ry == -4) return img("images/map/dot_voll.gif","($rx/$ry) ?? Zombies, Feld REGENERIERT! GRABEN!");
		if ($rx ==  2 && $ry == -4) return img("images/map/dot_voll.gif","($rx/$ry) ?? Zombies, Feld REGENERIERT! GRABEN!");
		
		if ($rx ==  1 && $ry == -5) return img("images/map/dot_voll.gif","($rx/$ry) ?? Zombies, Feld REGENERIERT! GRABEN!");
	}
	return false;
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
			if ($data["tag"]) $tagimg = img(TagIconURL($data["tag"]),GetMapToolTip($x,$y));
		}
		if ($x == $cityx && $y == $cityy) $bgimg = "city.gif";
		
		$bgimg = "background='images/map/$bgimg'";
		$special = MapGetSpecial($x,$y);
		if ($special) $tagimg = $special;
		
		$style = ""; // "bgcolor=green"
		echo "<td $style $bgimg width=20 height=20>".$tagimg."</td>";
		//~ echo "<td width=16 height=16>".($data?("nvt=".$data["nvt"].",tag=".$data["tag"]):"")."</td>";
		//~ echo "<td width=16 height=16>".($data?"x":"")."</td>";
	}
	echo "</tr>\n";
}
echo "</table>\n";
echo img("images/map/zone.gif").":0 Zombies, alleine ok"."<br>\n";
echo img("images/map/zone_d1.gif").":1-2 Zombies, alleine ok"."<br>\n";
echo img("images/map/zone_d2.gif").":2-4 Zombies, mindestens zu zweit hin!"."<br>\n";
echo img("images/map/zone_d3.gif").":5+ Zombies, mindestens zu dritt hin!"."<br>\n";
echo img("images/map/zone_bg.gif").":0-99 Zombies, mindestens zu dritt hin! unerforscht, hier könnte noch eine ruine sein"."<br>\n";
echo img("images/map/zone_nv.gif").":0-99 Zombies, mindestens zu dritt hin! schon erforscht, aber HEUTE war noch niemand hier"."<br>\n";
echo img(TagIconURL(5))."als leer markiert, wenn sich die Zone nicht inzwischen regeneriert hat (ForschungsTurm!)<br> findet man hier nur noch ".
img($icon_url_item."wood_bad.gif","BaumStumpf")." und ".
img($icon_url_item."metal_bad.gif","MetallTr&uuml;mmer")."<br>\n";
//~ http://data.dieverdammten.de/gfx/icons/item_wood_bad.gif
//~ http://data.dieverdammten.de/gfx/icons/item_metal_bad.gif





echo "</td></tr></table>\n";

// ***** ***** ***** ***** ***** CITY TABLE END

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







/*
<map hei="12" wid="12"><zone x="3" y="1" nvt="1" tag="5"/><zone x="4" y="1" nvt="1"/><zone x="5" y="1" nvt="1"/><zone x="6" y="1" nvt="1"/><zone x="7" y="1" nvt="1"/><zone x="2" y="2" nvt="1"/><zone x="3" y="2" nvt="1" tag="5"/>
*/

//~ echo "xml=".htmlspecialchars($xml);

//~ $xmlurl = "sample.xml";
// 



PrintFooter(); 
?>