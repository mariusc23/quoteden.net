CREATE TABLE IF NOT EXISTS `authors` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(500) CHARACTER SET utf8 NOT NULL,
 `bio` longtext CHARACTER SET utf8,
 PRIMARY KEY (`id`),
 KEY `name` (`name`(333))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `categories` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(255) CHARACTER SET utf8 NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `quotes` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `text` longtext CHARACTER SET utf8 NOT NULL,
 `author_id` bigint(20) NOT NULL,
 `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 PRIMARY KEY (`id`),
 KEY `author_id` (`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `quote_category` (
 `quote_id` bigint(20) unsigned NOT NULL,
 `category_id` bigint(20) unsigned NOT NULL,
 PRIMARY KEY  (`quote_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `users` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `username` varchar(255) NOT NULL,
 `password` varchar(255) NOT NULL,
 `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `name` varchar(255) DEFAULT NULL,
 `email` varchar(255) NOT NULL,
 `logins` int(12) unsigned NOT NULL,
 `last_login` int(12) unsigned NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `username` (`username`),
 UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `roles` (
 `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `name` varchar(32) NOT NULL,
 `description` varchar(255) NOT NULL,
 PRIMARY KEY  (`id`),
 UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


INSERT INTO `roles` (`id`, `name`, `description`) VALUES(1, 'login', 'Login privileges, granted after account confirmation.');
INSERT INTO `roles` (`id`, `name`, `description`) VALUES(2, 'admin', 'Administrative user, has access to everything.');
INSERT INTO `roles` (`id`, `name`, `description`) VALUES(3, 'author', 'Can create, delete and edit own content.');


CREATE TABLE IF NOT EXISTS `roles_users` (
 `user_id` int(10) UNSIGNED NOT NULL,
 `role_id` int(10) UNSIGNED NOT NULL,
 PRIMARY KEY  (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `votes` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `quote_id` bigint(20) unsigned NOT NULL,
 `voter` varchar(23) NOT NULL,
 `user_id` int(10) DEFAULT NULL,
 `rating` int(3) unsigned NOT NULL DEFAULT '0',
 `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 KEY `quote_id` (`quote_id`,`voter`),
 KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `vote_averages` (
 `quote_id` bigint(20) unsigned NOT NULL,
 `average` float unsigned NOT NULL DEFAULT '0',
 `count` bigint(20) unsigned NOT NULL,
 `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`quote_id`),
 KEY `average` (`average`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

