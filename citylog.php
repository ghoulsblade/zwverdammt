<?php

// idee : summe items (sabo, pharma, drogen)
// idee taschen verbleib (besondere items)
// idee : wasser-log (brunnen+bank)		(zementblöcke werden nicht beachtet) "Unförmige Zementblöcke" "Zementsack"
// idee : rein+rausrennen
// idee : werkstatt herstellung 
// idee : angriffe/diebstähle
// brunnen icon : http://data.dieverdammten.de/gfx/icons/small_well.gif

require_once("defines.php");
require_once("roblib.php");
require_once("lib.verdammt.php");

//~ define("kCityLogSampleFile","samplelog_04.09.2010_tag3.txt");
define("kCityLogSampleFile","samplelog_04.09.2010.txt");

$gImportantItems = array("Ration Wasser","Paracetoid","bandage","Twinoid","Anaboles steroid"); // tasche.gürtel,einkwagen,säge
$gCityLogItemSum = array();


$temp_seelenid = isset($_COOKIE["SeelenID"]) ? $_COOKIE["SeelenID"] : false;
if ($temp_seelenid) LogAccess($temp_seelenid,"citylog");
if ($temp_seelenid) $temp_seelenid = preg_replace('/[^a-zA-Z0-9]/','',$temp_seelenid);
define("kSeelenID",$temp_seelenid); // replaces the old $gSeelenID
if (!kSeelenID) exit("seelenid fehlt!");
define("kGameID",GetGameIDForSeelenID(kSeelenID));




// utf8_decode

function ParseCityLog($txt) {
	$lines = array_reverse(explode("\n",strtr($txt,array("<br>"=>""))));
	$ptime = '(\\[[0-9]+:[0-9]+\\])';
	foreach ($lines as $k => $line) {
		$line = trim($line); 
		if ($line == "") continue;
		//~ if (!(strpos($line,"Pepper") && strpos($line,"Wasser"))) continue;
		//~ echo "line=$line\n";
		$ok = false
			|| CityLog($line,"BankNehmen"	,"$ptime (.*) hat folgenden Gegenstand aus der Bank genommen: (.*) !"									,"aus der Bank genommen") 	
			|| CityLog($line,"BankGeben"	,"$ptime (.*) hat der Stadt folgendes gespendet: (.*)"													,"folgendes gespendet:") 	
			|| CityLog($line,"Brunnen"		,"$ptime (.*) hat eine Ration Wasser genommen"															,"eine Ration Wasser genommen") 
			|| CityLog($line,"BrunnenExtra"	,"$ptime (.*) hat mehr Wasser genommen, als erlaubt ist..."												,"mehr Wasser genommen, als erlaubt") 
			|| CityLog($line,"BrunnenAdd"	,"$ptime (.*) hat ([0-9]+) Rationen Wasser in den Brunnen gesch.+ttet"									,"Wasser in den Brunnen gesch") 
			|| CityLog($line,"Zurueck"		,"$ptime (.*) ist in die Stadt zur.+ckgekehrt."															,"ist in die Stadt zur") 
			|| CityLog($line,"Verlassen"	,"$ptime (.*) hat die Stadt verlassen..."																,"Stadt verlassen") 	
			|| CityLog($line,"TorAuf"		,"$ptime (.*) hat das Stadttor ge.+ffnet"																,"Stadttor ge") 
			|| CityLog($line,"TorZu"		,"$ptime (.*) hat das Stadttor geschlossen..."															,"Stadttor ges") 
			|| CityLog($line,"Werkstatt"	,"$ptime (.*) hat folgenden Gegenstand hergestellt: (.*) und daf.+r diese Materialien vebraucht: (.*)"	,"folgenden Gegenstand hergestellt") 
			|| CityLog($line,"Gestohlen"	,"$ptime (.*) wurde dabei ertappt, wie er bei (.*) folgendes gestohlen hat: (.*) !"						,"wurde dabei ertappt") 
			|| CityLog($line,"Gebaeude"		,"$ptime Es wurde ein neues Geb.+ude gebaut: (.*)."														,"Es wurde ein neues Geb") 
			|| CityLog($line,"Gebaeude2"	,"$ptime F.+r den Bau dieses Geb.+udes: (.*) wurden diese Materialien verbraucht: (.*)"					,"den Bau dieses Geb") 
			|| CityLog($line,"Gebaeude3"	,"Der Bau hat folgende Ressourcen verbraucht: (.*)"														,"Der Bau hat folgende Ressourcen verbraucht") 
			|| CityLog($line,"WasserPlus"	,"$ptime Das Geb.+ude: (.*) hat der Stadt \\+(.*) zus.+tzliche Wasserrationen gebracht!"				,"zliche Wasserrationen gebracht") 
			|| CityLog($line,"Hausbau"		,"$ptime (.*) hat seine Behausung in ein\\(e,n\\) (.*) verwandelt \\(Lvl. (.*)\\)..."					,"seine Behausung in ein") 
			|| CityLog($line,"HeldBauarbeit","$ptime (.*) hat etwas an seinem Haus rumgeschraubt..."												,"an seinem Haus rumgeschraubt") 
			|| CityLog($line,"HeldRettung"	,"$ptime Niemand wei.+ wie das m.+glich war, aber (.*) ist mit (.*) \\(der sich in (.*) aufhielt\\) heil zur.+ckgekehrt! Es lebe unser Held!","Es lebe unser Held!") 
			|| CityLog($line,"Tippex"		,"$ptime Dieser Registereintrag wurde durchgestrichen und ist nicht mehr lesbar! Wer wollte damit etwas verbergen?","Registereintrag wurde durchgestrichen") 
			|| CityLog($line,"NeuerBuerger"	,"$ptime Ein neuer B.+rger ist in der Stadt angekommen: (.*) \\((.*)\\)"								,"ist in der Stadt angekommen:") 
			|| CityLog($line,"Pluenderung"	,"$ptime (.*) hat bei der Pl.+nderung von . (.*)\\(s\\) Haus diesen Gegenstand gestohlen: (.*) !"		,"Haus diesen Gegenstand gestohlen:") 
			|| CityLog($line,"Zyanid"		,"$ptime (.*) hat uns verlassen: Selbstmord durch Zyanid"												,"Selbstmord durch Zyanid") 
			|| CityLog($line,"Ban"			,"$ptime Die Einwohner haben beschlossen (.*) aus der Stadt zu verbannen!"								,"aus der Stadt zu verbannen!") 
			|| CityLog($line,"EskortHome"	,"$ptime Unser tapferer B.+rger (.*) hat (.*) zur.+ck in die Stadt begleitet!"							,"in die Stadt begleitet!") 
			            //~ [00:00] Es ist etwas Gemüse in unserem Gemüsegarten gewachsen: 4x icon Verdächtiges Gemüse und 2x icon Darmmelone .
			|| CityLog($line,"Zombie1"		,"$ptime Nicht ein Zombie hat die Stadt betreten!"														,"hat die Stadt betreten!") 
			|| CityLog($line,"Zombie2"		,"$ptime Deine ehemaligen Mitb.+rger haben sich an den Verteidigungsanlagen der Stadt ihre faulenden Z.+hne ausgebissen und sind wieder hungrig abgezogen.","sind wieder hungrig abgezogen.") 
			|| CityLog($line,"Zombie3"		,"$ptime Wie aus dem Nichts stand die Meute auf einmal vor dem Stadttor...",							"stand die Meute auf einmal vor dem Stadttor") 
			|| CityLog($line,"Zombie4"		,"$ptime (.*) verstorbene B.+rger wurden in der letzten Zombiemeute gesichtet. Z.+hnefletschend und mit leeren Blick wankten sie durch die Gegend...","leeren Blick wankten sie durch") 
			|| CityLog($line,"Zombie5"		,"$ptime Resigniert und untr.+stlich sahen die B.+rger wie eine Horde von (.*) Zombies sich in Richtung Stadt bewegte...","sich in Richtung Stadt bewegte") 
			|| CityLog($line,"ZombieDelay"	,"$ptime Es sind nicht mehr gen.*gend Einwohner in der Stadt. Der Zombieangriff und die Wiederherstellung der AP wurden auf morgen Abend verschoben.","wurden auf morgen Abend verschoben")
			;
		//~ if (!$ok) echo "UNKNOWN LINE: $line<br>\n";
	}
}


function CityLog ($line,$cat,$pattern,$earlyout=false) {
	if ($earlyout && strpos($line,$earlyout) === false) return false;
	if (!eregi($pattern,$line,$r)) return false;
	switch ($cat) { 
		case "BankNehmen":		CityLog_ItemSum_Delta($cat,$r[2],$r[3],+1); break;
		case "BankGeben":		CityLog_ItemSum_Delta($cat,$r[2],$r[3],-1); break;
		case "Brunnen":			CityLog_ItemSum_Delta($cat,$r[2],"Ration Wasser",1); break;
		case "BrunnenExtra":	CityLog_ItemSum_Delta($cat,$r[2],"Ration Wasser",1); break;
	}
	// icon Ration Wasser
	// icon Verrotteter Baumstumpf
	return true;
}

function CityLog_FixItemName ($item) {
	$item = ereg_replace("^icon ","",$item);
	$item = eregi_replace("[^a-z0-9]","",$item);
	$item = strtolower($item);
	return $item;
}

function TCells ($arr) { return "<td>".implode("</td><td>",$arr)."</td>"; }

function CityLog_ItemSum_Delta ($cat,$player,$item,$delta=0) {
	global $gCityLogItemSum;
	$item = CityLog_FixItemName($item);
	//~ if ($delta == 0) echo "CityLog_ItemSum_Delta ($cat,$player,$item,$delta)<br>\n";
	if (!isset($gCityLogItemSum[$player])) $gCityLogItemSum[$player] = array();
	if (!isset($gCityLogItemSum[$player][$item])) $gCityLogItemSum[$player][$item] = 0;
	$cur = $gCityLogItemSum[$player][$item] + $delta;
	$cur = max(0,$cur); // erstes "geben" wird nicht angerechnet
	$gCityLogItemSum[$player][$item] = $cur;
	
	if (kSearchPlayer || kSearchItem) {
		echo "<tr>";
		if (kSearchPlayer && strpos($player,kSearchPlayer) !== false)	echo TCells(array($cat,$delta,$cur,$item,$player));
		if (kSearchItem	&& strpos($item,kSearchItem) !== false)			echo TCells(array($cat,$delta,$cur,$item,$player));
		echo "</tr>\n";
	}
	
	//~ echo "CityLog_ItemSum_Delta($player,$item,$delta) -> ".$cur."\n";
	return $cur; // evil, but can be used in get to avoid dublicate array-isset-init
}
function CityLog_ItemSum_Get ($player,$item) { return CityLog_ItemSum_Delta(false,$player,$item,0); }

function CityLog_ItemSort ($itemlist,$min=false) {
	if (!is_array($itemlist)) $itemlist = array($itemlist); // allow single name or array of names
	//~ echo "CityLog_ItemSort: ".implode(",",$itemlist)."<br>\n";
	global $gCityLogItemSum;
	$arr = array();
	foreach ($gCityLogItemSum as $player=>$data) { 
		$c = 0; foreach ($itemlist as $item) $c += CityLog_ItemSum_Get($player,$item);
		//~ echo "$player : $c<br>\n";
		if ($min === false || $c >= $min) $arr[$player] = $c;
	}
	arsort($arr); // descending
	return $arr;
}

$maxday = intval(sqlgetone("SELECT MAX(day_log) FROM citylog WHERE gameid = ".intval(kGameID)));
define("kMaxDay",$maxday);

define("kSearchItem",isset($_REQUEST["item"])?CityLog_FixItemName($_REQUEST["item"]):false);
define("kSearchPlayer",isset($_REQUEST["player"])?($_REQUEST["player"]):false);

if (kSearchPlayer || kSearchItem) echo "<table border=1 cellspacing=0 cellpadding=0>";
//~ ParseCityLog((file_get_contents(kCityLogSampleFile)));
for ($d=1;$d<=$maxday;++$d) {
	$txt = sqlgetone("SELECT logtxt FROM citylog WHERE gameid = ".intval(kGameID)." AND day_log = ".$d." ORDER BY id DESC LIMIT 1");
	if ($txt) ParseCityLog($txt);
}
if (kSearchPlayer || kSearchItem) echo "</table>";

if (kSearchItem) exit("kSearchItem : end.");
if (kSearchPlayer) exit("kSearchPlayer : end.");



$arr_tiere = array("Huhn","Übelriechendes Schwein","Riesige Ratte","Bissiger Hund","Großer knuddeliger Kater","Zwei-Meter Schlange");
$arr_essen = array(
	"Konservendose",
	"Offene Konservendose",
	"Undefinierbares Fleisch",
	"Verdächtiges Gemüse",
	"Doggybag",
	"Tüte mit labbrigen Chips",
	"Verschimmelte Waffeln",
	"Trockene Kaugummis",
	"Ranzige Butterkekse",
	"Angebissene Hähnchenflügel",
	"Abgelaufene Pim's Kekse",
	"Fades Gebäck",
	"Verschimmelte Stulle",
	"Chinesische Nudeln",
	"Verdächtige Speise",
	
	"Leckeres Steak",
	"Darmmelone",
	"Leckere Speise",
	);
$arr_alk = array("'Wake The Dead'","Wodka Marinostov");
$arr_drogen = array(
	"Anaboles Steroid",
	"Twinoid 500mg",
	"Etikettenloses Medikament",
	"Abgelaufene Betapropin-Tablette 5mg",
	);

//~ Starke GewÃ¼rze
//~ GewÃ¼rzte chinesische Nudeln
// Getrocknete Marshmallows
// Fackel

//~ Wasserspender (leer)
//~ Wasserspender (1 Ration)
//~ Wasserspender (2 Rationen)
//~ Wasserspender (3 Rationen)

function MyLinkPlayer ($name) { return href("?player=".urlencode($name),$name); }
function CityLog_HighScore ($itemlist,$title=false) {
	if (!is_array($itemlist)) $itemlist = array($itemlist);
	if (!$title) $title = $itemlist[0];
	$arr = CityLog_ItemSort($itemlist);
	$bNonEmpty = false;
	$html = "";
	$html .= "<b>".$title."</b><br>\n";
	foreach ($arr as $player => $num) if ($num > 0) { $bNonEmpty = true; $html .= "$num => ".MyLinkPlayer($player)."<br>\n"; }
	//~ if (!$bNonEmpty) return "";
	return $html;
}

echo "(nicht berücksichtigt werden :  privat-nachricht verschickte items, auf der aussenwelt durch fallenlassen/aufheben ausgetauschte items, für zementblöcke verbrauchtes wasser)<br>\n";

echo "<table><tr>";
echo "<td valign='top'>".CityLog_HighScore("Ration Wasser")."</td>";
echo "<td valign='top'>".CityLog_HighScore($arr_essen,"Essen")."</td>";
echo "<td valign='top'>".CityLog_HighScore($arr_drogen,"Drogen")."</td>";
echo "<td valign='top'>".CityLog_HighScore($arr_alk,"Alkohol")."</td>";
echo "<td valign='top'>".CityLog_HighScore($arr_tiere,"Tiere")."</td>";
echo "<td valign='top'>";
echo CityLog_HighScore("Pharmazeutische Substanz");
echo CityLog_HighScore("Extra Tasche");
echo CityLog_HighScore("Gürtel mit Tasche");
echo CityLog_HighScore("Schraubenzieher");
echo CityLog_HighScore("Knochen mit Fleisch");
echo CityLog_HighScore("Bandage");
echo CityLog_HighScore("Paracetoid 7g");
echo CityLog_HighScore("Kanister");
echo CityLog_HighScore("Menschenfleisch");
echo CityLog_HighScore("Nahrungsmittelkiste");
echo CityLog_HighScore("Reparaturset");
echo CityLog_HighScore("Metallsäge");
echo CityLog_HighScore("Micropur Brausetablette");
echo CityLog_HighScore("Einkaufswagen");
echo CityLog_HighScore("Reparatur Fix");
echo CityLog_HighScore("Dosenöffner");
echo CityLog_HighScore("Handvoll Schrauben und Muttern");
if (kMaxDay <= 2) echo CityLog_HighScore("Unförmige Zementblöcke");
if (kMaxDay <= 2) echo CityLog_HighScore("Zementsack");
echo "</td>";
//~ echo "<td valign='top>".CityLog_HighScore("Heißer Kaffee")."</td>";
echo "</tr></table>";



?>