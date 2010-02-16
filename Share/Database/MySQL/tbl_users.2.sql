CREATE TABLE tbl_users (
  pk_users int(10) unsigned NOT NULL auto_increment,
  username varchar(40) character set utf8 collate utf8_unicode_ci NOT NULL,
  `password` varchar(255) collate utf8_bin NOT NULL default '',
  email varchar(255) collate utf8_bin NOT NULL default '',
  activation_string varchar(40) collate utf8_bin default NULL,
  create_timestamp timestamp NOT NULL default CURRENT_TIMESTAMP,
  last_timestamp timestamp NULL default NULL,
  PRIMARY KEY  (pk_users),
  UNIQUE KEY username (username),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE tbl_users COMMENT = 'Revision: 2';