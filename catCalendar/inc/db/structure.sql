-- --------------------------------------------------------
-- Please note:
-- The table prefix (cat_) will be replaced by the
-- installer! Do NOT use this file to create the tables
-- manually! (Or patch it to fit your needs first.)
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

DROP TABLE IF EXISTS
	`:prefix:mod_catCalendar_events`,
	`:prefix:mod_catCalendar_options`,
	`:prefix:mod_catCalendar`;

CREATE TABLE `:prefix:mod_catCalendar` (
	`section_id` INT(11) NOT NULL DEFAULT 0,
	`calURL` VARCHAR(255) NOT NULL DEFAULT '',
	`TZID` VARCHAR(31) NOT NULL DEFAULT '',
	`summertime` TINYINT(1) NOT NULL DEFAULT '',
	PRIMARY KEY ( `section_id` ),
	CONSTRAINT `:prefix:cCal_sections` FOREIGN KEY (`section_id`) REFERENCES `:prefix:sections`(`section_id`) ON DELETE CASCADE
) COMMENT='Main table for catCalendar'
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE='utf8_general_ci';

CREATE TABLE `:prefix:mod_catCalendar_options` (
	`section_id` INT(11) NOT NULL DEFAULT 0,
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`value` VARCHAR(2047) NULL DEFAULT NULL,
	PRIMARY KEY ( `section_id`, `name` ),
	CONSTRAINT `:prefix:cCalOptSec` FOREIGN KEY (`section_id`) REFERENCES `:prefix:sections`(`section_id`) ON DELETE CASCADE
) COMMENT='Options for catCalendar'
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE='utf8_general_ci';

CREATE TABLE `:prefix:mod_catCalendar_events` (
	`eventID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`section_id` INT(11) NULL DEFAULT 0,
	`published` TINYINT(1) NOT NULL DEFAULT 0,
	`location` VARCHAR(255) NOT NULL DEFAULT '',
	`title` TEXT DEFAULT '',
	`description` TEXT DEFAULT '',
	`kind` TINYINT NOT NULL DEFAULT 0,
	`start` DATETIME DEFAULT 0,
	`end` DATETIME DEFAULT 0,
	`timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`eventURL` VARCHAR(255) NOT NULL DEFAULT '',
	`UID` VARCHAR(255) NOT NULL DEFAULT '',
	`fullDay` TINYINT NOT NULL DEFAULT 0,
	`modified` DATETIME DEFAULT 0,
	`createdID` INT(11) UNSIGNED DEFAULT 0,
	`modifiedID` INT(11) UNSIGNED DEFAULT 0,
	PRIMARY KEY ( `eventID` ),
	CONSTRAINT `:prefix:cCalEventSec` FOREIGN KEY (`section_id`) REFERENCES `:prefix:sections`(`section_id`) ON DELETE CASCADE,
	CONSTRAINT `:prefix:cCalCreated` FOREIGN KEY (`createdID`) REFERENCES `:prefix:users`(`user_id`) ON DELETE CASCADE,
	CONSTRAINT `:prefix:cCalModified` FOREIGN KEY (`modifiedID`) REFERENCES `:prefix:users`(`user_id`) ON DELETE CASCADE
) COMMENT='Table for events for catCalendar'
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE='utf8_general_ci';

/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;