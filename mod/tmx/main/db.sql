CREATE TABLE IF NOT EXISTS `User` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`gid` DECIMAL(22) UNSIGNED NOT NULL UNIQUE COMMENT 'google id',
	`email` VARCHAR(50) NOT NULL UNIQUE,
	`login` VARCHAR(16) NOT NULL UNIQUE,
	`auth` VARCHAR(10) NOT NULL DEFAULT 'user' COMMENT 'auth level',
	`ts_seen` INT(11) NOT NULL DEFAULT UNIX_TIMESTAMP(),
	`is_active` TINYINT(1) DEFAULT 0,
	`is_removed` TINYINT(1) DEFAULT 0,

	PRIMARY KEY (`id`)
) ENGINE=InnoDB;
-- CREATE INDEX iudex_User_email ON `User`(`email`);
-- CREATE INDEX iudex_User_login ON `User`(`login`);
CREATE INDEX iudex_User_gid ON `User`(`gid`);

CREATE TABLE IF NOT EXISTS `Session` (
	`user_id` INT UNSIGNED,
	`ts` BIGINT(14) NOT NULL,
	`salt` VARCHAR(64) NOT NULL UNIQUE,
	`hash` VARCHAR(50) NOT NULL,
	`data` VARCHAR(512) DEFAULT '{}',

	PRIMARY KEY (`hash`)
) ENGINE=MEMORY;
CREATE INDEX iudex_Session_hash ON `Session`(`hash`);

-- CREATE OR REPLACE VIEW `UserSessions` AS
-- 	SELECT
-- 		s.user_id as id,
-- 		s.ts as ts,
-- 		s.salt as salt,
-- 		s.hash as hash,
-- 		s.data as data,

-- 	FROM `Session` s RIGHT JOIN `User` u
-- 	ON s.user_id=u.id
-- ;

CREATE TABLE IF NOT EXISTS `Blog` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(140),
	`content` TEXT NOT NULL,
	`type` INT,
	`ts_publ` INT(11) NOT NULL COMMENT 'ts published',
	`ts_mod` INT(11) NOT NULL COMMENT 'ts modified',
	`is_draft` TINYINT(1) NOT NULL DEFAULT 0,

	PRIMARY KEY (`id`)
) ENGINE=InnoDB;
CREATE INDEX iudex_Blog_ts_publ ON `Blog`(`ts_publ`);
CREATE INDEX iudex_Blog_ts_mod ON `Blog`(`ts_mod`);

CREATE TABLE IF NOT EXISTS `Comments` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`blog` INT UNSIGNED NOT NULL,
	`author` INT DEFAULT NULL,
	`content` TEXT NOT NULL,
	`ts` INT(11) NOT NULL COMMENT 'ts published',

	PRIMARY KEY (`id`),
	FOREIGN KEY (`blog`) REFERENCES `Blog`(`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `Locker` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`content` TEXT NOT NULL,
	`note` VARCHAR(140),
	`src` INT,
	`ts` INT(11) NOT NULL,

	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

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
