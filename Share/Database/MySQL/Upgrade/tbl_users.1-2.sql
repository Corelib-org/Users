ALTER TABLE tbl_users ADD createtime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER create_timestamp;
UPDATE tbl_users SET createtime = FROM_UNIXTIME(create_timestamp);
ALTER TABLE tbl_users DROP create_timestamp;
ALTER TABLE tbl_users CHANGE createtime create_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE tbl_users CHANGE last_timestamp last_timestamp TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE tbl_users COMMENT = 'Revision: 2';