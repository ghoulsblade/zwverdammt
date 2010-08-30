-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 30. August 2010 um 18:28
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
-- Tabellenstruktur für Tabelle `itemtype`
--

CREATE TABLE IF NOT EXISTS `itemtype` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `cat` varchar(64) NOT NULL,
  `img` text NOT NULL,
  `cat2` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `itemtype`
--

INSERT INTO `itemtype` (`id`, `name`, `cat`, `img`, `cat2`) VALUES
(110, 'Reparaturset', 'Misc', 'repair_kit', 'Misc'),
(81, 'Klebeband', 'Rsc', 'rustine', 'Rsc'),
(137, 'UnkrautbekÃ¤mpfungsmittel Ness-Quick', 'Misc', 'digger', 'Misc'),
(107, 'Alte TÃ¼r', 'Armor', 'door', 'Armor'),
(134, 'UnfÃ¶rmige ZementblÃ¶cke', 'Armor', 'concrete_wall', 'Armor'),
(162, 'Verrotteter Baumstumpf', 'Rsc', 'wood_bad', 'Rsc'),
(161, 'MetalltrÃ¼mmer', 'Rsc', 'metal_bad', 'Rsc'),
(76, 'PlastiktÃ¼te', 'Weapon', 'grenade_empty', 'Weapon'),
(104, 'Kassettenradio (ohne Strom)', 'Misc', 'radio_off', 'Misc'),
(132, 'ZÃ¼nder', 'Rsc', 'deto', 'Rsc'),
(160, 'Metallstruktur', 'Rsc', 'metal_beam', 'Rsc'),
(188, 'Minibar', 'Furniture', 'machine_3', 'Furniture'),
(159, 'Zusammengeschusterter Holzbalken', 'Rsc', 'wood_beam', 'Rsc'),
(187, 'Krebserregender Ofen', 'Furniture', 'machine_2', 'Furniture'),
(158, 'Angeknackster menschlicher Knochen', 'Weapon', 'bone', 'Weapon'),
(186, 'Alte Waschmaschine', 'Furniture', 'machine_1', 'Furniture'),
(73, 'Sprengstoff', 'Rsc', 'explo', 'Rsc'),
(101, 'Elektronisches Bauteil', 'Rsc', 'electro', 'Rsc'),
(15, 'GroÃŸer trockener Stock', 'Weapon', 'staff', 'Weapon'),
(210, 'Angefangene Zigarettenschachtel', 'Misc', 'cigs', 'Misc'),
(41, 'Handvoll Schrauben und Muttern', 'Rsc', 'meca_parts', 'Rsc'),
(124, 'UnvollstÃ¤ndiger ZerstÃ¶rer', 'Misc', 'big_pgun_part', 'Misc'),
(179, 'Solide Holzplatte', 'Armor', 'wood_plate', 'Armor'),
(66, 'Bandage', 'Drug', 'bandage', 'Drug'),
(234, 'Verbrauchte Fackel', 'Weapon', 'torch_off', 'Weapon'),
(149, 'Starke GewÃ¼rze', 'Misc', 'spices', 'Misc'),
(205, 'Wasserpistole (3 Ladungen)', 'Weapon', 'watergun_3', 'Weapon'),
(64, 'Blechplatte', 'Armor', 'plate', 'Armor'),
(173, 'Raketenpulver', 'Misc', 'powder', 'Misc'),
(32, 'Ausgeschaltete Nachttischlampe', 'Furniture', 'lamp', 'Furniture'),
(60, 'Alteisen', 'Rsc', 'metal', 'Rsc'),
(31, 'Matratze', 'Armor', 'bed', 'Armor'),
(59, 'Krummes Holzbrett', 'Rsc', 'wood2', 'Rsc'),
(171, 'Micropur Brausetablette', 'Drug', 'water_cleaner', 'Drug'),
(2, 'Batterie', 'Rsc', 'pile', 'Rsc'),
(170, 'JÃ¤rpen-Tisch', 'Armor', 'table', 'Armor'),
(85, 'Wackliger Einkaufswagen', 'Misc', 'cart_part', 'Misc'),
(169, 'Holzbock', 'Armor', 'trestle', 'Armor'),
(84, 'Kupferrohr', 'Rsc', 'tube', 'Rsc'),
(111, 'Wasserpistole (leer)', 'Weapon', 'watergun_empty', 'Weapon'),
(52, 'Leckeres Steak', 'Food', 'meat', 'Food'),
(3, 'Konservendose', 'Food', 'can', 'Food'),
(219, 'BeschÃ¤digte AutotÃ¼r', 'Misc', 'car_door_part', 'Misc'),
(29, 'Schaukelstuhl', 'Furniture', 'chair', 'Furniture'),
(157, 'Knochen mit Fleisch', 'Food', 'bone_meat', 'Food'),
(185, 'UnvollstÃ¤ndiger Motor', 'Misc', 'engine_part', 'Misc'),
(95, 'Pharmazeutische Substanz', 'Drug', 'pharma', 'Drug'),
(14, 'Schraubenzieher', 'Weapon', 'screw', 'Weapon'),
(17, 'Machete', 'Weapon', 'cutcut', 'Weapon'),
(109, 'Reparturset (kaputt)', 'Misc', 'repair_kit_part', 'Misc'),
(39, 'Motor', 'Misc', 'engine', 'Misc'),
(196, 'GroÃŸe rostige Kette', 'Weapon', 'chain', 'Weapon'),
(221, 'Ã„tzmittel', 'Misc', 'poison_part', 'Misc'),
(108, 'VerdÃ¤chtiges GemÃ¼se', 'Food', 'vegetable', 'Food'),
(51, 'Anaboles Steroid', 'Drug', 'drug', 'Drug'),
(163, 'MetallsÃ¤ge', 'Misc', 'saw_tool', 'Misc'),
(106, 'Zyanid', 'Drug', 'cyanure', 'Drug'),
(218, 'AutotÃ¼r', 'Armor', 'car_door', 'Armor'),
(48, 'Vibrator (geladen)', 'Misc', 'vibr', 'Misc'),
(103, 'Hydraton 100mg', 'Drug', 'drug_water', 'Drug'),
(214, 'Zerquetschte Batterie', 'Misc', 'pile_broken', 'Misc'),
(100, 'UnvollstÃ¤ndiger Kaffeekocher', 'Misc', 'coffee_machine_part', 'Misc'),
(211, 'Druckregler PDTT Mark II', 'Misc', 'pilegun_upkit', 'Misc'),
(154, 'Vibrator (entladen)', 'Misc', 'vibr_empty', 'Misc'),
(97, '''Wake The Dead''', 'Food', 'rhum', 'Food'),
(40, 'Riemen', 'Rsc', 'courroie', 'Rsc'),
(208, 'Aqua-Splash (5 Ladungen)', 'Weapon', 'watergun_opt_5', 'Weapon'),
(151, 'UnvollstÃ¤ndiges Kartenspiel', 'Misc', 'cards', 'Misc'),
(38, 'Ein paar WÃ¼rfel', 'Misc', 'dice', 'Misc'),
(148, 'Chinesische Nudeln', 'Food', 'food_noodles', 'Food'),
(89, 'Twinoid 500mg', 'Drug', 'drug_hero', 'Drug'),
(198, 'Leckere Speise', 'Food', 'dish_tasty', 'Food'),
(28, 'Beruhigungsspritze', 'Drug', 'xanax', 'Drug'),
(26, 'Streichholzschachtel', 'Misc', 'lights', 'Misc'),
(82, 'Zerlegter RasenmÃ¤her', 'Misc', 'lawn_part', 'Misc'),
(25, 'Extra Tasche', 'Box', 'bag', 'Box'),
(136, 'Paracetoid 7g', 'Drug', 'disinfect', 'Drug'),
(23, 'DosenÃ¶ffner', 'Weapon', 'can_opener', 'Weapon'),
(79, 'UnvollstÃ¤ndige KettensÃ¤ge', 'Misc', 'chainsaw_part', 'Misc'),
(135, 'Etikettenloses Medikament', 'Drug', 'drug_random', 'Drug'),
(22, 'Einkaufswagen', 'Box', 'cart', 'Box'),
(18, 'LÃ¤cherliches Taschenmesser', 'Weapon', 'small_knife', 'Weapon'),
(16, 'Jagdmesser', 'Weapon', 'knife', 'Weapon'),
(184, 'Reparatur Fix', 'Misc', 'repair_one', 'Misc'),
(69, 'Wodka Marinostov', 'Food', 'vodka', 'Food'),
(125, 'Zonenmarker ''Radius''', 'Misc', 'tagger', 'Misc'),
(181, 'Loses Werkzeug', 'Misc', 'repair_kit_part_raw', 'Misc'),
(233, 'Fackel', 'Armor', 'torch', 'Armor'),
(119, 'Elektrischer Bauchmuskeltrainer (ohne Strom)', 'Misc', 'sport_elec_empty', 'Misc'),
(62, 'Wasserbombe', 'Weapon', 'grenade', 'Weapon'),
(174, 'SchieÃŸpulverbombe', 'Misc', 'flash', 'Misc'),
(117, 'Batteriewerfer 1-PDTG (entladen)', 'Weapon', 'pilegun_empty', 'Weapon'),
(4, 'Offene Konservendose', 'Food', 'can_open', 'Food'),
(172, 'Darmmelone', 'Food', 'vegetable_tasty', 'Food'),
(1, 'Ration Wasser', 'Food', 'water', 'Food'),
(105, 'Kassettenradio', 'Furniture', 'radio_on', 'Furniture'),
(178, 'Holzkistendeckel', 'Misc', 'wood_plate_part', 'Misc'),
(147, 'Verschimmelte Stulle', 'Food', 'food_sandw', 'Food'),
(146, 'Fades GebÃ¤ck', 'Food', 'food_tarte', 'Food'),
(144, 'Angebissene HÃ¤hnchenflÃ¼gel', 'Food', 'food_chick', 'Food'),
(141, 'Verschimmelte Waffeln', 'Food', 'food_bar2', 'Food'),
(140, 'TÃ¼te mit labbrigen Chips', 'Food', 'food_bar1', 'Food'),
(138, 'Nahrungsmittelkiste', 'Food', 'chest_food', 'Food'),
(92, 'Werkzeugkiste', 'Box', 'chest_tools', 'Box'),
(133, 'Zementsack', 'Misc', 'concrete', 'Misc'),
(90, 'Metallkiste', 'Box', 'chest', 'Box'),
(58, 'Kanister', 'Misc', 'jerrycan', 'Misc'),
(165, 'Defektes ElektrogerÃ¤t', 'Rsc', 'electro_box', 'Rsc'),
(47, 'Zwei-Meter Schlange', 'Misc', 'pet_snake', 'Misc'),
(20, 'Teppichmesser', 'Weapon', 'cutter', 'Weapon'),
(242, 'PC-GehÃ¤use', 'Furniture', 'pc', 'Furniture'),
(213, 'Batteriewerfer Mark II (geladen)', 'Weapon', 'pilegun_up', 'Weapon'),
(44, 'Riesige Ratte', 'Misc', 'pet_rat', 'Misc'),
(43, 'Ãœbelriechendes Schwein', 'Misc', 'pet_pig', 'Misc'),
(42, 'Huhn', 'Misc', 'pet_chick', 'Misc'),
(5, 'Batteriewerfer 1-PDTG (geladen)', 'Weapon', 'pilegun', 'Weapon'),
(168, 'Getriebe', 'Rsc', 'mecanism', 'Rsc'),
(35, 'Kette + VorhÃ¤ngeschloss', 'Furniture', 'lock', 'Furniture'),
(200, 'Kartons', 'Furniture', 'home_box', 'Furniture'),
(53, 'Undefinierbares Fleisch', 'Food', 'undef', 'Food'),
(139, 'Doggybag', 'Food', 'food_bag', 'Food'),
(74, 'Menschenfleisch', 'Food', 'hmeat', 'Food'),
(128, 'Ektorp-Gluten Stuhl', 'Furniture', 'chair_basic', 'Furniture'),
(122, 'ZerstÃ¶rer (entladen)', 'Weapon', 'big_pgun_empty', 'Weapon'),
(34, 'Mini Hi-Fi Anlage (defekt)', 'Furniture', 'music_part', 'Furniture'),
(145, 'Abgelaufene Pim''s Kekse', 'Food', 'food_pims', 'Food'),
(143, 'Ranzige Butterkekse', 'Food', 'food_biscuit', 'Food'),
(197, 'VerdÃ¤chtige Speise', 'Food', 'dish', 'Food'),
(142, 'Trockene Kaugummis', 'Food', 'food_bar3', 'Food'),
(245, 'Wasserspender (leer)', 'Misc', 'water_can_empty', 'Misc'),
(65, 'Kanisterpumpe (zerlegt)', 'Misc', 'jerrygun_part', 'Misc'),
(19, 'Schweizer Taschenmesser', 'Weapon', 'swiss_knife', 'Weapon'),
(204, 'MaschendrahtzaunstÃ¼ck', 'Furniture', 'fence', 'Furniture'),
(250, 'Abgelaufene Betapropin-Tablette 5mg', 'Drug', 'beta_drug_bad', 'Drug'),
(96, 'Unverarbeitete Blechplatten', 'Misc', 'plate_raw', 'Misc'),
(120, 'Elektrischer Bauchmuskeltrainer (geladen)', 'Misc', 'sport_elec', 'Misc'),
(78, 'PlastiktÃ¼te mit Sprengstoff', 'Weapon', 'bgrenade_empty', 'Weapon'),
(235, 'Getrocknete Marshmallows', 'Food', 'chama', 'Food'),
(77, 'Explodierende Wasserbombe', 'Weapon', 'bgrenade', 'Weapon');
