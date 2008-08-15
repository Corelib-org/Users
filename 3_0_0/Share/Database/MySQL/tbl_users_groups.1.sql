CREATE TABLE `tbl_users_groups` (
  `pk_users_groups` int(10) unsigned NOT NULL auto_increment,
  `group_name` varchar(255) collate utf8_bin NOT NULL default '',
  `group_desc` text collate utf8_bin,
  PRIMARY KEY  (`pk_users_groups`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Revision: 1';