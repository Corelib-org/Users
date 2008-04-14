ALTER TABLE `tbl_users` CHANGE `password` `password` CHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL 
ALTER TABLE `tbl_users` ADD `activated` ENUM( 'TRUE', 'FALSE' ) NOT NULL DEFAULT 'FALSE' AFTER `email` ;
ALTER TABLE `tbl_users` ADD INDEX ( `activated` ) ;
UPDATE tbl_users SET activated='TRUE' WHERE activation_string IS NULL;
ALTER TABLE `tbl_users`  COMMENT = 'Revision: 2671'