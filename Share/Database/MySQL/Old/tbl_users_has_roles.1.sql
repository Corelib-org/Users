CREATE TABLE `tbl_users_has_roles` (
  `fk_users` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_users_roles` int(10) unsigned NOT NULL DEFAULT '0',
  `comment` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `expire` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`fk_users`,`fk_users_roles`),
  KEY `expire` (`expire`),
  KEY `fk_users_groups` (`fk_users_roles`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 1';

ALTER TABLE `tbl_users_has_roles`
  ADD CONSTRAINT `tbl_users_has_roles_ibfk_2` FOREIGN KEY (`fk_users_roles`) REFERENCES `tbl_users_roles` (`pk_users_roles`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_users_has_roles_ibfk_1` FOREIGN KEY (`fk_users`) REFERENCES `tbl_users` (`pk_users`) ON DELETE CASCADE ON UPDATE CASCADE;
