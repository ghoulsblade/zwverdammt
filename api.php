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

require_once("defines.php");
require_once("roblib.php");


$xml = new SimpleXMLElement('<api/>');

if (isset($_REQUEST["gameid"])) {
	$limits = sqlgetobject("SELECT MIN(x) as minx,MAX(x) as maxx,MIN(y) as miny,MAX(y) as maxy FROM mapnote WHERE gameid = ".intval($_REQUEST["gameid"]));
	$mapnotes = $xml->addChild('mapnotes');
	//~ $mapnotes->addAttribute("limits",obj2sql($limits));
	if ($limits) {
		for ($y=intval($limits->miny);$y<=intval($limits->maxy);++$y) 
		for ($x=intval($limits->minx);$x<=intval($limits->maxx);++$x) {
			$o = sqlgetobject("SELECT * FROM mapnote WHERE gameid = ".intval($_REQUEST["gameid"])." AND x = $x AND y = $y ORDER BY id DESC LIMIT 1");
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
	
	//~ $mapnotes->addAttribute('type');
} else {
	$o_cities = sqlgettable("SELECT *,MAX(day) as maxday,MAX(time) maxt FROM xml GROUP BY gameid");
	$x_cities = $xml->addChild('cities');
	foreach ($o_cities as $o) if ($o->gameid != 0) {
		$node = $x_cities->addChild('city');
		$node->addAttribute("gameid",$o->gameid);
		$node->addAttribute("cityname",utf8_encode($o->cityname));
		$node->addAttribute("day",$o->maxday);
		$node->addAttribute("time",$o->maxt);
	}
	
	//~ exit("missing param, try gameid=123");
}

echo $xml->asXML();
?>