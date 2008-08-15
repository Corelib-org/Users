CREATE TABLE `tbl_users_roles_has_groups` (
  `fk_users_roles` int(10) unsigned NOT NULL default '0',
  `fk_users_groups` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fk_users_roles`,`fk_users_groups`),
  KEY `fk_users_groups` (`fk_users_groups`),
  CONSTRAINT `tbl_users_roles_has_groups_ibfk_2` FOREIGN KEY (`fk_users_groups`) REFERENCES `tbl_users_groups` (`pk_users_groups`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_users_roles_has_groups_ibfk_1` FOREIGN KEY (`fk_users_roles`) REFERENCES `tbl_users_roles` (`pk_users_roles`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 1';