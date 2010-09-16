-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 16. September 2010 um 20:08
-- Server Version: 5.0.51
-- PHP-Version: 5.2.6-1+lenny8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `g_verdammt`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `accesslog`
--

CREATE TABLE IF NOT EXISTS `accesslog` (
  `id` int(11) NOT NULL auto_increment,
  `seelenid` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `ip` varchar(32) NOT NULL,
  `browser` text NOT NULL,
  `context` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7282 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buildingtype`
--

CREATE TABLE IF NOT EXISTS `buildingtype` (
  `id` int(11) NOT NULL auto_increment,
  `buildingid` int(11) NOT NULL,
  `name` varchar(128) character set utf8 NOT NULL,
  `img` text character set utf8 NOT NULL,
  `notfall` int(11) NOT NULL,
  `wiki_html` text character set utf8 NOT NULL,
  `wiki_src` text character set utf8 NOT NULL,
  `def` int(11) NOT NULL,
  `plv` float NOT NULL,
  `ap0` int(11) NOT NULL,
  `ap1` int(11) NOT NULL,
  `ap2` int(11) NOT NULL,
  `ap3` int(11) NOT NULL,
  `ap4` int(11) NOT NULL,
  `ap5` int(11) NOT NULL,
  `mats` text character set utf8 NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `buildingid` (`buildingid`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1310 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `citylog`
--

CREATE TABLE IF NOT EXISTS `citylog` (
  `id` int(11) NOT NULL auto_increment,
  `gameid` int(11) NOT NULL,
  `day_log` int(11) NOT NULL,
  `day_entry` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `seelenid` varchar(255) character set utf8 NOT NULL,
  `logtxt` text character set utf8 NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `day_log` (`day_log`),
  KEY `time` (`time`),
  KEY `gameid` (`gameid`),
  KEY `day_entry` (`day_entry`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `itemtype`
--

CREATE TABLE IF NOT EXISTS `itemtype` (
  `id` int(11) NOT NULL,
  `name` text character set utf8 NOT NULL,
  `cat` varchar(64) character set utf8 NOT NULL,
  `img` text character set utf8 NOT NULL,
  `cat2` varchar(64) character set utf8 NOT NULL,
  `wiki_html` text character set utf8 NOT NULL,
  `wiki_src` text character set utf8 NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mapitem`
--

CREATE TABLE IF NOT EXISTS `mapitem` (
  `id` int(11) NOT NULL auto_increment,
  `gameid` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `itemtype` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `broken` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `gameid_2` (`gameid`,`x`,`y`),
  KEY `gameid_3` (`gameid`,`itemtype`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=242 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mapnote`
--

CREATE TABLE IF NOT EXISTS `mapnote` (
  `id` int(11) NOT NULL auto_increment,
  `time` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `gameid` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `icon` int(11) NOT NULL,
  `txt` text character set utf8 NOT NULL,
  `seelenid` varchar(255) character set utf8 NOT NULL,
  `zombies` varchar(4) character set utf8 NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `gameid` (`gameid`,`x`,`y`),
  KEY `gameid_2` (`gameid`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1081 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mapzone`
--

CREATE TABLE IF NOT EXISTS `mapzone` (
  `gameid` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `seelenid` varchar(128) NOT NULL,
  `dried` int(11) NOT NULL,
  `h` int(11) NOT NULL,
  `z` int(11) NOT NULL,
  UNIQUE KEY `gameid` (`gameid`,`x`,`y`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ruin`
--

CREATE TABLE IF NOT EXISTS `ruin` (
  `gameid` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `ap` int(11) NOT NULL,
  `name` varchar(255) character set utf8 NOT NULL,
  UNIQUE KEY `gameid` (`gameid`,`x`,`y`),
  KEY `type` (`type`),
  KEY `ap` (`ap`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `stream_debug`
--

CREATE TABLE IF NOT EXISTS `stream_debug` (
  `id` int(11) NOT NULL auto_increment,
  `seelenid` varchar(255) character set utf8 NOT NULL,
  `xml` mediumtext character set utf8 NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1861 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `stream_ghost_debug`
--

CREATE TABLE IF NOT EXISTS `stream_ghost_debug` (
  `id` int(11) NOT NULL auto_increment,
  `seelenid` varchar(255) character set utf8 NOT NULL,
  `ghostkey` varchar(255) character set utf8 NOT NULL,
  `xml` mediumtext character set utf8 NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `xml`
--

CREATE TABLE IF NOT EXISTS `xml` (
  `id` int(11) NOT NULL auto_increment,
  `seelenid` varchar(255) character set utf8 NOT NULL,
  `time` int(11) NOT NULL,
  `gameid` int(11) NOT NULL,
  `cityname` varchar(255) character set utf8 NOT NULL,
  `day` int(11) NOT NULL,
  `xml` mediumtext character set utf8 NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `seelenid` (`seelenid`),
  KEY `time` (`time`),
  KEY `gameid` (`gameid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3666 ;
