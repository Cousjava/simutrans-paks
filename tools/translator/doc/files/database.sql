-- phpMyAdmin SQL Dump
-- version 2.6.4-pl2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 22, 2005 at 03:08 AM
-- Server version: 4.0.25
-- PHP Version: 4.3.11
--
-- Database: `st_translator`
--

-- --------------------------------------------------------

--
-- Table structure for table `Images`
--

CREATE TABLE `Images` (
  `Object_obj_name` varchar(100) NOT NULL default '',
  `Object_Version_version_id` int(11) NOT NULL default '0',
  `image_name` varchar(30) NOT NULL default 'Image[0]',
  `unzoomable` tinyint(1) NOT NULL default '0',
  `image_data` blob NOT NULL,
  PRIMARY KEY  (`Object_obj_name`,`Object_Version_version_id`,`image_name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `Languages`
--

CREATE TABLE `Languages` (
  `language_id` varchar(5) NOT NULL default '',
  `language_name` varchar(255) NOT NULL default '',
  `font1` varchar(50) default NULL,
  `font2` varchar(50) default NULL,
  `f_desc` text,
  `lng_coding` varchar(20) default 'cp852',
  PRIMARY KEY  (`language_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `Objects`
--

CREATE TABLE `Objects` (
  `obj_name` varchar(100) NOT NULL default '',
  `Version_version_id` int(11) NOT NULL default '0',
  `obj` varchar(50) NOT NULL default '',
  `image_path` varchar(255) NOT NULL default '',
  `img_dsc` text,
  `obj_note` varchar(255) default NULL,
  `obj_copyright` varchar(15) default NULL,
  `img_state` enum('discarded','active','pending','dummy') NOT NULL default 'dummy',
  PRIMARY KEY  (`obj_name`,`Version_version_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `Property`
--

CREATE TABLE `Property` (
  `p_value` varchar(100) NOT NULL default '',
  `p_name` varchar(50) NOT NULL default '',
  `having_obj_name` varchar(100) NOT NULL default '',
  `having_Version_version_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`p_name`,`having_obj_name`,`having_Version_version_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `Translate`
--

CREATE TABLE `Translate` (
  `lng_tr_language_id` varchar(5) NOT NULL default '',
  `translator_user_id` varchar(20) NOT NULL default '',
  UNIQUE KEY `lng_tr_language_id` (`lng_tr_language_id`,`translator_user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `Translations`
--

CREATE TABLE `Translations` (
  `Object_obj_name` varchar(100) NOT NULL default '',
  `Object_Version_version_id` int(11) NOT NULL default '0',
  `language_language_id` varchar(5) NOT NULL default '',
  `tr_text` text,
  `suggestion` text,
  `mod_date` timestamp(14) NOT NULL,
  `reservator_user_id` varchar(20) default NULL,
  `date_to` datetime default NULL,
  `author_user_id` varchar(20) NOT NULL default '',
  `update_lock` tinyint(1) default '0',
  PRIMARY KEY  (`Object_obj_name`,`Object_Version_version_id`,`language_language_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `u_user_id` varchar(20) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `role` enum('tr1','tr2','admin','gu','painter') NOT NULL default 'tr1',
  `real_name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `note` varchar(255) default NULL,
  `state` enum('active','suspended','removed') NOT NULL default 'active',
  `last_login` timestamp(14) NOT NULL,
  `config4` int(11) default NULL,
  `config3` int(11) default NULL,
  `config1` int(11) default NULL,
  `config2` int(11) default NULL,
  PRIMARY KEY  (`u_user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `Versions`
--

CREATE TABLE `Versions` (
  `version_id` int(11) NOT NULL default '0',
  `v_name` varchar(50) NOT NULL default '',
  `tile_size` tinyint(3) unsigned NOT NULL default '64',
  `maintainer_user_id` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`version_id`)
) TYPE=MyISAM;
