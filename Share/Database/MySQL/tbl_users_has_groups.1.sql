CREATE TABLE `tbl_users_has_groups` (
  `fk_users` int(10) unsigned NOT NULL default '0',
  `fk_users_groups` int(10) unsigned NOT NULL default '0',
  `comment` varchar(255) collate utf8_bin default NULL,
  `expire` timestamp NULL default NULL,
  PRIMARY KEY  (`fk_users`,`fk_users_groups`),
  KEY `expire` (`expire`),
  KEY `fk_users_groups` (`fk_users_groups`),
  CONSTRAINT `tbl_users_has_groups_ibfk_2` FOREIGN KEY (`fk_users_groups`) REFERENCES `tbl_users_groups` (`pk_users_groups`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_users_has_groups_ibfk_1` FOREIGN KEY (`fk_users`) REFERENCES `tbl_users` (`pk_users`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 1';