CREATE TABLE IF NOT EXISTS `tbl_users_has_permissions` (
  `fk_users` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_user_permissions` int(10) unsigned NOT NULL DEFAULT '0',
  `comment` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `expire_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`fk_users`,`fk_user_permissions`),
  KEY `expire` (`expire_timestamp`),
  KEY `fk_users_permissions` (`fk_user_permissions`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 1';

ALTER TABLE `tbl_users_has_permissions`
  ADD CONSTRAINT `tbl_users_has_permissions_ibfk_2` FOREIGN KEY (`fk_user_permissions`) REFERENCES `tbl_user_permissions` (`pk_user_permissions`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_users_has_permissions_ibfk_1` FOREIGN KEY (`fk_users`) REFERENCES `tbl_users` (`pk_users`) ON DELETE CASCADE ON UPDATE CASCADE;
