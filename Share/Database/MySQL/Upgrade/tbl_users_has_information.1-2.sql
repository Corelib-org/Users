ALTER TABLE `tbl_users_has_information` CHANGE `fk_information` `fk_information` SMALLINT( 6 ) UNSIGNED NOT NULL  
ALTER TABLE `tbl_users_has_information` ADD FOREIGN KEY ( `fk_information_items` ) REFERENCES `getaling_dk_new`.`tbl_information_items` (
`pk_information_items`
) ON DELETE CASCADE ON UPDATE CASCADE ;
 ALTER TABLE `tbl_users_has_information`  COMMENT = 'Revision: 2' 