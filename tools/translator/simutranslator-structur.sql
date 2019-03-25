-- phpMyAdmin SQL Dump
-- version 4.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Erstellungszeit: 20. Okt 2017 um 22:15
-- Server Version: 5.5.54-0ubuntu0.12.04.1
-- PHP-Version: 5.5.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET GLOBAL innodb_flush_log_at_trx_commit = 0;


--
-- Datenbank: `translator`
--

    $sql_table = sprintf("CREATE TABLE IF NOT EXISTS `%s` (
      `translation_id` int(11) NOT NULL AUTO_INCREMENT,
      `object_object_id` int(11) NOT NULL,
      `object_obj_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
      `object_version_version_id` int(11) NOT NULL default '0',
      `language_language_id` varchar(5) collate utf8_bin NOT NULL default '',
      `tr_text` text character set utf8 collate utf8_unicode_ci,
      `suggestion` text collate utf8_unicode_ci,
      `mod_date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
      `reservator_user_id` varchar(20) collate utf8_bin default NULL,
      `date_to` datetime default NULL,
      `author_user_id` varchar(20) collate utf8_bin NOT NULL default '',
      `update_lock` tinyint(1) default '0',
      `details_text` text CHARACTER SET utf8,
      `details_suggestion` text CHARACTER SET utf8,
      `history_text` text CHARACTER SET utf8,
      `history_suggestion` text CHARACTER SET utf8,
      `history_link_url` varchar(255) CHARACTER SET utf8  DEFAULT '',
      `history_link_suggestion` varchar(255) CHARACTER SET utf8  DEFAULT '',
      PRIMARY KEY (`translation_id`),	     
      KEY (`object_object_id`),
      KEY (`object_version_version_id`),
      KEY (`language_language_id`), 
      KEY (`object_obj_name`)
    ) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;", 
    'translations_'.$ver );

    
    
   
    
	$sql_table = sprintf("CREATE TABLE IF NOT EXISTS `%s` (
		`image_id` int(11) NOT NULL AUTO_INCREMENT,
  		`object_obj_id` int(11) NOT NULL,
  		`object_obj_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  		`object_version_version_id` int(11) NOT NULL default '0',
  		`object_obj_type` varchar(100) collate utf8_bin NOT NULL default '',
  		`image_name` varchar(255) collate utf8_bin NOT NULL default 'Image[0]',
  		`unzoomable` tinyint(1) NOT NULL default '0',
  		`image_order` varchar(100) collate utf8_bin NOT NULL default '',
  		`image_data` blob NULL,
  		`tile_size` int(11) NOT NULL default '64',
  		`filename` varchar(255) character  set utf8 collate utf8_unicode_ci NOT NULL,
  		`offset_x` int(11) NOT NULL default '0',
  		`offset_y` int(11) NOT NULL default '0',
  	PRIMARY KEY (`image_id`),	
  	KEY (`object_obj_name`),
  	KEY (`object_obj_id`),
  	KEY (`object_version_version_id`),
  	KEY (`image_name`)
	) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
    'images_'.$ver );
 





-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `language_id` varchar(5) COLLATE utf8_bin NOT NULL DEFAULT '',
  `language_name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `font1` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `font2` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `f_desc` text COLLATE utf8_bin,
  `lng_coding` varchar(20) COLLATE utf8_bin DEFAULT 'cp852',
  `lang_code2` VARCHAR(2) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL,
  PRIMARY KEY (`language_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lang_maintaint`
--

CREATE TABLE IF NOT EXISTS `lang_maintaint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_id` int(3) NOT NULL,
  `lang_id` varchar(5) COLLATE utf8_bin NOT NULL,
  `data` longtext COLLATE utf8_bin,
  `data1` longtext COLLATE utf8_bin,
  `data2` longtext COLLATE utf8_bin,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `licenses`
--

CREATE TABLE IF NOT EXISTS `licenses` (
  `license_id` int(11) NOT NULL AUTO_INCREMENT,
  `license_name` varchar(100) COLLATE utf8_bin NOT NULL,
  `license_link` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`license_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `objects`
--

CREATE TABLE IF NOT EXISTS `objects` (
  `object_id` int(11) NOT NULL AUTO_INCREMENT,
  `obj_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `version_version_id` int(11) NOT NULL DEFAULT '0',
  `obj` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `type`       varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `image_path` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `mod_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `comments` varchar(16000) COLLATE utf8_bin DEFAULT NULL,
  `obj_copyright` varchar(80) COLLATE utf8_bin DEFAULT NULL,
  `note` varchar(2550) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`object_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `property`
--

CREATE TABLE IF NOT EXISTS `property` (
  `property_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_value` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `p_name` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `having_obj_id` int(11) NOT NULL,
  `having_obj_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `having_version_version_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`property_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `translate`
--

CREATE TABLE IF NOT EXISTS `translate` (
  `lng_tr_language_id` varchar(5) COLLATE utf8_bin NOT NULL DEFAULT '',
  `translator_user_id` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT ''
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `u_user_id` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
  `pass_bin` varchar(60) COLLATE utf8_bin NOT NULL,
  `role` enum('tr1','tr2','admin','gu','painter','pakadmin') COLLATE utf8_bin NOT NULL DEFAULT 'tr1',
  `real_name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `note` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `state` enum('active','suspended','removed') COLLATE utf8_bin NOT NULL DEFAULT 'active',
  `last_login` datetime NOT NULL DEFAULT 0,
  `config4` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `config3` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `config1` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `config2` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `user_points` int(11) NOT NULL DEFAULT '0',
  `user_points_upload` int(11) NOT NULL DEFAULT '0',
  `last_edit` datetime NOT NULL DEFAULT 0,
  `ref_lang` varchar(5) COLLATE utf8_bin NOT NULL DEFAULT '',
  `user_lang` varchar(5) COLLATE utf8_bin NOT NULL DEFAULT '',
  `set_enabled` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`u_user_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `versions`
--

CREATE TABLE IF NOT EXISTS `versions` (
  `version_id` int(11) NOT NULL AUTO_INCREMENT,
  `v_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `tile_size` int(11) NOT NULL DEFAULT '64',
  `maintainer_user_id` varchar(20) COLLATE utf8_bin NOT NULL,
  `maintainer_user_id2` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `maintainer_user_id3` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `activ` int(1) NOT NULL DEFAULT '0',
  `lng_disabled` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `htmllink` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `open_source` tinyint(1) NOT NULL DEFAULT '0',
  `open_source_link` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `license` int(11) DEFAULT '0',
  `show_images` INT(1) NULL DEFAULT NULL,
  PRIMARY KEY (`version_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


--
-- Indexes for table `lang_maintaint`
--
ALTER TABLE `lang_maintaint`
 ADD KEY `set_lang` (`lang_id`,`set_id`);

--
-- Indexes for table `objects`
--
ALTER TABLE `objects`
 ADD Key (`version_version_id`),
 ADD KEY (`subversion`),
 ADD KEY (`obj_name`), 
 ADD KEY (`obj`),
 ADD KEY (`type`);
--
-- Indexes for table `property`
--
ALTER TABLE `property`
 ADD KEY (`having_obj_id`),
 ADD KEY (`having_version_version_id`),
 ADD KEY (`having_obj_name`),
 ADD KEY (`p_name`), 
 ADD KEY (`p_value`);

--
-- Indexes for table `translate`
--
ALTER TABLE `translate`
 ADD UNIQUE KEY `lng_tr_language_id` (`lng_tr_language_id`,`translator_user_id`);
ALTER TABLE `translate` add key (`translator_user_id`);   
--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD KEY `languser` (`state`,`email`);

