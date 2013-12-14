CREATE TABLE IF NOT EXISTS `%prefix%_admins_servers` (
  `admin_id` int(11) DEFAULT NULL,
  `server_id` int(11) DEFAULT NULL,
  `custom_flags` varchar(32) NOT NULL,
  `use_static_bantime` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`admin_id`,`server_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `%prefix%_amxadmins` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `access` varchar(32) DEFAULT NULL,
  `flags` varchar(32) DEFAULT NULL,
  `steamid` varchar(32) DEFAULT NULL,
  `nickname` varchar(32) DEFAULT NULL,
  `icq` int(9) DEFAULT NULL,
  `ashow` int(11) DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  `expired` int(11) DEFAULT NULL,
  `days` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `steamid` (`steamid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `%prefix%_bans` (
  `bid` int(11) NOT NULL AUTO_INCREMENT,
  `player_ip` varchar(32) DEFAULT NULL,
  `player_id` varchar(35) DEFAULT NULL,
  `player_nick` varchar(100) DEFAULT 'Unknown',
  `admin_ip` varchar(32) DEFAULT NULL,
  `admin_id` varchar(35) DEFAULT 'Unknown',
  `admin_nick` varchar(100) DEFAULT 'Unknown',
  `ban_type` varchar(10) DEFAULT 'S',
  `ban_reason` varchar(100) DEFAULT NULL,
  `cs_ban_reason` varchar(100) DEFAULT NULL,
  `ban_created` int(11) DEFAULT NULL,
  `ban_length` int(11) DEFAULT NULL,
  `server_ip` varchar(32) DEFAULT NULL,
  `server_name` varchar(100) DEFAULT 'Unknown',
  `ban_kicks` int(11) NOT NULL DEFAULT '0',
  `expired` int(1) NOT NULL DEFAULT '0',
  `imported` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bid`),
  KEY `player_id` (`player_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `%prefix%_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(35) DEFAULT NULL,
  `comment` text,
  `email` varchar(100) DEFAULT NULL,
  `addr` varchar(32) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `bid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `%prefix%_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upload_time` int(11) DEFAULT NULL,
  `down_count` int(11) DEFAULT NULL,
  `bid` int(11) DEFAULT NULL,
  `demo_file` varchar(100) DEFAULT NULL,
  `demo_real` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `comment` text,
  `name` varchar(64) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `addr` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `%prefix%_flagged` (
  `fid` int(11) NOT NULL AUTO_INCREMENT,
  `player_ip` varchar(32) DEFAULT NULL,
  `player_id` varchar(35) DEFAULT NULL,
  `player_nick` varchar(100) DEFAULT 'Unknown',
  `admin_ip` varchar(32) DEFAULT NULL,
  `admin_id` varchar(35) DEFAULT NULL,
  `admin_nick` varchar(100) DEFAULT 'Unknown',
  `reason` varchar(100) DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  `length` int(11) DEFAULT NULL,
  `server_ip` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`fid`),
  KEY `player_id` (`player_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `%prefix%_levels` (
  `level` int(12) NOT NULL DEFAULT '0',
  `bans_add` enum('yes','no') DEFAULT 'no',
  `bans_edit` enum('yes','no','own') DEFAULT 'no',
  `bans_delete` enum('yes','no','own') DEFAULT 'no',
  `bans_unban` enum('yes','no','own') DEFAULT 'no',
  `bans_import` enum('yes','no') DEFAULT 'no',
  `bans_export` enum('yes','no') DEFAULT 'no',
  `amxadmins_view` enum('yes','no') DEFAULT 'no',
  `amxadmins_edit` enum('yes','no') DEFAULT 'no',
  `webadmins_view` enum('yes','no') DEFAULT 'no',
  `webadmins_edit` enum('yes','no') DEFAULT 'no',
  `websettings_view` enum('yes','no') DEFAULT 'no',
  `websettings_edit` enum('yes','no') DEFAULT 'no',
  `permissions_edit` enum('yes','no') DEFAULT 'no',
  `prune_db` enum('yes','no') DEFAULT 'no',
  `servers_edit` enum('yes','no') DEFAULT 'no',
  `ip_view` enum('yes','no') DEFAULT 'no',
  PRIMARY KEY (`level`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `%prefix%_levels` (`level`, `bans_add`, `bans_edit`, `bans_delete`, `bans_unban`, `bans_import`, `bans_export`, `amxadmins_view`, `amxadmins_edit`, `webadmins_view`, `webadmins_edit`, `websettings_view`, `websettings_edit`, `permissions_edit`, `prune_db`, `servers_edit`, `ip_view`) VALUES
(1, 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes');

CREATE TABLE IF NOT EXISTS `%prefix%_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(11) DEFAULT NULL,
  `ip` varchar(32) DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  `action` varchar(64) DEFAULT NULL,
  `remarks` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `%prefix%_logs` (`id`, `timestamp`, `ip`, `username`, `action`, `remarks`) VALUES
(1, UNIX_TIMESTAMP(), '127.0.0.1', 'admin', 'Install', 'Installation CS:Bans 1.0');

CREATE TABLE IF NOT EXISTS `%prefix%_reasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reason` varchar(100) DEFAULT NULL,
  `static_bantime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `%prefix%_reasons_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setname` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `%prefix%_reasons_to_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setid` int(11) NOT NULL,
  `reasonid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `%prefix%_serverinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(11) DEFAULT NULL,
  `hostname` varchar(100) DEFAULT 'Unknown',
  `address` varchar(100) DEFAULT NULL,
  `gametype` varchar(32) DEFAULT NULL,
  `rcon` varchar(32) DEFAULT NULL,
  `amxban_version` varchar(32) DEFAULT NULL,
  `amxban_motd` varchar(250) DEFAULT NULL,
  `motd_delay` int(10) DEFAULT '10',
  `amxban_menu` int(10) NOT NULL DEFAULT '1',
  `reasons` int(10) DEFAULT NULL,
  `timezone_fixx` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `%prefix%_usermenu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pos` int(11) DEFAULT NULL,
  `activ` tinyint(1) NOT NULL DEFAULT '1',
  `lang_key` varchar(64) DEFAULT NULL,
  `url` varchar(64) DEFAULT NULL,
  `lang_key2` varchar(64) DEFAULT NULL,
  `url2` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

INSERT INTO `%prefix%_usermenu` VALUES ('1', '1', '1', '_HOME', '/site/index', '_HOME', '/site/index');
INSERT INTO `%prefix%_usermenu` VALUES ('2', '2', '1', '_BANLIST', '/bans/index', '_BANLIST', '/bans/index');
INSERT INTO `%prefix%_usermenu` VALUES ('3', '3', '1', '_ADMLIST', '/amxadmins/index', '_ADMLIST', '/amxadmins/index');
INSERT INTO `%prefix%_usermenu` VALUES ('5', '5', '1', '_SERVER', '/serverinfo/index', '_SERVER', '/serverinfo/index');

CREATE TABLE IF NOT EXISTS `%prefix%_webadmins` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `level` int(11) DEFAULT '99',
  `logcode` varchar(64) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `last_action` int(11) DEFAULT NULL,
  `try` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `%prefix%_webconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cookie` varchar(32) DEFAULT NULL,
  `bans_per_page` int(11) DEFAULT NULL,
  `design` varchar(32) DEFAULT NULL,
  `banner` varchar(64) DEFAULT NULL,
  `banner_url` varchar(128) NOT NULL,
  `default_lang` varchar(32) DEFAULT NULL,
  `start_page` varchar(64) DEFAULT NULL,
  `show_comment_count` int(1) DEFAULT '1',
  `show_demo_count` int(1) DEFAULT '1',
  `show_kick_count` int(1) DEFAULT '1',
  `demo_all` int(1) NOT NULL DEFAULT '0',
  `comment_all` int(1) NOT NULL DEFAULT '0',
  `use_capture` int(1) DEFAULT '1',
  `max_file_size` int(11) DEFAULT '2',
  `file_type` varchar(64) DEFAULT 'dem,zip,rar,jpg,gif',
  `auto_prune` int(1) NOT NULL DEFAULT '0',
  `max_offences` smallint(6) NOT NULL DEFAULT '10',
  `max_offences_reason` varchar(128) NOT NULL DEFAULT 'max offences reached',
  `use_demo` int(1) DEFAULT '1',
  `use_comment` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `%prefix%_webconfig` (`id`, `cookie`, `bans_per_page`, `design`, `banner`, `banner_url`, `default_lang`, `start_page`, `show_comment_count`, `show_demo_count`, `show_kick_count`, `demo_all`, `comment_all`, `use_capture`, `max_file_size`, `file_type`, `auto_prune`, `max_offences`, `max_offences_reason`, `use_demo`, `use_comment`) VALUES
(1, 'csbans', 50, 'default', 'amxbans.png', 'http://craft-soft.ru', 'russian', '/site/index', 1, 1, 1, 0, 0, 1, 2, 'dem,zip,rar,jpg,gif,png', 0, 10, 'max offences reached', 1, 1);
