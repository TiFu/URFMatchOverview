-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 14. Apr 2015 um 20:17
-- Server Version: 5.6.21
-- PHP-Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `challenge`
--
-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS challenge;

USE challenge;
--
-- Tabellenstruktur für Tabelle `analyzedgames`
--

CREATE TABLE IF NOT EXISTS `analyzedgames` (
  `championId` int(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `numberOfGames` int(255) NOT NULL,
  `pickRate` decimal(15,10) NOT NULL,
  `winRate` decimal(15,10) NOT NULL,
  `banRate` decimal(15,10) NOT NULL,
  `kills` decimal(15,10) NOT NULL,
  `deaths` decimal(15,10) NOT NULL,
  `assists` decimal(15,10) NOT NULL,
  `minionsKilled` decimal(15,10) NOT NULL,
  `goldEarned` decimal(15,10) NOT NULL,
  `towerKills` decimal(15,10) NOT NULL,
  `firstBloodKill` decimal(15,10) NOT NULL,
  `firstTowerKill` decimal(15,10) NOT NULL,
  `totalDamageDealtToChampions` decimal(15,10) NOT NULL,
  `magicDamageDealtToChampions` decimal(15,10) NOT NULL,
  `physicalDamageDealtToChampions` decimal(15,10) NOT NULL,
  `trueDamageDealtToChampions` decimal(15,10) NOT NULL,
  `totalDamageTaken` decimal(15,10) NOT NULL,
  `magicDamageTaken` decimal(15,10) NOT NULL,
  `physicalDamageTaken` decimal(15,10) NOT NULL,
  `trueDamageTaken` decimal(15,10) NOT NULL,
  `totalHeal` decimal(15,10) NOT NULL,
  `totalTimeCrowdControlDealt` decimal(15,10) NOT NULL,
  `wardsKilled` decimal(15,10) NOT NULL,
  `wardsPlaced` decimal(15,10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `champs`
--

CREATE TABLE IF NOT EXISTS `champs` (
  `id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `champs`
--

INSERT INTO `champs` (`id`, `name`) VALUES
(1, 'Annie'),
(2, 'Olaf'),
(3, 'Galio'),
(4, 'Twisted Fate'),
(5, 'Xin Zhao'),
(6, 'Urgot'),
(7, 'LeBlanc'),
(8, 'Vladimir'),
(9, 'Fiddlesticks'),
(10, 'Kayle'),
(11, 'Master Yi'),
(12, 'Alistar'),
(13, 'Ryze'),
(14, 'Sion'),
(15, 'Sivir'),
(16, 'Soraka'),
(17, 'Teemo'),
(18, 'Tristana'),
(19, 'Warwick'),
(20, 'Nunu'),
(21, 'Miss Fortune'),
(22, 'Ashe'),
(23, 'Tryndamere'),
(24, 'Jax'),
(25, 'Morgana'),
(26, 'Zilean'),
(27, 'Singed'),
(28, 'Evelynn'),
(29, 'Twitch'),
(30, 'Karthus'),
(31, 'Cho''Gath'),
(32, 'Amumu'),
(33, 'Rammus'),
(34, 'Anivia'),
(35, 'Shaco'),
(36, 'Dr. Mundo'),
(37, 'Sona'),
(38, 'Kassadin'),
(39, 'Irelia'),
(40, 'Janna'),
(41, 'Gangplank'),
(42, 'Corki'),
(43, 'Karma'),
(44, 'Taric'),
(45, 'Veigar'),
(48, 'Trundle'),
(50, 'Swain'),
(51, 'Caitlyn'),
(53, 'Blitzcrank'),
(54, 'Malphite'),
(55, 'Katarina'),
(56, 'Nocturne'),
(57, 'Maokai'),
(58, 'Renekton'),
(59, 'Jarvan IV'),
(60, 'Elise'),
(61, 'Orianna'),
(62, 'Wukong'),
(63, 'Brand'),
(64, 'Lee Sin'),
(67, 'Vayne'),
(68, 'Rumble'),
(69, 'Cassiopeia'),
(72, 'Skarner'),
(74, 'Heimerdinger'),
(75, 'Nasus'),
(76, 'Nidalee'),
(77, 'Udyr'),
(78, 'Poppy'),
(79, 'Gragas'),
(80, 'Pantheon'),
(81, 'Ezreal'),
(82, 'Mordekaiser'),
(83, 'Yorick'),
(84, 'Akali'),
(85, 'Kennen'),
(86, 'Garen'),
(89, 'Leona'),
(90, 'Malzahar'),
(91, 'Talon'),
(92, 'Riven'),
(96, 'Kog''Maw'),
(98, 'Shen'),
(99, 'Lux'),
(101, 'Xerath'),
(102, 'Shyvana'),
(103, 'Ahri'),
(104, 'Graves'),
(105, 'Fizz'),
(106, 'Volibear'),
(107, 'Rengar'),
(110, 'Varus'),
(111, 'Nautilus'),
(112, 'Viktor'),
(113, 'Sejuani'),
(114, 'Fiora'),
(115, 'Ziggs'),
(117, 'Lulu'),
(119, 'Draven'),
(120, 'Hecarim'),
(121, 'Kha''Zix'),
(122, 'Darius'),
(126, 'Jayce'),
(127, 'Lissandra'),
(131, 'Diana'),
(133, 'Quinn'),
(134, 'Syndra'),
(143, 'Zyra'),
(150, 'Gnar'),
(154, 'Zac'),
(157, 'Yasuo'),
(161, 'Vel''Koz'),
(201, 'Braum'),
(222, 'Jinx'),
(236, 'Lucian'),
(238, 'Zed'),
(254, 'Vi'),
(266, 'Aatrox'),
(267, 'Nami'),
(268, 'Azir'),
(412, 'Thresh'),
(421, 'Rek''Sai'),
(429, 'Kalista'),
(432, 'Bard');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `id` int(255) NOT NULL,
  `stock` int(100) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `items`
--

INSERT INTO `items` (`id`, `stock`, `name`) VALUES
(1001, 1, 'Boots of Speed'),
(1004, 1, 'Faerie Charm'),
(1006, 1, 'Rejuvenation Bead'),
(1011, 1, 'Giant''s Belt'),
(1018, 1, 'Cloak of Agility'),
(1026, 1, 'Blasting Wand'),
(1027, 1, 'Sapphire Crystal'),
(1028, 1, 'Ruby Crystal'),
(1029, 1, 'Cloth Armor'),
(1031, 1, 'Chain Vest'),
(1033, 1, 'Null-Magic Mantle'),
(1036, 1, 'Long Sword'),
(1037, 1, 'Pickaxe'),
(1038, 1, 'B. F. Sword'),
(1039, 1, 'Hunter''s Machete'),
(1042, 1, 'Dagger'),
(1043, 1, 'Recurve Bow'),
(1051, 1, 'Brawler''s Gloves'),
(1052, 1, 'Amplifying Tome'),
(1053, 1, 'Vampiric Scepter'),
(1054, 1, 'Doran''s Shield'),
(1055, 1, 'Doran''s Blade'),
(1056, 1, 'Doran''s Ring'),
(1057, 1, 'Negatron Cloak'),
(1058, 1, 'Needlessly Large Rod'),
(1062, 1, 'Prospector''s Blade'),
(1063, 1, 'Prospector''s Ring'),
(1074, 1, 'Doran''s Shield (Showdown)'),
(1075, 1, 'Doran''s Blade (Showdown)'),
(1076, 1, 'Doran''s Ring (Showdown)'),
(2003, 5, 'Health Potion'),
(2004, 5, 'Mana Potion'),
(2009, 1, 'Total Biscuit of Rejuvenation'),
(2010, 5, 'Total Biscuit of Rejuvenation'),
(2041, 1, 'Crystalline Flask'),
(2043, 2, 'Vision Ward'),
(2044, 3, 'Stealth Ward'),
(2045, 1, 'Ruby Sightstone'),
(2047, 1, 'Oracle''s Extract'),
(2049, 1, 'Sightstone'),
(2050, 1, 'Explorer''s Ward'),
(2051, 1, 'Guardian''s Horn'),
(2052, 1, 'Poro-Snax'),
(2053, 1, 'Raptor Cloak'),
(2054, 1, 'Diet Poro-Snax'),
(2137, 1, 'Elixir of Ruin'),
(2138, 1, 'Elixir of Iron'),
(2139, 1, 'Elixir of Sorcery'),
(2140, 1, 'Elixir of Wrath'),
(3001, 1, 'Abyssal Scepter'),
(3003, 1, 'Archangel''s Staff'),
(3004, 1, 'Manamune'),
(3006, 1, 'Berserker''s Greaves'),
(3007, 1, 'Archangel''s Staff (Crystal Scar)'),
(3008, 1, 'Manamune (Crystal Scar)'),
(3009, 1, 'Boots of Swiftness'),
(3010, 1, 'Catalyst the Protector'),
(3020, 1, 'Sorcerer''s Shoes'),
(3022, 1, 'Frozen Mallet'),
(3023, 1, 'Twin Shadows'),
(3024, 1, 'Glacial Shroud'),
(3025, 1, 'Iceborn Gauntlet'),
(3026, 1, 'Guardian Angel'),
(3027, 1, 'Rod of Ages'),
(3028, 1, 'Chalice of Harmony'),
(3029, 1, 'Rod of Ages (Crystal Scar)'),
(3031, 1, 'Infinity Edge'),
(3035, 1, 'Last Whisper'),
(3040, 1, 'Seraph''s Embrace'),
(3041, 1, 'Mejai''s Soulstealer'),
(3042, 1, 'Muramana'),
(3043, 1, 'Muramana'),
(3044, 1, 'Phage'),
(3046, 1, 'Phantom Dancer'),
(3047, 1, 'Ninja Tabi'),
(3048, 1, 'Seraph''s Embrace'),
(3050, 1, 'Zeke''s Herald'),
(3056, 1, 'Ohmwrecker'),
(3057, 1, 'Sheen'),
(3060, 1, 'Banner of Command'),
(3065, 1, 'Spirit Visage'),
(3067, 1, 'Kindlegem'),
(3068, 1, 'Sunfire Cape'),
(3069, 1, 'Talisman of Ascension'),
(3070, 1, 'Tear of the Goddess'),
(3071, 1, 'The Black Cleaver'),
(3072, 1, 'The Bloodthirster'),
(3073, 1, 'Tear of the Goddess (Crystal Scar)'),
(3074, 1, 'Ravenous Hydra (Melee Only)'),
(3075, 1, 'Thornmail'),
(3077, 1, 'Tiamat (Melee Only)'),
(3078, 1, 'Trinity Force'),
(3082, 1, 'Warden''s Mail'),
(3083, 1, 'Warmog''s Armor'),
(3084, 1, 'Overlord''s Bloodmail'),
(3085, 1, 'Runaan''s Hurricane (Ranged Only)'),
(3086, 1, 'Zeal'),
(3087, 1, 'Statikk Shiv'),
(3089, 1, 'Rabadon''s Deathcap'),
(3090, 1, 'Wooglet''s Witchcap'),
(3091, 1, 'Wit''s End'),
(3092, 1, 'Frost Queen''s Claim'),
(3093, 1, 'Avarice Blade'),
(3096, 1, 'Nomad''s Medallion'),
(3097, 1, 'Targon''s Brace'),
(3098, 1, 'Frostfang'),
(3100, 1, 'Lich Bane'),
(3101, 1, 'Stinger'),
(3102, 1, 'Banshee''s Veil'),
(3104, 1, 'Lord Van Damm''s Pillager'),
(3105, 1, 'Aegis of the Legion'),
(3106, 1, 'Madred''s Razors'),
(3108, 1, 'Fiendish Codex'),
(3110, 1, 'Frozen Heart'),
(3111, 1, 'Mercury''s Treads'),
(3112, 1, 'Orb of Winter'),
(3113, 1, 'Aether Wisp'),
(3114, 1, 'Forbidden Idol'),
(3115, 1, 'Nashor''s Tooth'),
(3116, 1, 'Rylai''s Crystal Scepter'),
(3117, 1, 'Boots of Mobility'),
(3122, 1, 'Wicked Hatchet'),
(3124, 1, 'Guinsoo''s Rageblade'),
(3134, 0, 'The Brutalizer'),
(3135, 1, 'Void Staff'),
(3136, 0, 'Haunting Guise'),
(3137, 1, 'Dervish Blade'),
(3139, 1, 'Mercurial Scimitar'),
(3140, 1, 'Quicksilver Sash'),
(3141, 1, 'Sword of the Occult'),
(3142, 1, 'Youmuu''s Ghostblade'),
(3143, 1, 'Randuin''s Omen'),
(3144, 1, 'Bilgewater Cutlass'),
(3145, 1, 'Hextech Revolver'),
(3146, 1, 'Hextech Gunblade'),
(3151, 0, 'Liandry''s Torment'),
(3152, 1, 'Will of the Ancients'),
(3153, 1, 'Blade of the Ruined King'),
(3154, 1, 'Wriggle''s Lantern'),
(3155, 0, 'Hexdrinker'),
(3156, 0, 'Maw of Malmortius'),
(3157, 1, 'Zhonya''s Hourglass'),
(3158, 1, 'Ionian Boots of Lucidity'),
(3159, 1, 'Grez''s Spectral Lantern'),
(3165, 1, 'Morellonomicon'),
(3166, 1, 'Bonetooth Necklace'),
(3167, 1, 'Bonetooth Necklace'),
(3168, 1, 'Bonetooth Necklace'),
(3169, 1, 'Bonetooth Necklace'),
(3170, 1, 'Moonflair Spellblade'),
(3171, 1, 'Bonetooth Necklace'),
(3172, 1, 'Zephyr'),
(3174, 1, 'Athene''s Unholy Grail'),
(3175, 1, 'Head of Kha''Zix'),
(3180, 1, 'Odyn''s Veil'),
(3181, 1, 'Sanguine Blade'),
(3184, 1, 'Entropy'),
(3185, 1, 'The Lightbringer'),
(3187, 1, 'Hextech Sweeper'),
(3190, 1, 'Locket of the Iron Solari'),
(3191, 1, 'Seeker''s Armguard'),
(3196, 1, 'The Hex Core mk-1'),
(3197, 1, 'The Hex Core mk-2'),
(3198, 1, 'Perfect Hex Core'),
(3200, 1, 'Prototype Hex Core'),
(3211, 1, 'Spectre''s Cowl'),
(3222, 1, 'Mikael''s Crucible'),
(3250, 1, 'Enchantment: Homeguard'),
(3251, 1, 'Enchantment: Captain'),
(3252, 1, 'Enchantment: Furor'),
(3253, 1, 'Enchantment: Distortion'),
(3254, 1, 'Enchantment: Alacrity'),
(3255, 1, 'Enchantment: Homeguard'),
(3256, 1, 'Enchantment: Captain'),
(3257, 1, 'Enchantment: Furor'),
(3258, 1, 'Enchantment: Distortion'),
(3259, 1, 'Enchantment: Alacrity'),
(3260, 1, 'Enchantment: Homeguard'),
(3261, 1, 'Enchantment: Captain'),
(3262, 1, 'Enchantment: Furor'),
(3263, 1, 'Enchantment: Distortion'),
(3264, 1, 'Enchantment: Alacrity'),
(3265, 1, 'Enchantment: Homeguard'),
(3266, 1, 'Enchantment: Captain'),
(3267, 1, 'Enchantment: Furor'),
(3268, 1, 'Enchantment: Distortion'),
(3269, 1, 'Enchantment: Alacrity'),
(3270, 1, 'Enchantment: Homeguard'),
(3271, 1, 'Enchantment: Captain'),
(3272, 1, 'Enchantment: Furor'),
(3273, 1, 'Enchantment: Distortion'),
(3274, 1, 'Enchantment: Alacrity'),
(3275, 1, 'Enchantment: Homeguard'),
(3276, 1, 'Enchantment: Captain'),
(3277, 1, 'Enchantment: Furor'),
(3278, 1, 'Enchantment: Distortion'),
(3279, 1, 'Enchantment: Alacrity'),
(3280, 1, 'Enchantment: Homeguard'),
(3281, 1, 'Enchantment: Captain'),
(3282, 1, 'Enchantment: Furor'),
(3283, 1, 'Enchantment: Distortion'),
(3284, 1, 'Enchantment: Alacrity'),
(3285, 1, 'Luden''s Echo'),
(3290, 1, 'Twin Shadows'),
(3301, 1, 'Ancient Coin'),
(3302, 1, 'Relic Shield'),
(3303, 1, 'Spellthief''s Edge'),
(3340, 1, 'Warding Totem (Trinket)'),
(3341, 1, 'Sweeping Lens (Trinket)'),
(3342, 1, 'Scrying Orb (Trinket)'),
(3345, 1, 'Soul Anchor (Trinket)'),
(3361, 1, 'Greater Stealth Totem (Trinket)'),
(3362, 1, 'Greater Vision Totem (Trinket)'),
(3363, 1, 'Farsight Orb (Trinket)'),
(3364, 1, 'Oracle''s Lens (Trinket)'),
(3401, 1, 'Face of the Mountain'),
(3405, 1, 'Bonetooth Necklace'),
(3406, 1, 'Bonetooth Necklace'),
(3407, 1, 'Bonetooth Necklace'),
(3408, 1, 'Bonetooth Necklace'),
(3409, 1, 'Bonetooth Necklace'),
(3410, 1, 'Head of Kha''Zix'),
(3411, 1, 'Bonetooth Necklace'),
(3412, 1, 'Bonetooth Necklace'),
(3413, 1, 'Bonetooth Necklace'),
(3414, 1, 'Bonetooth Necklace'),
(3415, 1, 'Bonetooth Necklace'),
(3416, 1, 'Head of Kha''Zix'),
(3417, 1, 'Bonetooth Necklace'),
(3418, 1, 'Bonetooth Necklace'),
(3419, 1, 'Bonetooth Necklace'),
(3420, 1, 'Bonetooth Necklace'),
(3421, 1, 'Bonetooth Necklace'),
(3422, 1, 'Head of Kha''Zix'),
(3450, 1, 'Bonetooth Necklace'),
(3451, 1, 'Bonetooth Necklace'),
(3452, 1, 'Bonetooth Necklace'),
(3453, 1, 'Bonetooth Necklace'),
(3454, 1, 'Bonetooth Necklace'),
(3455, 1, 'Head of Kha''Zix'),
(3460, 1, 'Golden Transcendence'),
(3504, 1, 'Ardent Censer'),
(3508, 1, 'Essence Reaver'),
(3512, 1, 'Zz''Rot Portal'),
(3599, 1, 'The Black Spear'),
(3706, 1, 'Stalker''s Blade'),
(3707, 1, 'Enchantment: Warrior'),
(3708, 1, 'Enchantment: Magus'),
(3709, 1, 'Enchantment: Cinderhulk'),
(3710, 1, 'Enchantment: Devourer'),
(3711, 1, 'Poacher''s Knife'),
(3713, 1, 'Ranger''s Trailblazer'),
(3714, 1, 'Enchantment: Warrior'),
(3715, 1, 'Skirmisher''s Sabre'),
(3716, 1, 'Enchantment: Magus'),
(3717, 1, 'Enchantment: Cinderhulk'),
(3718, 1, 'Enchantment: Devourer'),
(3719, 1, 'Enchantment: Warrior'),
(3720, 1, 'Enchantment: Magus'),
(3721, 1, 'Enchantment: Cinderhulk'),
(3722, 1, 'Enchantment: Devourer'),
(3723, 1, 'Enchantment: Warrior'),
(3724, 1, 'Enchantment: Magus'),
(3725, 1, 'Enchantment: Cinderhulk'),
(3726, 1, 'Enchantment: Devourer'),
(3751, 1, 'Bami''s Cinder'),
(3800, 1, 'Righteous Glory'),
(3801, 1, 'Crystalline Bracer');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `summoners`
--

CREATE TABLE IF NOT EXISTS `summoners` (
  `id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `summoners`
--

INSERT INTO `summoners` (`id`, `name`) VALUES
(21, 'Barrier'),
(1, 'Cleanse'),
(2, 'Clairvoyance'),
(14, 'Ignite'),
(3, 'Exhaust'),
(4, 'Flash'),
(6, 'Ghost'),
(7, 'Heal'),
(13, 'Clarity'),
(17, 'Garrison'),
(30, 'To the King!'),
(31, 'Poro Toss'),
(11, 'Smite'),
(12, 'Teleport');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `winrate`
--

CREATE TABLE IF NOT EXISTS `winrate` (
  `region` varchar(255) NOT NULL,
  `blueSideWins` int(255) NOT NULL,
  `redSideWins` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `analyzedgames`
--
ALTER TABLE `analyzedgames`
 ADD UNIQUE KEY `index` (`championId`,`region`) COMMENT 'championId and region';

--
-- Indizes für die Tabelle `champs`
--
ALTER TABLE `champs`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `items`
--
ALTER TABLE `items`
 ADD PRIMARY KEY (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
