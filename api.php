<?php
// xml api
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

header("Content-Type: text/xml");

require_once("defines.php");
require_once("roblib.php");
require_once("lib.verdammt.php");

// text/xml


$xml = new SimpleXMLElement('<api xmlns:dc="http://purl.org/dc/elements/1.1" xmlns:content="http://purl.org/rss/1.0/modules/content/"/>',LIBXML_NOXMLDECL);
//~ $xml->addAttribute("xmlns:dc","http://purl.org/dc/elements/1.1");
//~ $xml->addAttribute("xmlns:content","http://purl.org/rss/1.0/modules/content/");
$xmldecl		= '<?xml version="1.0" encoding="UTF-8"?>';
$xmldecl_bad	= '<?xml version="1.0"?>';
//~ xmlns:dc="http://purl.org/dc/elements/1.1" xmlns:content="http://purl.org/rss/1.0/modules/content/"



function ExportMapNotes ($xml,$gameid) {
	$gameid = intval($gameid);
	$limits = sqlgetobject("SELECT MIN(x) as minx,MAX(x) as maxx,MIN(y) as miny,MAX(y) as maxy FROM mapnote WHERE gameid = $gameid");
	$mapnotes = $xml->addChild('mapnotes');
	$mapnotes->addAttribute("limits",obj2sql($limits));
	$mapnotes->addAttribute("gameid",$gameid);
	//~ $mapnotes->addAttribute("limits",obj2sql($limits));
	if ($limits) {
		for ($y=intval($limits->miny);$y<=intval($limits->maxy);++$y) 
		for ($x=intval($limits->minx);$x<=intval($limits->maxx);++$x) {
			$o = sqlgetobject("SELECT * FROM mapnote WHERE gameid = $gameid AND x = $x AND y = $y ORDER BY id DESC LIMIT 1");
			if ($o) {
				// id 	time 	day 	gameid 	x 	y 	icon 	txt 	seelenid 	zombies
				$node = $mapnotes->addChild('note',utf8_encode($o->txt));
				$node->addAttribute("time",$o->time);
				$node->addAttribute("day",$o->day);
				$node->addAttribute("x",$o->x);
				$node->addAttribute("y",$o->y);
				$node->addAttribute("icon",$o->icon);
				$node->addAttribute("zombies",utf8_encode($o->zombies));
			}
		}
	}
}


if (isset($_REQUEST["mode"])) {
	switch ($_REQUEST["mode"]) {
		case "cityxml":
			if (!isset($_REQUEST["gameid"])) exit("missing gameid=x in url");
			if (!isset($_REQUEST["day"])) exit("missing day=x in url");
			$o = sqlgetobject("SELECT * FROM xml WHERE ".arr2sql(array("gameid"=>$_REQUEST["gameid"],"day"=>$_REQUEST["day"])," AND ")." ORDER BY id DESC LIMIT 1");
			if (!$o) exit("error, not found");
			exit($o->xml);
		break;
		case "citylist":
			$o_cities = sqlgettable("SELECT gameid,cityname,MAX(day) as maxday,MAX(time) maxt FROM xml GROUP BY gameid");
			$x_cities = $xml->addChild('cities');
			foreach ($o_cities as $o_city) if ($o_city->gameid != 0) {
				$x_city = $x_cities->addChild('city');
				$x_city->addAttribute("gameid",$o_city->gameid);
				$x_city->addAttribute("cityname",utf8_encode($o_city->cityname));
				$x_city->addAttribute("day",$o_city->maxday);
				$x_city->addAttribute("time",$o_city->maxt);
				$saves = sqlgettable("SELECT id,day,MAX(time) maxt FROM xml WHERE gameid = ".intval($o_city->gameid)." GROUP BY day");
				foreach ($saves as $o_save) {
					$x_save = $x_city->addChild('save');
					$x_save->addAttribute("day",$o_save->day);
					$x_save->addAttribute("time",$o_save->maxt);
					$x_save->addAttribute("id",$o_save->id);
				}
			}
		break;
	}
} else if (isset($_REQUEST["gameid"])) {
	ExportMapNotes($xml,$_REQUEST["gameid"]);
} else if (isset($_REQUEST["seelenid"])) {
	LogAccess($_REQUEST["seelenid"],"api");
	ExportMapNotes($xml,GetGameIDForSeelenID($_REQUEST["seelenid"]));
} else {
	exit("use mode=citylist in url");
	// see case 
	//~ exit("missing param, try gameid=123");
}

echo $xml->asXML();
?>