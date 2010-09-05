-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 05. September 2010 um 16:54
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2269 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buildingtype`
--

CREATE TABLE IF NOT EXISTS `buildingtype` (
  `id` int(11) NOT NULL auto_increment,
  `buildingid` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `img` text NOT NULL,
  `notfall` int(11) NOT NULL,
  `wiki_html` text NOT NULL,
  `wiki_src` text NOT NULL,
  `def` int(11) NOT NULL,
  `plv` float NOT NULL,
  `ap0` int(11) NOT NULL,
  `ap1` int(11) NOT NULL,
  `ap2` int(11) NOT NULL,
  `ap3` int(11) NOT NULL,
  `ap4` int(11) NOT NULL,
  `ap5` int(11) NOT NULL,
  `mats` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `buildingid` (`buildingid`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1308 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `itemtype`
--

CREATE TABLE IF NOT EXISTS `itemtype` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `cat` varchar(64) NOT NULL,
  `img` text NOT NULL,
  `cat2` varchar(64) NOT NULL,
  `wiki_html` text NOT NULL,
  `wiki_src` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `txt` text NOT NULL,
  `seelenid` varchar(255) NOT NULL,
  `zombies` varchar(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `gameid` (`gameid`,`x`,`y`),
  KEY `gameid_2` (`gameid`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=570 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `stream_debug`
--

CREATE TABLE IF NOT EXISTS `stream_debug` (
  `id` int(11) NOT NULL auto_increment,
  `seelenid` varchar(255) NOT NULL,
  `xml` mediumtext NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=587 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `xml`
--

CREATE TABLE IF NOT EXISTS `xml` (
  `id` int(11) NOT NULL auto_increment,
  `seelenid` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `gameid` int(11) NOT NULL,
  `cityname` varchar(255) NOT NULL,
  `day` int(11) NOT NULL,
  `xml` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `seelenid` (`seelenid`),
  KEY `time` (`time`),
  KEY `gameid` (`gameid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1347 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `xmldump`
--

CREATE TABLE IF NOT EXISTS `xmldump` (
  `id` int(11) NOT NULL auto_increment,
  `time` int(11) NOT NULL,
  `gameid` int(11) NOT NULL,
  `cityname` varchar(255) NOT NULL,
  `day` int(11) NOT NULL,
  `xml` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `time` (`time`),
  KEY `gameid` (`gameid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=810 ;
