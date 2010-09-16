<?php
// uebersicht und karte fuer das browsergame www.dieverdammten.de
// note : xml api via http://www.php.net/manual/de/book.simplexml.php
/*
Copyright (c) 2010 <copyright holders>

 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:

 The above copyright notice and this permission notice shall be included in
 all copies or substantial portions of the Software.

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 THE SOFTWARE.
*/

/*
IDEEN : 
* tagebuch tools, beispiel : Geniales Gehöft (rang1) http://forum.der-holle.de/viewtopic.php?f=8&t=55&start=30 (GesamtFazit interessant btw)
* eingebauter chat:  http://02.chat.mibbit.com/?channel=%23dieverdammten&server=irc.mibbit.net&autoConnect=true&nick=teeest123
** http://wiki.mibbit.com/index.php/Uri_parameters       Ocoma tipp : http://tools.mibbit.com/widget-uri-creator/
* tooltip : ruinen texte aus wiki (fundliste)
* sabo warnung : seelen-auszeichnung nicht im stream, aber hängung(todesart) und ban(bürgerliste) aus städten in der db kriegt man
* auswertung des stadtlogs, also alles copy-pasten , dann kriegt man hübsch angezeigt wer interessante sachen rausgenommen hat, und angriffe etc um sabos früh zu erkennen.  (auch nutzung von werkstatt ohne säge usw)
* tooltip : ruine-noch-nicht-ausgegraben : möglichkeiten anhand entfernung auflisten (wiki)
* ruinen-icon : aufklärer kann ruinen in der nähe? sehen ohne das das feld aufgedeckt ist (zombiezahl?)
* optimale aufklärer verteilung für stadtpos+kartengrösse berechnen (zombiezahl in umgebung->ruinen), 18ap+rückkehr, keine drogen/alk.
* stadtauflistung mit suchfunktion (stadtname,spielername), und verschiedene stats nach denen man sortieren kann (tage,leute am leben, vieleicht paar bewertungen, gesamt def, dev vs zombies, vll sogar abschätzung der lebenserwartung nach statistischem durchschnitt der zombie-angriffe vs baumöglichkeiten mit material+ap der lebenden einwohner inklusive bankvorräten)
* rückblick über mehrere tage, "wie sah stadt x gestern aus"
* eingetragene exp und leute hinterlassen "spur" (leer, oder fussstapfen)
* dvnavi idea asid : sturm richtung = wichtigste info

IDEEN : style : 
* style : starwars:jawas/schrotthändler http://images3.wikia.nocookie.net/__cb20090730135822/starwars/images/2/27/JawaEngineer-SWGTCGAoD.jpg
* style : fallout ? 
bilder meta/mola : 
	http://www.geo-reisecommunity.de/bild/regular/38772/Duenen-von-Merzouga.jpg
	http://www.geo-reisecommunity.de/bild/regular/46336/Halbwueste-Karoo-erodierter-Fels-auf-den-Huegeln.jpg
	http://img.fotocommunity.com/photos/2858888.jpg
	http://giz.me/wp-content/uploads/2008/12/fallout_playground.jpg
	http://www.pcgameshardware.de/screenshots/medium/2008/11/Fallout3_02.jpg
	http://www.ps3blog.de/wp-content/gallery/fout200808/fallout-3_2008_08-20-08_03.jpg
	http://blog.gcshop.ch/pebble/images/november/fallout3.jpg
	http://www.wallpaperez.info/de/games/Fallout-3-nuclear-mountain-1007.html
	http://olbertz.de/blog/wp-content/uploads/2008/12/fallout3.jpg
	http://www.pcgameshardware.de/screenshots/medium/2008/11/Fallout3_04.jpg
		(sidenote, nice logo new vegas : http://onipepper.de/wp-content/uploads/2010/02/fallout_newvegas.jpg)
	green console style (also bioshock) http://t1.gstatic.com/images?q=tbn:n3Oxc-m5SkSM_M:http://www.ps-spotlight.de/~pics/review/fallout3/fallout3_3.jpg&t=1
		http://forum.exp.de/members/allucard-albums-games-picture7-der-kleine-helfer-pipboy-3000-fallout-3.jpg
	http://www.gamers.at/images/screenshots/screenshot_fallout_online_03_35188.jpg
	http://fidgit.com/Fallout_3_diaries_grocer.jpg
	hightech map : http://www.forumla.de/attachments/sony-ps3-forum/28041d1231015479-fallout-3-loesungen-hinweise-ratschlaege-unbenannt.jpg
	mission map with annotations : http://t1.gstatic.com/images?q=tbn:IufZzObCwlbevM:http://i4.photobucket.com/albums/y109/Ultradyne/Fallout3RPmap.jpg&t=1
	ruined highway with western style houses : http://www.play3.de/wp-content/gallery/fallout_new_vegas_060310/fallout-new-vegas_2010_03-06-10_04.jpg
	
	http://verdammten.bplaced.net/phpBB3/styles/DirtyBoard2.0/theme/images/bg_body.jpg
	http://etacar.put.poznan.pl/piotr.pieranski//Physics%20Around%20Us/Sand%20waves%2010.jpg
*/
$gIndex_StartT = time();
if (!file_exists("defines.php")) exit('error: please rename "defines.dist.php" to "defines.php"');

require_once("defines.php");
require_once("roblib.php");
require_once("lib.verdammt.php");

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
function StripUml($txt) { return preg_replace('/[^a-zA-Z0-9]/','',$txt); }


function MyImgTitleConst ($txt) { return utf8_encode($txt); }

function WikiName ($name) { return strtr((string)$name,array("ß"=>"ss"," "=>"_")); }
function LinkWiki		($name,$html=false) { return href("http://nobbz.de/wiki/index.php/".urlencode(WikiName($name)),$html?$html:($name)); }
function LinkRuin		($name,$html=false) { return LinkWiki($name,$html); }
function LinkBuilding	($name,$html=false) { return LinkWiki($name,$html); }
function LinkItem		($name,$html=false) { return LinkWiki($name,$html); }

// note : htmlentities() is identical to htmlspecialchars() in all ways, except with htmlentities(), all characters which have HTML character entity equivalents are translated into these entities. 

define("kNumIcons",7);
define("kIconID_DigLeer",0);
define("kIconID_DigVoll",1);
define("kIconID_Verboten",4);
define("kIconID_Notiz",6);

define("kBuildingLevelMax",5);

define("kSearchGameID",isset($_REQUEST["gameid"])?intval($_REQUEST["gameid"]):false);
define("kSearchGameDay",isset($_REQUEST["day"])?intval($_REQUEST["day"]):false);
define("kSearchXMLID",isset($_REQUEST["xmlid"])?intval($_REQUEST["xmlid"]):false);
define("kGhostKey",isset($_REQUEST["key"])?($_REQUEST["key"]):false);


define("kMapMode_Marker"		,1);
define("kMapMode_Buerger"		,2);
define("kMapMode_InGameTags"	,3);

$gGhostStreamOk = false;
$gRegisteredItemTypeIDs = sqlgettable("SELECT id FROM itemtype","id");
$gRegisteredItemTypes = sqlgettable("SELECT * FROM itemtype","id");
$gRegisteredBuildingTypeIDs = sqlgettable("SELECT id,buildingid FROM buildingtype","buildingid");

$gIconText = array(
	0=>"Feld leer",
	1=>"Feld regeneriert : graben!",
	2=>"Feld temporaer gesichert",
	3=>"Notruf",
	4=>"!Verboten!(für DVNavi)",
	5=>"ok",
	6=>"notiz",
);

function RegisterRuin ($gameid,$rx,$ry,$name,$type) { // <building name="Autowracks" type="7" dig="0">
	if (intval($type) == -1) return;
	$o = false;
	$o->gameid = $gameid;
	$o->x = $rx;
	$o->y = $ry;
	if (sqlgetone("SELECT 1 FROM ruin WHERE ".obj2sql($o," AND "))) return;
	$o->ap = abs($rx)+abs($ry);
	$o->name = utf8_decode($name);
	$o->type = intval($type);
	sql("REPLACE INTO ruin SET ".obj2sql($o));
}

function GetTextBetween ($text,$start,$end,$startskipto=false,$bReturnFullOnFail=false) {
	if ($text === false) return $bReturnFullOnFail?$text:false;
	$pos0 = strpos($text,$start);
	if ($pos0 === false) return $bReturnFullOnFail?$text:false;
	$pos0 += strlen($start);
	if ($startskipto) {
		$pos0 = strpos($text,$startskipto,$pos0);
		if ($pos0 === false) return $bReturnFullOnFail?$text:false;
		$pos0 += strlen($startskipto);
	}
	$pos1 = strpos($text,$end,$pos0);
	if ($pos1 === false) return $bReturnFullOnFail?$text:false;
	return substr($text,$pos0,$pos1-$pos0);
}
function ExtractWikiTextArea ($txt)		{ return GetTextBetween($txt,"<textarea","</textarea",">",true); } // <textarea name="wpTextbox1" id="wpTextbox1" cols="80" rows="25" tabindex="1" accesskey=",">
function ExtractWikiPageContent ($txt)	{ return GetTextBetween($txt,"<!-- start content -->",'<div class="printfooter">',false,true); } // <textarea name="wpTextbox1" id="wpTextbox1" cols="80" rows="25" tabindex="1" accesskey=",">

function GetWikiSrcRedirect ($src) {
	
	if ((stripos($src,"#redirect") !== false || stripos($src,"#weiterleitung") !== false) && eregi("#[a-z]+[ \t]+\\[\\[(.+)\\]\\]",$src,$r)) {
		echo "GetWikiSrcRedirect 1=".$r[1]."<br>\n";
		return $r[1];
	}
	return false;
}

if (isset($_REQUEST["download_wiki"])) {
	echo "download wiki entries<br>\n";
	set_time_limit(0); // disable max execution time
	$itemtypes = sqlgettable("SELECT * FROM itemtype");
	$i = 0;
	foreach ($itemtypes as $o) {
		$redirect = GetWikiSrcRedirect($o->wiki_src);
		if (trim($o->wiki_src) != "" && trim($o->wiki_html) != "" && !$redirect) continue;
		// #redirect [[HÃ¤hnchenflÃ¼gel]]
		if ($i >= 120) break; else ++$i;
		$wikiname = $o->name;
		
		$wikiname = eregi_replace("\\([0-9]+ [a-z]+\\)","(gefüllt)",$wikiname);
		
		// (3 rationen/ladungen) -> gefüllt
		
		$url_html = "http://nobbz.de/wiki/index.php?title=".urlencode($wikiname); echo $o->id." ".href($url_html)."<br>\n";
		$url_wiki = "http://nobbz.de/wiki/index.php?action=edit&title=".urlencode($redirect ? $redirect : $wikiname); echo $o->id." ".href($url_wiki)."<br>\n";
		$new = false;
		if (trim($new->wiki_html) == "") $new->wiki_html = ExtractWikiPageContent(file_get_contents($url_html));
		$new->wiki_src = ExtractWikiTextArea(file_get_contents($url_wiki));
		//~ echo "<textarea cols=80 rows=20>".$wiki_src."</textarea>";
		sql("UPDATE itemtype SET ".obj2sql($new)." WHERE id = ".intval($o->id));
		
		/*
		Warning: file_get_contents(http://nobbz.de/wiki/index.php?title=Reparturset+%28kaputt%29) 
		Warning: file_get_contents(http://nobbz.de/wiki/index.php?title=Angebissene+H%C3%A4hnchenfl%C3%BCgel) 
		Warning: file_get_contents(http://nobbz.de/wiki/index.php?title=Kanisterpumpe+%28zerlegt%29) 
		Warning: file_get_contents(http://nobbz.de/wiki/index.php?title=Unverarbeitete+Blechplatten)
		*/
	}
	exit(0);
}

if (isset($_REQUEST["refresh_other"])) {
	//~ $arr = sqlgettable("SELECT *,MAX(time) as maxtime FROM accesslog GROUP BY seelenid");
	$arr = sqlgettable("SELECT id,seelenid,cityname,MAX(time) as maxtime FROM xml GROUP BY seelenid ORDER BY maxtime");
	echo count($arr)." found "."<br>\n";
	$today_start_t = floor(time() / (24*3600))*24*3600;
	$seelenid = false;
	$otherid = false;
	foreach ($arr as $o) {
		//~ if ($o->maxtime < $today_start_t) {
		if ($o->maxtime < time() - 6*3600) {
			echo "refresh id ".$o->id." ".$o->cityname." ".$seelenid."<br>\n";
			$seelenid = $o->seelenid;
			$otherid = $o->id;
		}
	}
	if ($seelenid) {
		if (!define("kSeelenID",$seelenid)) exit("failed to set constant, already set?");
		$xmlurl = "http://www.dieverdammten.de/xml/?k=".urlencode(kSeelenID).";sk=".urlencode(kDV_SiteKey);
		define("kXMLUrl_Basic",$xmlurl); // kein sitekey
		define("kXMLUrl_Secret",$xmlurl); // enthaelt sitekey! das sollte der user nicht zu sehen kriegen
		$xmlstr = file_get_contents(kXMLUrl_Secret);
		if (!$xmlstr) exit("failed to load xml");
		$xml = simplexml_load_string(MyEscXML($xmlstr));
		MyLoadGlobals();
		StoreXML();
		echo "stored '$otherid' : ".(string)$city["city"]." ".kSeelenID."<br>\n";
	}
	exit("refresh other");
}





$gShowAvatars = false;


$temp_seelenid = isset($_COOKIE["SeelenID"]) ? $_COOKIE["SeelenID"] : false;
if ($temp_seelenid) LogAccess($temp_seelenid);
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

function GetLatestXmlByID					($xmlid)		{ return sqlgetone("SELECT xml FROM xml WHERE ".arr2sql(array("id"=>$xmlid))); }
function GetLatestXmlStrFromGameID			($gameid)		{ return sqlgetone("SELECT xml FROM xml WHERE ".arr2sql(array("gameid"=>$gameid))." ORDER BY id DESC LIMIT 1"); }
function GetLatestXmlStrFromGameIDAndDay	($gameid,$day)	{ return sqlgetone("SELECT xml FROM xml WHERE ".arr2sql(array("gameid"=>$gameid,"day"=>$day)," AND ")." ORDER BY id DESC LIMIT 1"); }
function GetLatestXmlStrFromSeelenID		($seelenid)		{ return sqlgetone("SELECT xml FROM xml WHERE ".arr2sql(array("seelenid"=>$seelenid))." ORDER BY id DESC LIMIT 1"); }


if (isset($_REQUEST["scanitemtypes"])) {
	// only used once during development, later new items will be registered automatically when they are seen in the bank
	$r = sql("SELECT xml FROM xml");
	while ($arr = mysql_fetch_array($r)) {
		$xml = simplexml_load_string(MyEscXML($arr[0]));
		foreach ($xml->data[0]->bank[0]->item as $item) RegisterItemType($item);
	}
	exit(0);
}

function RegisterItemType ($item) {
	global $gRegisteredItemTypeIDs;
	$o = false;
	$o->id = (string)$item["id"];
	if ($gRegisteredItemTypeIDs[$o->id]) return;
	$o->cat = (string)$item["cat"];
	$o->img = (string)$item["img"];
	$o->name = (string)$item["name"];
	$o->cat2 = $o->cat; // later used to mark special types : alcohol,drugs,tools(weapons)#
	if (!sqlgetone("SELECT 1 FROM itemtype WHERE id = ".intval($o->id)))
		sql("REPLACE INTO itemtype SET ".obj2sql($o));
}
function RegisterBuildingType ($building) {
	global $gRegisteredBuildingTypeIDs;
	$o = false;
	$o->buildingid = (string)$building["id"];
	if ($gRegisteredBuildingTypeIDs[$o->buildingid]) return;
	$o->img = (string)$building["img"];
	$o->name = (string)$building["name"];
	$o->notfall = (int)$building["temporary"];
	if (!sqlgetone("SELECT 1 FROM buildingtype WHERE id = ".intval($o->id)))
		sql("REPLACE INTO buildingtype SET ".obj2sql($o));
}


if (isset($_REQUEST["ajax"])) {
	if (kSearchXMLID) $xmlstr = GetLatestXmlByID(kSearchXMLID);
	else if (kSearchGameID && kSearchGameDay) $xmlstr = GetLatestXmlStrFromGameIDAndDay(kSearchGameID,kSearchGameDay);
	else if (kSearchGameID) $xmlstr = GetLatestXmlStrFromGameID(kSearchGameID);
	else $xmlstr = GetLatestXmlStrFromSeelenID(kSeelenID);
	if (!$xmlstr) exit("failed to load xml");
	$xml = simplexml_load_string(MyEscXML($xmlstr));
	MyLoadGlobals();
	$rx = intval($_REQUEST["x"]);
	$ry = intval($_REQUEST["y"]);
	switch ($_REQUEST["ajax"]) {
		case "addmapnote":		Ajax_AddMapNote($rx,$ry); break;
		case "cellinfo":		Ajax_MapCellInfo($rx,$ry); break;
		case "maputil_digg":	Ajax_MapUtil_Digg($rx,$ry); break;
		case "maputil_scout":	Ajax_MapUtil_Scout($rx,$ry); break;
		case "mapmode":			Ajax_MapMode(); break;
		case "shownavimenu":	Ajax_ShowNaviMenu(); break;
		default:			echo "unknown request ".$_REQUEST["ajax"]; break;
	}
	exit();
}


function Ajax_MapMode() { RenderMapBlock(intval($_REQUEST["mapmode"])); }



function Ajax_ShowNaviMenu () {
	global $gIconText;
	?>
	DVNavi : Expeditions-Routen-Vorschlag<br>
	Tipp : <?=img("images/map/icon_".kIconID_Verboten.".gif",$gIconText[$i])?> Marker = verbotenes Feld.<br>
	<form action="?" method="post" class='mapadd' id='form_dvnavi'>
	<input class='mapaddsmall_input' type="text" size="3" maxlength="5" name="ap" value='18'>AP
	<input class='mapaddsmall_button' type="button" name="util_scout" value="Route berechnen" onclick="DVNavi_Execute(this.form.ap.value)"></td>
	</form>
	<span id='idDVNaviStatus'></span>
	<span id='idDVNaviResult'></span>
	<?php
}


function IsUnexploredRelPos ($rx,$ry) {
	$data = Map($rx + kCityX,kCityY - $ry);
	if ($data) return false;
	return true;
}
function MapSetIconRelPos ($rx,$ry,$icon) {
	if ($rx == 0 && $ry == 0) return; // no mark on city
	if (IsUnexploredRelPos($rx,$ry)) return; // no mark on unexplored
	$o = GetMapNoteRelPos($rx,$ry);
	AddMapNote($rx,$ry,$icon,$o?$o->zombies:"",$o?$o->txt:"");
}
function MapSetZombieRelPos ($rx,$ry,$zombies) {
	if ($rx == 0 && $ry == 0) return; // no mark on city
	if (IsUnexploredRelPos($rx,$ry)) return; // no mark on unexplored
	$o = GetMapNoteRelPos($rx,$ry);
	AddMapNote($rx,$ry,$o?$o->icon:-1,$zombies,$o?$o->txt:"");
}
function Ajax_MapUtil_Digg ($rx,$ry) {
	$n = intval($_REQUEST["dig_north"]);
	$w = intval($_REQUEST["dig_west"]);
	$m = intval($_REQUEST["dig_mid"]);
	$e = intval($_REQUEST["dig_east"]);
	$s = intval($_REQUEST["dig_south"]);
	//~ echo "$n,$w,$m,$e,$s<br>\n";
	MapSetIconRelPos($rx  ,$ry+1,$n?kIconID_DigVoll:kIconID_DigLeer);
	MapSetIconRelPos($rx-1,$ry  ,$w?kIconID_DigVoll:kIconID_DigLeer);
	MapSetIconRelPos($rx  ,$ry  ,$m?kIconID_DigVoll:kIconID_DigLeer);
	MapSetIconRelPos($rx+1,$ry  ,$e?kIconID_DigVoll:kIconID_DigLeer);
	MapSetIconRelPos($rx  ,$ry-1,$s?kIconID_DigVoll:kIconID_DigLeer);
	RenderMapBlock();
}
function Ajax_MapUtil_Scout ($rx,$ry) {
	$n = ($_REQUEST["zombie_north"]);
	$w = ($_REQUEST["zombie_west"]);
	$m = ($_REQUEST["zombie_mid"]);
	$e = ($_REQUEST["zombie_east"]);
	$s = ($_REQUEST["zombie_south"]);
	//~ echo "$n,$w,$m,$e,$s<br>\n";
	MapSetZombieRelPos($rx  ,$ry+1,$n);
	MapSetZombieRelPos($rx-1,$ry  ,$w);
	MapSetZombieRelPos($rx  ,$ry  ,$m);
	MapSetZombieRelPos($rx+1,$ry  ,$e);
	MapSetZombieRelPos($rx  ,$ry-1,$s);
	RenderMapBlock();
}

function Ajax_AddMapNote ($rx,$ry) {
	AddMapNote($rx,$ry,intval($_REQUEST["icon"]),$_REQUEST["zombies"],$_REQUEST["msg"]);
	echo MapGetCellContentRelPos($rx,$ry);
}

function MapGetCellContentRelPos ($rx,$ry) { return MapGetCellContent(kCityX + $rx,kCityY - $ry); }

function Ajax_MapCellInfo ($rx,$ry) { // idMapCellInfo
	global $gGameID,$gIconText;
	
	
	$lastnote = GetMapNote($rx,$ry);
	$icon = $lastnote ? intval($lastnote->icon) : kIconID_Notiz;
	$msg = $lastnote ? $lastnote->txt : "";
	$zombies = $lastnote ? $lastnote->zombies : "?";
	if ($zombies == -1) $zombies = "?";
	//~ echo "$rx,$ry lastnote=".($lastnote?"ok":"-")." gameid=$gGameID ".$lastnote->icon." ".$lastnote->txt."<br>\n";
	
	$x = kCityX + $rx;
	$y = kCityY - $ry;
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
		<?php
		$zone = sqlgetobject("SELECT * FROM mapzone WHERE ".arr2sql(array("gameid"=>kGameID,"x"=>$x,"y"=>$y)," AND "));
		$items = sqlgettable("SELECT * FROM mapitem WHERE ".arr2sql(array("gameid"=>kGameID,"x"=>$x,"y"=>$y)," AND "));
		if ($zone) {
			echo "(".date("Y.d.m H:i",$zone->time)." Z:".$zone->z." ".(($zone->dried!=0)?"LEER":"")."):";
			global $gRegisteredItemTypes;
			foreach ($items as $o) {
				$t = $gRegisteredItemTypes[$o->itemtype];
				$c = $o->num;
				$bBroken = $o->broken != 0;
				$html = (($c>1)?($c."x"):"").LinkItem($t->name,img(kIconUrlItem.$t->img.".gif",($t->name),$bBroken?"class='broken'":""));
				echo " ".$html;
			}
			echo "<br>\n";
		}
		?>
		<textarea cols="40" rows="3" name='msg'><?=htmlspecialchars($msg)?></textarea><br>
		<?php if (!IsOwnGame()) {?>
		(kann nur in der eigenen Stadt bearbeitet werden)
		<?php }?>	
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
				<?php $attr = " title='ankreuzen = REGENERIERTES feld = gruen'"; ?>
				<?php $attr .= " onchange=\"this.parentNode.style.backgroundColor = this.checked?'green':'red';\""; ?>
				<?php $cellattr = " style='background-color:red;'"; ?>
				<table border=1 cellspacing=0>
				<tr>
					<td><?=img(kIconURL_hero_dig,MyImgTitleConst("Helden die den Beruf Buddler wählen können sehen ob umgebende Felder leer sind"))?></td>
					<td <?=$cellattr?>><input type="checkbox" name="dig_north" value="1" <?=$attr?>></td>
					<td></td>
				</tr><tr>
					<td <?=$cellattr?>><input type="checkbox" name="dig_west" value="1" <?=$attr?>></td>
					<td <?=$cellattr?>><input type="checkbox" name="dig_mid" value="1" <?=$attr?>></td>
					<td <?=$cellattr?>><input type="checkbox" name="dig_east" value="1" <?=$attr?>></td>
				</tr><tr>
					<td></td>
					<td <?=$cellattr?>><input type="checkbox" name="dig_south" value="1" <?=$attr?>></td>
					<td><input class='mapaddsmall_button2' type="button" name="util_digg" value="ok" onclick="Form_Map_Digg(this.form)"></td>
				</tr></table>
			</td><td valign='top'>
				<?php /* ***** *****  UTIL : AUFKLÄRER ***** ***** */ ?>
				<?php $tipp = "title='Geschätzte Zombieanzahl'"; ?>
				<table border=1 cellspacing=0>
				<tr>
					<td><?=img(kIconURL_hero_scout,MyImgTitleConst("Helden die den Beruf Aufklärer wählen können die Anzahl der Zombies in umgebenden Feldern abschätzen."))?></td>
					<td><input class='mapaddsmall_input' type="text" size="3" maxlength="5" name="zombie_north" <?=$tipp?> /></td>
					<td></td>
				</tr><tr>
					<td><input class='mapaddsmall_input' type="text" size="3" maxlength="5" name="zombie_west" <?=$tipp?> /></td>
					<td><input class='mapaddsmall_input' type="text" size="3" maxlength="5" name="zombie_mid" <?=$tipp?> /></td>
					<td><input class='mapaddsmall_input' type="text" size="3" maxlength="5" name="zombie_east" <?=$tipp?> /></td>
				</tr><tr>
					<td></td>
					<td><input class='mapaddsmall_input' type="text" size="3" maxlength="5" name="zombie_south" <?=$tipp?> /></td>
					<td><input class='mapaddsmall_button2' type="button" name="util_scout" value="ok" onclick="Form_Map_Scout(this.form)"></td>
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
function GetMapNoteRelPos ($rx,$ry) { return GetMapNote($rx,$ry); }
function GetMapNote ($x,$y) {
	global $gGameID; 
	//~ echo "SELECT * FROM mapnote WHERE ".arr2sql(array("gameid"=>$gGameID,"x"=>$x,"y"=>$y)," AND ");
	return sqlgetobject("SELECT * FROM mapnote WHERE ".arr2sql(array("gameid"=>$gGameID,"x"=>$x,"y"=>$y)," AND ")." ORDER BY `id` DESC LIMIT 1"); 
}




// ***** ***** ***** ***** ***** ghost xml parser


function ParseGhostXMLMapInfo($ghostxmlstr,$dbentry=false) {
	@$xml = simplexml_load_string(MyEscXML($ghostxmlstr)); if (!$xml) return;
	$x_zone		= $xml->headers[0]->owner[0]->myZone[0]; 	if (!$x_zone) return;
	$x_citizen	= $xml->headers[0]->owner[0]->citizen[0]; 	if (!$x_citizen) return;
	$x_game		= $xml->headers[0]->game[0]; 				if (!$x_game) return;
	
	//~ <citizen dead="0" hero="1" name="ghoulsblade" avatar="hordes/4/9/7d2611ba_9720.jpg" x="8" y="3" id="9720" ban="0" job="guardian" out="1" baseDef="1">
	
	$zone = false;
	$zone->gameid		= intval($x_game["id"]);
	$zone->x			= intval($x_citizen["x"]);
	$zone->y			= intval($x_citizen["y"]);
	$zone->time			= $dbentry ? $dbentry->time : time();
	$zone->seelenid		= $dbentry ? $dbentry->seelenid : kSeelenID;
	$zone->dried		= intval($x_zone["dried"]);	// <myZone dried="1" h="30" z="2">
	$zone->h			= intval($x_zone["h"]);		// times explored ? aufklaerer sicherheit ?
	$zone->z			= intval($x_zone["z"]);		// zombies
	sql("REPLACE INTO mapzone SET ".obj2sql($zone));
	
	sql("DELETE FROM mapitem WHERE ".arr2sql(array("gameid"=>$zone->gameid,"x"=>$zone->x,"y"=>$zone->y)," AND "));
	
	foreach ($x_zone->item as $item_x) { // <item name="Raketenpulver" count="1" id="173" cat="Misc" img="powder" broken="0"/>
		$item = false;
		$item->gameid = $zone->gameid;
		$item->x = $zone->x;
		$item->y = $zone->y;
		$item->itemtype	= intval($item_x["id"]);
		$item->num		= intval($item_x["count"]);
		$item->broken	= intval($item_x["broken"]);
		sql("INSERT INTO mapitem SET ".obj2sql($item));
	}
}

if (isset($_REQUEST["parse_ghost_db"])) {
	$r = sql("SELECT * FROM stream_ghost_debug ORDER BY id");
	while ($o = mysql_fetch_object($r)) {
		ParseGhostXMLMapInfo($o->xml,$o);
	}
	exit(0);
}










function PrintFooter () { global $gIndex_StartT; echo "total time for page ".(time()-$gIndex_StartT)."msec<br>\n"; ?></body></html><?php }

// htmlspecialchars

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<title>ZWMap</title>
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
.dvnavimap * {
	line-height:5px;
}
.map * {
	line-height:5px;
}
.bframe {
	border:1px solid black;
}
.iconmark {
	position:relative;
	left:0px;
	top:0px;
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
<body onload='MyOnLoad()'>
<?php
function PrintJavaScriptBlock () { global $gGameID;?>
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
<?php function BuildJSUrl ($prefix,$arr_val,$arr_check=array()) { $url = '"'.$prefix; 
	foreach ($arr_val as $n) $url .= '&'.$n.'="+escape(""+(form.'.$n.'.value'.'))+"'; 
	foreach ($arr_check as $n) $url .= '&'.$n.'="+escape(""+(form.'.$n.'.checked?1:0'.'))+"'; 
	return $url.'"'; 
} ?>
function Form_Map_Digg (form) { MyAjaxGet(<?=BuildJSUrl("?ajax=maputil_digg",array("x","y"),array("dig_north","dig_west","dig_mid","dig_east","dig_south"))?>,"idMapContainer"); }
function Form_Map_Scout (form) { MyAjaxGet(<?=BuildJSUrl("?ajax=maputil_scout",array("x","y","zombie_north","zombie_west","zombie_mid","zombie_east","zombie_south"))?>,"idMapContainer"); }

function ColorCheckBox (el,col) {
	el.style.backgroundColor = col;
	el.style.color = col;
}

function GetAjaxUrlParamAdd () {
	return "&gameid="+escape(<?=$gGameID?>)+"&day="+escape(<?=kSearchGameDay?kSearchGameDay:"false"?>);
}

function MapCellTooltip (td) {
	// TODO 
}
function MapClickCell (x,y) {
	//~ alert("ClickCell"+x+","+y);
	MyAjaxGet("?ajax=cellinfo&x="+escape(x)+"&y="+escape(y)+GetAjaxUrlParamAdd(),"idMapCellInfo");
}
function MapClickCell_Dummy (x,y) { // IsOwnGame()?"MapClickCell":"MapClickCell_Dummy"
	MyAjaxGet("?ajax=cellinfo&x="+escape(x)+"&y="+escape(y)+GetAjaxUrlParamAdd(),"idMapCellInfo");
	//~ document.getElementById("idMapCellInfo").innerHTML = "nur in der eigenen Stadt möglich";
}

function ShowHide (id) {
	var e = document.getElementById(id);
	if (!e) { alert("ShowHide target element not found : "+id); return; }
	if (	e.style.display != "none") 
			e.style.display = "none";
	else	e.style.display = "inline";
}

function SetMapMode (d) { MyAjaxGet("?ajax=mapmode&mapmode="+escape(d)+GetAjaxUrlParamAdd(),"idMapContainer"); }

// ***** ***** ***** ***** *****  DVNavi START

function ShowNaviMenu () { MyAjaxGet("?ajax=shownavimenu"+GetAjaxUrlParamAdd(),"idMapCellInfo"); }


<?php

function DVNaviGetMapClass($x,$y) {
	global $gGameDay;
	if ($x == kCityX && $y == kCityY) return "city";
	$rx = $x-kCityX;
	$ry = kCityY-$y;
	$o = GetMapNote($rx,$ry);
	if ($o->day == $gGameDay && $o->icon == kIconID_Verboten) return "verboten"; // per icon manuell deaktiviert
	$data = Map($x,$y);
	if (!$data) return "unexp"; // unexplored -> sure that it is NONEMPTY, could have ruin
	if (IsMapCellRuine($x,$y)) return "ruin";
	if ($o && (int)$o->day == (int)$gGameDay) { // von heute
		if ($o->icon == kIconID_DigVoll) return "voll";
		if ($o->icon == kIconID_DigLeer) return "leer";
	}
	return "old"; // old
}
echo "gDVNavi_MapW = ".kMapW.";\n";
echo "gDVNavi_MapH = ".kMapH.";\n";
echo "gDVNavi_CityX = ".(kCityX+1).";\n";
echo "gDVNavi_CityY = ".(kCityY+1).";\n";
echo "gDVNavi_MapClass = new Array();\n";
echo "gDVNavi_MapScore = new Array();\n";
for ($y=0;$y<kMapH;++$y) { echo "gDVNavi_MapScore[$y] = new Array();\n"; }
for ($y=0;$y<kMapH;++$y) { echo "gDVNavi_MapClass[$y] = new Array("; for ($x=0;$x<kMapW;++$x) { echo "'".DVNaviGetMapClass($x,$y)."',"; } echo "0);\n"; }


?>

kDVNaviMaxScore				= 1000;
kDVNavi2ndMaxScore			= 100; // zweithoechste moegliche punktzahl

gDVNavi_ScoreTable_Unexplored = new Object();
gDVNavi_ScoreTable_Unexplored.verboten = -10*kDVNaviMaxScore;
gDVNavi_ScoreTable_Unexplored.city = 0;
gDVNavi_ScoreTable_Unexplored.old = 5;
gDVNavi_ScoreTable_Unexplored.leer = 0;
gDVNavi_ScoreTable_Unexplored.voll = 10;
gDVNavi_ScoreTable_Unexplored.unexp = kDVNaviMaxScore;
gDVNavi_ScoreTable_Unexplored.ruin = 100;


function InitScoreMap (scoretable) {
	//~ gMaxScorePerField = 0;
	//~ for (var k in scoretable) gMaxScorePerField = Math.max(gMaxScorePerField,scoretable[k]);
	for (y=0;y<gDVNavi_MapH;++y) for (x=0;x<gDVNavi_MapW;++x) gDVNavi_MapScore[y][x] = scoretable[gDVNavi_MapClass[y][x]];
}

gMap = {}
for (y=1;y<=gDVNavi_MapH;++y) { var row = new Array(); gMap[y] = row; for (x=1;x<=gDVNavi_MapW;++x) { row[x] = new Object(); } } // every cell is a table
function Map			(x,y) { return gMap[y][x]; }
function Score			(x,y) { return gDVNavi_MapScore[y-1][x-1]; } // gMap indices are one-based, gDVNavi_MapScore zero-based
function DVNavi_Class	(x,y) { return gDVNavi_MapClass[y-1][x-1]; } // gMap indices are one-based, gDVNavi_MapScore zero-based
function IsCity			(x,y) { return x == gDVNavi_CityX && y == gDVNavi_CityY; }
function Valid			(x,y) { return x >= 1 && x <= gDVNavi_MapW && y >= 1 && y <= gDVNavi_MapH; }
function ReturnAP		(x,y) { return Math.abs(x-gDVNavi_CityX) + Math.abs(y-gDVNavi_CityY); }

// expeditions


function clonemod1 (t,k1,v1) { // copy assoc-array t, and modify one value by key k1 -> v1
	var res = new Object(); 
	for (var k in t) res[k] = t[k];
	res[k1] = v1;
	return res;
}

function DVNavi_Heuristic (x,y,ap) {
	// determine the area than can be travalled, and see how often we can achieve max-score in it
	var e = Math.floor((ap - ReturnAP(x,y)) / 2);
	var minx = Math.max(1,Math.min(gDVNavi_MapW,	Math.min(x,gDVNavi_CityX)-e	));
	var maxx = Math.max(1,Math.min(gDVNavi_MapW,	Math.max(x,gDVNavi_CityX)+e	));
	var miny = Math.max(1,Math.min(gDVNavi_MapH,	Math.min(y,gDVNavi_CityY)-e	));
	var maxy = Math.max(1,Math.min(gDVNavi_MapH,	Math.max(y,gDVNavi_CityY)+e	));
	var maxc = 0;
	for (var ty=miny;ty<=maxy && maxc < ap;++ty) 
	for (var tx=minx;tx<=maxx;++tx) if (Score(tx,ty) >= kDVNaviMaxScore) { ++maxc; if (maxc >= ap) break; }
	return maxc * kDVNaviMaxScore + (ap - maxc) * kDVNavi2ndMaxScore; // an upper limit for the score achievable with the remaining ap
}


function AddExpedition (x,y,ap,visited,score,txt) {
	if (ap < 0 || !Valid(x,y) || ReturnAP(x,y) > ap) return;
	var pos = (x-gDVNavi_CityX) + "/" + (gDVNavi_CityY-y); // as string
	if (!visited[pos]) score += Score(x,y);
	if (score + ap*kDVNaviMaxScore <= gMinScore) return;
	var heur = score + DVNavi_Heuristic(x,y,ap);
	if (heur <= gMinScore) return;
	
	var newexp = new Object();
	newexp.x = x;
	newexp.y = y;
	newexp.ap = ap;
	newexp.score = score;
	newexp.heur = heur;
	newexp.visited = clonemod1(visited,pos,true);
	newexp.txt = txt+" "+pos;
	gExpeditions.push(newexp);
	gExpeditionC = gExpeditionC + 1;
}

function InExp (e,x,y) {
	var pos = " "+(x-gDVNavi_CityX)+"/"+(gDVNavi_CityY-y)+" "
	return e.txt.search(pos) != -1;
}


function MyPrintStatus (txt) { gDVNaviStatus.innerHTML = txt; }
function MyPrintLine (txt) { gDVNaviConsole.innerHTML += txt+"<br>\n"; }

function DVNavi_Execute (ap) {

	gExpeditions = new Array();
	gExpeditionC = 0;
	gFinishedExp = new Array();
	gMinScore = 0;
	gBestExpPath = "";
	gDVNaviConsole = false;
	gDVNaviStatus = false;
	gExpeditionMaxAP = 18;
	gDVNavi_BlockSteps = 200;

	gExpeditionMaxAP = ap;
	gDVNavi_BlockSteps = <?=isset($_REQUEST["dvnavi_cpu"])?$_REQUEST["dvnavi_cpu"]:200?>;
	InitScoreMap(gDVNavi_ScoreTable_Unexplored);
	gDVNaviConsole = document.getElementById("idMapCellInfo");
	//~ gDVNaviConsole.innerHTML += "<br>\n";
	gDVNaviStatus = document.getElementById("idDVNaviStatus");
	AddExpedition(gDVNavi_CityX,gDVNavi_CityY,gExpeditionMaxAP,new Object(),0,"");
	gDVNavi_Steps = 0;
	DVNavi_StepBlock();
	return false;
}

function DVNavi_StepBlock () { // delayed execution to avoid browser hang
	var blocksteps = gDVNavi_BlockSteps;
	while (gExpeditions.length > 0 && blocksteps > 0) { DVNavi_Step(); --blocksteps; }
	if (gExpeditions.length > 0)
			window.setTimeout("DVNavi_StepBlock()",10); // short pause, then continue
	else	DVNavi_Finished();
}

function DVNavi_Step () { // main step, this eats cpu for breakfast
	<?php $bHeuristicsActive = isset($_REQUEST["heuristic"]);?>
	<?php $bHeuristicsActive = true;?>
	<?php if ($bHeuristicsActive) {?>
	// try to pick the "best" element. but didn't work, neither with current score, nor with heuristic
	var len = Math.min(2000,gExpeditions.length);
	var e = false;
	var e_i = 0;
	var next_heur = -1;
	var next_score = -1;
	for (var i=0;i<len;++i) {
		var cur = gExpeditions[i];
		if (cur.heur > next_heur) { e_i = i; next_heur = cur.heur; e = cur; }
		//~ if (cur.score > next_score || (cur.score == next_score && cur.heur > next_heur)) { e_i = i; next_heur = cur.heur; next_score = cur.score; e = cur; }
	}
	if (e) { gExpeditions[e_i] = gExpeditions[gExpeditions.length-1]; gExpeditions.pop(); }
	<?php } else {?>
	var e = gExpeditions.pop(); // just take the last element
	<?php }?>
	
	if (!e) return;
	gExpeditionC -= 1;
	var bFinished = e.ap <= 2 && ReturnAP(e.x,e.y) == 0;
	if (bFinished && e.score > gMinScore) {
		gMinScore = e.score;
		//~ table.insert(gFinishedExp,e) 
		//~ MyPrintLine("new highscore",gMinScore,e.txt);
		gBestExp = e;
		gBestExpPath = e.txt;
		DVNavi_ShowBestResult();
	}
	AddExpedition(e.x-1,e.y,e.ap-1,e.visited,e.score,e.txt);
	AddExpedition(e.x+1,e.y,e.ap-1,e.visited,e.score,e.txt);
	AddExpedition(e.x,e.y-1,e.ap-1,e.visited,e.score,e.txt);
	AddExpedition(e.x,e.y+1,e.ap-1,e.visited,e.score,e.txt);
	++gDVNavi_Steps;
	if ((gDVNavi_Steps % 500) == 0) MyPrintStatus("step "+gDVNavi_Steps+","+gExpeditionC+","+gMinScore+","+gBestExpPath);
	//~ print("step",gDVNavi_Steps,gExpeditionC,#gFinishedExp,e.ap,e.x..","..e.y,Score(e.x,e.y),bFinished and "HOME" or "",e.score,e.txt)
}

function DVNavi_Finished () { MyPrintStatus("Fertig. Beste Route:"+gBestExpPath); }
function img (url) { return "<img src='"+url+"'>"; }

// display a little table showing the route
function DVNavi_ShowBestResult () {
	var html = "<table border=1 cellspacing=0 cellpadding=0 class='dvnavimap'>";
	var c_unexp = 0;
	var c_ruin  = 0;
	for (y=1;y<=gDVNavi_MapH;++y) {
		html += "<tr>";
		for (x=1;x<=gDVNavi_MapW;++x) {
			var cell;
			if (IsCity(x,y)) {
				cell = img("images/map/dvnavi_city.gif");
			} else {
				var bBigScore = Score(x,y) > 500;
				var cellclass = DVNavi_Class(x,y);
				if (InExp(gBestExp,x,y)) {
						 if (cellclass == "ruin") { cell = img("images/map/dvnavi_exp_ruin.gif"); ++c_ruin; }
					else if (cellclass == "unexp") { cell = img("images/map/dvnavi_exp_unexp.gif"); ++c_unexp; }
					else cell = bBigScore ? img("images/map/dvnavi_exp_high.gif") : img("images/map/dvnavi_exp_low.gif");
				} else {
						 if (cellclass == "ruin") cell = img("images/map/dvnavi_ruin.gif");
					else if (cellclass == "unexp") cell = img("images/map/dvnavi_unexp.gif");
					else cell = img("images/map/dvnavi_zone.gif");
				}
			}
			html += "<td>"+cell+"</td>";
		}
		html += "</tr>\n";
	}
	html += "</table>\n";
	html += gExpeditionMaxAP+" AP<br>\n";
	html += c_unexp+" Unerforschte Felder<br>\n";
	html += c_ruin+" Ruinen<br>\n";
	document.getElementById("idDVNaviResult").innerHTML = html;
}


function MyOnLoad () {
	MyOnLoad_OtherCity();
}

// ***** ***** ***** ***** *****  DVNavi END




</script>
<noscript>
(!javascript needed!)
</noscript>
<?php
}



$xmlurl = kSeelenID ? ("http://www.dieverdammten.de/xml/?k=".urlencode(kSeelenID)) : false;
$xmlurl_secret = $xmlurl; // soll nicht für den user sichtbar sein, enthaelt evtl sitekey
if (kDV_SiteKey && kDV_SiteKey != "kDV_SiteKey" && $xmlurl_secret) $xmlurl_secret .= ";sk=".urlencode(kDV_SiteKey); // einziger unterschied : fastcache
define("kXMLUrl_SampleFile","sample.xml"); // kein sitekey
define("kXMLUrl_Basic",$xmlurl); // kein sitekey
define("kXMLUrl_Secret",$xmlurl_secret); // enthaelt sitekey! das sollte der user nicht zu sehen kriegen


/*
http://www.dieverdammten.de/xml?k=USER_KEY;sk=SITE_KEY
http://www.dieverdammten.de/xml/ghost?k=USER_KEY;sk=SITE_KEY
http://www.dieverdammten.de/xml/ghost?k=USER_KEY;sk=SITE_KEY;comment=1
http://www.dieverdammten.de/disclaimer?id=DESTINATION_ID

DV-FORUM


The error "only_available_to_secure_request" means that you are trying
to call the Ghost XML request URL
("http://www.dieverdammten.de/xml/ghost? k=....;sk=...") with an
invalid key (k). This is more likely that the provided user-key (k) is
not a secure key.

To get access to this XML, the visitor has to follow these steps :

- access your site through the site listing on DieVerdammten.de (not
yet available, unfortunately),
- your script will then receive a personal specific user key from this call,
- you can then request the desired XML URL using your permanent site
key (sk) AND this user specific key (k).

The "secured" user key is built on-the-fly with the "public user key"
+ "your site key" : this means that a webmaster can't use a secured
user-key on another external website, as this key is specific to his
very own website...

You should ask Dayan to add your URL to the Dieverdammten directory,
so you would be able to get the secured key feature.


...
Außerdem ist der Name der POST-Variable des UserKeys, der über die Seite ermittelt wird "key"(simple as that fand das trotzdem nicht in der Hilfe...)
...
Deine Webseite kann dann nur noch über das Verzeichnis externer Anwendungen aufgerufen werden und du erhälst von uns einen "key" in POST-Form. Ruft die folgenden URL auf das XML zu erhalten: 


*/

if (isset($_REQUEST["sktest"])) {
	//~ $url = "http://www.dieverdammten.de/xml/ghost?k=".urlencode(kSeelenID).";sk=".kDV_SiteKey;
	//~ $url = "http://www.dieverdammten.de/xml/ghost?k=".urlencode(kSeelenID)."&sk=".kDV_SiteKey;
	//~ $url = "http://www.dieverdammten.de/xml/ghost?key=".urlencode(kSeelenID)."&sk=".kDV_SiteKey;
	$url = "http://www.dieverdammten.de/xml/ghost?k=XXX_MEINE_SEELENID_XXX;sk=XXX_MEIN_SITEKEY_XXX";
	// TODO : strtr
	
	//~ echo "url=$url<br><hr>\n\n\n";
	echo "SiteKeyTest<br>\n";
	exit(file_get_contents($url));
}

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
	global $gGameID;
	echo "<table><tr><td valign=top>";

		echo "<table><tr><td>";
			echo href("http://verdammt.zwischenwelt.org/".(kGhostKey?("?key=".urlencode(kGhostKey)):""),"(aktualisieren)"); 
		echo "</td><td>";
			if (kSeelenID) { ?><form action="" method="POST"><input type="submit" name="LogOut" value="LogOut"></form><?php }
		echo "</td><td>";
			$otherCities = sqlgettable("SELECT *,MAX(`day`) as maxday,MAX(`time`) as maxtime,MIN(IF(UNIX_TIMESTAMP() - `time` > 1.5*24*3600,1,0)) as bDead FROM xml GROUP BY gameid ORDER BY bDead ASC,maxday DESC");
			// date("H:i d-m-Y",$city->maxtime)
			if (kSeelenID && count($otherCities) > 1) {
			$mygameid = kSearchGameID ? kSearchGameID : $gGameID;
			$mygameday = kSearchGameDay ? kSearchGameDay : sqlgetone("SELECT MAX(`day`) FROM xml WHERE gameid = ".intval($mygameid));
			?>
			<script type="text/javascript">
			gOtherCitySelectDays = new Object();
			<?php foreach ($otherCities as $city) if ($city->cityname != "") {?>gOtherCitySelectDays[<?=$city->gameid?>] = <?=$city->maxday?>;
			<?php }?>
			function InitOtherCityDayDropdown (val,ctl_drop,bOnLoad) {
				var html = "";
				var selected_day = bOnLoad ? <?=$mygameday?> : val;
				for (var i=val;i>=1;--i) html += "<option"+((i==selected_day)?" selected":"")+">"+i+"</option>\n";
				ctl_drop.innerHTML = html;
				//~ alert(val+":"+ctl_drop); 
			}
			function MyOnLoad_OtherCity () { 
				InitOtherCityDayDropdown(gOtherCitySelectDays[document.getElementById("idOtherCityGameIDSelect").value],document.getElementById("idOtherCityDaySelect"),true); 
			}
			</script>
			
			<form action='?' method="GET">
			<select name="gameid" onchange='InitOtherCityDayDropdown(gOtherCitySelectDays[this.value],this.form.day,false)' id='idOtherCityGameIDSelect'>
			<?php foreach ($otherCities as $city) if ($city->cityname != "") {?>
			<option value="<?=$city->gameid?>" <?=($mygameid == $city->gameid)?"selected":""?>><?=($city->bDead==1)?"(TOT)":""?> <?=htmlspecialchars(utf8_decode($city->cityname))?>(Tag<?=$city->maxday?>)</option>
			<?php }?>
			</select>
			<select name="day" id='idOtherCityDaySelect'></select>
			<input type="submit" name="Go" value="Go"></form>
			</form>
			<?php
			}
		echo "</td></tr></table>";


		echo "Links: ".
			href("mailto:ghoulsblade@schattenkind.net","(email)")." ".
			href("http://forum.der-holle.de/viewtopic.php?f=42&t=106","ForenThread")." ".
			href("http://dvmap.nospace.de/index.php","DVMap")." ".
			href("http://emptycookie.de/index.php?id=".(kSeelenID?kSeelenID:""),"EmptyCookie")." ".
			href("http://verdammt.mnutz.de/","Baldwin","title='in entwicklung'")." ".
			href("http://nobbz.de/wiki/","NobbzWiki")." ".
			href("http://forum.der-holle.de/","HolleForum")." ".
			href("http://chat.mibbit.com/?channel=%23dieverdammten&server=irc.mibbit.net","Chat")." ".
			href("http://www.patamap.com/index.php?page=patastats","PataMap")." ".
			href("http://github.com/ghoulsblade/zwverdammt","github(sourcecode)")." ". 
			href("http://poll.nobbz.de/attack","AngriffsStatistik")." ".   
			href("http://www.twinpedia.com/","twinpedia(fr)").
			href("http://translate.google.com/translate?hl=de&sl=fr&tl=de&u=http%3A%2F%2Fwww.twinpedia.com%2F","(t)")." ".   
			href(kXMLUrl_Basic,"XmlStream")." ". 
			"<br>\n";
			// http://verdammt.mnutz.de/  (baldwin,stadt)
			// http://asid.dyndns.org/exphelper2 (asid,holleirc)
			// http://coding-bereich.de/dieverdammten/
			
	echo "</td><td valign=top>";

		MotionTwinNote();

	echo "</td></tr></table>";
	
	global $gProfileHTML;
	if (isset($_REQUEST["profile"])) echo $gProfileHTML;
}


if (!kSeelenID) { 
	PrintHeaderSection();
	?> <form action="" method="POST"> Seelen-ID:<input name="SeelenID"> <input type="submit" name="Login" value="Login"> </form> <?php
	//~ echo href("?sample=1","(Vorschau ohne SeelenID)")."<br>\n";
	PrintFooter(); exit(0);
}


// ***** ***** ***** ***** ***** Load XML

$temp_loadinfotxt = "";
$gStoreXML = true;
$gDemo = false;
$xmlstr = false;
$xml = false;
$gDebugStreamID = false;

$gProfileHTML = "";
$gMyProfile_LastT = false;
$gMyProfile_LastName = false;
function MyProfile ($name=false) {
	global $gProfileHTML,$gMyProfile_LastT,$gMyProfile_LastName;
	if ($gMyProfile_LastT) {
		$gProfileHTML .= (time() - $gMyProfile_LastT)."msec ".$gMyProfile_LastName."<br>\n";
		$gMyProfile_LastT = false;
	}
	if ($name) {
		$gMyProfile_LastT = time();
		$gMyProfile_LastName = $name;
	}
}
		
		
if (kSearchGameID && kSearchGameDay) {
	$xmlstr = GetLatestXmlStrFromGameIDAndDay(kSearchGameID,kSearchGameDay); 
	if (!$xmlstr) exit("failed to load xml");
	$xml = simplexml_load_string(MyEscXML($xmlstr));
	$gStoreXML = false;
} else if (kSearchGameID) {
	$xmlstr = GetLatestXmlStrFromGameID(kSearchGameID); 
	if (!$xmlstr) exit("failed to load xml");
	$xml = simplexml_load_string(MyEscXML($xmlstr));
	$gStoreXML = false;
} else if (kSearchXMLID) {
	$xmlstr = GetLatestXmlByID(kSearchXMLID);
	if (!$xmlstr) exit("failed to load xml");
	$xml = simplexml_load_string(MyEscXML($xmlstr));
	$gStoreXML = false;
} else {
	if ($gUseSampleData) { 
		$gDemo = true;
		$gStoreXML = false;
		$xmlstr = GetLatestXmlStrFromSeelenID(kDV_SampleSoulID);
		if ($xmlstr) {
			$temp_loadinfotxt .= "(Beispiel aus der Datenbank)<br>\n";
		} else {
			$xmlstr = file_get_contents(kXMLUrl_SampleFile);
			if ($xmlstr) {
				$temp_loadinfotxt .= "(Beispiel aus Datei)<br>\n";
			} else {
				exit("Laden eines Beispiels aus Datenbank und Datei fehlgeschlagen!");
			}
		}
		//~ if ($xmlstr) $temp_loadinfotxt .= "sample load from db OK<br>\n"; else $temp_loadinfotxt .= "sample load from db failed<br>\n";
	}
	if (!$xmlstr) {
		MyProfile("download xml-stream");
		$xmlstr = file_get_contents(kXMLUrl_Secret);
		MyProfile();
		$o = false;
		$o->time = time();
		$o->seelenid = (string)kSeelenID;
		$o->xml = $xmlstr;
		MyProfile("save xml-stream to debug");
		sql("INSERT INTO stream_debug SET ".obj2sql($o)); 
		MyProfile();
		// save stream right after download and delete it later after successful save.
		// this way we can capture streams that trigger errors before being saved in fully analyzed format
		$gDebugStreamID = mysql_insert_id();
		
		if (kGhostKey) {
			//~ $ghosturl = "http://www.dieverdammten.de/xml/?k=".urlencode(kGhostKey).";sk=".urlencode(kDV_SiteKey);
			$ghosturl = "http://www.dieverdammten.de/xml/ghost?k=".urlencode(kGhostKey).";sk=".urlencode(kDV_SiteKey);
			
			MyProfile("download ghost-stream");
			$ghostxmlstr = file_get_contents($ghosturl);
			MyProfile();
			
			
			$o = false;
			$o->time = time();
			$o->seelenid = (string)kSeelenID;
			$o->ghostkey = (string)kGhostKey;
			$o->xml = $ghostxmlstr;
			MyProfile("save ghost-stream to debug");
			sql("INSERT INTO stream_ghost_debug SET ".obj2sql($o)); 
			MyProfile();
			
			if (!$ghostxmlstr) echo "ghost stream failed to load<br>\n";
			
			//~ $ghostxmlstr = file_get_contents("sample_ghost.xml");
			if ($ghostxmlstr) {
				MyProfile("parsexml: ghost-stream");
				$gGhostStreamOk = true;
				ParseGhostXMLMapInfo($ghostxmlstr);
				MyProfile();
			}
		}
	}
	MyProfile("parsexml: xml-stream");
	@$xml = simplexml_load_string(MyEscXML($xmlstr));
	MyProfile();

	if (!$xml->data[0]->city[0]["city"] || $xml->status[0]["open"] == "0") {
		$xmlstr = GetLatestXmlStrFromSeelenID(kSeelenID);
		$temp_loadinfotxt .= "<h1>Webseite down, Zombie-Angriff im Gange!</h1>\n";
		if ($xmlstr) {
			$temp_loadinfotxt .= "(lade letzten Stand)<br>\n";
		} else {
			$temp_loadinfotxt .= "(lade dummy/demo daten)<br>\n";
			$xmlstr = file_get_contents(kXMLUrl_SampleFile);
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
		case kDeathType_Zyanid:			return img(kIconURL_death			,"Zyanid. ".$txt); break;
		case kDeathType_Erhaengt:		return img(kIconURL_death			,("Erhaengt. ").$txt); break;
		case kDeathType_Infektion:		return img(kIconURL_infektion		,"Infektion. ".$txt); break;
		case kDeathType_Dehydriert:		return img(kIconURL_dehydration		,"Dehydriert. ".$txt); break;
		case kDeathType_ZombieAngriff:	return img(kIconURL_ZombieAngriff	,"ZombieAngriff. ".$txt); break;
		case kDeathType_Entzug:			return img(kIconURL_death			,"Entzug. ".$txt); break;
		case kDeathType_Kopfschuss:		return img(kIconURL_death			,("Kopfschuss. ").$txt); break;
		case kDeathType_AccountDeleted:	return img(kIconURL_death			,("Account geloescht. ").$txt); break;
		case kDeathType_Vergiftet:		return img(kIconURL_death			,("Mord(Vergiftung). ").$txt); break;
	}
	return img(kIconURL_death,"Unbekannt[".intval($dtype)."]. ".$txt);
}
function MyLoadGlobals () {
	global $xml,$icon_url,$icon_url_item,$avatar_url,$city;
	global $def,$gGameDay,$gGameID;
	global $gCitizens,$buerger_draussen,$buerger_alive,$buerger_hero;

	$icon_url			= $xml->headers[0]["iconurl"];
	$icon_url_item		= $xml->headers[0]["iconurl"]."item_";
	$avatar_url			= $xml->headers[0]["avatarurl"];
	$city				= $xml->data[0]->city[0];
	define("kIconUrl",$icon_url);
	define("kIconUrlItem",$icon_url_item);
	
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
	define("kIconURL_ruindig"		, $icon_url."tag_7.gif");
	
	define("kIconURL_aussenwelt"	, $icon_url."r_doutsd.gif");
	define("kIconURL_infektion"		, $icon_url."r_dinfec.gif");
	define("kIconURL_dehydration"	, $icon_url."r_dwater.gif");
	define("kIconURL_ZombieAngriff"	, "http://data.dieverdammten.de/gfx/forum/smiley/h_zhead.gif");
	
	
	define("kZombieIconHTML",LinkWiki("Zombie",img(kIconURL_zombie,"Zombies")));
	define("kDefIconHTML",img(kIconURL_def,"def"));

	define("kDeathType_Dehydriert",1);
	define("kDeathType_Zyanid",3);
	define("kDeathType_Erhaengt",4);
	define("kDeathType_Aussenwelt",5);
	define("kDeathType_ZombieAngriff",6);
	define("kDeathType_Entzug",7);
	define("kDeathType_Infektion",8);
	define("kDeathType_Kopfschuss",9);
	define("kDeathType_AccountDeleted",10);
	define("kDeathType_Vergiftet",11); // Mord(Vergiftung)
	
	$timetxt = $xml->headers[0]->game[0]["datetime"]; // 2010-08-29 14:11:44
	sscanf($timetxt,"%u-%u-%u %u:%u:%u",$year,$month,$day,$h,$m,$s);
	$time = mktime($h,$m,$s,$month,$day,$year);
	define("kRecordTime",$time);
	$def = (int)($city->defense[0]["total"]);
	$gGameDay = (int)$xml->headers[0]->game[0]["days"];
	$gGameID = (int)$xml->headers[0]->game[0]["id"];
	define("kGameID",$gGameID);
	define("kGameDay",$gGameDay);

	define("kCityX",$xml->data[0]->city["x"]);
	define("kCityY",$xml->data[0]->city["y"]);

	global $gBuergerOnPos;
	$gBuergerOnPos = array();
	$buerger_draussen = 0;
	$buerger_alive = 0;
	$buerger_hero = 0;
	$gCitizens = $xml->data[0]->citizens[0]->citizen;
	if ($gCitizens) foreach ($gCitizens as $citizen) { 
		if ($citizen["dead"] == "0") ++$buerger_alive;
		if ($citizen["hero"] != "0") ++$buerger_hero;
		$x = intval($citizen["x"]);
		$y = intval($citizen["y"]);
		if ($x == kCityX && $y == kCityY) {} else { ++$buerger_draussen; }
		if (!isset($gBuergerOnPos["$x,$y"])) $gBuergerOnPos["$x,$y"] = array();
		$gBuergerOnPos["$x,$y"][] = $citizen;
	}
	
	
	$e = $xml->data[0]->estimations[0]->e[0];
	define("kZombieEstimationQualityMaxxed",$e["maxed"]!="0"); // schon maximale qualität ?

	
	global $gBuildingDone,$gUpgrades;
	$gBuildingDone = array();
	$gUpgrades = array();
	$arr = $xml->data[0]->upgrades[0]->up; if ($arr) foreach ($arr as $upgrade) $gUpgrades[StripUml($upgrade["name"])] = (int)$upgrade["level"];
	$arr = $xml->data[0]->city[0]->building; if ($arr) foreach ($arr as $building) $gBuildingDone[StripUml($building["name"])] = true;

	
	global $gMap;
	global $gRuinen;
	$gRuinen = array();
	$map = $xml->data[0]->map[0];
	define("kMapW",$map["wid"]);
	define("kMapH",$map["hei"]);
	$gMap = array();
	function MapSet ($x,$y,$data) { 
		global $gMap;
		$gMap["$x,$y"] = $data; 
		//~ echo "MapSet($x,$y,nvt=".$data["nvt"].",tag=".$data["tag"].")<br>\n";
	}
	if ($map->zone) foreach ($map->zone as $zone) {
		$x = intval($zone["x"]);
		$y = intval($zone["y"]);
		MapSet($x,$y,$zone);
		$r = $zone->building[0];
		if ($r) { RegisterRuin($gGameID,$x-kCityX,kCityY-$y,$r["name"],$r["type"]); $gRuinen[] = array("x"=>$x,"y"=>$y,"node"=>$r); }
	}
}

MyProfile("MyLoadGlobals");
MyLoadGlobals();
MyProfile();
function StoreXML () {
	global $gGameID,$gGameDay,$city,$xmlstr;
	$o = false;
	$o->seelenid = (string)kSeelenID;
	$o->time = time();
	$o->gameid = $gGameID;
	$o->cityname = (string)$city["city"];
	$o->day = (int)$gGameDay;
	$o->xml = $xmlstr;
	sql("INSERT INTO xml SET ".obj2sql($o));
}

if ($gStoreXML) {
	MyProfile("StoreXML");
	StoreXML();
	MyProfile("Delete debug-xml");
	if ($gDebugStreamID) sql("DELETE FROM stream_debug WHERE id = ".intval($gDebugStreamID));
	MyProfile();
}

PrintJavaScriptBlock();
echo $temp_loadinfotxt;

PrintHeaderSection(); // late, so full xml is available



// ***** ***** ***** ***** ***** hilfs-funktionen

function CheckBuilding ($bname,$minlevel,$text,$pre="den/die") { 
	if (GetBuildingLevel($bname) < 0) { echo "Hilf mit ".$pre." <b>$bname</b> zu bauen: ".$text."<br>\n"; return false; }
	if (GetBuildingLevel($bname) < $minlevel) { echo "Hilf mit ".$pre." <b>$bname</b> als <b>Verbesserung des Tages</b> zu wählen: ".$text."<br>\n"; return false; }
	return true;
}


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
echo " Tag=".$gGameDay." (".date("Y-m-d H:i",kRecordTime).")";
echo " ".LinkWiki("Wasser",img($icon_url."small_water.gif","Wasser")).":".$city["water"];
echo " &Uuml;berlebende=".$buerger_alive;
echo " Helden=".$buerger_hero;
echo " draussen=".$buerger_draussen;
if ($gDemo) echo " <b>(demo/offline daten)</b>";
if ($gGhostStreamOk) echo " <b>ghost=ok</b>";
echo "<br>\n";

$definfo = $xml->data[0]->city[0]->defense[0];

echo LinkWiki("Verteidigungsgegenstände")." : ".$definfo["items"]." * ".LinkWiki("Verteidigungsanlage",$definfo["itemsMul"])." = ".($definfo["items"]*$definfo["itemsMul"]).kDefIconHTML."<br>\n";

$vlevel = GetBuildingLevel("Verteidigungsanlage");
if ($vlevel < kBuildingLevelMax) {
	$deffactors = array(2,2.5,3.2,4.4,5.6,6.8,8);
	echo LinkWiki("Verteidigungsanlage")."(L:".$vlevel.") aufstufen (*".$deffactors[$vlevel+2]."): + ".($deffactors[$vlevel+2]*$definfo["items"] - $deffactors[$vlevel+1]*$definfo["items"]).kDefIconHTML."<br>\n";
}

//~ <defense base="5" items="43" citizen_guardians="3" citizen_homes="18" upgrades="0" buildings="368" total="634" itemsMul="5.6"/>

//~ echo "SeelenPunkte: ".GetSoulPoint($gGameDay-1)." für Tod VOR Zombieangriff<br>\n";
//~ echo "SeelenPunkte: ".GetSoulPoint($gGameDay)."(+".($gGameDay).") für Tod beim Zombieangriff oder morgen<br>\n";
//~ echo "SeelenPunkte: ".GetSoulPoint($gGameDay+1)."(+".($gGameDay+1).") für Tod beim morgigen Zombieangriff oder übermorgen<br>\n";

if (!kZombieEstimationQualityMaxxed) echo "<b>Hilf mit die Schätzung im ".LinkBuilding("Wachturm")." zu verbessern!</b><br>\n";

if (CheckBuilding("Werkstatt",0,"wird benötigt um BaumStümpfe, MetallTrümmer und viele andere Sachen umzuwandeln","die")) {
	CheckBuilding("Wachturm",0,"wird benötigt um den Forschungsturm zu bauen","den");
	CheckBuilding("Forschungsturm",2,"sorgt dafür dass sich leere Felder,<br> auf denen man sonst nur BaumStümpfe und MetallTrümmer findet wieder regenerieren","den");
}
if ($gGameDay == 1) { echo ("bau dein Feldbett zu einem Zelt aus, aber NICHT zu einer Baracke, Holzbretter werden dringend für die Werkstatt benötigt")."<br>\n"; }


$f = GetBuildingLevel("Forschungsturm");
$leer_regen = array("fast 0",25,37,49,61,73,85,false);
$p0 = $leer_regen[$f+1];
$p1 = $leer_regen[$f+2];
echo LinkBuilding("Forschungsturm")." ".(($f >= 0)?"Stufe $f":"nicht gebaut")." -&gt; Chance das ein leeres Feld sich regeneriert : ".$p0."% ".($p1?("nächste:".$p1."%)"):"")."<br>\n";


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
	echo "<td nowrap align='right'>".$basedef.($bHeld?"+2":"").img(kIconURL_def).(isset($gDefIcon[$basedef])?img($gDefIcon[$basedef]):"").($bBarackenBauer?"<b title='$tippb'>BARACKENBAUER!</b>":"")."</td>";
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

function GetRuinPossibleTipp ($ap) {
	$sum = sqlgetone("SELECT COUNT(*) FROM ruin WHERE ap = ".intval($ap));
	$types = sqlgettable("SELECT *,COUNT(*) as c FROM ruin WHERE ap = ".intval($ap)." GROUP BY type ORDER BY c DESC");
	$txt = "";
	foreach ($types as $o) $txt .= sprintf("%2.0f%%",$o->c*100/$sum)." ".$o->name."<br>\n";
	return $txt;
}

echo "<br>\n";
echo href("http://nobbz.de/wiki/index.php/Ruinen","Ruinen (".count($gRuinen)."/10)")."<br>\n";
$i = 0;
function absap	  ($x,$y) { return abs($x-kCityX)+abs(kCityY-$y); } // in: absolute position
function ruin_cmp ($a,$b) { return absap($a["x"],$a["y"]) - absap($b["x"],$b["y"]); }
usort($gRuinen,"ruin_cmp");
// $gRuinen
echo "<table border=1 cellspacing=0 cellpadding=1>\n";
foreach ($gRuinen as $r) {
	++$i;
	$x = $r["x"] - kCityX;
	$y = kCityY - $r["y"];
	$textid = "idRuinText".$i;
	$dig = $r["node"]["dig"];
	$dithtml = ($dig && $dig>0)?(" ($dig ".img(kIconURL_ruindig).")"):"";
	$bUnknown = $r["node"]["type"] == -1;
	$ap = abs($x)+abs($y);
	
	echo "<tr>\n";
	echo "<td align=center>$x/$y</td>\n";
	echo "<td>".$ap."AP</td>\n";
	echo "<td>";
	echo "".($bUnknown?"???":LinkRuin(utf8_decode($r["node"]["name"]))).$dithtml." ".href("javascript:ShowHide(\"".$textid."\")",$bUnknown?"(möglich)":"(text)")."<br>\n";
	
	$tipp = ($bUnknown)?GetRuinPossibleTipp($ap):"";
	
	echo "<span style='display:none;' id='".$textid."'>".htmlspecialchars(utf8_decode($r["node"])).$tipp."</span>\n";
	echo "</td>\n";
	echo "</tr>\n";
}
echo "</table>\n";

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
	echo "<td>".(($msg && $msg != "")?img(kIconURL_msg,($msg)):"")."</td>";
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
	echo img($icon_upgrade_url,"Verbesserung").$upgrade["level"]." ".LinkBuilding(utf8_decode($upgrade["name"]))."<br>\n"; // $upgrade["buildingId"]
}

echo "<br>\n";
// ***** ***** ***** ***** ***** GEBÄUDE
echo href("http://nobbz.de/wiki/index.php/Geb%C3%A4ude_%C3%9Cbersicht","Gebäude").":<br>\n";
foreach ($xml->data[0]->city[0]->building as $building) {
	RegisterBuildingType($building);
	echo img($icon_url.$building["img"].".gif").LinkBuilding(utf8_decode($building["name"]))."<br>\n";
}



echo "</td></tr></table>\n"; // END sub table : (bürger, exp+tote, verbesser+gebäude)



echo "</td><td valign=top>\n";



// layout table : links bank, rechts zombie-abschätzung
echo "<table border=0 width='100%'><tr><td valign=top align=left>\n";

// ***** ***** ***** ***** ***** BANK
//~ echo "Bank:<br>";
$cats = array();
foreach ($xml->data[0]->bank[0]->item as $item) { 
	RegisterItemType($item);
	$c = (int)$item["count"];
	$cat = (string)$item["cat"];
	$bBroken = $item["broken"] != 0;
	$html = (($c>1)?($c."x"):"").LinkItem($item["name"],img(kIconUrlItem.$item["img"].".gif",($item["name"]),$bBroken?"class='broken'":""));
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
foreach ($cats2 as $k => $arr) echo "<tr><th>".(isset($gCatTrans[$k])?$gCatTrans[$k]:$k).":</th><td align=left>\n ".implode(" &nbsp;\n ",$arr)."\n</td></tr>\n";
echo "</table>\n";



echo "</td><td valign=top align=right>\n"; // layout



// ***** ***** ***** ***** ***** Zombie-Angriff
$e = $xml->data[0]->estimations[0]->e[0];
$e2 = $xml->data[0]->estimations[0]->e[1];
$zombie_min = (int)($e["min"]);
$zombie_max = (int)($e["max"]);
$estimate_bad_html = img(kIconURL_warning,MyImgTitleConst("ungenau, Hilf mit die Schätzung im Wachturm zu verbessern!"));

echo LinkWiki("Verteidigung")."<br>\n";
echo "<table>";
echo "<tr><td>".img(kIconURL_wachturm,MyImgTitleConst("Schätzung")).(kZombieEstimationQualityMaxxed?"":$estimate_bad_html)."</td><td>".kZombieIconHTML."$zombie_min-$zombie_max</td><td>-&gt; ".kDefIconHTML."$def</td><td>-&gt; ".img(kIconURL_attackin,"tote")."".max(0,$zombie_min-$def)."-".max(0,$zombie_max-$def)."</td></tr>\n";
if ($e2) {
	$zombie2_min = (int)($e2["min"]);
	$zombie2_max = (int)($e2["max"]);
	$estimate_bad_html = img(kIconURL_warning,MyImgTitleConst("ungenau, Hilf mit die Schätzung im Wachturm zu verbessern!"));
	echo "<tr><td>".img(kIconURL_wachturm,MyImgTitleConst("Schätzung für Morgen"))."+1".(($e2["maxed"]!="0")?"":$estimate_bad_html)."</td><td>".kZombieIconHTML."$zombie2_min-$zombie2_max</td><td>-&gt; ".kDefIconHTML."$def</td><td>-&gt; ".img(kIconURL_attackin,"tote")."".max(0,$zombie2_min-$def)."-".max(0,$zombie2_max-$def)."</td></tr>\n";
}

$stat = array(0,24,50,97,149,215,294,387,489,595,709,831,935,1057,1190,1354,1548,1738,1926,2140,2353,2618,2892,3189,3506,3882,3952,4393,4841,5339,5772,6271,6880,7194,7736,8285,8728,9106,9671,9888,10666,11508,11705,12608,12139,12921,15248,11666);
$zombie_av = isset($stat[$gGameDay]) ? $stat[$gGameDay] : false;
$zombie_av2 = isset($stat[$gGameDay+1]) ? $stat[$gGameDay+1] : false;
$zombie_av3 = isset($stat[$gGameDay+2]) ? $stat[$gGameDay+2] : false;
if ($zombie_av) echo "<tr><td>".img(kIconURL_statistic,MyImgTitleConst("Statistik"))."</td><td>".kZombieIconHTML."$zombie_av</td><td>-&gt; ".kDefIconHTML."$def</td><td>-&gt; ".img(kIconURL_attackin,"tote")."".max(0,$zombie_av-$def)."</td></tr>\n";
if ($zombie_av2) echo "<tr><td>".img(kIconURL_statistic,MyImgTitleConst("Statistik für Morgen"))."+1</td><td>".kZombieIconHTML."$zombie_av2</td><td>-&gt; ".kDefIconHTML."$def</td><td>-&gt; ".img(kIconURL_attackin,"tote")."".max(0,$zombie_av2-$def)."</td></tr>\n";
if ($zombie_av3) echo "<tr><td>".img(kIconURL_statistic,MyImgTitleConst("Statistik für ÜberMorgen"))."+2</td><td>".kZombieIconHTML."$zombie_av3</td><td>-&gt; ".kDefIconHTML."$def</td><td>-&gt; ".img(kIconURL_attackin,"tote")."".max(0,$zombie_av3-$def)."</td></tr>\n";
echo "</table>";

$def_graben_delta = array(20,13,21,32,33,51,0);
//~ echo LinkBuilding("Grosser Graben")." verbessern/bauen:+".$def_graben_delta[GetBuildingLevel("Großer Graben")+1].kDefIconHTML."<br>\n";
if ($zombie_av && $zombie_av - $def > 0) { echo img(kIconURL_warning,"es wird Tote geben")."Baut mehr ".href("http://nobbz.de/wiki/index.php/Verteidigung","Verteidigung")."!<br>\n"; }

echo "</td></tr></table>\n";


// ***** ***** ***** ***** ***** MAP




function TagIconURL ($tagid) { return "http://data.dieverdammten.de/gfx/icons/tag_".((int)$tagid).".gif"; }





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

function GetBuergerNamenOnAbsPos($x,$y) { 
	global $gBuergerOnPos; 
	$arr = array();
	if ($gBuergerOnPos["$x,$y"]) foreach ($gBuergerOnPos["$x,$y"] as $b) if ($b["dead"] == "0") $arr[] = $b["name"];
	return $arr;
}
function GetAnzahlBuergerOnAbsPos($x,$y) { global $gBuergerOnPos; return count($gBuergerOnPos["$x,$y"]); }

function GetMapToolTip ($x,$y) {
	global $gGameDay,$gIconText;
	$txt = "";
	$rx = $x - kCityX;
	$ry = kCityY - $y;
	$txt .= "($rx,$ry)";
	if ($x != kCityX || $y != kCityY) { 
		$o = GetMapNote($rx,$ry);
		if ($o) {
			$age = (int)$gGameDay - (int)$o->day;
			$bToday = ($age == 0);
			$zombies = $bToday ? trim($o->zombies) : false; // GetZombieNumText($x,$y)
			if ($zombies == "?" || $zombies == "") $zombies = false;
			$txt .= " ";
			if (!$zombies) $zombies = GetZombieNumText($x,$y);
			if ($zombies) $txt .= " ".$zombies." Zombies.";
			if ($o->icon >= 0) $txt .= " <".$gIconText[$o->icon].">";
			$txt .= " [".GetAgeText($o->day,$o->time)."]";
			$txt .= " ".$o->txt;
		}
		$txt .= " ".implode(",",GetBuergerNamenOnAbsPos($x,$y));
	}
	return $txt;
}

function IsMapCellRuine ($x,$y) { $data = Map($x,$y); return $data && $data->building[0]; }

function MapGetCellContent ($x,$y,$mode=kMapMode_Marker) { // kMapMode_Marker,kMapMode_Buerger
	global $gGameID,$gGameDay;
	$rx = $x-kCityX;
	$ry = kCityY-$y;
	$o = GetMapNote($rx,$ry);
	$data = Map($x,$y);
	$r = $data->building[0];
	$html = "";
	$mode = intval($mode);
	$tipp = GetMapToolTip($x,$y);
	$ingametag = $data["tag"] ? $data["tag"] : false;
	if ($mode == kMapMode_Marker) {
		if ($o) {
			$age = (int)$gGameDay - (int)$o->day;
			$bToday = ($age == 0);
			$zombies = $bToday ? $o->zombies : false; // GetZombieNumText($x,$y)
			if ($zombies == "?" || $zombies == "") $zombies = false;
			$old = (!$bToday) ? "_old" : "";
			if ($o->icon >= 0 && $o->icon < kNumIcons)
				$html .= img("images/map/icon_".$o->icon.$old.".gif",$tipp);
			elseif ($ingametag) 
				$html .= img(TagIconURL($ingametag),$tipp);
			if ($zombies) $html .= ("<span class='mapcellzombietxt' title='".htmlspecialchars($zombies)." Zombies'>$zombies</span>"); // GetZombieNumText($x,$y)
			//~ if ($r) $html .= img("images/map/iconmark_ruin.gif",($r["name"]));
		} else {
			if ($ingametag) $html .= img(TagIconURL($ingametag),$tipp);
		}
	}
	if ($mode == kMapMode_InGameTags) {
		if ($ingametag) $html .= img(TagIconURL($ingametag),$tipp);
	}
	if ($mode == kMapMode_Buerger) {
		if ($x != kCityX || $y != kCityY) {
			$c = GetAnzahlBuergerOnAbsPos($x,$y);
			for ($i=0;$i<$c;++$i) $html .= img("images/map/citizen.gif",$tipp);
		}
	}
	return $html;
	//~ if ($r) $html .= img("images/map/iconmark_ruin.gif",($r["name"])," class='iconmark'");
	
	/*
	ruin etc  zwmap mapjs7_core.js layers : divs : .building_<?=$l?> { background-image:url(<?=g("gebaeude/house-$l.png")?>);
	vdmap : <img style="left: 6px; top: 15px;" class="citizen" src="/images/citizen.gif" width="5" height="5" />  (kleines gelbes pünktchen)
		#map div .citizen {
		position:absolute;
		}

	<div id="map" style="width: 240px; height: 240px;">
		<div style="left:60px; top:100px; width:20px; height:20px; background-image:url(/images/map/ruin_d3.gif);" onclick="showDetails(3, 5);" onmouseover="showInfo(this);">
			<div class="data">-4/0 &middot; 3x<img class="tag" src="/images/human.gif" width="16" height="16"></div>      ---> tooltip
			<img style="left: 9px; top: 11px;" class="citizen" src="/images/citizen.gif" width="5" height="5" />
			<img style="left: 4px; top: 11px;" class="citizen" src="/images/citizen.gif" width="5" height="5" />
			<img style="left: 0px; top: 7px;" class="citizen" src="/images/citizen.gif" width="5" height="5" />
		</div>


	*/
}

echo "<table border=0 cellspacing=0><tr><td valign=top>\n";
echo "<span id='idMapContainer'>\n";
RenderMapBlock();

function RenderMapBlock ($mode=kMapMode_Marker) {
	//~ echo "mapmode=$mode<br>\n";
	echo "<table class='map' border=0 cellspacing=0 cellpadding=0>\n";
	for ($y=0;$y<kMapH;++$y) {
		echo "<tr>";
		for ($x=0;$x<kMapW;++$x) {
			$data = Map($x,$y);
			$bgimg = "zone_bg.gif";
			if ($data) {
				$bHasBuilding = isset($data->building);
				$bViewed = ((int)$data["nvt"]) == 0; // nvt : 1/0 (value is 1 was already discovered, but Not Visited Today)
				$bgimg = $bViewed ? "zone.gif" : "zone_nv.gif";
				if ($bHasBuilding) $bgimg = $bViewed ? "ruin.gif" : "ruin_nv.gif";
				if ($bHasBuilding) {
					if ($data["danger"] == 1) $bgimg = "ruin_d1.gif";
					if ($data["danger"] == 2) $bgimg = "ruin_d2.gif";
					if ($data["danger"] >= 3) $bgimg = "ruin_d3.gif";
				} else {
					if ($data["danger"] == 1) $bgimg = "zone_d1.gif";
					if ($data["danger"] == 2) $bgimg = "zone_d2.gif";
					if ($data["danger"] >= 3) $bgimg = "zone_d3.gif";
				}
			}
			if ($x == kCityX && $y == kCityY) $bgimg = "city.gif";
			
			$bgimg = "background='images/map/$bgimg'";
			
			$rx = $x-kCityX;
			$ry = kCityY-$y;
			
			$style = ""; // "bgcolor=green"
			$fname = IsOwnGame()?"MapClickCell":"MapClickCell_Dummy";
			$tipp = GetMapToolTip($x,$y);
			echo "<td $style $bgimg onclick='$fname($rx,$ry);' title='".strtr(htmlspecialchars($tipp),array("'"=>'"'))."' id='map_".$rx."_".$ry."' onmouseover='MapCellTooltip(this);'>".MapGetCellContent($x,$y,$mode)."</td>\n";
			//~ echo "<td width=16 height=16>".($data?("nvt=".$data["nvt"].",tag=".$data["tag"]):"")."</td>";
			//~ echo "<td width=16 height=16>".($data?"x":"")."</td>";
		}
		echo "</tr>\n";
	}
	echo "</table>\n";
}
echo "</span><br>\n";


?>
<a href='javascript:SetMapMode(<?=kMapMode_Marker?>)'>(Marker)</a>
<a href='javascript:SetMapMode(<?=kMapMode_InGameTags?>)'>(InGame)</a>
<a href='javascript:SetMapMode(<?=kMapMode_Buerger?>)'>(Bürger)</a>
<a href='javascript:ShowNaviMenu()'>(DVNavi)</a>
<?php

echo "</td><td valign=top align=left>\n";
echo "<span id='idMapCellInfo'>auf die Karte clicken...</span>\n";
echo "</td></tr></table>\n";




echo img("images/map/zone.gif")		.kZombieIconHTML."0, alleine ok"."<br>\n";
echo img("images/map/zone_d1.gif")	.kZombieIconHTML."1-2, alleine ok"."<br>\n";
echo img("images/map/zone_d2.gif")	.kZombieIconHTML."2-4, mindestens zu zweit hin!"."<br>\n";
echo img("images/map/zone_d3.gif")	.kZombieIconHTML."5+, mindestens zu dritt hin!"."<br>\n";
echo img("images/map/zone_bg.gif")	.kZombieIconHTML."0-99, mindestens zu dritt hin! unerforscht, hier könnte noch eine Ruine sein"."<br>\n";
echo img("images/map/zone_nv.gif")	.kZombieIconHTML."0-99, mindestens zu dritt hin! schon erforscht, aber HEUTE war noch Niemand hier"."<br>\n";
echo img("images/map/icon_1.gif")." das Feld wurde heute als regeneriert markiert, hier lohnt es sich zu graben!<br>\n";
echo img("images/map/icon_0.gif")." oder ".img(TagIconURL(5))." : als leer markiert, wenn sich die Zone nicht inzwischen regeneriert hat (ForschungsTurm!)<br> findet man hier nur noch ".
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

// 



PrintFooter(); 
?>