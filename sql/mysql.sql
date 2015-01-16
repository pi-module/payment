CREATE TABLE `{invoice}` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `random_id` int(10) unsigned NOT NULL default '0',
    `module` varchar(64) NOT NULL default '',
    `part` varchar(64) NOT NULL default '',
    `item` int(10) unsigned NOT NULL default '0',
    `amount` double(16,2) NOT NULL default '0.00',
    `adapter` varchar(64) NOT NULL default '',
    `description` text,
    `uid` int(10) unsigned NOT NULL default '0',
    `ip` char(15) NOT NULL default '',
    `status` tinyint(1) unsigned NOT NULL default '0',
    `time_create` int(10) unsigned NOT NULL default '0',
    `time_payment` int(10) unsigned NOT NULL default '0',
    `time_cancel` int(10) unsigned NOT NULL default '0',
    `note` text,
    PRIMARY KEY  (`id`),
    UNIQUE KEY `random_id` (`random_id`),
    KEY `module` (`module`),
    KEY `part` (`part`),
    KEY `item` (`item`),
    KEY `amount` (`amount`),
    KEY `adapter` (`adapter`),
    KEY `uid` (`uid`),
    KEY `status` (`status`),
    KEY `uid_status` (`uid`, `status`),
    KEY `time_create` (`time_create`),
    KEY `id_time_create` (`id`, `time_create`)
);

CREATE TABLE `{gateway}` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `title` varchar(255) NOT NULL default '',
    `path` varchar(64) NOT NULL default '',
    `description` text,
    `image` varchar(255) NOT NULL default '',
    `status` tinyint(1) unsigned NOT NULL default '0',
    `type` enum('online','offline') NOT NULL default 'online',
    `option` text,
    PRIMARY KEY  (`id`),
    KEY `status` (`status`)
);

CREATE TABLE `{log}` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `invoice` int(10) unsigned NOT NULL default '0',
    `gateway` varchar(64) NOT NULL default '',
    `time_create` int(10) unsigned NOT NULL default '0',
    `uid` int(10) unsigned NOT NULL default '0',
    `amount` double(16,2) NOT NULL default '0.00',
    `authority` varchar(255) NOT NULL default '',
    `status` tinyint(1) unsigned NOT NULL default '0',
    `ip` char(15) NOT NULL default '',
    `value` text,
    `message` varchar(255) NOT NULL default '',
    PRIMARY KEY  (`id`),
    KEY `uid` (`uid`),
    KEY `ip` (`ip`)
);

CREATE TABLE `{processing}` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `uid` int(10) unsigned NOT NULL default '0',
    `ip` char(15) NOT NULL default '',
    `invoice` int(10) unsigned NOT NULL default '0',
    `random_id` int(10) unsigned NOT NULL default '0',
    `adapter` varchar(64) NOT NULL default '',
    `time_create` int(10) unsigned NOT NULL default '0',
    PRIMARY KEY  (`id`),
    UNIQUE KEY `random_id` (`random_id`),
    KEY `uid` (`uid`),
    KEY `invoice` (`invoice`),
    KEY `ip` (`ip`)
);