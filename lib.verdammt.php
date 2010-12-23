<?php
function LogAccess ($seelenid,$context="main") {
	$o = false;
	$o->seelenid = $seelenid;
	$o->ip = $_SERVER["REMOTE_ADDR"];
	$o->browser = $_SERVER["HTTP_USER_AGENT"];
	$o->time = time();
	$o->context = $context;
	sql("INSERT INTO accesslog SET ".obj2sql($o));
}

function GetGameIDForSeelenID ($seelenid) { return sqlgetone("SELECT gameid FROM xml WHERE ".arr2sql(array("seelenid"=>$seelenid))." ORDER BY id DESC LIMIT 1"); }

function href ($url,$title=false) { return "<a href='$url'>".($title?$title:$url)."</a>"; }
function img ($url,$title=false,$special="") { $title = $title?strtr((htmlentities(utf8_decode($title))),array("'"=>'"')):false; return "<img $special src='$url' ".($title?("alt='$title' title='$title'"):"")."/>"; }

// set to ue for sueden to avoid ajax/tooltip trouble after messing around for half an hour
$gHimmelsRichtungTxtByCode = array("Norden","Nordosten","Osten","Suedosten","Sueden","Suedwesten","Westen","Nordwesten");

function GetHimmelsRichtung ($rx,$ry) {
	if ($rx == 0 && $ry == 0) return 99; // city
	if ($rx > floor($ry/2) && $ry > floor($rx/2)) return 1; // nordost
	if ($rx > floor(-$ry/2) && -$ry > floor($rx/2)) return 3; // suedost
	if (-$rx > floor($ry/2) && $ry > floor(-$rx/2)) return 5; // nordwest
	if (-$rx > floor(-$ry/2) && -$ry > floor(-$rx/2)) return 7; // suedwest
	if (abs($rx) > abs($ry)) return ($rx > 0) ? 2 : 6; // ost / west
	return ($ry > 0) ? 0 : 4; // nord / sued
}
function GetHimmelsRichtungTxt ($rx,$ry) { 
	global $gHimmelsRichtungTxtByCode; 
	return ($rx == 0 && $ry == 0) ? "(Stadt)" : ($gHimmelsRichtungTxtByCode[GetHimmelsRichtung($rx,$ry)]);
}
