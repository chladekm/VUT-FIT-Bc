-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Stř 27. kvě 2020, 16:12
-- Verze serveru: 10.4.11-MariaDB
-- Verze PHP: 7.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `familytreedb`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `collisionrelationship`
--

CREATE TABLE `collisionrelationship` (
  `CollisionId` int(11) NOT NULL,
  `RelationshipId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `collisionrelationship`
--

INSERT INTO `collisionrelationship` (`CollisionId`, `RelationshipId`) VALUES
(6, 27),
(6, 143),
(7, 29),
(7, 144),
(8, 33),
(8, 145),
(9, 26),
(9, 166),
(10, 28),
(10, 167),
(11, 32),
(11, 168),
(12, 252),
(12, 255);

-- --------------------------------------------------------

--
-- Struktura tabulky `collisions`
--

CREATE TABLE `collisions` (
  `Id` int(11) NOT NULL,
  `Type` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `collisions`
--

INSERT INTO `collisions` (`Id`, `Type`) VALUES
(6, 'differentFather'),
(7, 'differentFather'),
(8, 'differentFather'),
(9, 'differentMother'),
(10, 'differentMother'),
(11, 'differentMother'),
(12, 'marriageOrAncestor');

-- --------------------------------------------------------

--
-- Struktura tabulky `familytreecollision`
--

CREATE TABLE `familytreecollision` (
  `FamilyTreeId` int(11) NOT NULL,
  `CollisionId` int(11) NOT NULL,
  `IsSolved` tinyint(1) NOT NULL,
  `SolutionDate` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `familytreecollision`
--

INSERT INTO `familytreecollision` (`FamilyTreeId`, `CollisionId`, `IsSolved`, `SolutionDate`) VALUES
(1, 6, 0, NULL),
(1, 7, 1, '2020-05-22 18:51:46.450408'),
(1, 8, 0, NULL),
(1, 9, 0, NULL),
(1, 10, 0, NULL),
(1, 11, 0, NULL),
(2, 6, 0, NULL),
(2, 7, 1, '2020-05-26 22:21:31.834111'),
(2, 8, 1, '2020-05-22 18:00:38.169707'),
(2, 9, 0, NULL),
(2, 10, 1, '2020-05-26 22:21:41.697631'),
(2, 11, 0, NULL),
(2, 12, 0, NULL),
(5, 12, 0, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `familytreeperson`
--

CREATE TABLE `familytreeperson` (
  `FamilyTreeId` int(11) NOT NULL,
  `PersonId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `familytreeperson`
--

INSERT INTO `familytreeperson` (`FamilyTreeId`, `PersonId`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(1, 41),
(1, 86),
(1, 135),
(1, 136),
(1, 137),
(1, 142),
(1, 143),
(2, 18),
(2, 19),
(2, 21),
(2, 25),
(2, 27),
(2, 32),
(2, 36),
(2, 37),
(2, 46),
(2, 51),
(2, 72),
(2, 73),
(2, 74),
(2, 75),
(2, 76),
(2, 77),
(2, 78),
(2, 79),
(2, 80),
(2, 81),
(2, 82),
(2, 83),
(2, 84),
(2, 85),
(2, 87),
(2, 88),
(2, 89),
(2, 90),
(2, 91),
(2, 114),
(2, 115),
(2, 133),
(2, 134),
(2, 138),
(2, 139),
(2, 140),
(2, 141),
(3, 26),
(3, 56),
(3, 57),
(3, 58),
(3, 59),
(3, 60),
(3, 61),
(3, 62),
(3, 63),
(3, 64),
(3, 65),
(3, 66),
(3, 67),
(3, 68),
(3, 69),
(3, 70),
(3, 71),
(5, 21),
(5, 36),
(5, 37),
(5, 74),
(5, 75),
(5, 92),
(5, 93),
(5, 94),
(5, 95),
(5, 96),
(5, 97),
(5, 98),
(5, 99),
(5, 100),
(5, 101),
(5, 102),
(5, 103),
(5, 104),
(5, 105),
(5, 106),
(5, 107),
(5, 108),
(5, 109),
(5, 117),
(5, 124),
(5, 125),
(5, 126),
(5, 127),
(5, 128),
(5, 129),
(5, 130),
(5, 131),
(5, 132),
(5, 133);

-- --------------------------------------------------------

--
-- Struktura tabulky `familytreerelationship`
--

CREATE TABLE `familytreerelationship` (
  `FamilyTreeId` int(11) NOT NULL,
  `RelationshipId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `familytreerelationship`
--

INSERT INTO `familytreerelationship` (`FamilyTreeId`, `RelationshipId`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 46),
(1, 48),
(1, 49),
(1, 50),
(1, 63),
(1, 65),
(1, 66),
(1, 68),
(1, 70),
(1, 72),
(1, 73),
(1, 74),
(1, 75),
(1, 76),
(1, 77),
(1, 78),
(1, 170),
(1, 171),
(1, 256),
(1, 257),
(1, 258),
(1, 259),
(1, 260),
(1, 268),
(1, 269),
(1, 270),
(2, 39),
(2, 50),
(2, 63),
(2, 65),
(2, 66),
(2, 90),
(2, 91),
(2, 103),
(2, 141),
(2, 143),
(2, 144),
(2, 145),
(2, 146),
(2, 147),
(2, 148),
(2, 149),
(2, 150),
(2, 151),
(2, 152),
(2, 153),
(2, 154),
(2, 155),
(2, 156),
(2, 157),
(2, 158),
(2, 159),
(2, 160),
(2, 161),
(2, 162),
(2, 163),
(2, 164),
(2, 165),
(2, 166),
(2, 167),
(2, 168),
(2, 169),
(2, 172),
(2, 173),
(2, 174),
(2, 175),
(2, 176),
(2, 177),
(2, 178),
(2, 179),
(2, 217),
(2, 218),
(2, 219),
(2, 253),
(2, 254),
(2, 255),
(2, 261),
(2, 262),
(2, 263),
(2, 264),
(2, 265),
(2, 266),
(2, 267),
(3, 116),
(3, 117),
(3, 118),
(3, 119),
(3, 120),
(3, 121),
(3, 122),
(3, 123),
(3, 124),
(3, 125),
(3, 126),
(3, 127),
(3, 128),
(3, 129),
(3, 130),
(3, 131),
(3, 132),
(3, 133),
(3, 134),
(3, 135),
(3, 136),
(3, 137),
(3, 138),
(3, 139),
(3, 140),
(5, 63),
(5, 65),
(5, 66),
(5, 148),
(5, 149),
(5, 150),
(5, 180),
(5, 181),
(5, 182),
(5, 183),
(5, 184),
(5, 185),
(5, 186),
(5, 187),
(5, 188),
(5, 189),
(5, 190),
(5, 191),
(5, 192),
(5, 193),
(5, 194),
(5, 195),
(5, 196),
(5, 197),
(5, 198),
(5, 199),
(5, 200),
(5, 201),
(5, 202),
(5, 203),
(5, 204),
(5, 205),
(5, 206),
(5, 207),
(5, 221),
(5, 222),
(5, 237),
(5, 238),
(5, 239),
(5, 240),
(5, 241),
(5, 242),
(5, 243),
(5, 244),
(5, 245),
(5, 246),
(5, 247),
(5, 248),
(5, 249),
(5, 250),
(5, 251),
(5, 252);

-- --------------------------------------------------------

--
-- Struktura tabulky `familytrees`
--

CREATE TABLE `familytrees` (
  `Id` int(11) NOT NULL,
  `Type` longtext NOT NULL,
  `Title` longtext DEFAULT NULL,
  `UserId` int(11) NOT NULL,
  `StartPersonId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `familytrees`
--

INSERT INTO `familytrees` (`Id`, `Type`, `Title`, `UserId`, `StartPersonId`) VALUES
(1, '_public', 'Svobodovi', 1, 1),
(2, '_public', 'Kovářovi', 2, 25),
(3, '_private', 'Uhlířovi', 2, 26),
(5, '_nonpublic', 'Dvořákovi', 1, 92);

-- --------------------------------------------------------

--
-- Struktura tabulky `marriages`
--

CREATE TABLE `marriages` (
  `Id` int(11) NOT NULL,
  `MarriageDate` datetime(6) NOT NULL,
  `MarriageAddress` longtext DEFAULT NULL,
  `RelationshipId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `marriages`
--

INSERT INTO `marriages` (`Id`, `MarriageDate`, `MarriageAddress`, `RelationshipId`) VALUES
(1, '1802-02-03 23:02:16.000000', 'Pod kopcem', 1),
(2, '1945-08-05 22:00:00.000000', '', 10),
(3, '1918-01-29 23:00:00.000000', 'Na zámku 82', 180);

-- --------------------------------------------------------

--
-- Struktura tabulky `originalrecords`
--

CREATE TABLE `originalrecords` (
  `Id` int(11) NOT NULL,
  `RecordId` int(11) NOT NULL,
  `PersonId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `originalrecords`
--

INSERT INTO `originalrecords` (`Id`, `RecordId`, `PersonId`) VALUES
(3, 14, 74);

-- --------------------------------------------------------

--
-- Struktura tabulky `personnames`
--

CREATE TABLE `personnames` (
  `Id` int(11) NOT NULL,
  `Name` longtext NOT NULL,
  `isFirstName` tinyint(1) NOT NULL,
  `PersonId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `personnames`
--

INSERT INTO `personnames` (`Id`, `Name`, `isFirstName`, `PersonId`) VALUES
(1, 'Oldřich', 1, 1),
(2, 'Svoboda', 0, 1),
(3, 'Erika', 1, 2),
(4, 'Svobodová', 0, 2),
(5, 'Marek', 1, 3),
(6, 'Svoboda', 0, 3),
(7, 'Marie', 1, 4),
(8, 'Svobodová', 0, 4),
(9, 'Gustav', 1, 5),
(10, 'Svoboda', 0, 5),
(11, 'Arnošt', 1, 6),
(12, 'Svoboda', 0, 6),
(13, 'Alžběta', 1, 7),
(14, 'Svobodová', 0, 7),
(15, 'Viktorie', 1, 8),
(16, 'Svobodová', 0, 8),
(17, 'Eliška', 1, 9),
(18, 'Svobodová', 0, 9),
(19, 'Ondřej', 1, 10),
(20, 'Svoboda', 0, 10),
(21, 'Tereza', 1, 11),
(22, 'Svobodová', 0, 11),
(23, 'Oldřich', 1, 12),
(24, 'Kovář', 0, 12),
(25, 'Václav', 1, 13),
(26, 'Svoboda', 0, 13),
(27, 'Oldřich', 1, 14),
(28, 'Svoboda', 0, 14),
(29, 'Kristýna', 1, 15),
(30, 'Svobodová', 0, 15),
(31, 'Václav', 1, 17),
(32, 'Svoboda', 0, 17),
(33, 'Zita', 1, 18),
(34, 'Kovářová', 0, 18),
(35, 'Artuš', 1, 19),
(36, 'Kovář', 0, 19),
(37, 'Judita', 1, 20),
(38, 'Svobodová', 0, 20),
(39, 'Karel', 1, 21),
(40, 'Kovář', 0, 21),
(41, 'Valerie', 1, 22),
(42, 'Svobodová', 0, 22),
(43, 'Kolomazníková', 0, 22),
(44, 'Anna', 1, 4),
(45, 'Helena', 1, 23),
(46, 'Svobodová', 0, 23),
(49, 'František', 1, 25),
(50, 'Kovář', 0, 25),
(51, 'Kryštof', 1, 26),
(52, 'Uhlíř', 0, 26),
(53, 'Simona', 1, 29),
(54, 'Svobodová', 0, 29),
(55, 'Uhlířová', 0, 29),
(56, 'Erika', 1, 30),
(57, 'Svobodová', 0, 30),
(58, 'Gregor', 1, 32),
(59, 'Kovář', 0, 32),
(62, 'Beáta', 1, 36),
(63, 'Kovářová', 0, 36),
(64, 'Alena', 1, 37),
(65, 'Kovářová', 0, 37),
(66, 'Oldřich', 1, 39),
(67, 'Svoboda', 0, 39),
(68, 'Jan', 1, 40),
(69, 'Svoboda', 0, 40),
(70, 'Albert', 1, 41),
(71, 'Svoboda', 0, 41),
(74, 'Marie', 1, 46),
(75, 'Kovářová', 0, 46),
(76, 'Aneta', 1, 51),
(77, 'Kovářová', 0, 51),
(80, 'Simona', 1, 57),
(81, 'Uhlířová', 0, 57),
(82, 'Karel', 1, 58),
(83, 'Uhlíř', 0, 58),
(84, 'Jan', 1, 59),
(85, 'Uhlíř', 0, 59),
(86, 'Cukrářská', 0, 57),
(87, 'Igor', 1, 60),
(88, 'Cukrář', 0, 60),
(89, 'Aneta', 1, 62),
(90, 'Uhlířová', 0, 62),
(91, 'Alois', 1, 63),
(92, 'Smetana', 0, 63),
(93, 'Josef', 1, 64),
(94, 'Cukrář', 0, 64),
(95, 'Václav', 1, 66),
(96, 'Cukrář', 0, 66),
(97, 'Božena', 1, 67),
(98, 'Uhlířová', 0, 67),
(99, 'Kamila', 1, 68),
(100, 'Cukrářská', 0, 68),
(101, 'Maxmilián', 1, 70),
(102, 'Uhlíř', 0, 70),
(103, 'Ivan', 1, 71),
(104, 'Uhlíř', 0, 71),
(105, 'Simona', 1, 72),
(106, 'Kovářová', 0, 72),
(107, 'Oldřich', 1, 73),
(108, 'Kovář', 0, 73),
(109, 'Jan', 1, 74),
(110, 'Botek', 0, 74),
(111, 'Karel', 1, 75),
(112, 'Botek', 0, 75),
(113, 'Jan', 1, 76),
(114, 'Kovář', 0, 76),
(115, 'Renata', 1, 77),
(116, 'Kovářová', 0, 77),
(117, 'Ivana', 1, 78),
(118, 'Kovářová', 0, 78),
(119, 'David', 1, 79),
(120, 'Kovář', 0, 79),
(121, 'Jana', 1, 80),
(122, 'Kovářová', 0, 80),
(123, 'Stanislav', 1, 82),
(124, 'Kovář', 0, 82),
(125, 'Cyril', 1, 84),
(126, 'Kovář', 0, 84),
(127, 'Viktorie', 1, 85),
(128, 'Svobodová', 0, 85),
(129, 'Kovářová', 0, 85),
(130, 'Igor', 1, 86),
(131, 'Kovář', 0, 86),
(132, 'Viktorie', 1, 87),
(133, 'Crháková', 0, 87),
(134, 'Kovářová', 0, 87),
(135, 'Roman', 1, 88),
(136, 'Kovář', 0, 88),
(137, 'Eduard', 1, 89),
(138, 'Kovář', 0, 89),
(139, 'Anežka', 1, 91),
(140, 'Kovářová', 0, 91),
(141, 'Renata', 1, 92),
(142, 'Dvořáková', 0, 92),
(143, 'Oskar', 1, 93),
(144, 'Dvořák', 0, 93),
(145, 'Vratislav', 1, 94),
(146, 'Dvořák', 0, 94),
(147, 'Alfred', 1, 95),
(148, 'Dvořák', 0, 95),
(149, 'Daniela', 1, 96),
(150, 'Dvořáková', 0, 96),
(151, 'Antonín', 1, 97),
(152, 'Dvořák', 0, 97),
(153, 'Silvie', 1, 98),
(154, 'Dvořáková', 0, 98),
(155, 'Uhlířová', 0, 98),
(156, 'Anna', 1, 99),
(157, 'Dvořáková', 0, 99),
(158, 'Marie', 1, 99),
(159, 'Jan', 1, 100),
(160, 'Dvořák', 0, 100),
(161, 'Václav', 1, 101),
(162, 'Dvořák', 0, 101),
(163, 'Filip', 1, 103),
(164, 'Dvořák', 0, 103),
(165, 'Bohumil', 1, 105),
(166, 'Dvořák', 0, 105),
(167, 'Gustav', 1, 106),
(168, 'Uhlíř', 0, 106),
(169, 'Emil', 1, 107),
(170, 'Uhlíř', 0, 107),
(171, 'Helena', 1, 108),
(172, 'Veselá', 0, 108),
(173, 'Dvořáková', 0, 108),
(174, 'Aneta', 1, 109),
(175, 'Dvořáková', 0, 109),
(176, 'Cyril', 1, 115),
(177, 'Kovář', 0, 115),
(180, 'Jan', 1, 117),
(181, 'Kovář', 0, 117),
(182, 'Kateřina', 1, 124),
(183, 'Veselá', 0, 124),
(184, 'Kovářová', 0, 124),
(185, 'Tomáš', 1, 125),
(186, 'Dvořák', 0, 125),
(187, 'Ferdinand', 1, 126),
(188, 'Dvořák', 0, 126),
(189, 'Barbora', 1, 127),
(190, 'Dvořáková', 0, 127),
(191, 'Anna', 1, 128),
(192, 'Dvořáková', 0, 128),
(193, 'Natálie', 1, 129),
(194, 'Dvořáková', 0, 129),
(195, 'Michaela', 1, 130),
(196, 'Dvořáková', 0, 130),
(197, 'Simon', 1, 131),
(198, 'Dvořák', 0, 131),
(199, 'Jana', 1, 132),
(200, 'Uhlířová', 0, 132),
(201, 'Božena', 1, 133),
(202, 'Botková', 0, 133),
(203, 'Jindřich', 1, 135),
(204, 'Svoboda', 0, 135),
(205, 'Jan', 1, 137),
(206, 'Svoboda', 0, 137),
(207, 'Natálie', 1, 138),
(208, 'Kovářová', 0, 138),
(209, 'Jan', 1, 139),
(210, 'Kovář', 0, 139),
(211, 'Božena', 1, 140),
(212, 'Kovářová', 0, 140),
(213, 'Daniel', 1, 141),
(214, 'Kovář', 0, 141),
(215, 'Barbora', 1, 143),
(216, 'Svobodová', 0, 143),
(217, 'Marie', 1, 143);

-- --------------------------------------------------------

--
-- Struktura tabulky `persons`
--

CREATE TABLE `persons` (
  `Id` int(11) NOT NULL,
  `IsFemale` tinyint(1) NOT NULL,
  `BirthDate` datetime(6) DEFAULT NULL,
  `BaptismDate` datetime(6) DEFAULT NULL,
  `DeathDate` datetime(6) DEFAULT NULL,
  `IsPrivate` tinyint(1) NOT NULL,
  `IsUndefined` tinyint(1) NOT NULL,
  `BirthPlace` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `persons`
--

INSERT INTO `persons` (`Id`, `IsFemale`, `BirthDate`, `BaptismDate`, `DeathDate`, `IsPrivate`, `IsUndefined`, `BirthPlace`) VALUES
(1, 0, '1782-02-17 00:02:16.000000', NULL, '1856-01-10 23:02:16.000000', 0, 0, 'Prace'),
(2, 1, '1792-04-06 00:02:16.000000', '1792-05-17 23:02:16.000000', '1872-06-08 23:02:16.000000', 0, 0, NULL),
(3, 0, '1806-02-04 00:02:16.000000', '1806-08-08 23:02:16.000000', '1855-04-01 23:02:16.000000', 0, 0, NULL),
(4, 1, '1804-08-06 00:02:16.000000', '1804-08-08 23:02:16.000000', '1880-04-05 23:02:16.000000', 0, 0, 'Kobylí'),
(5, 0, '1812-12-17 01:00:00.000000', NULL, NULL, 0, 0, 'Kobylí'),
(6, 0, '1816-06-14 00:02:16.000000', NULL, '1904-02-03 23:00:00.000000', 0, 0, 'Kobylí'),
(7, 1, '1812-09-04 00:02:16.000000', NULL, NULL, 0, 0, NULL),
(8, 1, '1836-01-31 00:02:16.000000', NULL, NULL, 0, 0, 'Kobylí'),
(9, 1, '1825-02-12 00:02:16.000000', NULL, NULL, 0, 0, NULL),
(10, 0, '1845-02-25 00:02:16.000000', NULL, '1920-06-04 23:00:00.000000', 0, 0, NULL),
(11, 1, '1812-08-17 00:02:16.000000', NULL, NULL, 0, 0, NULL),
(12, 0, '1842-09-12 00:02:16.000000', NULL, '1900-01-03 23:00:00.000000', 0, 0, NULL),
(13, 0, '1841-02-20 00:02:16.000000', NULL, NULL, 0, 0, 'Kobylí'),
(14, 0, NULL, NULL, '1901-08-16 23:00:00.000000', 0, 0, NULL),
(15, 1, '1860-01-12 00:02:16.000000', NULL, NULL, 0, 0, NULL),
(16, 1, NULL, NULL, NULL, 1, 1, NULL),
(17, 0, '1865-05-17 00:02:16.000000', NULL, '1920-05-03 23:00:00.000000', 0, 0, NULL),
(18, 1, '1868-01-03 00:02:16.000000', NULL, NULL, 0, 0, NULL),
(19, 0, '1872-06-02 00:02:16.000000', NULL, '1940-03-02 23:00:00.000000', 0, 0, NULL),
(20, 1, '1886-02-14 00:02:16.000000', NULL, NULL, 0, 0, NULL),
(21, 0, '1874-08-12 00:02:16.000000', NULL, NULL, 0, 0, NULL),
(22, 1, '1846-02-14 00:02:16.000000', NULL, NULL, 0, 0, NULL),
(23, 1, NULL, NULL, '1960-04-10 23:00:00.000000', 0, 0, NULL),
(25, 0, '1814-05-14 00:02:16.000000', NULL, '1902-06-02 23:00:00.000000', 0, 0, 'Ústí nad Labem'),
(26, 0, '1832-04-16 00:02:16.000000', '1853-05-10 23:02:16.000000', NULL, 1, 0, 'Pratec'),
(27, 1, NULL, NULL, NULL, 1, 1, NULL),
(29, 1, '1925-05-14 00:00:00.000000', NULL, '1970-04-15 23:00:00.000000', 0, 0, NULL),
(30, 1, '1902-07-06 00:00:00.000000', NULL, NULL, 0, 0, NULL),
(31, 1, NULL, NULL, NULL, 1, 1, NULL),
(32, 0, NULL, NULL, '1996-04-02 22:00:00.000000', 0, 0, NULL),
(36, 1, '1854-02-14 00:02:16.000000', NULL, '1932-02-01 23:00:00.000000', 0, 0, NULL),
(37, 1, NULL, NULL, NULL, 0, 0, NULL),
(38, 1, NULL, NULL, NULL, 1, 1, NULL),
(39, 0, NULL, NULL, '2003-02-14 23:00:00.000000', 0, 0, NULL),
(40, 0, '1956-04-15 00:00:00.000000', NULL, NULL, 0, 0, NULL),
(41, 0, '1896-06-14 00:00:00.000000', NULL, NULL, 0, 0, NULL),
(46, 1, '1896-05-01 23:00:00.000000', NULL, NULL, 0, 0, NULL),
(51, 1, '1908-05-17 00:00:00.000000', NULL, NULL, 0, 0, NULL),
(56, 1, NULL, NULL, NULL, 1, 1, NULL),
(57, 1, '1859-02-17 00:02:16.000000', '1900-05-03 23:00:00.000000', '1930-02-20 23:00:00.000000', 1, 0, NULL),
(58, 0, NULL, NULL, NULL, 1, 0, NULL),
(59, 0, '1876-02-16 00:02:16.000000', NULL, NULL, 1, 0, NULL),
(60, 0, '1862-06-18 00:02:16.000000', NULL, NULL, 1, 0, NULL),
(61, 1, NULL, NULL, NULL, 1, 1, NULL),
(62, 1, '1920-02-05 00:00:00.000000', NULL, NULL, 1, 0, NULL),
(63, 0, '1914-04-12 00:00:00.000000', NULL, '1985-08-15 22:00:00.000000', 1, 0, NULL),
(64, 0, '1879-08-20 00:02:16.000000', NULL, NULL, 1, 0, NULL),
(65, 1, NULL, NULL, NULL, 1, 1, NULL),
(66, 0, '1918-01-01 01:00:00.000000', NULL, NULL, 1, 0, 'Kobylí'),
(67, 1, '1986-02-02 00:00:00.000000', NULL, NULL, 1, 0, NULL),
(68, 1, '1896-06-10 00:00:00.000000', NULL, NULL, 1, 0, NULL),
(69, 0, NULL, NULL, NULL, 1, 1, NULL),
(70, 0, NULL, NULL, NULL, 1, 0, NULL),
(71, 0, NULL, NULL, NULL, 1, 0, NULL),
(72, 1, '1832-05-04 00:02:16.000000', NULL, NULL, 0, 0, NULL),
(73, 0, '1840-09-04 00:02:16.000000', NULL, '1900-01-03 23:00:00.000000', 0, 0, NULL),
(74, 0, '1885-03-05 00:02:16.000000', NULL, NULL, 0, 0, NULL),
(75, 0, '1919-05-17 00:00:00.000000', NULL, NULL, 0, 0, NULL),
(76, 0, '1853-06-04 00:02:16.000000', NULL, NULL, 0, 0, NULL),
(77, 1, '1856-02-07 00:02:16.000000', NULL, '1912-04-02 23:00:00.000000', 0, 0, NULL),
(78, 1, '1861-09-09 00:02:16.000000', NULL, NULL, 0, 0, 'Kobylí'),
(79, 0, NULL, NULL, '1941-08-06 22:00:00.000000', 0, 0, NULL),
(80, 1, NULL, NULL, NULL, 0, 0, NULL),
(81, 0, NULL, NULL, NULL, 1, 1, NULL),
(82, 0, '1900-05-18 00:00:00.000000', NULL, NULL, 0, 0, NULL),
(83, 0, NULL, NULL, NULL, 1, 1, NULL),
(84, 0, '1899-05-14 00:00:00.000000', NULL, NULL, 0, 0, NULL),
(85, 1, '1836-01-31 00:02:16.000000', NULL, NULL, 0, 0, 'Kobylí'),
(86, 0, '1900-01-02 00:00:00.000000', NULL, NULL, 0, 0, NULL),
(87, 1, '1904-02-05 00:00:00.000000', '1904-03-03 23:00:00.000000', NULL, 0, 0, 'Poděbrady'),
(88, 0, '1932-06-03 00:00:00.000000', '1932-06-02 23:00:00.000000', NULL, 0, 0, 'Poděbrady'),
(89, 0, '1912-09-07 00:00:00.000000', NULL, '1945-03-04 23:00:00.000000', 0, 0, 'Brno'),
(90, 1, NULL, NULL, NULL, 1, 1, NULL),
(91, 1, '1904-03-02 00:00:00.000000', NULL, '1980-08-06 22:00:00.000000', 0, 0, NULL),
(92, 1, '1782-07-03 00:02:16.000000', '1782-03-06 23:02:16.000000', '1890-02-02 23:02:16.000000', 0, 0, 'Blučina'),
(93, 0, '1780-02-08 00:02:16.000000', '1780-02-07 23:02:16.000000', '1865-02-02 23:02:16.000000', 0, 0, NULL),
(94, 0, '1821-01-30 00:02:16.000000', '1821-01-29 23:02:16.000000', NULL, 0, 0, 'Frýdek Mýstek'),
(95, 0, '1824-08-04 00:02:16.000000', NULL, NULL, 0, 0, NULL),
(96, 1, '1830-02-03 00:02:16.000000', NULL, NULL, 0, 0, 'Prace'),
(97, 0, '1862-10-12 01:00:00.000000', NULL, NULL, 0, 0, ''),
(98, 1, NULL, NULL, NULL, 0, 0, 'Komořany'),
(99, 1, '1863-04-30 00:02:16.000000', NULL, NULL, 0, 0, NULL),
(100, 0, '1890-03-11 00:02:16.000000', NULL, '1919-03-01 23:00:00.000000', 0, 0, 'Blučina'),
(101, 0, '1892-09-25 00:00:00.000000', '1892-09-24 23:00:00.000000', '1960-02-22 23:00:00.000000', 0, 0, NULL),
(102, 1, NULL, NULL, NULL, 1, 1, NULL),
(103, 0, '1924-04-04 00:00:00.000000', NULL, '1945-03-01 23:00:00.000000', 0, 0, NULL),
(104, 1, NULL, NULL, NULL, 1, 1, NULL),
(105, 0, '1904-02-03 00:00:00.000000', NULL, NULL, 0, 0, 'Blučina'),
(106, 0, NULL, NULL, '1912-09-05 23:00:00.000000', 0, 0, NULL),
(107, 0, '1863-01-02 00:02:16.000000', NULL, NULL, 0, 0, NULL),
(108, 1, '1896-04-03 00:00:00.000000', NULL, '1969-12-02 23:00:00.000000', 0, 0, 'Blučina'),
(109, 1, '1836-03-02 00:02:16.000000', NULL, '1907-03-03 23:00:00.000000', 0, 0, NULL),
(114, 0, NULL, NULL, NULL, 1, 1, NULL),
(115, 0, '1892-07-08 00:00:00.000000', NULL, NULL, 0, 0, NULL),
(117, 0, '1895-03-06 00:00:00.000000', NULL, '1962-09-07 23:00:00.000000', 0, 0, NULL),
(124, 1, '1900-08-03 00:00:00.000000', NULL, NULL, 0, 0, 'Poděbrady'),
(125, 0, '1908-02-06 00:00:00.000000', NULL, NULL, 0, 0, NULL),
(126, 0, '1908-05-16 00:00:00.000000', NULL, NULL, 0, 0, 'Poděbrady'),
(127, 1, '1912-05-06 00:00:00.000000', NULL, NULL, 0, 0, 'Poděbrady'),
(128, 1, '1917-09-03 23:00:00.000000', NULL, '1993-04-02 22:00:00.000000', 0, 0, NULL),
(129, 1, NULL, NULL, NULL, 0, 0, 'Brno'),
(130, 1, '1921-04-05 00:00:00.000000', NULL, NULL, 0, 0, 'Telč'),
(131, 0, '1863-04-05 00:02:16.000000', NULL, NULL, 0, 0, 'Ostrava'),
(132, 1, '1867-04-06 00:02:16.000000', NULL, NULL, 0, 0, 'Šlapanice'),
(133, 1, NULL, NULL, NULL, 0, 0, NULL),
(134, 1, NULL, NULL, NULL, 1, 1, NULL),
(135, 0, NULL, NULL, NULL, 0, 0, NULL),
(136, 0, NULL, NULL, NULL, 1, 1, NULL),
(137, 0, '1914-04-05 00:00:00.000000', '1914-04-07 23:00:00.000000', NULL, 0, 0, 'Příbram'),
(138, 1, '1896-02-10 00:00:00.000000', NULL, '1957-04-04 23:00:00.000000', 0, 0, 'Moravany u Brna'),
(139, 0, '1928-09-05 00:00:00.000000', '1928-09-04 23:00:00.000000', NULL, 0, 0, 'Slavkov u Brna'),
(140, 1, NULL, NULL, NULL, 0, 0, NULL),
(141, 0, '1935-04-17 00:00:00.000000', NULL, NULL, 0, 0, 'Brno'),
(142, 1, NULL, NULL, NULL, 1, 1, NULL),
(143, 1, NULL, NULL, NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `relationships`
--

CREATE TABLE `relationships` (
  `Id` int(11) NOT NULL,
  `Type` longtext NOT NULL,
  `AncestorOrHusbandPersonId` int(11) NOT NULL,
  `DescendantOrWifePersonId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `relationships`
--

INSERT INTO `relationships` (`Id`, `Type`, `AncestorOrHusbandPersonId`, `DescendantOrWifePersonId`) VALUES
(1, 'isInMarriageWith', 1, 2),
(2, 'isMotherOf', 2, 3),
(3, 'isFatherOf', 1, 3),
(4, 'isMotherOf', 2, 4),
(5, 'isFatherOf', 1, 4),
(6, 'isMotherOf', 2, 5),
(7, 'isFatherOf', 1, 5),
(8, 'isMotherOf', 2, 6),
(9, 'isFatherOf', 1, 6),
(10, 'isInMarriageWith', 3, 7),
(11, 'isMotherOf', 7, 8),
(12, 'isFatherOf', 3, 8),
(13, 'isInMarriageWith', 5, 9),
(14, 'isMotherOf', 9, 10),
(15, 'isFatherOf', 5, 10),
(16, 'isInMarriageWith', 6, 11),
(17, 'isInMarriageWith', 12, 8),
(18, 'isMotherOf', 11, 13),
(19, 'isFatherOf', 6, 13),
(20, 'isMotherOf', 9, 14),
(21, 'isFatherOf', 5, 14),
(22, 'isInMarriageWith', 10, 15),
(23, 'isInMarriageWith', 10, 16),
(24, 'isMotherOf', 16, 17),
(25, 'isFatherOf', 10, 17),
(26, 'isMotherOf', 8, 18),
(27, 'isFatherOf', 12, 18),
(28, 'isMotherOf', 8, 19),
(29, 'isFatherOf', 12, 19),
(30, 'isMotherOf', 15, 20),
(31, 'isFatherOf', 10, 20),
(32, 'isMotherOf', 8, 21),
(33, 'isFatherOf', 12, 21),
(34, 'isInMarriageWith', 13, 22),
(35, 'isMotherOf', 22, 23),
(36, 'isFatherOf', 13, 23),
(39, 'isInMarriageWith', 25, 27),
(46, 'isMotherOf', 29, 30),
(48, 'isInMarriageWith', 19, 31),
(49, 'isMotherOf', 31, 32),
(50, 'isFatherOf', 19, 32),
(63, 'isInMarriageWith', 21, 36),
(65, 'isMotherOf', 36, 37),
(66, 'isFatherOf', 21, 37),
(68, 'isMotherOf', 38, 39),
(70, 'isMotherOf', 38, 40),
(72, 'isMotherOf', 16, 41),
(73, 'isFatherOf', 10, 41),
(74, 'isInMarriageWith', 41, 29),
(75, 'isFatherOf', 41, 30),
(76, 'isInMarriageWith', 41, 38),
(77, 'isFatherOf', 41, 39),
(78, 'isFatherOf', 41, 40),
(90, 'isMotherOf', 46, 32),
(91, 'isInMarriageWith', 19, 46),
(103, 'isInMarriageWith', 32, 51),
(116, 'isInMarriageWith', 26, 56),
(117, 'isMotherOf', 56, 57),
(118, 'isFatherOf', 26, 57),
(119, 'isMotherOf', 56, 58),
(120, 'isFatherOf', 26, 58),
(121, 'isMotherOf', 56, 59),
(122, 'isFatherOf', 26, 59),
(123, 'isInMarriageWith', 60, 57),
(124, 'isInMarriageWith', 58, 61),
(125, 'isMotherOf', 61, 62),
(126, 'isFatherOf', 58, 62),
(127, 'isInMarriageWith', 63, 62),
(128, 'isMotherOf', 57, 64),
(129, 'isFatherOf', 60, 64),
(130, 'isInMarriageWith', 64, 65),
(131, 'isMotherOf', 65, 66),
(132, 'isFatherOf', 64, 66),
(133, 'isInMarriageWith', 59, 67),
(134, 'isMotherOf', 57, 68),
(135, 'isFatherOf', 60, 68),
(136, 'isInMarriageWith', 69, 62),
(137, 'isMotherOf', 62, 70),
(138, 'isFatherOf', 69, 70),
(139, 'isMotherOf', 67, 71),
(140, 'isFatherOf', 59, 71),
(141, 'isInMarriageWith', 25, 72),
(143, 'isFatherOf', 73, 18),
(144, 'isFatherOf', 73, 19),
(145, 'isFatherOf', 73, 21),
(146, 'isMotherOf', 27, 73),
(147, 'isFatherOf', 25, 73),
(148, 'isInMarriageWith', 74, 37),
(149, 'isMotherOf', 37, 75),
(150, 'isFatherOf', 74, 75),
(151, 'isMotherOf', 72, 76),
(152, 'isFatherOf', 25, 76),
(153, 'isMotherOf', 72, 77),
(154, 'isFatherOf', 25, 77),
(155, 'isInMarriageWith', 76, 78),
(156, 'isMotherOf', 78, 79),
(157, 'isFatherOf', 76, 79),
(158, 'isMotherOf', 78, 80),
(159, 'isFatherOf', 76, 80),
(160, 'isInMarriageWith', 81, 77),
(161, 'isMotherOf', 77, 82),
(162, 'isFatherOf', 81, 82),
(163, 'isInMarriageWith', 83, 80),
(164, 'isMotherOf', 80, 84),
(165, 'isFatherOf', 83, 84),
(166, 'isMotherOf', 85, 18),
(167, 'isMotherOf', 85, 19),
(168, 'isMotherOf', 85, 21),
(169, 'isInMarriageWith', 73, 85),
(170, 'isMotherOf', 36, 86),
(171, 'isFatherOf', 21, 86),
(172, 'isInMarriageWith', 82, 87),
(173, 'isMotherOf', 87, 88),
(174, 'isFatherOf', 82, 88),
(175, 'isMotherOf', 80, 89),
(176, 'isFatherOf', 83, 89),
(177, 'isInMarriageWith', 84, 90),
(178, 'isMotherOf', 90, 91),
(179, 'isFatherOf', 84, 91),
(180, 'isInMarriageWith', 93, 92),
(181, 'isMotherOf', 92, 94),
(182, 'isFatherOf', 93, 94),
(183, 'isMotherOf', 92, 95),
(184, 'isFatherOf', 93, 95),
(185, 'isInMarriageWith', 95, 96),
(186, 'isMotherOf', 96, 97),
(187, 'isFatherOf', 95, 97),
(188, 'isMotherOf', 92, 98),
(189, 'isFatherOf', 93, 98),
(190, 'isInMarriageWith', 97, 99),
(191, 'isMotherOf', 99, 100),
(192, 'isFatherOf', 97, 100),
(193, 'isMotherOf', 99, 101),
(194, 'isFatherOf', 97, 101),
(195, 'isInMarriageWith', 101, 102),
(196, 'isMotherOf', 102, 103),
(197, 'isFatherOf', 101, 103),
(198, 'isInMarriageWith', 97, 104),
(199, 'isMotherOf', 104, 105),
(200, 'isFatherOf', 97, 105),
(201, 'isInMarriageWith', 106, 98),
(202, 'isMotherOf', 98, 107),
(203, 'isFatherOf', 106, 107),
(204, 'isInMarriageWith', 100, 108),
(205, 'isInMarriageWith', 94, 109),
(206, 'isMotherOf', 109, 36),
(207, 'isFatherOf', 94, 36),
(217, 'isInMarriageWith', 114, 18),
(218, 'isMotherOf', 18, 115),
(219, 'isFatherOf', 114, 115),
(221, 'isMotherOf', 36, 117),
(222, 'isFatherOf', 21, 117),
(237, 'isInMarriageWith', 117, 124),
(238, 'isMotherOf', 108, 125),
(239, 'isFatherOf', 100, 125),
(240, 'isMotherOf', 108, 126),
(241, 'isFatherOf', 100, 126),
(242, 'isMotherOf', 108, 127),
(243, 'isFatherOf', 100, 127),
(244, 'isInMarriageWith', 125, 128),
(245, 'isInMarriageWith', 105, 129),
(246, 'isMotherOf', 129, 130),
(247, 'isFatherOf', 105, 130),
(248, 'isMotherOf', 109, 131),
(249, 'isFatherOf', 94, 131),
(250, 'isMotherOf', 98, 132),
(251, 'isFatherOf', 106, 132),
(252, 'isInMarriageWith', 75, 133),
(253, 'isInMarriageWith', 75, 134),
(254, 'isMotherOf', 134, 133),
(255, 'isFatherOf', 75, 133),
(256, 'isMotherOf', 7, 135),
(257, 'isFatherOf', 3, 135),
(258, 'isInMarriageWith', 136, 20),
(259, 'isMotherOf', 20, 137),
(260, 'isFatherOf', 136, 137),
(261, 'isInMarriageWith', 115, 138),
(262, 'isMotherOf', 51, 139),
(263, 'isFatherOf', 32, 139),
(264, 'isMotherOf', 46, 140),
(265, 'isFatherOf', 19, 140),
(266, 'isMotherOf', 51, 141),
(267, 'isFatherOf', 32, 141),
(268, 'isInMarriageWith', 17, 142),
(269, 'isMotherOf', 142, 143),
(270, 'isFatherOf', 17, 143);

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `Id` int(11) NOT NULL,
  `Name` longtext DEFAULT NULL,
  `Surname` longtext DEFAULT NULL,
  `Nickname` longtext NOT NULL,
  `Email` longtext DEFAULT NULL,
  `Password` longtext NOT NULL,
  `Salt` longtext NOT NULL,
  `RegisterDate` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`Id`, `Name`, `Surname`, `Nickname`, `Email`, `Password`, `Salt`, `RegisterDate`) VALUES
(1, 'Jan', 'Svoboda', 'svoboda', 'svoboda@gmail.com', '1q3cxEXgGqCvScEEvPlYgOQ+s8CYfXqd7uxWzxbayBk=', 'f0IUJWpCzBF7oTi5Agsil2WL7GSPghhnauz4LeKbecM=', '2020-05-22 12:45:32.159000'),
(2, 'Edvard', 'Kovář', 'kovar', 'kovar@gmail.com', 'Kgo3QU+3+4amRXV079GrosXiNW8fLWZTgSyzQduJnkg=', 'oM/Hq9uDVYImsFlJH9JFdAPxuSy0gDzNTZTO2is9NIQ=', '2020-05-22 14:15:43.794000');

-- --------------------------------------------------------

--
-- Struktura tabulky `__efmigrationshistory`
--

CREATE TABLE `__efmigrationshistory` (
  `MigrationId` varchar(95) NOT NULL,
  `ProductVersion` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Vypisuji data pro tabulku `__efmigrationshistory`
--

INSERT INTO `__efmigrationshistory` (`MigrationId`, `ProductVersion`) VALUES
('20200425133827_InitialCreate', '3.1.0'),
('20200508215727_AddedOriginalRecords', '3.1.0'),
('20200516111426_AddedBirthPlace', '3.1.0');

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `collisionrelationship`
--
ALTER TABLE `collisionrelationship`
  ADD PRIMARY KEY (`CollisionId`,`RelationshipId`),
  ADD KEY `IX_CollisionRelationship_RelationshipId` (`RelationshipId`);

--
-- Klíče pro tabulku `collisions`
--
ALTER TABLE `collisions`
  ADD PRIMARY KEY (`Id`);

--
-- Klíče pro tabulku `familytreecollision`
--
ALTER TABLE `familytreecollision`
  ADD PRIMARY KEY (`FamilyTreeId`,`CollisionId`),
  ADD KEY `IX_FamilyTreeCollision_CollisionId` (`CollisionId`);

--
-- Klíče pro tabulku `familytreeperson`
--
ALTER TABLE `familytreeperson`
  ADD PRIMARY KEY (`FamilyTreeId`,`PersonId`),
  ADD KEY `IX_FamilyTreePerson_PersonId` (`PersonId`);

--
-- Klíče pro tabulku `familytreerelationship`
--
ALTER TABLE `familytreerelationship`
  ADD PRIMARY KEY (`FamilyTreeId`,`RelationshipId`),
  ADD KEY `IX_FamilyTreeRelationship_RelationshipId` (`RelationshipId`);

--
-- Klíče pro tabulku `familytrees`
--
ALTER TABLE `familytrees`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `IX_FamilyTrees_StartPersonId` (`StartPersonId`),
  ADD KEY `IX_FamilyTrees_UserId` (`UserId`);

--
-- Klíče pro tabulku `marriages`
--
ALTER TABLE `marriages`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `IX_Marriages_RelationshipId` (`RelationshipId`);

--
-- Klíče pro tabulku `originalrecords`
--
ALTER TABLE `originalrecords`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `IX_OriginalRecords_PersonId` (`PersonId`);

--
-- Klíče pro tabulku `personnames`
--
ALTER TABLE `personnames`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `IX_PersonNames_PersonId` (`PersonId`);

--
-- Klíče pro tabulku `persons`
--
ALTER TABLE `persons`
  ADD PRIMARY KEY (`Id`);

--
-- Klíče pro tabulku `relationships`
--
ALTER TABLE `relationships`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `IX_Relationships_AncestorOrHusbandPersonId` (`AncestorOrHusbandPersonId`),
  ADD KEY `IX_Relationships_DescendantOrWifePersonId` (`DescendantOrWifePersonId`);

--
-- Klíče pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`);

--
-- Klíče pro tabulku `__efmigrationshistory`
--
ALTER TABLE `__efmigrationshistory`
  ADD PRIMARY KEY (`MigrationId`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `collisions`
--
ALTER TABLE `collisions`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pro tabulku `familytrees`
--
ALTER TABLE `familytrees`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pro tabulku `marriages`
--
ALTER TABLE `marriages`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pro tabulku `originalrecords`
--
ALTER TABLE `originalrecords`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pro tabulku `personnames`
--
ALTER TABLE `personnames`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;

--
-- AUTO_INCREMENT pro tabulku `persons`
--
ALTER TABLE `persons`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT pro tabulku `relationships`
--
ALTER TABLE `relationships`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `collisionrelationship`
--
ALTER TABLE `collisionrelationship`
  ADD CONSTRAINT `FK_CollisionRelationship_Collisions_CollisionId` FOREIGN KEY (`CollisionId`) REFERENCES `collisions` (`Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_CollisionRelationship_Relationships_RelationshipId` FOREIGN KEY (`RelationshipId`) REFERENCES `relationships` (`Id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `familytreecollision`
--
ALTER TABLE `familytreecollision`
  ADD CONSTRAINT `FK_FamilyTreeCollision_Collisions_CollisionId` FOREIGN KEY (`CollisionId`) REFERENCES `collisions` (`Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_FamilyTreeCollision_FamilyTrees_FamilyTreeId` FOREIGN KEY (`FamilyTreeId`) REFERENCES `familytrees` (`Id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `familytreeperson`
--
ALTER TABLE `familytreeperson`
  ADD CONSTRAINT `FK_FamilyTreePerson_FamilyTrees_FamilyTreeId` FOREIGN KEY (`FamilyTreeId`) REFERENCES `familytrees` (`Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_FamilyTreePerson_Persons_PersonId` FOREIGN KEY (`PersonId`) REFERENCES `persons` (`Id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `familytreerelationship`
--
ALTER TABLE `familytreerelationship`
  ADD CONSTRAINT `FK_FamilyTreeRelationship_FamilyTrees_FamilyTreeId` FOREIGN KEY (`FamilyTreeId`) REFERENCES `familytrees` (`Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_FamilyTreeRelationship_Relationships_RelationshipId` FOREIGN KEY (`RelationshipId`) REFERENCES `relationships` (`Id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `familytrees`
--
ALTER TABLE `familytrees`
  ADD CONSTRAINT `FK_FamilyTrees_Persons_StartPersonId` FOREIGN KEY (`StartPersonId`) REFERENCES `persons` (`Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_FamilyTrees_Users_UserId` FOREIGN KEY (`UserId`) REFERENCES `users` (`Id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `marriages`
--
ALTER TABLE `marriages`
  ADD CONSTRAINT `FK_Marriages_Relationships_RelationshipId` FOREIGN KEY (`RelationshipId`) REFERENCES `relationships` (`Id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `originalrecords`
--
ALTER TABLE `originalrecords`
  ADD CONSTRAINT `FK_OriginalRecords_Persons_PersonId` FOREIGN KEY (`PersonId`) REFERENCES `persons` (`Id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `personnames`
--
ALTER TABLE `personnames`
  ADD CONSTRAINT `FK_PersonNames_Persons_PersonId` FOREIGN KEY (`PersonId`) REFERENCES `persons` (`Id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `relationships`
--
ALTER TABLE `relationships`
  ADD CONSTRAINT `FK_Relationships_Persons_AncestorOrHusbandPersonId` FOREIGN KEY (`AncestorOrHusbandPersonId`) REFERENCES `persons` (`Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_Relationships_Persons_DescendantOrWifePersonId` FOREIGN KEY (`DescendantOrWifePersonId`) REFERENCES `persons` (`Id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
