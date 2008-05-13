CREATE TABLE `tbl_users_has_information` (
  `fk_users` int(10) unsigned NOT NULL,
  `fk_information` smallint(6) unsigned NOT NULL,
  `fk_information_items` smallint(6) unsigned DEFAULT NULL,
  `value` text COLLATE utf8_bin,
  UNIQUE KEY `relations` (`fk_users`,`fk_information`,`fk_information_items`),
  KEY `fk_users` (`fk_users`),
  KEY `fk_information` (`fk_information`),
  KEY `fk_information_items` (`fk_information_items`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 1';

ALTER TABLE `tbl_users_has_information`
  ADD CONSTRAINT `tbl_users_has_information_ibfk_1` FOREIGN KEY (`fk_users`) REFERENCES `tbl_users` (`pk_users`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_users_has_information_ibfk_2` FOREIGN KEY (`fk_information`) REFERENCES `tbl_information` (`pk_information`) ON DELETE CASCADE ON UPDATE CASCADE;