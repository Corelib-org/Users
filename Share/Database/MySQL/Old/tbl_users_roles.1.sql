CREATE TABLE `tbl_users_roles` (
  `pk_users_roles` int(10) unsigned NOT NULL default '0',
  `role_name` varchar(255) collate utf8_bin NOT NULL default '',
  `role_desc` text collate utf8_bin,
  PRIMARY KEY  (`pk_users_roles`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 1';