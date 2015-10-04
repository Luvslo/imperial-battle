-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 08 Mar 2010 om 22:28
-- Serverversie: 5.0.90
-- PHP-Versie: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `rogierva_ib`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_adminlog`
--

CREATE TABLE IF NOT EXISTS `g03_adminlog` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp` int(11) NOT NULL default '0',
  `username` varchar(255) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `fulldesc` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=74 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_alliance`
--

CREATE TABLE IF NOT EXISTS `g03_alliance` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `tag` varchar(12) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `founder_id` int(11) NOT NULL default '0',
  `subcommander_id` int(11) NOT NULL default '0',
  `startdate` varchar(255) NOT NULL default '',
  `message` text NOT NULL,
  `message_lastedit` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=73 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_allianceforum_posts`
--

CREATE TABLE IF NOT EXISTS `g03_allianceforum_posts` (
  `id` int(11) NOT NULL auto_increment,
  `thread_id` int(11) NOT NULL default '0',
  `alliance_id` int(11) NOT NULL default '0',
  `poster_id` int(11) NOT NULL default '0',
  `text` text NOT NULL,
  `date` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=599 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_allianceforum_threads`
--

CREATE TABLE IF NOT EXISTS `g03_allianceforum_threads` (
  `id` int(11) NOT NULL auto_increment,
  `alliance_id` int(11) NOT NULL default '0',
  `subject` varchar(255) NOT NULL default '',
  `starter` int(11) NOT NULL default '0',
  `date` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=132 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_alliancenews`
--

CREATE TABLE IF NOT EXISTS `g03_alliancenews` (
  `id` int(11) NOT NULL auto_increment,
  `alliance_id` int(11) NOT NULL default '0',
  `subject` varchar(255) NOT NULL default '',
  `message` text NOT NULL,
  `player_id` int(11) NOT NULL default '0',
  `timestamp` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_defense`
--

CREATE TABLE IF NOT EXISTS `g03_defense` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `class` int(11) NOT NULL default '0',
  `firepower` int(11) NOT NULL default '0',
  `armor` int(11) NOT NULL default '0',
  `accurate` float NOT NULL default '0',
  `initiative` int(11) NOT NULL default '0',
  `depends` int(11) NOT NULL default '0',
  `cost_steel` int(11) NOT NULL default '0',
  `cost_crystal` int(11) NOT NULL default '0',
  `cost_erbium` int(11) NOT NULL default '0',
  `cost_titanium` int(11) NOT NULL default '0',
  `eta` int(11) NOT NULL default '0',
  `primary_target` int(11) NOT NULL default '0',
  `secondary_target` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_galaxy`
--

CREATE TABLE IF NOT EXISTS `g03_galaxy` (
  `id` bigint(100) NOT NULL auto_increment,
  `x` int(4) NOT NULL default '0',
  `y` int(4) NOT NULL default '0',
  `topic` varchar(255) NOT NULL default 'Imperial Battle',
  `image_url` varchar(255) NOT NULL default '',
  `commander_id` bigint(20) NOT NULL default '0',
  `moc_id` bigint(20) NOT NULL default '0',
  `mow_id` bigint(20) NOT NULL default '0',
  `moe_id` bigint(20) NOT NULL default '0',
  `fund_steel` bigint(100) NOT NULL default '0',
  `fund_crystal` bigint(100) NOT NULL default '0',
  `fund_erbium` bigint(100) NOT NULL default '0',
  `fund_titanium` bigint(100) NOT NULL default '0',
  `private` tinyint(1) NOT NULL default '0',
  `password` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_galaxyforum_posts`
--

CREATE TABLE IF NOT EXISTS `g03_galaxyforum_posts` (
  `id` int(11) NOT NULL auto_increment,
  `thread_id` int(11) NOT NULL default '0',
  `galaxy_id` int(11) NOT NULL default '0',
  `poster_id` int(11) NOT NULL default '0',
  `text` text NOT NULL,
  `date` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_galaxyforum_threads`
--

CREATE TABLE IF NOT EXISTS `g03_galaxyforum_threads` (
  `id` int(11) NOT NULL auto_increment,
  `galaxy_id` int(11) NOT NULL default '0',
  `subject` varchar(255) NOT NULL default '',
  `starter` int(11) NOT NULL default '0',
  `date` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_items`
--

CREATE TABLE IF NOT EXISTS `g03_items` (
  `id` int(11) NOT NULL auto_increment,
  `type_id` int(11) NOT NULL default '0',
  `order` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `depends` int(11) NOT NULL default '0',
  `cost_steel` int(11) NOT NULL default '0',
  `cost_crystal` int(11) NOT NULL default '0',
  `cost_erbium` int(11) NOT NULL default '0',
  `cost_titanium` int(11) NOT NULL default '0',
  `eta` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_itemtypes`
--

CREATE TABLE IF NOT EXISTS `g03_itemtypes` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(255) NOT NULL default '',
  `amount` int(11) NOT NULL default '0',
  `build_text` varchar(255) NOT NULL default '',
  `building_text` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_mail`
--

CREATE TABLE IF NOT EXISTS `g03_mail` (
  `id` int(11) NOT NULL auto_increment,
  `from_player` int(11) NOT NULL default '0',
  `to_player` int(11) NOT NULL default '0',
  `subject` varchar(255) NOT NULL default '',
  `text` text NOT NULL,
  `date` varchar(255) NOT NULL default '',
  `read` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4590 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_market`
--

CREATE TABLE IF NOT EXISTS `g03_market` (
  `id` bigint(20) NOT NULL auto_increment,
  `player_id` int(11) NOT NULL default '0',
  `steel` bigint(20) NOT NULL default '0',
  `crystal` bigint(20) NOT NULL default '0',
  `erbium` bigint(20) NOT NULL default '0',
  `titanium` bigint(20) NOT NULL default '0',
  `status` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_market_ships`
--

CREATE TABLE IF NOT EXISTS `g03_market_ships` (
  `order_id` bigint(20) NOT NULL default '0',
  `ship_id` int(11) NOT NULL default '0',
  `amount` bigint(20) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_news`
--

CREATE TABLE IF NOT EXISTS `g03_news` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `timestamp` varchar(255) NOT NULL default '',
  `username` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_playerfleet`
--

CREATE TABLE IF NOT EXISTS `g03_playerfleet` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `player_id` int(11) unsigned NOT NULL default '0',
  `target_id` int(11) unsigned NOT NULL default '0',
  `action` enum('home','attack','defend') NOT NULL default 'home',
  `action_start` int(11) unsigned NOT NULL default '0',
  `action_time` int(11) NOT NULL default '0',
  `sent_tick` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1004 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_playerfleet_ships`
--

CREATE TABLE IF NOT EXISTS `g03_playerfleet_ships` (
  `id` bigint(100) NOT NULL auto_increment,
  `player_id` int(11) NOT NULL default '0',
  `fleet_id` bigint(100) NOT NULL default '0',
  `ship_id` int(11) NOT NULL default '0',
  `type_id` int(11) NOT NULL default '0',
  `class_id` int(11) NOT NULL default '0',
  `amount` int(11) NOT NULL default '0',
  `primary` int(11) NOT NULL default '0',
  `secondary` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3129 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_playerip`
--

CREATE TABLE IF NOT EXISTS `g03_playerip` (
  `id` int(11) NOT NULL auto_increment,
  `player_id` int(11) NOT NULL default '0',
  `ip` varchar(255) NOT NULL default '',
  `lastused` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_playeritem`
--

CREATE TABLE IF NOT EXISTS `g03_playeritem` (
  `id` int(11) NOT NULL auto_increment,
  `player_id` int(11) NOT NULL default '0',
  `type_id` int(11) NOT NULL default '0',
  `item_id` int(11) NOT NULL default '0',
  `amount` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1279 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_playerlog`
--

CREATE TABLE IF NOT EXISTS `g03_playerlog` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp` int(11) NOT NULL default '0',
  `tick` int(11) NOT NULL default '0',
  `player_id` int(11) NOT NULL default '0',
  `description` varchar(255) NOT NULL default '',
  `fulldesc` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_playernews`
--

CREATE TABLE IF NOT EXISTS `g03_playernews` (
  `id` int(11) NOT NULL auto_increment,
  `player_id` int(11) NOT NULL default '0',
  `subject` varchar(255) NOT NULL default '',
  `text` text NOT NULL,
  `category` varchar(255) NOT NULL default '',
  `date` varchar(255) NOT NULL default '',
  `read` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14610 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_players`
--

CREATE TABLE IF NOT EXISTS `g03_players` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `username` varchar(25) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `ip` varchar(15) NOT NULL default '0.0.0.0',
  `hostname` varchar(255) NOT NULL default '',
  `activated` tinyint(1) NOT NULL default '0',
  `activation_code` varchar(255) NOT NULL default '',
  `rules_accepted` tinyint(1) NOT NULL default '0',
  `rulername` varchar(25) NOT NULL default '',
  `planetname` varchar(25) NOT NULL default '',
  `lastlogin` bigint(20) NOT NULL default '0',
  `galaxy_id` int(4) NOT NULL default '0',
  `galaxy_spot` int(2) NOT NULL default '0',
  `alliance_id` int(11) NOT NULL default '0',
  `res_steel` bigint(100) unsigned NOT NULL default '0',
  `res_crystal` bigint(100) unsigned NOT NULL default '0',
  `res_erbium` bigint(100) unsigned NOT NULL default '0',
  `res_titanium` bigint(100) unsigned NOT NULL default '0',
  `roid_steel` bigint(100) unsigned NOT NULL default '0',
  `roid_crystal` bigint(100) unsigned NOT NULL default '0',
  `roid_erbium` bigint(100) unsigned NOT NULL default '0',
  `roid_unused` bigint(100) unsigned NOT NULL default '0',
  `score` bigint(100) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=151 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_playerunit`
--

CREATE TABLE IF NOT EXISTS `g03_playerunit` (
  `id` int(11) NOT NULL auto_increment,
  `player_id` int(11) NOT NULL default '0',
  `type_id` int(11) NOT NULL default '0',
  `unit_id` int(11) NOT NULL default '0',
  `amount` bigint(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=361 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_politics`
--

CREATE TABLE IF NOT EXISTS `g03_politics` (
  `id` int(11) NOT NULL auto_increment,
  `galaxy_id` int(11) NOT NULL default '0',
  `player_id` int(11) NOT NULL default '0',
  `voted_on` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=54 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_productions`
--

CREATE TABLE IF NOT EXISTS `g03_productions` (
  `id` bigint(100) NOT NULL auto_increment,
  `player_id` bigint(20) NOT NULL default '0',
  `type_id` int(11) NOT NULL default '0',
  `item_id` int(11) NOT NULL default '0',
  `amount` int(11) NOT NULL default '0',
  `ready_tick` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4380 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_rules`
--

CREATE TABLE IF NOT EXISTS `g03_rules` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `cat_id` int(11) NOT NULL default '0',
  `order` int(11) NOT NULL default '0',
  `online` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_rulescat`
--

CREATE TABLE IF NOT EXISTS `g03_rulescat` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `order` int(11) NOT NULL default '0',
  `online` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_ships`
--

CREATE TABLE IF NOT EXISTS `g03_ships` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `class` int(11) NOT NULL default '0',
  `firepower` int(11) NOT NULL default '0',
  `armor` int(11) NOT NULL default '0',
  `accurate` float NOT NULL default '0',
  `initiative` int(11) NOT NULL default '0',
  `depends` int(11) NOT NULL default '0',
  `cost_steel` int(11) NOT NULL default '0',
  `cost_crystal` int(11) NOT NULL default '0',
  `cost_erbium` int(11) NOT NULL default '0',
  `cost_titanium` int(11) NOT NULL default '0',
  `eta` int(11) NOT NULL default '0',
  `fuel` int(11) NOT NULL default '0',
  `traveltime` int(11) NOT NULL default '0',
  `primary_target` int(11) NOT NULL default '0',
  `secondary_target` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_tick`
--

CREATE TABLE IF NOT EXISTS `g03_tick` (
  `id` int(11) NOT NULL auto_increment,
  `start` varchar(255) NOT NULL default '',
  `current` int(11) NOT NULL default '0',
  `last` int(11) NOT NULL default '0',
  `time_next` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_titanium_factory`
--

CREATE TABLE IF NOT EXISTS `g03_titanium_factory` (
  `id` int(11) NOT NULL auto_increment,
  `player_id` int(11) NOT NULL default '0',
  `steel_investment` int(11) NOT NULL default '0',
  `crystal_investment` int(11) NOT NULL default '0',
  `erbium_investment` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=50 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_universe`
--

CREATE TABLE IF NOT EXISTS `g03_universe` (
  `id` int(11) NOT NULL auto_increment,
  `player_id` int(11) NOT NULL default '0',
  `rulername` varchar(255) NOT NULL default '',
  `planetname` varchar(255) NOT NULL default '',
  `tag` varchar(255) NOT NULL default '',
  `score` bigint(100) unsigned NOT NULL default '0',
  `asteroids` bigint(100) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_universe_alliance`
--

CREATE TABLE IF NOT EXISTS `g03_universe_alliance` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `tag` varchar(255) NOT NULL default '',
  `total_members` int(11) NOT NULL default '0',
  `score` bigint(100) unsigned NOT NULL default '0',
  `asteroids` bigint(100) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `g03_universe_galaxy`
--

CREATE TABLE IF NOT EXISTS `g03_universe_galaxy` (
  `id` int(11) NOT NULL auto_increment,
  `galaxy_id` int(11) NOT NULL default '0',
  `x` int(11) NOT NULL default '0',
  `y` int(11) NOT NULL default '0',
  `topic` varchar(255) NOT NULL default '',
  `total_members` int(11) NOT NULL default '0',
  `score` bigint(100) unsigned NOT NULL default '0',
  `asteroids` bigint(100) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;
