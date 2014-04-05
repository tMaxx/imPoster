CREATE TABLE IF NOT EXISTS `User` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`gid` DECIMAL(22) UNSIGNED NOT NULL UNIQUE COMMENT 'google id',
	`email` VARCHAR(50) NOT NULL UNIQUE,
	`login` VARCHAR(16) NOT NULL UNIQUE,
	`auth` VARCHAR(10) NOT NULL DEFAULT 'user' COMMENT 'authorization',
	`ts_seen` INT(11),
	`is_active` TINYINT(1) DEFAULT 0,
	`is_removed` TINYINT(1) DEFAULT 0,

	PRIMARY KEY (`id`)
) ENGINE=InnoDB;
CREATE INDEX iudex_User_email ON `User`(`email`);
CREATE INDEX iudex_User_login ON `User`(`login`);
CREATE INDEX iudex_User_gid ON `User`(`gid`);

CREATE TABLE IF NOT EXISTS `Session` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT UNSIGNED,
	`ts` BIGINT(14),
	`salt` VARCHAR(64),
	`hash` VARCHAR(64),
	`data` VARCHAR(2048) DEFAULT '{}',

	PRIMARY KEY (`id`)
) ENGINE=MEMORY;
CREATE INDEX iudex_Session_hash ON `Session`(`hash`);

-- CREATE TABLE IF NOT EXISTS `UserACL` (
-- 	`user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
-- 	`role` VARCHAR(50),

-- 	PRIMARY KEY (`user_id`),
-- 	FOREIGN KEY (`user_id`) REFERENCES `User`(`user_id`) ON DELETE CASCADE
-- ) ENGINE=InnoDB;

-- CREATE TABLE IF NOT EXISTS `ACLRoles` (
-- 	`role` VARCHAR(20) NOT NULL UNIQUE,
-- 	`child` VARCHAR(62) DEFAULT NULL
-- ) ENGINE=InnoDB;

-- CREATE TABLE IF NOT EXISTS `ACLActions` (
-- 	`role` VARCHAR(20) NOT NULL,
-- 	`action` VARCHAR(30) NOT NULL
-- ) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `Entry` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT UNSIGNED NOT NULL,
	-- `user_dest` INT UNSIGNED NULL DEFAULT NULL,
	-- `list_id` INT UNSIGNED NULL DEFAULT NULL,
	`name` VARCHAR(140),
	`content` TEXT NOT NULL,
	`type` INT,
	`ts` INT(11) NOT NULL,
	`is_draft` TINYINT(1) NOT NULL DEFAULT 0,
	`is_read` TINYINT(1) NOT NULL DEFAULT 0,

	PRIMARY KEY (`elem_id`),
	FOREIGN KEY (`list_id`) REFERENCES `Entry`(`elem_id`),
	FOREIGN KEY (`user_id`) REFERENCES `User`(`user_id`),
) ENGINE=InnoDB;
CREATE INDEX iudex_Elem_user_id ON `Entry`(`user_id`);

-- CREATE TABLE IF NOT EXISTS `Ping` (
-- 	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
-- 	`user_id` INT UNSIGNED,
-- 	`user_dest` INT UNSIGNED,
-- 	`elem_id` INT UNSIGNED DEFAULT NULL,
-- 	`ts` INT(11) NOT NULL,
-- 	`note` VARCHAR(140),

-- 	PRIMARY KEY (`ping_id`),
-- 	FOREIGN KEY (`elem_id`) REFERENCES `Entry`(`elem_id`),
-- 	FOREIGN KEY (`user_id`) REFERENCES `User`(`user_id`),
-- 	FOREIGN KEY (`user_dest`) REFERENCES `User`(`user_id`)
-- ) ENGINE=InnoDB;

-- CREATE TABLE IF NOT EXISTS `UserFriends` (
-- 	`user_one` INT UNSIGNED NOT NULL,
-- 	`user_two` INT UNSIGNED NOT NULL,
-- 	`status` TINYINT(1) NULL DEFAULT NULL,

-- 	FOREIGN KEY (`user_one`) REFERENCES `User`(`user_id`),
-- 	FOREIGN KEY (`user_two`) REFERENCES `User`(`user_id`)
-- ) ENGINE=InnoDB;
-- CREATE INDEX iudex_UserFriends_user_one ON `UserFriends`(`user_one`);
-- CREATE INDEX iudex_UserFriends_user_two ON `UserFriends`(`user_two`);
