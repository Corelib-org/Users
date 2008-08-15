CREATE TABLE `tbl_users_has_permissions` (
  `fk_users` int(10) unsigned NOT NULL default '0',
  `fk_users_permissions` int(10) unsigned NOT NULL default '0',
  `comment` varchar(255) collate utf8_bin default NULL,
  `expire` timestamp NULL default NULL,
  PRIMARY KEY  (`fk_users`,`fk_users_permissions`),
  KEY `expire` (`expire`),
  KEY `fk_users_permissions` (`fk_users_permissions`),
  CONSTRAINT `tbl_users_has_permissions_ibfk_2` FOREIGN KEY (`fk_users_permissions`) REFERENCES `tbl_users_permissions` (`pk_users_permissions`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_users_has_permissions_ibfk_1` FOREIGN KEY (`fk_users`) REFERENCES `tbl_users` (`pk_users`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 1';
