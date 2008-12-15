CREATE TABLE IF NOT EXISTS `tbl_user_settings` (
  `fk_users` int(10) unsigned NOT NULL,
  `ident` varchar(100) COLLATE utf8_bin NOT NULL,
  `value` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`fk_users`,`ident`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 1';

ALTER TABLE `tbl_user_settings`
  ADD CONSTRAINT `tbl_user_settings_ibfk_1` FOREIGN KEY (`fk_users`) REFERENCES `tbl_users` (`pk_users`) ON DELETE CASCADE ON UPDATE CASCADE;
