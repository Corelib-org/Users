CREATE TABLE `tbl_users` (
  `pk_users` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` char(40) COLLATE utf8_bin NOT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `activated` enum('TRUE','FALSE') COLLATE utf8_bin NOT NULL DEFAULT 'FALSE',
  `activation_string` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `create_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`pk_users`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `activated` (`activated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 2671' AUTO_INCREMENT=1 ;