CREATE TABLE IF NOT EXISTS `k_authors` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(500) CHARACTER SET utf8 NOT NULL,
 `bio` longtext CHARACTER SET utf8,
 PRIMARY KEY (`id`),
 KEY `name` (`name`(333))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `k_categories` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(255) CHARACTER SET utf8 NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `k_quotes` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `text` longtext CHARACTER SET utf8 NOT NULL,
 `author_id` bigint(20) NOT NULL,
 `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 PRIMARY KEY (`id`),
 KEY `author_id` (`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `k_quote_category` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `quote_id` bigint(20) unsigned NOT NULL,
 `category_id` bigint(20) unsigned NOT NULL,
 PRIMARY KEY (`id`),
 KEY `quote_id` (`quote_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;