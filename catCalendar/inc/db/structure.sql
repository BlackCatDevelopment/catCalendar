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
	`:prefix:mod_catCalendarURL`,
	`:prefix:mod_catCalendar_events`,
	`:prefix:mod_catCalendar_options`,
	`:prefix:mod_catCalendar`;

CREATE TABLE `:prefix:mod_catCalendar` (
	`calID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`section_id` INT(11) NOT NULL DEFAULT 0,
	`name` VARCHAR(255) NOT NULL DEFAULT 'New calendar',
	`color` CHAR(6) NOT NULL DEFAULT 'f02d6a',
	`calURL` VARCHAR(255) NULL DEFAULT NULL,
	`TZID` VARCHAR(31) NULL DEFAULT NULL,
	`summertime` TINYINT(1) NULL DEFAULT NULL,
	PRIMARY KEY ( `calID` ),
	CONSTRAINT `:prefix:cCal_sections` FOREIGN KEY (`section_id`) REFERENCES `:prefix:sections`(`section_id`) ON DELETE CASCADE
) COMMENT='Main table for catCalendars'
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE='utf8_general_ci';

CREATE TABLE `:prefix:mod_catCalendar_options` (
	`section_id` INT(11) NOT NULL DEFAULT 0,
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`value` TEXT DEFAULT '',
	PRIMARY KEY ( `section_id`, `name` ),
	CONSTRAINT `:prefix:cCalOptSec` FOREIGN KEY (`section_id`) REFERENCES `:prefix:sections`(`section_id`) ON DELETE CASCADE
) COMMENT='Options for catCalendar'
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE='utf8_general_ci';

CREATE TABLE `:prefix:mod_catCalendar_events` (
	`eventID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`calID` INT(11) UNSIGNED NULL DEFAULT 0,
	`UID` VARCHAR(255) NOT NULL DEFAULT '',
	`published` TINYINT(1) NOT NULL DEFAULT 0,
	`location` VARCHAR(255) NOT NULL DEFAULT '',
	`title` TEXT DEFAULT '',
	`description` TEXT DEFAULT '',
	`kind` TINYINT(1) NOT NULL DEFAULT 0,
	`allday` TINYINT(1) NOT NULL DEFAULT 0,
	`start` DATETIME DEFAULT 0,
	`end` DATETIME DEFAULT 0,
	`eventURL` VARCHAR(255) NOT NULL DEFAULT '',
	`timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`modified` DATETIME DEFAULT 0,
	`createdID` INT(11) UNSIGNED DEFAULT 0,
	`modifiedID` INT(11) UNSIGNED DEFAULT 0,
	PRIMARY KEY ( `eventID` ),
	CONSTRAINT `:prefix:cCalEvCalID` FOREIGN KEY (`calID`) REFERENCES `:prefix:mod_catCalendar`(`calID`) ON DELETE CASCADE,
	CONSTRAINT `:prefix:cCalCreated` FOREIGN KEY (`createdID`) REFERENCES `:prefix:users`(`user_id`) ON DELETE CASCADE,
	CONSTRAINT `:prefix:cCalModified` FOREIGN KEY (`modifiedID`) REFERENCES `:prefix:users`(`user_id`) ON DELETE CASCADE
) COMMENT='Table for events for catCalendar'
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE='utf8_general_ci';

CREATE TABLE `:prefix:mod_catCalendarURL` (
	`eventID` INT(11) UNSIGNED NOT NULL DEFAULT 0,
	`URL` VARCHAR(255) NOT NULL DEFAULT '',
	`newURL` VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY ( `eventID`, `URL` ),
	CONSTRAINT `:prefix:cCalURL` FOREIGN KEY (`eventID`) REFERENCES `:prefix:mod_catCalendar_events`(`eventID`) ON DELETE CASCADE
) COMMENT='SEO Url for catCalendar'
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE='utf8_general_ci';

/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;