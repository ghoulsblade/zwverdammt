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

