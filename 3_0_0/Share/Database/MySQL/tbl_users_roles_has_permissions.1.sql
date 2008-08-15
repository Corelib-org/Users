CREATE TABLE `tbl_users_roles_has_permissions` (
  `fk_users_roles` int(10) unsigned NOT NULL default '0',
  `fk_users_permissions` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fk_users_roles`,`fk_users_permissions`),
  KEY `fk_users_permissions` (`fk_users_permissions`),
  CONSTRAINT `tbl_users_roles_has_permissions_ibfk_2` FOREIGN KEY (`fk_users_permissions`) REFERENCES `tbl_users_permissions` (`pk_users_permissions`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_users_roles_has_permissions_ibfk_1` FOREIGN KEY (`fk_users_roles`) REFERENCES `tbl_users_roles` (`pk_users_roles`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 1';