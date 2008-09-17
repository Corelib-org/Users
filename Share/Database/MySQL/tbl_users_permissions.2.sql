CREATE TABLE `tbl_users_permissions` (
  `pk_users_permissions` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_ident` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `permission_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`pk_users_permissions`),
  UNIQUE KEY `permission_ident` (`permission_ident`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 2';