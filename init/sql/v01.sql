CREATE TABLE IF NOT EXISTS `users` (
	`user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(50) NOT NULL,
	`login` VARCHAR(16) NOT NULL,
	`password` VARCHAR(128) NOT NULL,
	`ts_seen` timestamp, INT(11),
	`is_active` TINYINT(1), 
	`is_removed` TINYINT(1), 

	PRIMARY KEY (`user_id`)
);

CREATE TABLE IF NOT EXISTS `UserSession` (
	`user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`ts` timestamp, INT(11),
	`hash` VARCHAR(128),
	`addit` VARCHAR(128),

	PRIMARY KEY (`user_id`)
);

CREATE TABLE IF NOT EXISTS `UserACL` (
	`user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`role` VARCHAR(50),

	PRIMARY KEY (`user_id`)
);

CREATE TABLE IF NOT EXISTS `List` (
	`list_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT UNSIGNED NOT NULL,
	`id_user_to` INT UNSIGNED NOT NULL
	`ts` timestamp, INT(11),
	`type` VARCHAR(128),
	`name` VARCHAR(16),
	`note` VARCHAR(128),

	PRIMARY KEY (`list_id`),
	FOREIGN KEY (`user_id`) REFERENCES `User`(`user_id`) 
);

CREATE TABLE IF NOT EXISTS `Elem` (
	`elem_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`list_id` INT UNSIGNED NOT NULL,
	`content` VARCHAR(512),
	`ts` timestamp, INT(11),
	`is_read` TINYINT(1), 
	`note` VARCHAR(128),

	PRIMARY KEY (`elem_id`),
	FOREIGN KEY (`list_id`) REFERENCES `List`(`list_id`) 
);

CREATE TABLE IF NOT EXISTS `Ping` (
	`ping_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT UNSIGNED NOT NULL,
	`list_id` INT UNSIGNED NOT NULL,
	`elem_id` INT UNSIGNED NOT NULL,
	`ts` timestamp, INT(11),
	`ts_next` timestamp, INT(11),
	`content` VARCHAR(512),
	`note` VARCHAR(128),

	PRIMARY KEY (`ping_id`)
);
 
ENGINE = InnoDB;