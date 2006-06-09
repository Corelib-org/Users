CREATE TABLE `tbl_users_has_categories` (
  `fk_users` int(10) unsigned NOT NULL,
  `fk_categories` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`fk_users`,`fk_categories`),
  KEY `fk_users` (`fk_users`),
  KEY `fk_categories` (`fk_categories`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;