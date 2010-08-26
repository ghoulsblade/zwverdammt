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
function StripUml($txt) { return preg_replace('/[^a-zA-Z0-9]/','',$txt); }

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

//~ session_start(); // -> man kann $_SESSION benutzen

function GetLatestXmlStrFromSeelenID ($seelenid) { return sqlgetone("SELECT xml FROM xml WHERE ".arr2sql(array("seelenid"=>$seelenid))." ORDER BY id DESC LIMIT 1"); }

if (isset($_REQUEST["ajax"])) {
	$xmlstr = GetLatestXmlStrFromSeelenID($gSeelenID);
	if (!$xmlstr) exit("failed to load xml");
	$xml = simplexml_load_string(MyEscXML($xmlstr));
	MyLoadGlobals();
	$rx = intval($_REQUEST["x"]);
	$ry = intval($_REQUEST["y"]);
	$x = $gCityX + $rx;
	$y = $gCityY - $ry;
	AddMapNote($rx,$ry,intval($_REQUEST["icon"]),-1,$_REQUEST["msg"]);
	echo MapGetSpecial($x,$y);
	exit();
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
.map td {
	width: 21px; height: 21px;
	background-repeat:no-repeat;
	margin: 0px;
	padding: 0px;
}
//.mapcell img {
	//margin: 0px;
	//padding: 0px;
	//~ width: 18px; height: 18px;
//}
.bframe {
	border:1px solid black;
}
.mapaddsmall_input {
	font-family:Arial,sans-serif;
	color:#000000;
	background-color:#F4FFF4;
	font-size:12px;
	border: 1px solid #008030;
	height:18px;
	//~ width:20px;
	padding:0px;
	margin:0px;
}
.mapaddsmall_button {
	font-family:Arial,sans-serif;
	color:#000000;
	background-color:#F4FFF4;
	font-size:12px;
	border: 1px solid #008030;
	height:20px;
	width:20px;
	// padding:0px;
	margin:2px 0px 0px 0px; // top,bottom,left,right
}
</style>
</head>
<body>
<script type="text/javascript">
function RadioValue(rObj,vDefault) {
	for (var i=0; i<rObj.length; i++) if (rObj[i].checked) return rObj[i].value;
	return vDefault;
}


function AddMapNote_Form (form) {
	var x = form.x.value;
	var y = form.y.value;
	var sQuery = "?ajax=addmapnote&reply=map&x="+escape(""+x)+"&y="+escape(""+y)+"&icon="+RadioValue(form.icon,-1)+"&msg="+escape(form.msg.value);
	/*
	var myAjax = new Ajax.Request(
	  query, 
	  { method: 'get', onComplete: UpdateMapHTML }
	);
	*/
	
	if (window.XMLHttpRequest) 
			xmlhttp=new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
	else	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	// code for IE6, IE5
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			//~ document.getElementById("idMapContainer").innerHTML = xmlhttp.responseText; 
			document.getElementById("map_"+x+"_"+y).innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET",sQuery,true);
	xmlhttp.send();
}


</script>
<noscript>
(!javascript needed!)
</noscript>
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
$xmlstr = false;
if ($gUseSampleData) { 
	$xmlurl = $xmlurl_sample; 
	$gDemo = true;
	$gStoreXML = false;
	$xmlstr = GetLatestXmlStrFromSeelenID(kMySQL_SampleSoulID);
	//~ if ($xmlstr) echo "sample load from db OK<br>\n"; else echo "sample load from db failed<br>\n";
}
if (!$xmlstr) $xmlstr = file_get_contents($xmlurl);
@$xml = simplexml_load_string(MyEscXML($xmlstr));


if (!$xml->data[0]->city[0]["city"] || $xml->status[0]["open"] == "0") {
	echo "<h1>Webseite down, Zombie-Angriff im Gange!</h1>\n";
	echo "(lade dummy/demo daten)<br>\n";
	$xmlurl = $xmlurl_sample;
	$xmlstr = file_get_contents($xmlurl);
	$xml = simplexml_load_string(MyEscXML($xmlstr));
	$gStoreXML = false;
}

//~ $_SESSION["xml"] = $xml;

function Map ($x,$y) { global $gMap; return isset($gMap["$x,$y"])?$gMap["$x,$y"]:false; }
	
function MyLoadGlobals () {
	global $xml,$icon_url,$icon_url_item,$avatar_url,$city,$icon_url_zombie,$icon_url_attack_in,$icon_url_death,$icon_url_def,$def,$gGameDay,$gGameID,$gCityX,$gCityY;
	global $gCitizens,$buerger_draussen,$buerger_alive;

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
	$gGameDay = (int)$xml->headers[0]->game[0]["days"];
	$gGameID = (int)$xml->headers[0]->game[0]["id"];

	$gCityX = $xml->data[0]->city["x"];
	$gCityY = $xml->data[0]->city["y"];

	$buerger_draussen = 0;
	$buerger_alive = 0;
	$gCitizens = $xml->data[0]->citizens[0]->citizen;
	foreach ($gCitizens as $citizen) { 
		if ($citizen["dead"] == "0") ++$buerger_alive;
		if ((int)$citizen["x"] == $gCityX && (int)$citizen["y"] == $gCityY) {} else { ++$buerger_draussen; }
	}
	
	global $gMap,$w,$h;
	$map = $xml->data[0]->map[0];
	$w = $map["wid"];
	$h = $map["hei"];
	$gMap = array();
	function MapSet ($x,$y,$data) { 
		global $gMap;
		$gMap["$x,$y"] = $data; 
		//~ echo "MapSet($x,$y,nvt=".$data["nvt"].",tag=".$data["tag"].")<br>\n";
	}
	foreach ($map->zone as $zone) MapSet((int)$zone["x"],(int)$zone["y"],$zone);
}
MyLoadGlobals();

if ($gStoreXML) {
	$o = false;
	$o->seelenid = (string)$gSeelenID;
	$o->time = time();
	$o->gameid = $gGameID;
	$o->cityname = (string)$city["city"];
	$o->day = (int)$gGameDay;
	$o->xml = $xmlstr;
	sql("INSERT INTO xml SET ".obj2sql($o));
}

echo "Stadt=".$city["city"];
echo " Tag=".$gGameDay;
echo " ".img($icon_url."small_water.gif","Wasser").":".$city["water"];
echo " &Uuml;berlebende=".$buerger_alive;
echo " draussen=".$buerger_draussen;
if ($gDemo) echo " <b>(demo/offline daten)</b>";
echo "<br>\n";

$gBuildingDone = array();
$gUpgrades = array();
foreach ($xml->data[0]->upgrades[0]->up as $upgrade) $gUpgrades[StripUml($upgrade["name"])] = (int)$upgrade["level"];
foreach ($xml->data[0]->city[0]->building as $building) $gBuildingDone[StripUml($building["name"])] = true;

function GetBuildingLevel ($bname) { // -1= not build, 0=built but no upgrade, >1 = upgrade level 
	global $gBuildingDone,$gUpgrades;
	$bname = StripUml($bname);
	if (!isset($gBuildingDone[$bname])) return -1;
	if (!isset($gUpgrades[$bname])) return 0;
	return $gUpgrades[$bname];
}


$e = $xml->data[0]->estimations[0]->e[0];
$zombie_min = (int)($e["min"]);
$zombie_max = (int)($e["max"]);
$bEstMax = ($e["maxed"]!="0"); // schon maximale qualität ?
echo "Schätzung".($bEstMax?"(gut)":"(<b>schlecht</b>)").":".img($icon_url_zombie,"Zombies")."$zombie_min-$zombie_max -&gt; ".img($icon_url_def,"def")."$def -&gt; ".img($icon_url_attack_in,"tote")."".max(0,$zombie_min-$def)."-".max(0,$zombie_max-$def)."<br>\n";
$stat = array(0,24,50,97,149,215,294,387,489,595,709,831,935,1057,1190,1354,1548,1738,1926,2140,2353,2618,2892,3189,3506,3882,3952,4393,4841,5339,5772,6271,6880,7194,7736,8285,8728,9106,9671,9888,10666,11508,11705,12608,12139,12921,15248,11666);
$zombie_av = isset($stat[$gGameDay]) ? $stat[$gGameDay] : false;
if ($zombie_av) echo "Statistik:".img($icon_url_zombie,"Zombies")."$zombie_av -&gt; ".img($icon_url_def,"def")."$def -&gt; ".img($icon_url_attack_in,"tote")."".max(0,$zombie_av-$def)."<br>\n";
$def_graben_delta = array(20,13,21,32,33,51,0);
echo "Großer Graben verbessern/bauen:+".$def_graben_delta[GetBuildingLevel("Großer Graben")+1].img($icon_url_def,"def")."<br>\n";
if (!$bEstMax) echo "<b>Hilf mit die Schätzung im Wachturm zu verbessern!</b><br>\n";


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

Der Nordosten wurde gestern von einem heftigen Unwetter heimgesucht. 
holle:	Im Osten haben gestern ein paar heftige Sandstürme gewütet.
holle:	Im Südosten wurden gestern ein paar meteorologische Anomalien gesichtet.
holle:	Im Norden wurden gestern ein paar meteorologische Anomalien gesichtet.
holle:	Ein paar Sandstürme wurden im Osten beobachtet. 

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
	$x = (int)$citizen["x"]; $rx = $x - $gCityX;
	$y = (int)$citizen["y"]; $ry = $gCityY - $y;
	$bIsHome = ($x == $gCityX && $y == $gCityY);
	$bHeld = $citizen["hero"] != "0";
	$basedef = (int)$citizen["baseDef"];
	echo "<tr>";
	if ($gShowAvatars) echo "<td>".img($avatar_url.$citizen["avatar"],null,"style='width:90px; height:30px;'")."</td>";
	echo "<td>".MyEscHTML($citizen["name"])."</td>";
	echo "<td>".$basedef.($bHeld?"+2":"").img($icon_url_def).(isset($gDefIcon[$basedef])?img($gDefIcon[$basedef]):"").(($gGameDay==1 && $basedef > 1)?"<b>VERSCHWENDER!</b>":"")."</td>";
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



// ***** ***** ***** ***** ***** GEBÄUDE
echo href("http://nobbz.de/wiki/index.php/Geb%C3%A4ude_%C3%9Cbersicht","Gebäude").":<br>\n";
foreach ($xml->data[0]->city[0]->building as $building) {
	echo img($icon_url.$building["img"].".gif").MyEscHTML($building["name"])."<br>\n";
}
// ***** ***** ***** ***** ***** upgrades

echo "<br>\n";
echo href("http://nobbz.de/wiki/index.php/Verbesserung_des_Tages","Verbesserungen:").":<br>\n";
$icon_upgrade_url = "http://data.dieverdammten.de/gfx/icons/item_electro.gif";
foreach ($xml->data[0]->upgrades[0]->up as $upgrade) {
	echo img($icon_upgrade_url,"Verbesserung").$upgrade["level"]." ".MyEscHTML($upgrade["name"])."<br>\n"; // $upgrade["buildingId"]
}

echo "</td><td valign=top>\n";




// ***** ***** ***** ***** ***** BANK
//~ echo "Bank:<br>";
$cats = array();
foreach ($xml->data[0]->bank[0]->item as $item) { 
	$c = (int)$item["count"];
	$cat = (string)$item["cat"];
	$bBroken = $item["broken"] != 0;
	$html = (($c>1)?($c."x"):"").img($icon_url_item.$item["img"].".gif",$item["name"]);
	if ($bBroken) $html = "<span style='border:1px solid red'>".$html."</span>";
	if (!isset($cats[$cat])) $cats[$cat] = array();
	$cats[$cat][] = $html;
}

echo "<table border=0 cellspacing=0 cellpadding=1>\n";
$gCatTransLong = array("Rsc"=>"Rohstoffe","Furniture"=>"Einrichtungsgegenstände","Drug"=>"Medikamente","Armor"=>"Verteidigung","Food"=>"Vorräte","Weapon"=>"Waffen","Misc"=>"Verschiedenes");
$gCatTrans = array("Rsc"=>"Rohst.",
	"Furniture"=>href("http://nobbz.de/wiki/index.php/Einrichtungsgegenst%C3%A4nde","Deko"),
	"Drug"=>"Drogen",
	"Armor"=>href("http://nobbz.de/wiki/index.php/Kategorie:Verteidigungsgegenstand","Verteid."),
	"Food"=>"Vorrat",
	"Weapon"=>href("http://nobbz.de/wiki/index.php/Waffen","Waffen"),
	"Misc"=>"Versch.");
$cats2 = array();
foreach ($gCatTrans as $k => $v) $cats2[$k] = isset($cats[$k])?$cats[$k]:array();
foreach ($cats as $k => $v) if (!isset($gCatTrans[$k])) $cats2[$k] = $v;
foreach ($cats2 as $k => $arr) echo "<tr><th>".(isset($gCatTrans[$k])?$gCatTrans[$k]:$k).":</th><td align=right>".implode("</td><td align=right>",$arr)."</td></tr>\n";
echo "</table>\n";

// TODO : KAPUTTE MARKIEREN!! broken=1 <item name="Großer trockener Stock" count="3" id="15" cat="Weapon" img="staff" broken="1"/>


// ***** ***** ***** ***** ***** MAP




function TagIconURL ($tagid) { return "http://data.dieverdammten.de/gfx/icons/tag_".((int)$tagid).".gif"; }
function GetMapToolTip ($x,$y) { 
	$txt = "";
	global $gCitizens;
	foreach ($gCitizens as $citizen) { 
		if ($citizen["dead"] == "0" && (int)$citizen["x"] == $x && (int)$citizen["y"] == $y) ;
	}
	return $txt;
}

function AddMapNote ($rx,$ry,$icon,$zombies,$txt) { // $rx,$ry relative from city   0, 1
	global $gGameID,$gSeelenID,$gGameDay;
	$o = false;
	$o->x = $rx;
	$o->y = $ry;
	$o->icon = $icon;
	$o->zombies = $zombies;
	$o->txt = $txt;
	$o->time = time();
	$o->day = $gGameDay;
	$o->gameid = $gGameID;
	$o->seelenid = $gSeelenID;
	sql("INSERT INTO mapnote SET ".obj2sql($o));
}
function GetMapNote ($x,$y) { global $gGameID; return sqlgetobject("SELECT * FROM mapnote WHERE ".arr2sql(array("gameid"=>$gGameID,"x"=>$x,"y"=>$y)," AND ")." ORDER BY `id` DESC LIMIT 1"); }

if ($gGameID == 826 && !sqlgetone("SELECT 1 FROM mapnote WHERE gameid = ".intval($gGameID))) {
	AddMapNote( 0, 1,0,-1,"");
	AddMapNote(-1, 0,0,-1,"");
	AddMapNote( 1, 0,0,-1,"");
	AddMapNote( 0,-1,0,2 ,"");
	AddMapNote(-1,-1,0,-1,"");
	AddMapNote( 1,-1,0,-1,"");
	AddMapNote( 0,-2,0,4 ,"");
	AddMapNote(-1,-2,1,-1,"REGENERIERT! GRABEN!");
	AddMapNote( 1,-2,1,-1,"(Feld wird bis morgen ausgegraben)");
	AddMapNote( 0,-3,0,5 ,"");
	AddMapNote( 1,-3,0,4 ,"");
	AddMapNote(-1,-3,1,-1,"REGENERIERT! GRABEN!");
	AddMapNote( 2,-3,0,-1,"");
	AddMapNote( 1,-4,1,6 ,"(Feld wird bis morgen ausgegraben)");
	AddMapNote( 0,-4,1,-1,"REGENERIERT! GRABEN!");
	AddMapNote( 2,-4,1,-1,"REGENERIERT! GRABEN!");
	AddMapNote( 1,-5,1,-1,"REGENERIERT! GRABEN!");
}


function GetZombieNumText ($x,$y) {
	$data = Map($x,$y);
	if ($data["danger"] == 1) return "1-2";
	if ($data["danger"] == 2) return "2-4";
	if ($data["danger"] >= 3) return "5+";
	if (((int)$data["nvt"]) == 0) return "0"; // heute viewed und kein danger
	return "0-99";
}

function MapGetSpecial ($x,$y) {
	global $gCityX,$gCityY,$gGameID,$gGameDay;
	$rx = $x-$gCityX;
	$ry = $gCityY-$y;
	$o = GetMapNote($rx,$ry); if (!$o) return false;
	$age = (int)$gGameDay - (int)$o->day;
	$agetxt = ($age != 0) ? (($age > 1) ? "[vor $age Tagen] " : "[gestern] ") : "";
	$old = ($age != 0) ? "_old" : "";
	if ($o->icon == 0) return img("images/map/dot8_leer".$old.".gif","($rx/$ry) ".(($o->zombies >= 0)?$o->zombies:GetZombieNumText($x,$y))." Zombies. $agetxt (leer) ".$o->txt);
	if ($o->icon == 1) return img("images/map/dot8_voll".$old.".gif","($rx/$ry) ".(($o->zombies >= 0)?$o->zombies:GetZombieNumText($x,$y))." Zombies. $agetxt (voll) ".$o->txt);
	return false;
}

echo "<span class='map' id='idMapContainer'>\n";
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
		if ($x == $gCityX && $y == $gCityY) $bgimg = "city.gif";
		
		$bgimg = "background='images/map/$bgimg'";
		$special = MapGetSpecial($x,$y); // map_$x_$y
		if ($special) $tagimg = $special;
		//~ $tagimg = "";
		
		$rx = $x-$gCityX;
		$ry = $gCityY-$y;
		
		$style = ""; // "bgcolor=green"
		echo "<td $style $bgimg><span class='mapcell' id='map_".$rx."_".$ry."'>".trim($tagimg)."</span></td>";
		//~ echo "<td width=16 height=16>".($data?("nvt=".$data["nvt"].",tag=".$data["tag"]):"")."</td>";
		//~ echo "<td width=16 height=16>".($data?"x":"")."</td>";
	}
	echo "</tr>\n";
}
echo "</table>\n";
echo "</span>\n";

?>
<form action="?" method="post" class='mapadd' id='form_mapadd_1'>
	<input class='mapaddsmall_input' type="text" size="1" maxlength="3" name="x" value=0>/
	<input class='mapaddsmall_input' type="text" size="1" maxlength="3" name="y" value=0>
	<span class='bframe'><input type="radio" name="icon" value="-1" selected></span>
	<span class='bframe'><input type="radio" name="icon" value="1"><?=img("images/map/dot8_voll.gif")?></span>
	<span class='bframe'><input type="radio" name="icon" value="0"><?=img("images/map/dot8_leer.gif")?></span>
	<input class='mapaddsmall_input' type="text" size="60" name="msg">
	<input class='mapaddsmall_button' type="button" name="BLA" value="ok" onclick="AddMapNote_Form(this.form)">
</form>
<?php




echo img("images/map/zone.gif")		.img($icon_url_zombie)."0, alleine ok"."<br>\n";
echo img("images/map/zone_d1.gif")	.img($icon_url_zombie)."1-2, alleine ok"."<br>\n";
echo img("images/map/zone_d2.gif")	.img($icon_url_zombie)."2-4, mindestens zu zweit hin!"."<br>\n";
echo img("images/map/zone_d3.gif")	.img($icon_url_zombie)."5+, mindestens zu dritt hin!"."<br>\n";
echo img("images/map/zone_bg.gif")	.img($icon_url_zombie)."0-99, mindestens zu dritt hin! unerforscht, hier könnte noch eine ruine sein"."<br>\n";
echo img("images/map/zone_nv.gif")	.img($icon_url_zombie)."0-99, mindestens zu dritt hin! schon erforscht, aber HEUTE war noch niemand hier"."<br>\n";
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