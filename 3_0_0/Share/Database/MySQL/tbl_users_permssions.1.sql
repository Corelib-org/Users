CREATE TABLE `tbl_users_permissions` (
  `pk_users_permissions` int(10) unsigned NOT NULL auto_increment,
  `permission_ident` varchar(255) collate utf8_bin NOT NULL default '',
  `permission_name` varchar(255) collate utf8_bin default NULL,
  PRIMARY KEY  (`pk_users_permissions`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 1';