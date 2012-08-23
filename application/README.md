KoolDevelop - Sample Application
================================

This is a sample application demonstrating the basic usage of the KoolDevelop framework.
To start with this sample application some things need to be configured. 

### Create Database
Create a new database and execute the following SQL:

CREATE TABLE `tips` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`language` VARCHAR(3) NOT NULL DEFAULT 'en',
	`title` VARCHAR(50) NOT NULL,
	`text` TEXT NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `lang` (`language`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

### Update Database configuration
Update config/database.ini settings (host, username, password)

