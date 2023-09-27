-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 28. Mrz 2019 um 10:43
-- Server-Version: 10.1.36-MariaDB
-- PHP-Version: 7.2.11

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `redbottledb`
--
CREATE DATABASE IF NOT EXISTS `redbottledb` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `redbottledb`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `attributefuersportstaette`
--

DROP TABLE IF EXISTS `attributefuersportstaette`;
CREATE TABLE `attributefuersportstaette` (
  `AName` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Typ` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `attributefuersportstaetteauswahlwerte`
--

DROP TABLE IF EXISTS `attributefuersportstaetteauswahlwerte`;
CREATE TABLE `attributefuersportstaetteauswahlwerte` (
  `AName` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Wert` varchar(60) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `attributefuerunterkunft`
--

DROP TABLE IF EXISTS `attributefuerunterkunft`;
CREATE TABLE `attributefuerunterkunft` (
  `AName` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Typ` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `attributefuerunterkunftauswahlwerte`
--

DROP TABLE IF EXISTS `attributefuerunterkunftauswahlwerte`;
CREATE TABLE `attributefuerunterkunftauswahlwerte` (
  `AName` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Wert` varchar(60) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `eignungsass`
--

DROP TABLE IF EXISTS `eignungsass`;
CREATE TABLE `eignungsass` (
  `SAName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `SSID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kontakte_sportstaette`
--

DROP TABLE IF EXISTS `kontakte_sportstaette`;
CREATE TABLE `kontakte_sportstaette` (
  `KPID` int(11) NOT NULL,
  `SSID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kontakte_unterkunft`
--

DROP TABLE IF EXISTS `kontakte_unterkunft`;
CREATE TABLE `kontakte_unterkunft` (
  `KPID` int(11) NOT NULL,
  `UID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kontaktpersonen`
--

DROP TABLE IF EXISTS `kontaktpersonen`;
CREATE TABLE `kontaktpersonen` (
  `ID` int(11) NOT NULL,
  `Vorname` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Nachname` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Telefonnummer` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Mobilnummer` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Fax` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `MailAdresse` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Funktion` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Kommentar` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sportart`
--

DROP TABLE IF EXISTS `sportart`;
CREATE TABLE `sportart` (
  `Name` varchar(40) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sportstaette`
--

DROP TABLE IF EXISTS `sportstaette`;
CREATE TABLE `sportstaette` (
  `ID` int(11) NOT NULL,
  `Name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Telefonnummer` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `MailAdresse` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Internetseite` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Strasse` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Hausnummer` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Postleitzahl` char(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Ort` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Land` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Deutschland',
  `Koordinaten` char(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Kommentar` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `KommentarPreis` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ssbesitzt_bool`
--

DROP TABLE IF EXISTS `ssbesitzt_bool`;
CREATE TABLE `ssbesitzt_bool` (
  `AName` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `SSID` int(11) NOT NULL,
  `Wert` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ssbesitzt_char`
--

DROP TABLE IF EXISTS `ssbesitzt_char`;
CREATE TABLE `ssbesitzt_char` (
  `SSID` int(11) NOT NULL,
  `AName` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Wert` varchar(60) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ssbesitzt_int`
--

DROP TABLE IF EXISTS `ssbesitzt_int`;
CREATE TABLE `ssbesitzt_int` (
  `SSID` int(11) NOT NULL,
  `AName` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Wert` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ubesitzt_bool`
--

DROP TABLE IF EXISTS `ubesitzt_bool`;
CREATE TABLE `ubesitzt_bool` (
  `AName` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `UID` int(11) NOT NULL,
  `Wert` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ubesitzt_char`
--

DROP TABLE IF EXISTS `ubesitzt_char`;
CREATE TABLE `ubesitzt_char` (
  `UID` int(11) NOT NULL,
  `AName` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Wert` varchar(60) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ubesitzt_int`
--

DROP TABLE IF EXISTS `ubesitzt_int`;
CREATE TABLE `ubesitzt_int` (
  `UID` int(11) NOT NULL,
  `AName` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Wert` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `unterkunft`
--

DROP TABLE IF EXISTS `unterkunft`;
CREATE TABLE `unterkunft` (
  `ID` int(11) NOT NULL,
  `Name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Telefonnummer` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `MailAdresse` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Internetseite` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Strasse` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Hausnummer` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Postleitzahl` char(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Ort` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `Land` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Deutschland',
  `Koordinaten` char(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Kommentar` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `KommentarPreis` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `zuordnungsaa`
--

DROP TABLE IF EXISTS `zuordnungsaa`;
CREATE TABLE `zuordnungsaa` (
  `SAName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `AName` varchar(30) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `attributefuersportstaette`
--
ALTER TABLE `attributefuersportstaette`
  ADD PRIMARY KEY (`AName`);

--
-- Indizes für die Tabelle `attributefuersportstaetteauswahlwerte`
--
ALTER TABLE `attributefuersportstaetteauswahlwerte`
  ADD PRIMARY KEY (`AName`,`Wert`);

--
-- Indizes für die Tabelle `attributefuerunterkunft`
--
ALTER TABLE `attributefuerunterkunft`
  ADD PRIMARY KEY (`AName`);

--
-- Indizes für die Tabelle `attributefuerunterkunftauswahlwerte`
--
ALTER TABLE `attributefuerunterkunftauswahlwerte`
  ADD PRIMARY KEY (`AName`,`Wert`);

--
-- Indizes für die Tabelle `eignungsass`
--
ALTER TABLE `eignungsass`
  ADD PRIMARY KEY (`SAName`,`SSID`),
  ADD KEY `SSID` (`SSID`);

--
-- Indizes für die Tabelle `kontakte_sportstaette`
--
ALTER TABLE `kontakte_sportstaette`
  ADD PRIMARY KEY (`SSID`,`KPID`),
  ADD KEY `KPID` (`KPID`);

--
-- Indizes für die Tabelle `kontakte_unterkunft`
--
ALTER TABLE `kontakte_unterkunft`
  ADD PRIMARY KEY (`KPID`,`UID`);

--
-- Indizes für die Tabelle `kontaktpersonen`
--
ALTER TABLE `kontaktpersonen`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `sportart`
--
ALTER TABLE `sportart`
  ADD PRIMARY KEY (`Name`);

--
-- Indizes für die Tabelle `sportstaette`
--
ALTER TABLE `sportstaette`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `ssbesitzt_bool`
--
ALTER TABLE `ssbesitzt_bool`
  ADD PRIMARY KEY (`AName`,`SSID`),
  ADD KEY `SID` (`SSID`);

--
-- Indizes für die Tabelle `ssbesitzt_char`
--
ALTER TABLE `ssbesitzt_char`
  ADD PRIMARY KEY (`SSID`,`AName`,`Wert`),
  ADD KEY `AName` (`AName`);

--
-- Indizes für die Tabelle `ssbesitzt_int`
--
ALTER TABLE `ssbesitzt_int`
  ADD PRIMARY KEY (`SSID`,`AName`),
  ADD KEY `AName` (`AName`);

--
-- Indizes für die Tabelle `ubesitzt_bool`
--
ALTER TABLE `ubesitzt_bool`
  ADD PRIMARY KEY (`AName`,`UID`),
  ADD KEY `UID` (`UID`);

--
-- Indizes für die Tabelle `ubesitzt_char`
--
ALTER TABLE `ubesitzt_char`
  ADD PRIMARY KEY (`UID`,`AName`,`Wert`),
  ADD KEY `AName` (`AName`);

--
-- Indizes für die Tabelle `ubesitzt_int`
--
ALTER TABLE `ubesitzt_int`
  ADD PRIMARY KEY (`UID`,`AName`),
  ADD KEY `AName` (`AName`);

--
-- Indizes für die Tabelle `unterkunft`
--
ALTER TABLE `unterkunft`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `zuordnungsaa`
--
ALTER TABLE `zuordnungsaa`
  ADD PRIMARY KEY (`SAName`,`AName`),
  ADD KEY `AName` (`AName`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `kontaktpersonen`
--
ALTER TABLE `kontaktpersonen`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `sportstaette`
--
ALTER TABLE `sportstaette`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `unterkunft`
--
ALTER TABLE `unterkunft`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `attributefuersportstaetteauswahlwerte`
--
ALTER TABLE `attributefuersportstaetteauswahlwerte`
  ADD CONSTRAINT `attributefuersportstaetteauswahlwerte_ibfk_1` FOREIGN KEY (`AName`) REFERENCES `attributefuersportstaette` (`AName`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `attributefuerunterkunftauswahlwerte`
--
ALTER TABLE `attributefuerunterkunftauswahlwerte`
  ADD CONSTRAINT `attributefuerunterkunftauswahlwerte_ibfk_1` FOREIGN KEY (`AName`) REFERENCES `attributefuerunterkunft` (`AName`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `eignungsass`
--
ALTER TABLE `eignungsass`
  ADD CONSTRAINT `eignungsass_ibfk_1` FOREIGN KEY (`SAName`) REFERENCES `sportart` (`Name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eignungsass_ibfk_2` FOREIGN KEY (`SSID`) REFERENCES `sportstaette` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `kontakte_sportstaette`
--
ALTER TABLE `kontakte_sportstaette`
  ADD CONSTRAINT `kontakte_sportstaette_ibfk_1` FOREIGN KEY (`KPID`) REFERENCES `kontaktpersonen` (`ID`),
  ADD CONSTRAINT `kontakte_sportstaette_ibfk_2` FOREIGN KEY (`SSID`) REFERENCES `sportstaette` (`ID`);

--
-- Constraints der Tabelle `ssbesitzt_bool`
--
ALTER TABLE `ssbesitzt_bool`
  ADD CONSTRAINT `ssbesitzt_bool_ibfk_1` FOREIGN KEY (`AName`) REFERENCES `attributefuersportstaette` (`AName`),
  ADD CONSTRAINT `ssbesitzt_bool_ibfk_2` FOREIGN KEY (`SSID`) REFERENCES `sportstaette` (`ID`);

--
-- Constraints der Tabelle `ssbesitzt_char`
--
ALTER TABLE `ssbesitzt_char`
  ADD CONSTRAINT `ssbesitzt_char_ibfk_1` FOREIGN KEY (`SSID`) REFERENCES `sportstaette` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ssbesitzt_char_ibfk_2` FOREIGN KEY (`AName`) REFERENCES `attributefuersportstaette` (`AName`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `ssbesitzt_int`
--
ALTER TABLE `ssbesitzt_int`
  ADD CONSTRAINT `ssbesitzt_int_ibfk_1` FOREIGN KEY (`AName`) REFERENCES `attributefuersportstaette` (`AName`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ssbesitzt_int_ibfk_2` FOREIGN KEY (`SSID`) REFERENCES `sportstaette` (`ID`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `ubesitzt_bool`
--
ALTER TABLE `ubesitzt_bool`
  ADD CONSTRAINT `ubesitzt_bool_ibfk_1` FOREIGN KEY (`AName`) REFERENCES `attributefuerunterkunft` (`AName`),
  ADD CONSTRAINT `ubesitzt_bool_ibfk_2` FOREIGN KEY (`UID`) REFERENCES `unterkunft` (`ID`);

--
-- Constraints der Tabelle `ubesitzt_char`
--
ALTER TABLE `ubesitzt_char`
  ADD CONSTRAINT `ubesitzt_char_ibfk_1` FOREIGN KEY (`AName`) REFERENCES `attributefuerunterkunft` (`AName`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ubesitzt_char_ibfk_2` FOREIGN KEY (`UID`) REFERENCES `unterkunft` (`ID`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `ubesitzt_int`
--
ALTER TABLE `ubesitzt_int`
  ADD CONSTRAINT `ubesitzt_int_ibfk_1` FOREIGN KEY (`AName`) REFERENCES `attributefuerunterkunft` (`AName`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ubesitzt_int_ibfk_2` FOREIGN KEY (`UID`) REFERENCES `unterkunft` (`ID`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `zuordnungsaa`
--
ALTER TABLE `zuordnungsaa`
  ADD CONSTRAINT `zuordnungsaa_ibfk_1` FOREIGN KEY (`SAName`) REFERENCES `sportart` (`Name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `zuordnungsaa_ibfk_3` FOREIGN KEY (`AName`) REFERENCES `attributefuersportstaette` (`AName`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
