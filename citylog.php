<?php

// idee : summe items (sabo, pharma, drogen)
// idee taschen verbleib (besondere items)
// idee : wasser-log (brunnen+bank)		(zementblöcke werden nicht beachtet) "Unförmige Zementblöcke" "Zementsack"
// idee : rein+rausrennen
// idee : werkstatt herstellung 
// idee : angriffe/diebstähle
// brunnen icon : http://data.dieverdammten.de/gfx/icons/small_well.gif

//~ define("kCityLogSampleFile","samplelog_04.09.2010_tag3.txt");
define("kCityLogSampleFile","samplelog_04.09.2010.txt");

$gImportantItems = array("Ration Wasser","Paracetoid","bandage","Twinoid","Anaboles steroid"); // tasche.gürtel,einkwagen,säge
$gCityLogItemSum = array();

// utf8_decode

function ParseCityLog($txt) {
	$lines = explode("\n",strtr($txt,array("<br>"=>"")));
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
			
			|| CityLog($line,"Zombie1"		,"$ptime Nicht ein Zombie hat die Stadt betreten!"														,"hat die Stadt betreten!") 
			|| CityLog($line,"Zombie2"		,"$ptime Deine ehemaligen Mitb.+rger haben sich an den Verteidigungsanlagen der Stadt ihre faulenden Z.+hne ausgebissen und sind wieder hungrig abgezogen.","sind wieder hungrig abgezogen.") 
			|| CityLog($line,"Zombie3"		,"$ptime Wie aus dem Nichts stand die Meute auf einmal vor dem Stadttor...",							"stand die Meute auf einmal vor dem Stadttor") 
			|| CityLog($line,"Zombie4"		,"$ptime (.*) verstorbene B.+rger wurden in der letzten Zombiemeute gesichtet. Z.+hnefletschend und mit leeren Blick wankten sie durch die Gegend...","leeren Blick wankten sie durch") 
			|| CityLog($line,"Zombie5"		,"$ptime Resigniert und untr.+stlich sahen die B.+rger wie eine Horde von (.*) Zombies sich in Richtung Stadt bewegte...","sich in Richtung Stadt bewegte") 
			|| CityLog($line,"ZombieDelay"	,"$ptime Es sind nicht mehr gen.*gend Einwohner in der Stadt. Der Zombieangriff und die Wiederherstellung der AP wurden auf morgen Abend verschoben.","wurden auf morgen Abend verschoben")
			;
		if ($ok == 0) echo "UNKNOWN LINE: $line<br>\n";
	}
}


function CityLog ($line,$cat,$pattern,$earlyout=false) {
	if ($earlyout && strpos($line,$earlyout) === false) return false;
	if (!eregi($pattern,$line,$r)) return false;
	switch ($cat) { 
		case "BankNehmen":		CityLog_ItemSum_Delta($r[2],$r[3],+1); break;
		case "BankGeben":		CityLog_ItemSum_Delta($r[2],$r[3],-1); break;
		case "Brunnen":			CityLog_ItemSum_Delta($r[2],"Ration Wasser",1); break;
		case "BrunnenExtra":	CityLog_ItemSum_Delta($r[2],"Ration Wasser",1); break;
	}
	// icon Ration Wasser
	// icon Verrotteter Baumstumpf
	return true;
}

function CityLog_FixItemName ($item) { return ereg_replace("^icon ","",$item); }


function CityLog_ItemSum_Delta ($player,$item,$delta=0) {
	global $gCityLogItemSum;
	$item = CityLog_FixItemName($item);
	if (!isset($gCityLogItemSum[$player])) $gCityLogItemSum[$player] = array();
	if (!isset($gCityLogItemSum[$player][$item])) $gCityLogItemSum[$player][$item] = 0;
	$gCityLogItemSum[$player][$item] += $delta;
	//~ echo "CityLog_ItemSum_Delta($player,$item,$delta) -> ".$gCityLogItemSum[$player][$item]."\n";
	return $gCityLogItemSum[$player][$item]; // evil, but can be used in get to avoid dublicate array-isset-init
}
function CityLog_ItemSum_Get ($player,$item) { return CityLog_ItemSum_Delta($player,$item,0); }

function CityLog_ItemSort ($itemlist) {
	if (!is_array($itemlist)) $itemlist = array($itemlist); // allow single name or array of names
	//~ echo "CityLog_ItemSort: ".implode(",",$itemlist)."<br>\n";
	global $gCityLogItemSum;
	$arr = array();
	foreach ($gCityLogItemSum as $player=>$data) { 
		$c = 0; foreach ($itemlist as $item) $c += CityLog_ItemSum_Get($player,$item);
		//~ echo "$player : $c<br>\n";
		$arr[$player] = $c;
	}
	arsort($arr); // descending
	return $arr;
}

ParseCityLog((file_get_contents(kCityLogSampleFile)));

$arr = CityLog_ItemSort("Ration Wasser");
foreach ($arr as $player => $num) if ($num > 1 || $num <= 0) echo "$num => $player<br>\n";
echo "<hr>\n";
$arr = CityLog_ItemSort("Pharmazeutische Substanz");
foreach ($arr as $player => $num) if ($num > 1 || $num <= 0) echo "$num => $player<br>\n";

?>