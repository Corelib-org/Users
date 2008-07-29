CREATE TABLE IF NOT EXISTS `tbl_users_has_keywords` (
  `fk_users` int(10) unsigned NOT NULL,
  `fk_keywords` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`fk_users`,`fk_keywords`),
  KEY `fk_keywords` (`fk_keywords`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 1';

ALTER TABLE `tbl_users_has_keywords`
  ADD CONSTRAINT `tbl_users_has_keywords_ibfk_2` FOREIGN KEY (`fk_keywords`) REFERENCES `tbl_keywords` (`pk_keywords`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_users_has_keywords_ibfk_1` FOREIGN KEY (`fk_users`) REFERENCES `tbl_users` (`pk_users`) ON DELETE CASCADE ON UPDATE CASCADE;