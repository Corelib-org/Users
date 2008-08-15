ALTER TABLE `tbl_users` ADD `deleted` ENUM( 'TRUE', 'FALSE' ) NOT NULL DEFAULT 'FALSE' AFTER `activation_string` ;
ALTER TABLE `tbl_users` ADD INDEX ( `deleted` ) ;
ALTER TABLE `tbl_users`  COMMENT = 'Revision: 2672';