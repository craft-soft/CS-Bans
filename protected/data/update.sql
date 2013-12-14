ALTER TABLE `%prefix%_admins_servers` ADD PRIMARY KEY (`admin_id`,`server_id`);

UPDATE `%prefix%_usermenu` SET `url` = '/site/index', `url2` = '/site/index' WHERE `lang_key` = '_HOME';
UPDATE `%prefix%_usermenu` SET `url` = '/bans/index', `url2` = '/bans/index' WHERE `lang_key` = '_BANLIST';
UPDATE `%prefix%_usermenu` SET `url` = '/amxadmins/index', `url2` = '/amxadmins/index' WHERE `lang_key` = '_ADMLIST';
DELETE FROM `%prefix%_usermenu` WHERE `lang_key` = '_SEARCH';
UPDATE `%prefix%_usermenu` SET `url` = '/serverinfo/index', `url2` = '/serverinfo/index' WHERE `lang_key` = '_SERVER';
DELETE FROM `%prefix%_usermenu` WHERE `lang_key` = '_LOGIN';
UPDATE `%prefix%_webconfig` SET `start_page` = '/site/index';
