CREATE TABLE `{invoice}` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `random_id` int(10) unsigned NOT NULL,
    `module` varchar(64) NOT NULL,
    `part` varchar(64) NOT NULL,
    `item` int(10) unsigned NOT NULL,
    `amount` double(16,2) NOT NULL,
    `adapter` varchar(64) NOT NULL,
    `description` text,
    `uid` int(10) unsigned NOT NULL,
    `ip` char(15) NOT NULL,
    `status` tinyint(1) unsigned NOT NULL,
    `time_create` int(10) unsigned NOT NULL,
    `time_payment` int(10) unsigned NOT NULL,
    `time_cancel` int(10) unsigned NOT NULL,
    `note` text,
    PRIMARY KEY  (`id`)
);

CREATE TABLE `{gateway}` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `title` varchar(255) NOT NULL,
    `path` varchar(64) NOT NULL,
    `description` text,
    `image` varchar(255) NOT NULL,
    `status` tinyint(1) unsigned NOT NULL,
    `type` enum('online','offline') NOT NULL,
    `option` text,
    PRIMARY KEY  (`id`)
);

CREATE TABLE `{log}` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `invoice` int(10) unsigned NOT NULL,
    `gateway` varchar(64) NOT NULL,
    `time_create` int(10) unsigned NOT NULL,
    `uid` int(10) unsigned NOT NULL,
    `amount` double(16,2) NOT NULL,
    `authority` varchar(255) NOT NULL,
    `status` tinyint(1) unsigned NOT NULL,
    `ip` char(15) NOT NULL,
    `value` text,
    `message` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
);

CREATE TABLE `{processing}` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `uid` int(10) unsigned NOT NULL,
    `ip` char(15) NOT NULL,
    `invoice` int(10) unsigned NOT NULL,
    `random_id` int(10) unsigned NOT NULL,
    `adapter` varchar(64) NOT NULL,
    `time_create` int(10) unsigned NOT NULL,
    PRIMARY KEY  (`id`)
);