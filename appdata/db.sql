CREATE TABLE IF NOT EXISTS `User` (
	`user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(50) NOT NULL UNIQUE,
	`login` VARCHAR(16) NOT NULL UNIQUE,
	`password` VARCHAR(128) NOT NULL,
	`ts_seen` INT(11),
	`is_active` TINYINT(1) DEFAULT 0,
	`is_removed` TINYINT(1) DEFAULT 0,

	PRIMARY KEY (`user_id`)
) ENGINE=InnoDB;
CREATE INDEX iudex_User_email ON `User`(`email`);
CREATE INDEX iudex_User_login ON `User`(`login`);

CREATE TABLE IF NOT EXISTS `Session` (
	`session_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`id` INT UNSIGNED NOT NULL,
	`ts` INT(11),
	`hash` VARCHAR(70),
	`signature` VARCHAR(70),
	`data` VARCHAR(256),

	PRIMARY KEY (`session_id`),
) ENGINE=MEMORY;
CREATE INDEX iudex_Session_hash ON `Session`(`hash`);

CREATE TABLE IF NOT EXISTS `UserACL` (
	`user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`role` VARCHAR(50),

	PRIMARY KEY (`user_id`),
	FOREIGN KEY (`user_id`) REFERENCES `User`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ACLRoles` (
	`role` VARCHAR(20) NOT NULL UNIQUE,
	`child` VARCHAR(62) DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ACLActions` (
	`role` VARCHAR(20) NOT NULL,
	`action` VARCHAR(30) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `Elem` (
	`elem_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT UNSIGNED NOT NULL,
	`user_dest` INT UNSIGNED NULL DEFAULT NULL,
	`list_id` INT UNSIGNED NULL DEFAULT NULL,
	`name` VARCHAR(140),
	`content` TEXT NOT NULL,
	`type` INT,
	`ts` INT(11) NOT NULL,
	`is_read` TINYINT(1) NOT NULL DEFAULT 0,

	PRIMARY KEY (`elem_id`),
	FOREIGN KEY (`list_id`) REFERENCES `Elem`(`elem_id`),
	FOREIGN KEY (`user_id`) REFERENCES `User`(`user_id`),
	FOREIGN KEY (`user_dest`) REFERENCES `User`(`user_id`)
) ENGINE=InnoDB;
CREATE INDEX iudex_Elem_user_id ON `Elem`(`user_id`);

CREATE TABLE IF NOT EXISTS `Ping` (
	`ping_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT UNSIGNED,
	`user_dest` INT UNSIGNED,
	`elem_id` INT UNSIGNED DEFAULT NULL,
	`ts` INT(11) NOT NULL,
	`note` VARCHAR(140),

	PRIMARY KEY (`ping_id`),
	FOREIGN KEY (`elem_id`) REFERENCES `Elem`(`elem_id`),
	FOREIGN KEY (`user_id`) REFERENCES `User`(`user_id`),
	FOREIGN KEY (`user_dest`) REFERENCES `User`(`user_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `UserFriends` (
	`user_one` INT UNSIGNED NOT NULL,
	`user_two` INT UNSIGNED NOT NULL,
	`status` TINYINT(1) NULL DEFAULT NULL,

	FOREIGN KEY (`user_one`) REFERENCES `User`(`user_id`),
	FOREIGN KEY (`user_two`) REFERENCES `User`(`user_id`)
) ENGINE=InnoDB;
CREATE INDEX iudex_UserFriends_user_one ON `UserFriends`(`user_one`);
CREATE INDEX iudex_UserFriends_user_two ON `UserFriends`(`user_two`);
