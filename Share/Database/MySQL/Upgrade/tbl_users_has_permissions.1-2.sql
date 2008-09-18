ALTER TABLE `tbl_users_has_permissions` CHANGE `expire` `expire_timestamp` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `tbl_users_has_permissions`  COMMENT = 'Revision: 2';