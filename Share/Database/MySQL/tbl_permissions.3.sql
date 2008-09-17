CREATE TABLE `tbl_users_permissions` (
  `pk_users_permissions` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ident` varchar(255) COLLATE utf8_bin NOT NULL,
  `title` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`pk_users_permissions`),
  UNIQUE KEY `permission_ident` (`ident`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 3';