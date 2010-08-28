<?php
// uebersicht und karte fuer das browsergame www.dieverdammten.de
// xml api via http://www.php.net/manual/de/book.simplexml.php

require_once("defines.php");
require_once("roblib.php");
function href ($url,$title=false) { return "<a href='$url'>".($title?$title:$url)."</a>"; }

//~ function MyEscXML ($txt) { return htmlspecialchars($txt); } // ö->uuml;
function MyEscXML ($txt) { 
	//~ return utf8_decode($txt);
	return strtr($txt,array("roßer"=>"rosser","ö"=>"&ouml;","ü"=>"&uuml;","ä"=>"ae","Ö"=>"&Ouml;","Ü"=>"&Uuml;","Ä"=>"&Auml;")); 
		//~ wegen den umlauten ein tip: utf8_decode 
} // htmlspecialchars
function MyEsc ($txt) { return utf8_decode($txt); } // htmlspecialchars
function MyEscHTML ($txt) { return utf8_decode($txt); } // htmlspecialchars
function MyEscHTML2 ($txt) { return htmlspecialchars(($txt)); } // htmlspecialchars
//~ function MyEsc ($txt) { return strtr($txt,array("Ã?"=>"ß","Ã¼"=>"ü","Ã¶"=>"ö")); } // htmlspecialchars
function img ($url,$title=false,$special="") { $title = $title?strtr(utf8_decode(htmlentities($title)),array("'"=>'"')):false; return "<img $special src='$url' ".($title?("alt='$title' title='$title'"):"")."/>"; }
function StripUml($txt) { return preg_replace('/[^a-zA-Z0-9]/','',$txt); }

// note : htmlentities() is identical to htmlspecialchars() in all ways, except with htmlentities(), all characters which have HTML character entity equivalents are translated into these entities. 

define("kNumIcons",7);
define("kIconID_Notiz",6);


$gIconText = array(
	0=>"Feld leer",
	1=>"Feld regeneriert : graben!",
	2=>"Feld temporaer gesichert",
	3=>"Notruf",
	4=>"warnung",
	5=>"ok",
	6=>"notiz",
);

$gShowAvatars = false;

$temp_seelenid = isset($_COOKIE["SeelenID"]) ? $_COOKIE["SeelenID"] : false;
$gUseSampleData = isset($_REQUEST["sample"]);
if ($gUseSampleData) $temp_seelenid = "abcdefghijklmnopqrstuvwxyz";

if (isset($_REQUEST["LogOut"])) {
	setcookie ("SeelenID", "", time() - 3600);
	//~ echo "logout<br>";
	$temp_seelenid = false;
} elseif (isset($_REQUEST["Login"])) {
	setcookie ("SeelenID", $_REQUEST["SeelenID"], time() + 30*24*3600);
	//~ echo "login:".$_REQUEST["SeelenID"]."<br>";
	$temp_seelenid = $_REQUEST["SeelenID"];
}
if ($temp_seelenid) $temp_seelenid = preg_replace('/[^a-zA-Z0-9]/','',$temp_seelenid);
define("kSeelenID",$temp_seelenid); // replaces the old $gSeelenID


//~ session_start(); // -> man kann $_SESSION benutzen

function GetLatestXmlStrFromGameID		($gameid)	{ return sqlgetone("SELECT xml FROM xml WHERE ".arr2sql(array("gameid"=>$gameid))." ORDER BY id DESC LIMIT 1"); }
function GetLatestXmlStrFromSeelenID	($seelenid)	{ return sqlgetone("SELECT xml FROM xml WHERE ".arr2sql(array("seelenid"=>$seelenid))." ORDER BY id DESC LIMIT 1"); }

define("kSearchGameID",isset($_REQUEST["gameid"])?intval($_REQUEST["gameid"]):false);

if (!kSearchGameID && isset($_REQUEST["ajax"])) {
	$xmlstr = GetLatestXmlStrFromSeelenID(kSeelenID);
	if (!$xmlstr) exit("failed to load xml");
	$xml = simplexml_load_string(MyEscXML($xmlstr));
	MyLoadGlobals();
	switch ($_REQUEST["ajax"]) {
		case "addmapnote":	Ajax_AddMapNote(); break;
		case "cellinfo":	Ajax_MapCellInfo(); break;
		default:			echo "unknown request ".$_REQUEST["ajax"]; break;
	}
	exit();
}

function Ajax_AddMapNote () {
	$rx = intval($_REQUEST["x"]);
	$ry = intval($_REQUEST["y"]);
	$x = kCityX + $rx;
	$y = kCityY - $ry;
	//~ echo "Ajax_AddMapNote z=".$_REQUEST["zombies"]."<br>\n";
	AddMapNote($rx,$ry,intval($_REQUEST["icon"]),$_REQUEST["zombies"],$_REQUEST["msg"]);
	echo MapGetCellContent($x,$y);
}


function Ajax_MapCellInfo () { // idMapCellInfo
	global $gGameID,$gIconText;
	$rx = intval($_REQUEST["x"]);
	$ry = intval($_REQUEST["y"]);
	$lastnote = GetMapNote($rx,$ry);
	$icon = $lastnote ? intval($lastnote->icon) : kIconID_Notiz;
	$msg = $lastnote ? $lastnote->txt : "";
	$zombies = $lastnote ? $lastnote->zombies : "?";
	if ($zombies == -1) $zombies = "?";
	//~ echo "$rx,$ry lastnote=".($lastnote?"ok":"-")." gameid=$gGameID ".$lastnote->icon." ".$lastnote->txt."<br>\n";
	
	?>
	<form action="?" method="post" class='mapadd' id='form_mapadd_1'>
		(<?=$rx?>/<?=$ry?>) [<?=abs($rx)+abs($ry)?>AP]  <?=$lastnote?("[".GetAgeText($lastnote->day,$lastnote->time)."]"):""?><br>
		<?=img(kIconURL_zombie,"zombies")?><input class='mapaddsmall_input' type="text" size="3" maxlength="5" name="zombies" value='<?=$zombies?>' />
		<input type="hidden" name="x" value='<?=$rx?>'/>
		<input type="hidden" name="y" value='<?=$ry?>'/>
		<span class='bframe'><input type="radio" name="icon" value="-1" <?=($icon==-1)?"checked":""?> /></span>
		<?php for ($i=0;$i<kNumIcons;++$i) {?>
		<span class='bframe'><input type="radio" name="icon" value="<?=$i?>" <?=($icon==$i)?"checked":""?> /><?=img("images/map/icon_".$i.".gif",$gIconText[$i])?></span>
		<?php }?>
		<br>
		<textarea cols="40" rows="10" name='msg'><?=htmlspecialchars($msg)?></textarea><br>
		<?php if (IsOwnGame()) {?>	
			<table><tr><td valign='top'>
				<input class='mapaddsmall_button' type="button" name="speichern" value="speichern" onclick="AddMapNote_Form(this.form)">
			</td><td valign='top'>
				&nbsp;
				&nbsp;
				&nbsp;
				&nbsp;
			</td><td valign='top'>
				<?php /* ***** *****  UTIL : BUDDLER ***** ***** */ ?>
				<?php $tipp = "title='ankreuzen = LEERES feld = rot'"; ?>
				<table border=1 cellspacing=0>
				<tr>
					<td><?=img(kIconURL_hero_dig,("Helden die den Beruf Buddler wählen können sehen ob umgebende Felder leer sind"))?></td>
					<td><input type="checkbox" name="dig_north" value="1" <?=$tipp?>></td>
					<td></td>
				</tr><tr>
					<td><input type="checkbox" name="dig_west" value="1" <?=$tipp?>></td>
					<td><input type="checkbox" name="dig_mid" value="1" <?=$tipp?>></td>
					<td><input type="checkbox" name="dig_east" value="1" <?=$tipp?>></td>
				</tr><tr>
					<td></td>
					<td><input type="checkbox" name="dig_south" value="1" <?=$tipp?>></td>
					<td><input class='mapaddsmall_button2' type="button" name="util_digg" value="ok" onclick="Map_Digg(this.form)"></td>
				</tr></table>
			</td><td valign='top'>
				<?php /* ***** *****  UTIL : AUFKLÄRER ***** ***** */ ?>
				<?php $tipp = "title='Geschätzte Zombieanzahl'"; ?>
				<table border=1 cellspacing=0>
				<tr>
					<td><?=img(kIconURL_hero_scout,("Helden die den Beruf Aufklärer wählen können die Anzahl der Zombies in umgebenden Feldern abschätzen."))?></td>
					<td><input class='mapaddsmall_input' type="text" size="3" maxlength="5" name="zombie_north" <?=$tipp?> /></td>
					<td></td>
				</tr><tr>
					<td><input class='mapaddsmall_input' type="text" size="3" maxlength="5" name="zombie_west" <?=$tipp?> /></td>
					<td><input class='mapaddsmall_input' type="text" size="3" maxlength="5" name="zombie_mid" <?=$tipp?> /></td>
					<td><input class='mapaddsmall_input' type="text" size="3" maxlength="5" name="zombie_east" <?=$tipp?> /></td>
				</tr><tr>
					<td></td>
					<td><input class='mapaddsmall_input' type="text" size="3" maxlength="5" name="zombie_south" <?=$tipp?> /></td>
					<td><input class='mapaddsmall_button2' type="button" name="util_scout" value="ok" onclick="Map_Scout(this.form)"></td>
				</tr></table>
			</td></tr></table>
		<?php }?>
	</form>
	<?php
}

function GetCurrentGameIDForSeelenID ($seelenid) { return sqlgetone("SELECT gameid FROM xml WHERE ".arr2sql(array("seelenid"=>$seelenid))." ORDER BY id DESC LIMIT 1"); }
function IsOwnGame () { 
	global $gGameID; 
	return GetCurrentGameIDForSeelenID(kSeelenID) == $gGameID;
}


function AddMapNote ($rx,$ry,$icon,$zombies,$txt) { // $rx,$ry relative from city   0, 1
	if (!IsOwnGame()) return;
	global $gGameID,$gGameDay;
	$o = false;
	$o->x = $rx;
	$o->y = $ry;
	$o->icon = $icon;
	$o->zombies = $zombies; // ($zombies=="?")?-1:intval($zombies);
	$o->txt = $txt;
	$o->time = time();
	$o->day = $gGameDay;
	$o->gameid = $gGameID;
	$o->seelenid = kSeelenID;
	sql("INSERT INTO mapnote SET ".obj2sql($o));
}
function GetMapNote ($x,$y) {
	global $gGameID; 
	//~ echo "SELECT * FROM mapnote WHERE ".arr2sql(array("gameid"=>$gGameID,"x"=>$x,"y"=>$y)," AND ");
	return sqlgetobject("SELECT * FROM mapnote WHERE ".arr2sql(array("gameid"=>$gGameID,"x"=>$x,"y"=>$y)," AND ")." ORDER BY `id` DESC LIMIT 1"); 
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
width:450px;
}
.broken { border:1px solid red; }
.map td {
	width: 21px; height: 21px;
	min-width: 21px; min-height: 21px;
	background-repeat:no-repeat;
	margin: 0px;
	padding: 0px;
}
//.mapcell img {
	//margin: 0px;
	//padding: 0px;
	//~ width: 18px; height: 18px;
//}
.map {	
	display:inline;
}
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
	width:20px;
	padding:0px;
	margin:0px;
}
.mapcellzombietxt {
	font-family:Arial,sans-serif;
	color:#800000;
	font-size:10px;
	font-weight:bold;
	background-color:#8aa534;
	cursor:default;
}
.mapaddsmall_button {
	font-family:Arial,sans-serif;
	color:#000000;
	background-color:#F4FFF4;
	font-size:12px;
	border: 1px solid #008030;
	height:20px;
	// width:20px;
	// padding:0px;
	margin:2px 0px 0px 0px; // top,bottom,left,right
}
.mapaddsmall_button2 {
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
a img { border:0px; }
</style>
</head>
<body>
<script type="text/javascript">
function RadioValue(rObj,vDefault) {
	for (var i=0; i<rObj.length; i++) if (rObj[i].checked) return rObj[i].value;
	return vDefault;
}


function MyAjaxGet (sQuery,sTargetID) {
	if (window.XMLHttpRequest) 
			xmlhttp=new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
	else	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	// code for IE6, IE5
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			if (document.getElementById(sTargetID)) 
				document.getElementById(sTargetID).innerHTML = xmlhttp.responseText;
			else alert("ajax target element not found : "+sTargetID);
		}
	}
	xmlhttp.open("GET",sQuery,true);
	xmlhttp.send();
}

function AddMapNote_Form (form) {
	var x = form.x.value;
	var y = form.y.value;
	var z = form.zombies.value;
	var sQuery = "?ajax=addmapnote&x="+escape(""+x)+"&y="+escape(""+y)+"&zombies="+escape(""+z)+"&icon="+RadioValue(form.icon,-1)+"&msg="+escape(form.msg.value);
	MyAjaxGet(sQuery,"map_"+x+"_"+y); // whole map would be idMapContainer
}

function MapClickCell (x,y) {
	//~ alert("ClickCell"+x+","+y);
	MyAjaxGet("?ajax=cellinfo&x="+escape(x)+"&y="+escape(y),"idMapCellInfo");
}
function MapClickCell_Dummy (x,y) { // IsOwnGame()?"MapClickCell":"MapClickCell_Dummy"
	document.getElementById("idMapCellInfo").innerHTML = "nur in der eigenen Stadt möglich";
}

</script>
<noscript>
(!javascript needed!)
</noscript>
<?php


$xmlurl = kSeelenID ? ("http://www.dieverdammten.de/xml/?k=".urlencode(kSeelenID)) : false;

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

// ***** ***** ***** ***** ***** HEADER

function PrintHeaderSection () { // login,links,disclaimer	
	global $gGameID,$xmlurl;
	echo "<table><tr><td valign=top>";

		echo "<table><tr><td>";
			echo href("http://verdammt.zwischenwelt.org/"); 
		echo "</td><td>";
			if (kSeelenID) { ?><form action="" method="POST"><input type="submit" name="LogOut" value="LogOut"></form><?php }
		echo "</td><td>";
			$otherCities = sqlgettable("SELECT *,MAX(`day`) as maxday FROM xml GROUP BY gameid ORDER BY id DESC");
			if (kSeelenID && count($otherCities) > 1) {
			$mygameid = kSearchGameID ? kSearchGameID : $gGameID;
			?>
			<form action='?' method="GET">
			<select name="gameid">
			<?php foreach ($otherCities as $city) {?>
			<option value="<?=$city->gameid?>" <?=($mygameid == $city->gameid)?"selected":""?>><?=htmlspecialchars(utf8_decode($city->cityname))?>(Tag<?=$city->maxday?>)</option>
			<?php }?>
			</select>
			<input type="submit" name="Go" value="Go"></form>
			</form>
			<?php
			}
		echo "</td></tr></table>";


		echo "Author: ".href("mailto:ghoulsblade@schattenkind.net","ghoulsblade@schattenkind.net")." ICQ:107677833 (opensource)<br>\n";
		echo "Links:".
			href("http://dvmap.nospace.de/index.php","Karte")." ".
			href("http://emptycookie.de/index.php?id=".(kSeelenID?kSeelenID:""),"Übersicht")." ".
			href("http://nobbz.de/wiki/","NobbzWiki")." ".
			href("http://forum.der-holle.de/","HolleForum")." ".
			href("http://chat.mibbit.com/?channel=%23dieverdammten&server=irc.mibbit.net","Chat")." ".
			href("http://www.patamap.com/index.php?page=patastats","PataMap")." ".
			href("http://github.com/ghoulsblade/zwverdammt","github(sourcecode)")." ". 
			href($xmlurl,"XmlStream")." ". 
			"<br>\n";
			// http://verdammt.mnutz.de/  (baldwin,stadt)
			// http://asid.dyndns.org/exphelper2 (asid,holleirc)
			// http://coding-bereich.de/dieverdammten/
			
	echo "</td><td valign=top>";

		MotionTwinNote();

	echo "</td></tr></table>";
}


if (!kSeelenID) { 
	PrintHeaderSection();
	?> <form action="" method="POST"> Seelen-ID:<input name="SeelenID"> <input type="submit" name="Login" value="Login"> </form> <?php
	//~ echo href("?sample=1","(Vorschau ohne SeelenID)")."<br>\n";
	PrintFooter(); exit(0);
}


// ***** ***** ***** ***** ***** Load XML

$gStoreXML = true;
$gDemo = false;
$xmlurl_sample = "sample.xml";
$xmlstr = false;
$xml = false;
if (kSearchGameID) {
	$xmlstr = GetLatestXmlStrFromGameID(kSearchGameID); 
	if (!$xmlstr) exit("failed to load xml");
	$xml = simplexml_load_string(MyEscXML($xmlstr));
	$gStoreXML = false;
} else {
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
		$xmlstr = GetLatestXmlStrFromSeelenID(kSeelenID);
		echo "<h1>Webseite down, Zombie-Angriff im Gange!</h1>\n";
		if ($xmlstr) {
			echo "(lade letzten stand)<br>\n";
		} else {
			echo "(lade dummy/demo daten)<br>\n";
			$xmlurl = $xmlurl_sample;
			$xmlstr = file_get_contents($xmlurl);
		}
		$xml = simplexml_load_string(MyEscXML($xmlstr));
		$gStoreXML = false;
	}
}

/*
if file_get_contents is too slow, try this
$gWGETOptions = "";
//~ $gWGETOptions = "--limit-rate=10k"; // -4 for ipv6 problems
$gMyOutFilePath = "myout.htm";
$gMyTempPath = "mytemp.".time().".htm";
function MyDownToFile ($url,$outfile) {
	global $gWgetOptions;
	shell_exec("wget $gWGETOptions ".escapeshellarg($url)." -O ".escapeshellarg($outfile));
}
function MyDownAndReadHTML ($url) {
	global $gMyTempPath;
	MyDownToFile($url,$gMyTempPath);
	return file_get_contents($gMyTempPath);
}
*/

//~ $_SESSION["xml"] = $xml;

function Map ($x,$y) { global $gMap; return isset($gMap["$x,$y"])?$gMap["$x,$y"]:false; }

function GetDeathTypeIconHTML ($dtype,$txt="") { 
	switch ($dtype) {
		case kDeathType_Aussenwelt:		return img(kIconURL_aussenwelt		,"Aussenwelt. ".$txt); break;
		case kDeathType_Erhaengt:		return img(kIconURL_death			,("Erhaengt. ").$txt); break;
		case kDeathType_Infektion:		return img(kIconURL_infektion		,"Infektion. ".$txt); break;
		case kDeathType_Dehydriert:		return img(kIconURL_dehydration		,"Dehydriert. ".$txt); break;
		case kDeathType_ZombieAngriff:	return img(kIconURL_ZombieAngriff	,"ZombieAngriff. ".$txt); break;
		case kDeathType_AccountDeleted:	return img(kIconURL_death			,("Account geloescht. ").$txt); break;
	}
	return img(kIconURL_death,"Unbekannt[".intval($dtype)."]. ".$txt);
}
function MyLoadGlobals () {
	global $xml,$icon_url,$icon_url_item,$avatar_url,$city;
	global $def,$gGameDay,$gGameID;
	global $gCitizens,$buerger_draussen,$buerger_alive;

	$icon_url			= $xml->headers[0]["iconurl"];
	$icon_url_item		= $xml->headers[0]["iconurl"]."item_";
	$avatar_url			= $xml->headers[0]["avatarurl"];
	$city				= $xml->data[0]->city[0];
	//~ kIconURL_attackin	= "http://data.dieverdammten.de/gfx/forum/smiley/h_zhead.gif";
	
	define("kIconURL_zombie"		,"http://www.dieverdammten.de/gfx/forum/smiley/h_zombie.gif");
	define("kIconURL_attackin"		,$icon_url."small_death.gif");
	define("kIconURL_def"			,$icon_url."item_shield.gif");
	
	global $gDefIcon;
	$gDefIcon = array();
	$gDefIcon[0] = $icon_url."upgrade_none.gif";
	$gDefIcon[1] = $icon_url."upgrade_tent.gif";
	$gDefIcon[3] = $icon_url."upgrade_house1.gif";

	
	define("kIconURL_msg"			,"http://data.dieverdammten.de/gfx/forum/smiley/h_chat.gif");
	define("kIconURL_death"			, $icon_url."small_death.gif");
	define("kIconURL_warning"		, $icon_url."small_warning.gif");
	define("kIconURL_nonhero"		, $icon_url."item_basic_suit.gif");
	define("kIconURL_hero"			, $icon_url."small_hero.gif");
	define("kIconURL_hero_dig"		, $icon_url."item_pelle.gif");
	define("kIconURL_hero_scout"	, $icon_url."item_vest_on.gif");
	define("kIconURL_hero_def"		, $icon_url."item_shield.gif");
	define("kIconURL_wachturm"		, $icon_url."item_tagger.gif");
	define("kIconURL_statistic"		, $icon_url."item_electro.gif");
	
	define("kIconURL_aussenwelt"	, $icon_url."r_doutsd.gif");
	define("kIconURL_infektion"		, $icon_url."r_dinfec.gif");
	define("kIconURL_dehydration"	, $icon_url."r_dwater.gif");
	define("kIconURL_ZombieAngriff"	, "http://data.dieverdammten.de/gfx/forum/smiley/h_zhead.gif");
	
	define("kDeathType_Dehydriert",1);
	define("kDeathType_Erhaengt",4);
	define("kDeathType_Aussenwelt",5);
	define("kDeathType_ZombieAngriff",6);
	define("kDeathType_Infektion",8);
	define("kDeathType_AccountDeleted",10);
	
	$def = (int)($city->defense[0]["total"]);
	$gGameDay = (int)$xml->headers[0]->game[0]["days"];
	$gGameID = (int)$xml->headers[0]->game[0]["id"];

	define("kCityX",$xml->data[0]->city["x"]);
	define("kCityY",$xml->data[0]->city["y"]);

	$buerger_draussen = 0;
	$buerger_alive = 0;
	$gCitizens = $xml->data[0]->citizens[0]->citizen;
	foreach ($gCitizens as $citizen) { 
		if ($citizen["dead"] == "0") ++$buerger_alive;
		if ((int)$citizen["x"] == kCityX && (int)$citizen["y"] == kCityY) {} else { ++$buerger_draussen; }
	}
	
	
	$e = $xml->data[0]->estimations[0]->e[0];
	define("kZombieEstimationQualityMaxxed",$e["maxed"]!="0"); // schon maximale qualität ?

	
	global $gBuildingDone,$gUpgrades;
	$gBuildingDone = array();
	$gUpgrades = array();
	foreach ($xml->data[0]->upgrades[0]->up as $upgrade) $gUpgrades[StripUml($upgrade["name"])] = (int)$upgrade["level"];
	foreach ($xml->data[0]->city[0]->building as $building) $gBuildingDone[StripUml($building["name"])] = true;

	
	global $gMap,$w,$h;
	global $gRuinen;
	$gRuinen = array();
	$map = $xml->data[0]->map[0];
	$w = $map["wid"];
	$h = $map["hei"];
	$gMap = array();
	function MapSet ($x,$y,$data) { 
		global $gMap;
		$gMap["$x,$y"] = $data; 
		//~ echo "MapSet($x,$y,nvt=".$data["nvt"].",tag=".$data["tag"].")<br>\n";
	}
	foreach ($map->zone as $zone) {
		$x = intval($zone["x"]);
		$y = intval($zone["y"]);
		MapSet($x,$y,$zone);
		$r = $zone->building[0];
		if ($r) $gRuinen[] = array("x"=>$x,"y"=>$y,"node"=>$r);
	}
}

MyLoadGlobals();

PrintHeaderSection(); // late, so full xml is available

if ($gStoreXML) {
	$o = false;
	$o->seelenid = (string)kSeelenID;
	$o->time = time();
	$o->gameid = $gGameID;
	$o->cityname = (string)$city["city"];
	$o->day = (int)$gGameDay;
	$o->xml = $xmlstr;
	sql("INSERT INTO xml SET ".obj2sql($o));
}



// ***** ***** ***** ***** ***** hilfs-funktionen

function CheckBuilding ($bname,$minlevel,$text,$pre="den/die") { 
	if (GetBuildingLevel($bname) < 0) { echo "Hilf mit ".$pre." <b>$bname</b> zu bauen: ".$text."<br>\n"; return false; }
	if (GetBuildingLevel($bname) < $minlevel) { echo "Hilf mit ".$pre." <b>$bname</b> als <b>Verbesserung des Tages</b> zu wählen: ".$text."<br>\n"; return false; }
	return true;
}
function WikiName ($name) { return strtr((string)$name,array("ß"=>"ss"," "=>"_")); }
function LinkWiki		($name,$html=false) { return href("http://nobbz.de/wiki/index.php/".urlencode(WikiName($name)),$html?$html:MyEscHTML($name)); }
function LinkRuin		($name,$html=false) { return LinkWiki($name,$html); }
function LinkBuilding	($name,$html=false) { return LinkWiki($name,$html); }
function LinkItem		($name,$html=false) { return LinkWiki($name,$html); }


function GetBuildingLevel ($bname) { // -1= not build, 0=built but no upgrade, >1 = upgrade level 
	global $gBuildingDone,$gUpgrades;
	$bname = StripUml($bname);
	if (!isset($gBuildingDone[$bname])) return -1;
	if (!isset($gUpgrades[$bname])) return 0;
	return $gUpgrades[$bname];
}

function GetSoulPoint ($days) { $c=0; for ($i=1;$i<=$days;++$i) $c += $i; return $c; }





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

// ***** ***** ***** ***** ***** MAIN TABLE START

echo "<table border=1 cellspacing=0><tr><td valign=top>\n";



// ***** ***** ***** ***** ***** STADT INFOS 

echo "Stadt=<b>".utf8_decode($city["city"])."</b>";
echo " Tag=".$gGameDay;
echo " ".img($icon_url."small_water.gif","Wasser").":".$city["water"];
echo " &Uuml;berlebende=".$buerger_alive;
echo " draussen=".$buerger_draussen;
if ($gDemo) echo " <b>(demo/offline daten)</b>";
echo "<br>\n";





echo "SeelenPunkte: ".GetSoulPoint($gGameDay-1)." für Tod VOR Zombieangriff<br>\n";
echo "SeelenPunkte: ".GetSoulPoint($gGameDay)."(+".($gGameDay).") für Tod beim Zombieangriff oder morgen<br>\n";
echo "SeelenPunkte: ".GetSoulPoint($gGameDay+1)."(+".($gGameDay+1).") für Tod beim morgigen Zombieangriff oder übermorgen<br>\n";

if (!kZombieEstimationQualityMaxxed) echo "<b>Hilf mit die Schätzung im ".LinkBuilding("Wachturm")." zu verbessern!</b><br>\n";

if (CheckBuilding("Werkstatt",0,"wird benötigt um BaumStümpfe, MetallTrümmer und viele andere Sachen umzuwandeln","die")) {
	CheckBuilding("Wachturm",0,"wird benötigt um den Forschungsturm zu bauen","den");
	CheckBuilding("Forschungsturm",2,"sorgt dafür dass sich leere Felder,<br> auf denen man sonst nur BaumStümpfe und MetallTrümmer findet wieder regenerieren","den");
}
if ($gGameDay == 1) { echo ("bau dein Feldbett zu einem Zelt aus, aber NICHT zu einer Baracke, Holzbretter werden dringend für die Werkstatt benötigt")."<br>\n"; }


$f = GetBuildingLevel("Forschungsturm");
$leer_regen = array(0,25,37,49,61,73,85,99,"??");
$p0 = $leer_regen[$f+1];
$p1 = $leer_regen[$f+2];
echo LinkBuilding("Forschungsturm")." ".(($f >= 0)?"Stufe $f":"nicht gebaut")." -&gt; Chance das ein leeres Feld sich regeneriert : ".$p0."% (nächste:".$p1."%)<br>\n";


// ***** ***** ***** ***** ***** BÜRGER
echo "<table border=1 cellspacing=0><tr><td valign=top>\n"; // sub table : (bürger, exp+tote, verbesser+gebäude)

// ***** ***** ***** ***** ***** BÜRGER

function GetHeldenBerufHTML ($job) {
	if ($job == "eclair") return img(kIconURL_hero_scout,"Aufklärer, kann Zombie-Anzahl in umliegenden Feldern schätzen und mit Glück durchschleichen");
	if ($job == "collec") return img(kIconURL_hero_dig,"Buddler, kann sehen ob umliegende Felder leer sind und alle 90 minuten graben");
	if ($job == "guardian") return img(kIconURL_hero_def,"Verteidiger, hat 4 Kontrollpunkte und bringt 1 Def Punkt für die Stadt");
	return $job;
}

// job="collec" job="basic"
echo "<table border=1 cellpadding=0 cellspacing=0>\n";
foreach ($xml->data[0]->citizens[0]->citizen as $citizen) { 
	if ($citizen["dead"] != "0") continue;
	$x = (int)$citizen["x"]; $rx = $x - kCityX;
	$y = (int)$citizen["y"]; $ry = kCityY - $y;
	$bIsHome = ($x == kCityX && $y == kCityY);
	$bHeld = $citizen["hero"] != "0";
	$basedef = (int)$citizen["baseDef"];
	$bHeld = $citizen["hero"]!=0;
	$bBan = $citizen["ban"]!=0;
	$bBarackenBauer = ($gGameDay==1 && $basedef > 1);
	$tippb = htmlspecialchars("Dieser Spieler hat ein Holzbrett verschwendet um eine Baracke zu errichten. Holzbretter werden am ersten Tag dringend für wichtige gebäude benötigt.");
	echo "<tr>";
	if ($gShowAvatars) echo "<td>".img($avatar_url.$citizen["avatar"],null,"style='width:90px; height:30px;'")."</td>";
	echo "<td>".MyEscHTML($citizen["name"])."</td>";
	echo "<td nowrap>".($bHeld?(img(kIconURL_hero,"Held").GetHeldenBerufHTML($citizen["job"])):img(kIconURL_nonhero))."</td>";
	echo "<td>".($bBan?img(kIconURL_warning,"!VERBANNT!"):"")."</td>";
	echo "<td nowrap>".$basedef.($bHeld?"+2":"").img(kIconURL_def).(isset($gDefIcon[$basedef])?img($gDefIcon[$basedef]):"").($bBarackenBauer?"<b title='$tippb'>BARACKENBAUER!</b>":"")."</td>";
	echo "<td ".($bIsHome?"":"bgcolor=orange").">".($bIsHome?(img("images/map/city.gif")):("$rx,$ry"))."</td>";
	echo "</tr>\n";
	/*
	kIconURL_nonhero	= $icon_url."item_basic_suit.gif";
	kIconURL_hero		= $icon_url."small_hero.gif";
	kIconURL_hero_dig	= $icon_url."item_pelle.gif";
	kIconURL_hero_scout	= $icon_url."item_vest_on.gif";
	kIconURL_hero_def	= $icon_url."item_shield.gif";
	*/
	// normalo http://data.dieverdammten.de/gfx/icons/item_basic_suit.gif
	// hero http://data.dieverdammten.de/gfx/icons/small_hero.gif
	// buddler http://data.dieverdammten.de/gfx/icons/item_pelle.gif
	// aufklaerer http://data.dieverdammten.de/gfx/icons/item_vest_on.gif
	// kaempfer http://www.dieverdammten.de/gfx/icons/item_shield.gif
}
echo "</table>\n";



// <citizen dead="0" hero="0" name="Baldwin" avatar="hordes/e/b/a11743a1_9061.jpg" x="4" y="4" id="9061" ban="0" job="basic" out="0" baseDef="3">Rohstoffe bunkern für die Stadt.</citizen>


echo "</td><td valign=top>\n";





// ***** ***** ***** ***** ***** Expeditionen

echo href("http://nobbz.de/wiki/index.php/Strategien_f%C3%BCr_Expeditionen","Expeditionen")."<br>\n";
$c = 0;
foreach ($xml->data[0]->expeditions[0]->expedition as $exp) {
	echo $exp["name"]." von ".$exp["author"]."<br>";
	++$c;
}
if ($c < 3) {
	
}
//~ $exp = 
//~ <expeditions>
//~ <expedition name="test [74PA]" author="ghoulsblade" length="74" authorId="9720">
//~ <point x="4" y="4"/>


// ***** ***** ***** ***** ***** Ruinen

echo "<br>\n";
echo href("http://nobbz.de/wiki/index.php/Ruinen","Ruinen (".count($gRuinen)."/10)")."<br>\n";
foreach ($gRuinen as $r) {
	$x = $r["x"] - kCityX;
	$y = kCityY - $r["y"];
	echo "($x/$y)[".(abs($x)+abs($y))."AP]".LinkRuin($r["node"]["name"])."<br>\n";
}

// ***** ***** ***** ***** ***** TOTE
echo "<br>\n";
echo "<table border=1 cellpadding=0 cellspacing=0>\n";
$arr = array();
foreach ($xml->data[0]->cadavers[0]->cadaver as $cadaver) $arr[] = $cadaver;
function cmp_cadaver ($a,$b) { $a = (int)$a["day"]; $b = (int)$b["day"]; if ($a == $b) return 0; return ($a > $b) ? -1 : 1; }
usort($arr,"cmp_cadaver");
foreach ($arr as $cadaver) {
	$msg = $cadaver->msg;
	$cleanup = $cadaver->cleanup[0];
	$dtype = $cadaver["dtype"]; //~ dtype="Int (death reason)"  5=kDeathType_Aussenwelt=aussenwelt
	$cleanuptype = $cleanup["dtype"]; //~ dtype="Int (death reason)" <cleanup type="String (possible values : {garbage, water})" user="String"/>
	
	
	// <cadaver name="Aerox" dtype="1" id="11174" day="4">
	echo "<tr>";
	$bAussenWelt = $dtype == kDeathType_Aussenwelt;
	$bMussEntsorgtWerden = ($cadaver["day"] == $gGameDay-1) && !$bAussenWelt && !($cleanup && $cleanup["user"] != "");
	$cleanup_txt = ($cleanup["user"] != "")?("entsorgt von ".htmlspecialchars($cleanup["user"])." : ".$cleanup["type"]):false;
	
	echo "<td>".GetDeathTypeIconHTML($dtype,$cleanup_txt?$cleanup_txt:"").($bMussEntsorgtWerden?img(kIconURL_warning,"LEICHE ENTSORGEN! SONST STEHT SIE ALS ZOMBIE WIEDER AUF!"):"")."</td>";
	echo "<td>".(($msg && $msg != "")?img(kIconURL_msg,utf8_decode($msg)):"")."</td>";
	echo "<td>".MyEscHTML($cadaver["name"])."</td>";
	echo "<td>Tag".$cadaver["day"]."</td>";
	echo "</tr>\n";
}
echo "</table>\n";



echo "</td><td valign=top>\n";



// ***** ***** ***** ***** ***** upgrades

echo href("http://nobbz.de/wiki/index.php/Verbesserung_des_Tages","Verbesserungen:").":<br>\n";
$icon_upgrade_url = "http://data.dieverdammten.de/gfx/icons/item_electro.gif";
foreach ($xml->data[0]->upgrades[0]->up as $upgrade) {
	echo img($icon_upgrade_url,"Verbesserung").$upgrade["level"]." ".LinkBuilding($upgrade["name"])."<br>\n"; // $upgrade["buildingId"]
}

echo "<br>\n";
// ***** ***** ***** ***** ***** GEBÄUDE
echo href("http://nobbz.de/wiki/index.php/Geb%C3%A4ude_%C3%9Cbersicht","Gebäude").":<br>\n";
foreach ($xml->data[0]->city[0]->building as $building) {
	echo img($icon_url.$building["img"].".gif").LinkBuilding($building["name"])."<br>\n";
}



echo "</td></tr></table>\n"; // END sub table : (bürger, exp+tote, verbesser+gebäude)



echo "</td><td valign=top>\n";



// layout table : links bank, rechts zombie-abschätzung
echo "<table border=0 width='100%'><tr><td valign=top align=left>\n";

// ***** ***** ***** ***** ***** BANK
//~ echo "Bank:<br>";
$cats = array();
foreach ($xml->data[0]->bank[0]->item as $item) { 
	$c = (int)$item["count"];
	$cat = (string)$item["cat"];
	$bBroken = $item["broken"] != 0;
	$html = (($c>1)?($c."x"):"").LinkItem($item["name"],img($icon_url_item.$item["img"].".gif",utf8_decode($item["name"]),$bBroken?"class='broken'":""));
	if (!isset($cats[$cat])) $cats[$cat] = array();
	$cats[$cat][] = "<span style='white-space: nowrap;'>".$html."</span>";
}

echo "<table border=0 cellspacing=0 cellpadding=1>\n";
$gCatTransLong = array("Rsc"=>"Rohstoffe","Furniture"=>"Einrichtungsgegenstände","Drug"=>"Medikamente","Armor"=>"Verteidigung","Food"=>"Vorräte","Weapon"=>"Waffen","Misc"=>"Verschiedenes");
$gCatTrans = array("Rsc"=>"Rohst.",
	"Furniture"=>href("http://nobbz.de/wiki/index.php/Einrichtungsgegenst%C3%A4nde","Deko"),
	"Drug"=>"Drogen",
	"Armor"=>href("http://nobbz.de/wiki/index.php/Kategorie:Verteidigungsgegenstand","Verteid."),
	"Food"=>"Vorrat",
	"Weapon"=>href("http://nobbz.de/wiki/index.php/Waffen","Waffen"),
	"Misc"=>href("http://nobbz.de/wiki/index.php/Liste_mit_Gegenst%C3%A4nden","Versch."));
$cats2 = array();
foreach ($gCatTrans as $k => $v) $cats2[$k] = isset($cats[$k])?$cats[$k]:array();
foreach ($cats as $k => $v) if (!isset($gCatTrans[$k])) $cats2[$k] = $v;
foreach ($cats2 as $k => $arr) echo "<tr><th>".(isset($gCatTrans[$k])?$gCatTrans[$k]:$k).":</th><td align=left>".implode(" &nbsp; ",$arr)."</td></tr>\n";
echo "</table>\n";



echo "</td><td valign=top align=right>\n"; // layout



// ***** ***** ***** ***** ***** Zombie-Angriff
$e = $xml->data[0]->estimations[0]->e[0];
$zombie_min = (int)($e["min"]);
$zombie_max = (int)($e["max"]);
$estimate_bad_html = img(kIconURL_warning,("ungenau, Hilf mit die Schätzung im Wachturm zu verbessern!"));
echo "<table>";
echo "<tr><td>".img(kIconURL_wachturm,("Schätzung")).(kZombieEstimationQualityMaxxed?"":$estimate_bad_html)."</td><td>".img(kIconURL_zombie,"Zombies")."$zombie_min-$zombie_max</td><td>-&gt; ".img(kIconURL_def,"def")."$def</td><td>-&gt; ".img(kIconURL_attackin,"tote")."".max(0,$zombie_min-$def)."-".max(0,$zombie_max-$def)."</td></tr>\n";
$stat = array(0,24,50,97,149,215,294,387,489,595,709,831,935,1057,1190,1354,1548,1738,1926,2140,2353,2618,2892,3189,3506,3882,3952,4393,4841,5339,5772,6271,6880,7194,7736,8285,8728,9106,9671,9888,10666,11508,11705,12608,12139,12921,15248,11666);
$zombie_av = isset($stat[$gGameDay]) ? $stat[$gGameDay] : false;
$zombie_av2 = isset($stat[$gGameDay+1]) ? $stat[$gGameDay+1] : false;
if ($zombie_av) echo "<tr><td>".img(kIconURL_statistic,("Statistik"))."</td><td>".img(kIconURL_zombie,"Zombies")."$zombie_av</td><td>-&gt; ".img(kIconURL_def,"def")."$def</td><td>-&gt; ".img(kIconURL_attackin,"tote")."".max(0,$zombie_av-$def)."</td></tr>\n";
if ($zombie_av2) echo "<tr><td>".img(kIconURL_statistic,("Statistik für Morgen"))."+1</td><td>".img(kIconURL_zombie,"Zombies")."$zombie_av2</td><td>-&gt; ".img(kIconURL_def,"def")."$def</td><td>-&gt; ".img(kIconURL_attackin,"tote")."".max(0,$zombie_av2-$def)."</td></tr>\n";
echo "</table>";

$def_graben_delta = array(20,13,21,32,33,51,0);
echo LinkBuilding("Grosser Graben")." verbessern/bauen:+".$def_graben_delta[GetBuildingLevel("Großer Graben")+1].img(kIconURL_def,"def")."<br>\n";
if ($zombie_av && $zombie_av - $def > 0) { echo img(kIconURL_warning,"es wird Tote geben")."Baut mehr ".href("http://nobbz.de/wiki/index.php/Verteidigung","Verteidigung")."!<br>\n"; }

echo "</td></tr></table>\n";


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




function GetZombieNumText ($x,$y) {
	$data = Map($x,$y);
	if ($data["danger"] == 1) return "1-2";
	if ($data["danger"] == 2) return "2-4";
	if ($data["danger"] >= 3) return "5+";
	if (((int)$data["nvt"]) == 0) return "0"; // heute viewed und kein danger
	return "0-99";
}

function GetAgeText ($day,$t) {
	global $gGameDay;
	$timetxt = date("H:i",$t);
	$age = (int)$gGameDay - (int)$day;
	return ($age != 0) ? (($age > 1) ? "vor $age Tagen $timetxt" : "gestern $timetxt") : "heute $timetxt";
}

function MapGetCellContent ($x,$y) {
	global $gGameID,$gGameDay,$gIconText;
	$rx = $x-kCityX;
	$ry = kCityY-$y;
	$o = GetMapNote($rx,$ry);
	$data = Map($x,$y);
	$r = $data->building[0];
	if ($o) {
		$age = (int)$gGameDay - (int)$o->day;
		$bToday = ($age == 0);
		$zombies = $bToday ? $o->zombies : "?"; // GetZombieNumText($x,$y)
		$timetxt = "[".GetAgeText($o->day,$o->time)."]";
		$old = (!$bToday) ? "_old" : "";
		$html = "";
		if ($o->icon >= 0 && $o->icon < kNumIcons)
				$html .= img("images/map/icon_".$o->icon.$old.".gif","($rx/$ry) ".$zombies." Zombies. <".$gIconText[$o->icon]."> ".$timetxt." ".$o->txt);
		else	$html .= img(TagIconURL($data["tag"]),GetMapToolTip($x,$y));
		$html .= ($zombies != "?") ? ("<span class='mapcellzombietxt' title='".htmlspecialchars($zombies)." Zombies'>$zombies</span>") : ""; // GetZombieNumText($x,$y)
		if ($r) $html .= img("images/map/iconmark_ruin.gif",($r["name"]));
		return $html;
	}
	if ($data["tag"]) return img(TagIconURL($data["tag"]),GetMapToolTip($x,$y));
	if ($r) $html .= img("images/map/iconmark_ruin.gif",($r["name"]));
	return "";
}

echo "<table border=0 cellspacing=0><tr><td valign=top>\n";
//~ echo "<span class='map' id='idMapContainer'>\n";
echo "<table class='map' border=0 cellspacing=0 cellpadding=0>\n";
for ($y=0;$y<$h;++$y) {
	echo "<tr>";
	for ($x=0;$x<$w;++$x) {
		$data = Map($x,$y);
		$bgimg = "zone_bg.gif";
		if ($data) {
			$bHasBuilding = isset($data->building);
			$bViewed = ((int)$data["nvt"]) == 0; // nvt : 1/0 (value is 1 was already discovered, but Not Visited Today)
			$bgimg = $bViewed ? "zone.gif" : "zone_nv.gif";
			if ($bHasBuilding) $bgimg = $bViewed ? "ruin.gif" : "ruin_nv.gif";
			if ($data["danger"] == 1) $bgimg = "zone_d1.gif";
			if ($data["danger"] == 2) $bgimg = "zone_d2.gif";
			if ($data["danger"] >= 3) $bgimg = "zone_d3.gif";
		}
		if ($x == kCityX && $y == kCityY) $bgimg = "city.gif";
		
		$bgimg = "background='images/map/$bgimg'";
		
		$rx = $x-kCityX;
		$ry = kCityY-$y;
		
		$style = ""; // "bgcolor=green"
		$fname = IsOwnGame()?"MapClickCell":"MapClickCell_Dummy";
		echo "<td $style $bgimg onclick='$fname($rx,$ry);' title='($rx,$ry)' id='map_".$rx."_".$ry."'>".MapGetCellContent($x,$y)."</td>";
		//~ echo "<td width=16 height=16>".($data?("nvt=".$data["nvt"].",tag=".$data["tag"]):"")."</td>";
		//~ echo "<td width=16 height=16>".($data?"x":"")."</td>";
	}
	echo "</tr>\n";
}
echo "</table>\n";
echo "</td><td valign=top align=left>\n";
//~ echo "</span>\n";
echo "<span id='idMapCellInfo'>auf die Karte clicken...</span>\n";
echo "</td></tr></table>\n";




echo img("images/map/zone.gif")		.img(kIconURL_zombie)."0, alleine ok"."<br>\n";
echo img("images/map/zone_d1.gif")	.img(kIconURL_zombie)."1-2, alleine ok"."<br>\n";
echo img("images/map/zone_d2.gif")	.img(kIconURL_zombie)."2-4, mindestens zu zweit hin!"."<br>\n";
echo img("images/map/zone_d3.gif")	.img(kIconURL_zombie)."5+, mindestens zu dritt hin!"."<br>\n";
echo img("images/map/zone_bg.gif")	.img(kIconURL_zombie)."0-99, mindestens zu dritt hin! unerforscht, hier könnte noch eine ruine sein"."<br>\n";
echo img("images/map/zone_nv.gif")	.img(kIconURL_zombie)."0-99, mindestens zu dritt hin! schon erforscht, aber HEUTE war noch niemand hier"."<br>\n";
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