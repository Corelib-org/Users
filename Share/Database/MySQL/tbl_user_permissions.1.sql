CREATE TABLE IF NOT EXISTS `tbl_user_permissions` (
  `pk_user_permissions` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ident` varchar(255) COLLATE utf8_bin NOT NULL,
  `title` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`pk_user_permissions`),
  UNIQUE KEY `ident` (`ident`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 1' AUTO_INCREMENT=1;