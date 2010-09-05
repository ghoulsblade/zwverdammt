-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 05. September 2010 um 16:53
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

--
-- Daten für Tabelle `buildingtype`
--

INSERT INTO `buildingtype` (`id`, `buildingid`, `name`, `img`, `notfall`, `wiki_html`, `wiki_src`, `def`, `plv`, `ap0`, `ap1`, `ap2`, `ap3`, `ap4`, `ap5`, `mats`) VALUES
(3, -1, 'Werkstatt', '', 0, '', '', 0, 0, 25, 0, 0, 0, 0, 0, 'Krummes Holzbrett:10,Alteisen:8,Unförmige Zementblöcke:1'),
(4, -1, 'Baustellenbuch', '', 0, '', '', 0, 0, 15, 14, 13, 12, 11, 10, 'Järpen-Tisch:1'),
(5, -1, 'Metzgerei', '', 0, '', '', 0, 0, 40, 38, 35, 32, 0, 0, 'Krummes Holzbrett:9,Alteisen:4'),
(6, -1, 'Kremato-Cue', '', 0, '', '', 0, 0, 45, 42, 39, 36, 33, 30, 'Zusammengeschusterter Holzbalken:8,Metallstruktur:1'),
(7, -1, 'Verteidigungsanlage', '', 0, '', '', 0, 0, 50, 47, 43, 40, 36, 33, 'Zusammengeschusterter Holzbalken:7,Metallstruktur:8,Handvoll Schrauben und Muttern:6'),
(8, -1, 'Manufaktur', '', 0, '', '', 0, 0, 40, 38, 35, 32, 29, 0, 'Zusammengeschusterter Holzbalken:5,Metallstruktur:5,Handvoll Schrauben und Muttern:3'),
(9, -1, 'Kreischende Sägen', '', 0, '', '', 40, 0, 65, 61, 56, 52, 47, 43, 'Handvoll Schrauben und Muttern:3,Metallstruktur:2,Alteisen:5,Klebeband:3'),
(10, -1, 'Galgen', '', 0, '', '', 0, 0, 25, 24, 22, 20, 18, 17, 'Krummes Holzbrett:5,Alteisen:3,Große rostige Kette:1'),
(11, -1, 'Kanonenhügel', '', 0, '', '', 10, 1, 50, 47, 43, 40, 36, 33, 'Unförmige Zementblöcke:1,Metallstruktur:1,Zusammengeschusterter Holzbalken:7'),
(12, -1, 'Blechplattenwerfer', '', 0, '', '', 50, 1, 0, 33, 31, 0, 26, 23, 'Blechplatte:3,Sprengstoff:3,Holzbalken:5,Metallstruktur:1,Handvoll Schrauben und Muttern:5'),
(13, -1, 'Selbstgebaute Railgun', '', 0, '', '', 45, 0, 0, 28, 26, 24, 22, 20, 'Metallstruktur:10,Kupferrohr:1,Elektronisches Bauteil:1,Handvoll Schrauben und Muttern:4'),
(14, -1, 'Steinkanone', '', 0, '', '', 60, 0, 0, 42, 39, 36, 33, 30, 'Holzbalken:5,Metallstruktur:5,Kupferrohr:2,Elektronisches Bauteil:1,Unförmige Zementblöcke:3'),
(15, -1, 'Holzbalkendrehkreuz', '', 0, '', '', 12, 0, 0, 14, 13, 0, 0, 10, 'Holzbalken:2,Metallstruktur:1'),
(16, -1, 'Portal', '', 0, '', '', 2, 2, 16, 15, 0, 0, 0, 0, 'Alteisen:2'),
(17, -1, 'Kolbenschließmechanismus', '', 0, '', '', 15, 2, 25, 24, 22, 20, 18, 17, 'Krummes Holzbrett:10,Metallstruktur:3,Handvoll Schrauben und Muttern:4,Kupferrohr:1'),
(18, -1, 'Torpanzerung', '', 0, '', '', 12, 0, 35, 33, 31, 28, 26, 0, 'Krummes Holzbrett:3'),
(19, -1, 'Fundament', '', 0, '', '', 0, 0, 30, 28, 26, 24, 0, 0, 'Krummes Holzbrett:8,Alteisen:5,Unförmige Zementblöcke:2'),
(20, -1, 'Falsche Stadt', '', 0, '', '', 210, 0, 600, 558, 516, 474, 432, 390, 'Krummes Holzbrett:20,Zusammengeschusterter Holzbalken:20,Alteisen:25,Handvoll Schrauben und Muttern:10'),
(21, -1, 'Grosses Feuerwerk}} {{Item|Temporäre Konstruktion|kurz=1', '', 0, '', '', 500, 0, 100, 93, 86, 79, 72, 65, 'Kupferrohr:1,Super-Fuzz Pulver:1,Abschussrohre Flush:1,Feuerwerkskörperkiste:2,Alteisen:5,Raketenpulver:5'),
(22, -1, 'Bohrturm', '', 0, '', '', 0, 0, 170, 159, 147, 135, 123, 111, 'Zusammengeschusterter Holzbalken:15,Metallstruktur:15,Kupferrohr:4,Unförmige Zementblöcke:3'),
(23, -1, 'Großer Umbau', '', 0, '', '', 180, 0, 800, 744, 688, 632, 576, 520, 'Zusammengeschusterter Holzbalken:15,Metallstruktur:7,Unförmige Zementblöcke:5'),
(24, -1, 'Armatur', '', 0, '', '', 0, 0, 130, 121, 112, 103, 94, 85, 'Zusammengeschusterter Holzbalken:6,Alteisen:10,Metallstruktur:3,Motor:1,Handvoll Schrauben und Muttern:4'),
(25, -1, 'Pumpe', '', 0, '', '', 0, 0, 25, 24, 22, 20, 0, 0, 'Alteisen:8,Kupferrohr:1'),
(26, -1, 'Wassergeschüztürme', '', 0, '', '', 60, 6, 60, 56, 52, 48, 44, 39, 'Metallstruktur:10,Ration Wasser:60,Kupferrohr:1'),
(27, -1, 'Wasserreiniger', '', 0, '', '', 0, 0, 50, 47, 43, 0, 0, 0, 'Krummes Holzbrett:5,Alteisen:6,Kupferrohr:1'),
(28, -1, 'Gemüsebeet', '', 0, '', '', 0, 0, 60, 56, 52, 48, 44, 39, 'Holzbalken:10,Ration Wasser:10,Pharmazeutische Substanz:1'),
(29, -1, 'Wasserminenfeld}} {{Item|Temporäre Konstruktion|kurz=1', '', 0, '', '', 75, 0, 25, 24, 22, 20, 18, 17, 'Ration Wasser:20,Sprengstoff:1,Alteisen:3,Zünder:1'),
(30, -1, 'Wasserleitungsnetz', '', 0, '', '', 0, 0, 40, 38, 35, 32, 29, 26, 'Alteisen:5,Metallstruktur:5,Kupferrohr:2,Handvoll Schrauben und Muttern:5'),
(31, -1, 'Sprinkleranlage', '', 0, '', '', 95, 1, 50, 0, 43, 40, 0, 0, ''),
(32, -1, 'Fäkalienhebeanlage', '', 0, '', '', 45, 0, 55, 0, 48, 44, 0, 0, ''),
(33, -1, 'Kärcher', '', 0, '', '', 50, 1, 50, 0, 43, 40, 0, 33, ''),
(34, -1, 'Brunnenbohrer', '', 0, '', '', 0, 0, 60, 56, 52, 48, 0, 0, 'Zusammengeschusterter Holzbalken:7,Metallstruktur:2'),
(35, -1, 'Projekt Eden', '', 0, '', '', 0, 0, 65, 61, 56, 52, 48, 43, 'Zusammengeschusterter Holzbalken:5,Metallstruktur:8,Sprengstoff:5'),
(36, -1, 'Wachturm', '', 0, '', '', 3, 2, 12, 12, 0, 0, 0, 0, 'Krummes Holzbrett:2,Alteisen:2'),
(37, -1, 'Verbesserte Karte', '', 0, '', '', 0, 0, 15, 14, 13, 12, 11, 10, 'Elektronisches Bauteil:1,Alteisen:1,Kassettenradio:2,Batterie:1'),
(38, -1, 'Primitives Katapult', '', 0, '', '', 0, 0, 40, 38, 35, 32, 29, 26, 'Krummes Holzbrett:2,Zusammengeschusterter Holzbalken:1,Alteisen:1,Metallstruktur:1'),
(39, -1, 'Kalibriertes Katapult', '', 0, '', '', 0, 0, 0, 0, 26, 24, 0, 20, 'Krummes Holzbrett:2,Alteisen:2,Elektronisches Bauteil:2,Riemen:1'),
(40, -1, 'Forschungsturm', '', 0, '', '', 0, 0, 30, 28, 26, 24, 22, 0, 'Elektronisches Bauteil:1,Zusammengeschusterter Holzbalken:3,Metallstruktur:1'),
(41, -1, 'Scanner', '', 0, '', '', 0, 0, 20, 19, 18, 16, 15, 13, 'Handvoll Schrauben und Muttern:1,Elektronisches Bauteil:1,Kassettenradio:2,Batterie:2'),
(42, -1, 'Rechenmaschine', '', 0, '', '', 0, 0, 0, 0, 18, 16, 0, 0, 'Elektronisches Bauteil:1,Klebeband:2'),
(43, -1, 'Notfallkonstruktion', '', 0, '', '', 5, 3, 40, 38, 35, 32, 29, 26, 'Krummes Holzbrett:5,Alteisen:7'),
(44, -1, 'Sprengung}} {{Item|Temporäre Konstruktion|kurz=1', '', 0, '', '', 35, 0, 20, 0, 18, 16, 15, 13, 'Sprengstoff:3'),
(45, -1, 'Abfallberg}} {{Item|Temporäre Konstruktion|kurz=1', '', 0, '', '', 10, 0, 10, 0, 9, 8, 8, 7, 'Alteisen:2,Krummes Holzbrett:2'),
(46, -1, 'Trümmerberg}} {{Item|Temporäre Konstruktion|kurz=1', '', 0, '', '', 50, 0, 0, 38, 35, 0, 0, 0, 'Alteisen:2'),
(47, -1, 'Guerilla}} {{Item|Temporäre Konstruktion|kurz=1', '', 0, '', '', 45, 0, 24, 23, 21, 19, 18, 16, 'Alteisen:1,Krummes Holzbrett:2,Handvoll Schrauben und Muttern:2'),
(48, -1, 'Wolfsfalle}} {{Item|Temporäre Konstruktion|kurz=1', '', 0, '', '', 45, 0, 20, 19, 18, 16, 15, 13, 'Menschenfleisch:3,Alteisen:2'),
(49, -1, 'Notfallabstützung}} {{Item|Temporäre Konstruktion|kurz=1', '', 0, '', '', 40, 0, 30, 28, 26, 24, 22, 20, 'Krummes Holzbrett:8'),
(50, -1, 'Verteidigungspfähle}} {{Item|Temporäre Konstruktion|kurz=1', '', 0, '', '', 25, 0, 12, 12, 11, 10, 9, 8, 'Krummes Holzbrett:6'),
(51, -1, 'Verstärkte Stadtmauer', '', 0, '', '', 7, 2, 30, 28, 0, 0, 0, 0, 'Krummes Holzbrett:6,Alteisen:4'),
(52, -1, 'Stacheldraht', '', 0, '', '', 9, 0, 20, 19, 18, 0, 0, 0, 'Alteisen:2'),
(53, -1, 'Köder}} {{Item|Temporäre Konstruktion|kurz=1', '', 0, '', '', 10, 0, 10, 9, 9, 8, 8, 7, 'Knochen mit Fleisch:1'),
(54, -1, 'Zaun', '', 0, '', '', 25, 1, 50, 47, 43, 40, 36, 33, 'Zusammengeschusterter Holzbalken:5,Handvoll Schrauben und Muttern:5'),
(55, -1, 'Grosser Graben', '', 0, '', '', 20, 0, 80, 75, 69, 64, 0, 0, 'Krummes Holzbrett:8'),
(56, -1, 'Pfahlgraben', '', 0, '', '', 50, 0, 60, 56, 52, 48, 44, 0, 'Krummes Holzbrett:20'),
(57, -1, 'Wassergraben', '', 0, '', '', 45, 2, 50, 47, 43, 40, 36, 33, 'Ration Wasser:20'),
(58, -1, 'Zombiereibe', '', 0, '', '', 75, 1, 60, 56, 52, 48, 44, 39, 'Alteisen:20,Blechplatte:3,Handvoll Schrauben und Muttern:5'),
(59, -1, 'Fallgruben', '', 0, '', '', 30, 0, 65, 61, 56, 52, 47, 0, 'Krummes Holzbrett:15'),
(60, -1, 'Rasierklingenmauer', '', 0, '', '', 40, 1, 40, 38, 35, 32, 29, 26, 'Alteisen:15,Handvoll Schrauben und Muttern:5'),
(61, -1, 'Weiterentwickelte Stadtmauer', '', 0, '', '', 5, 9, 40, 38, 35, 32, 0, 0, 'Zusammengeschusterter Holzbalken:9,Metallstruktur:6,Handvoll Schrauben und Muttern:6'),
(62, -1, 'Groooße Mauer', '', 0, '', '', 70, 0, 50, 47, 43, 40, 36, 33, 'Zusammengeschusterter Holzbalken:15,Metallstruktur:10,unförmige Zementblöcke:2,Krummes Holzbrett:10'),
(63, -1, 'Verstärkende Balken', '', 0, '', '', 25, 0, 55, 52, 48, 44, 40, 36, 'Zusammengeschusterter Holzbalken:1,Metallstruktur:3,Handvoll Schrauben und Muttern:2'),
(64, -1, 'Zweite Schicht', '', 0, '', '', 70, 0, 0, 61, 56, 52, 47, 43, 'krummes Holzbrett:35,Metallstruktur:5'),
(65, -1, 'Entwicklungsfähige Stadtmauer', '', 0, '', '', 0, 0, 65, 61, 56, 52, 47, 0, 'krummes Holzbrett:5,Alteisen:20,unförmige Zementblöcke:1'),
(66, -1, 'Zackenmauer', '', 0, '', '', 50, 0, 35, 33, 31, 28, 26, 23, 'Zusammengeschusterter Holzbalken:2,Alteisen:15,Handvoll Schrauben und Muttern:4'),
(1296, 1066, 'KÃ¶der', 'item_plate', 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1249, 1012, 'NotfallabstÃ¼tzung', 'item_wood_plate', 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1251, 1014, 'Guerilla', 'item_wood_plate', 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1252, 1015, 'Primitives Katapult', 'item_courroie', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1258, 1024, 'Rasierklingenmauer', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1267, 1035, 'Scanner', 'item_tagger', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1271, 1040, 'FÃ¤kalienhebeanlage', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1273, 1042, 'Verbesserte Karte', 'item_electro', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1275, 1044, 'Wolfsfalle', 'item_hmeat', 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1278, 1047, 'Selbstgebaute Railgun', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1279, 1048, 'Blechplattenwerfer', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1288, 1057, 'Falsche Stadt', 'small_home', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1289, 1058, 'Zaun', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1292, 1061, 'Armatur', 'item_tube', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1294, 1064, 'Rechenmaschine', 'item_tagger', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1298, 1068, 'Sprengung', 'item_plate', 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1303, 1073, 'WassergeschÃ¼tztÃ¼rme', 'item_tube', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1306, 1077, 'Kalibriertes Katapult', 'item_courroie', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1250, 1013, 'VerteidigungspfÃ¤hle', 'item_wood_plate', 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1253, 1019, 'Abfallberg', 'small_dig', 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1255, 1021, 'Metzgerei', 'item_meat', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1256, 1022, 'TrÃ¼mmerberg', 'small_dig', 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1257, 1023, 'GroÃŸer Graben', 'small_gather', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1259, 1025, 'Verteidigungsanlage', 'item_meca_parts', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1261, 1027, 'Pfahlgraben', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1262, 1028, 'Stacheldraht', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1263, 1029, 'Brunnenbohrer', 'small_water', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1264, 1030, 'Projekt Eden', 'small_water', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1265, 1031, 'Weiterentwickelte Stadtmauer', 'item_meca_parts', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1268, 1036, 'VerstÃ¤rkende Balken', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1269, 1037, 'Zackenmauer', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1270, 1039, 'KÃ¤rcher', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1272, 1041, 'Wassergraben', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1274, 1043, 'Steinkanone', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1276, 1045, 'Kremato-Cue', 'item_hmeat', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1277, 1046, 'KanonenhÃ¼gel', 'small_dig', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1280, 1049, 'Holzbalkendrehkreuz', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1282, 1051, 'Fundament', 'small_building', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1283, 1052, 'GroÃŸer Umbau', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1284, 1053, 'Bohrturm', 'small_water', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1285, 1054, 'Zombiereibe', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1287, 1056, 'GroooÃŸe Mauer', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1290, 1059, 'Sprinkleranlage', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1291, 1060, 'Wasserleitungsnetz', 'item_tube', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1297, 1067, 'Kreischende SÃ¤gen', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1299, 1069, 'Torpanzerung', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1300, 1070, 'Zweite Schicht', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1301, 1071, 'EntwicklungsfÃ¤hige Stadtmauer', 'item_home_def', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1304, 1074, 'Notfallkonstruktion', 'status_terror', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1305, 1075, 'Baustellenbuch', 'item_rp_book2', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1307, 1079, 'Galgen', 'r_dhang', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1247, 1010, 'VerstÃ¤rkte Stadtmauer', 'item_plate', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1248, 1011, 'Pumpe', 'small_water', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1254, 1020, 'Wasserreiniger', 'item_jerrycan', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1260, 1026, 'GemÃ¼sebeet', 'item_vegetable_tasty', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1266, 1033, 'Werkstatt', 'small_refine', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1281, 1050, 'Wachturm', 'item_tagger', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1286, 1055, 'Fallgruben', 'small_gather', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1293, 1062, 'Portal', 'small_door_closed', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1295, 1065, 'Manufaktur', 'small_refine', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1302, 1072, 'Forschungsturm', 'small_gather', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, '');
