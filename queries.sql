ALTER TABLE `jb_users` ADD `company_id` INT(5) NOT NULL DEFAULT '1' AFTER `user_type`;
ALTER TABLE `jb_users` ADD `lang_id` INT(2) NOT NULL DEFAULT '2' AFTER `company_id`;


CREATE TABLE `jb_company_profile` (
                              `id` int(11) NOT NULL,
                              `title` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


INSERT INTO `jb_company_profile` (`id`, `title`) VALUES
    (1, 'albadar');


ALTER TABLE `jb_company_profile`
    ADD PRIMARY KEY (`id`);


ALTER TABLE `jb_company_profile`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `jb_company_profile` ADD `published` TINYINT(1) NOT NULL DEFAULT '1' AFTER `title`;


ALTER TABLE `jb_zones` ADD `company_id` INT(3) NOT NULL DEFAULT '1' AFTER `published`;
ALTER TABLE `jb_zones` DROP `updated`;
ALTER TABLE `jb_zones` DROP `created`;
ALTER TABLE `jb_zones` DROP `updated_user_id`;
ALTER TABLE `jb_zones` DROP `created_user_id`;



ALTER TABLE `jb_sessions` ADD `company_id` INT(3) NOT NULL DEFAULT '1' AFTER `published`;
ALTER TABLE `jb_sessions` DROP `updated`;
ALTER TABLE `jb_sessions` DROP `created`;
ALTER TABLE `jb_sessions` DROP `updated_user_id`;
ALTER TABLE `jb_sessions` DROP `created_user_id`;



ALTER TABLE `jb_branches` ADD `company_id` INT(3) NOT NULL DEFAULT '1' AFTER `published`;
ALTER TABLE `jb_branches` DROP `updated`;
ALTER TABLE `jb_branches` DROP `created`;
ALTER TABLE `jb_branches` DROP `updated_user_id`;
ALTER TABLE `jb_branches` DROP `created_user_id`;


ALTER TABLE `jb_classes` ADD `company_id` INT(3) NOT NULL DEFAULT '1' AFTER `published`;
ALTER TABLE `jb_classes` DROP `updated`;
ALTER TABLE `jb_classes` DROP `created`;
ALTER TABLE `jb_classes` DROP `updated_user_id`;
ALTER TABLE `jb_classes` DROP `created_user_id`;


ALTER TABLE `jb_user_groups` ADD `company_id` INT(3) NOT NULL DEFAULT '1' AFTER `published`;
ALTER TABLE `jb_user_groups` DROP `updated`;
ALTER TABLE `jb_user_groups` DROP `created`;
ALTER TABLE `jb_user_groups` DROP `updated_user_id`;
ALTER TABLE `jb_user_groups` DROP `created_user_id`;


ALTER TABLE `jb_class_modules` ADD `company_id` INT(3) NOT NULL DEFAULT '1' AFTER `published`;
ALTER TABLE `jb_class_modules` DROP `updated`;
ALTER TABLE `jb_class_modules` DROP `created`;
ALTER TABLE `jb_class_modules` DROP `updated_user_id`;
ALTER TABLE `jb_class_modules` DROP `created_user_id`;

DELETE  FROM `jb_system_modules` WHERE `phpfile` = 'sessionsettings';

ALTER TABLE `jb_sections` ADD `company_id` INT(3) NOT NULL DEFAULT '1' AFTER `published`;
ALTER TABLE `jb_sections` DROP `updated`;
ALTER TABLE `jb_sections` DROP `created`;
ALTER TABLE `jb_sections` DROP `updated_user_id`;
ALTER TABLE `jb_sections` DROP `created_user_id`;




ALTER TABLE `jb_session_classes` CHANGE `id` `id` BIGINT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `jb_session_classes` DROP `updated`;
ALTER TABLE `jb_session_classes` DROP `updated_user_id`;
ALTER TABLE `jb_session_sections` DROP `updated`;
ALTER TABLE `jb_session_sections` DROP `updated_user_id`;


CREATE TABLE `jb_system_pages` (
                                   `id` bigint(20) NOT NULL,
                                   `bundle` varchar(100) NOT NULL,
                                   `page` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `jb_system_pages`
    ADD PRIMARY KEY (`id`);

ALTER TABLE `jb_system_pages`
    MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `jb_system_pages` ADD `parent_menu` INT(3) NULL DEFAULT NULL AFTER `page`;
ALTER TABLE `jb_system_modules` CHANGE `parent_id` `parent_id` INT(3) NULL;
UPDATE `jb_system_modules` SET `parent_id` = NULL WHERE `parent_id` = 0;


DELETE FROM `jb_system_modules`
WHERE parent_id NOT IN (SELECT id FROM (SELECT id FROM jb_system_modules) AS subquery);


ALTER TABLE `jb_system_modules` ADD CONSTRAINT `menu_with_parent` FOREIGN KEY (`parent_id`)
    REFERENCES `jb_system_modules`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;


ALTER TABLE `jb_user_branches` DROP `updated`;
ALTER TABLE `jb_user_branches` DROP `updated_user_id`;