<?php

/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

//USE ;end TO SEPERATE SQL STATEMENTS. DON'T USE ;end IN ANY OTHER PLACES!

$sql = array();
$count = 0;

// v19.0.00 and earlier removed to reduce file size
// Systems using older versions can update using prior release from https://github.com/GibbonEdu/core/releases

//v20.0.00
++$count;
$sql[$count][0] = '20.0.00';
$sql[$count][1] = "
ALTER TABLE `gibbonDepartment` ADD `sequenceNumber` INT(4) UNSIGNED NULL AFTER `logo`;end
UPDATE `gibboni18n` SET `name` = 'Español - España' WHERE `code` = 'es_ES';end
INSERT INTO `gibboni18n` (`code`, `name`, `active`, `installed`, `systemDefault`, `dateFormat`, `dateFormatRegEx`, `dateFormatPHP`, `rtl`) VALUES ('es_MX', 'Español - Mexico', 'Y', 'N', 'N', 'dd/mm/yyyy', '/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\\d\\d$/i', 'd/m/Y', 'N');end
ALTER TABLE `gibbonReportingValue` DROP INDEX `gibbonReportingCriteriaID`, ADD UNIQUE `gibbonReportingCriteriaID` (`gibbonReportingCriteriaID`, `gibbonPersonIDStudent`, `gibbonCourseClassID`) USING BTREE;end
ALTER TABLE `gibbonReportTemplateFont` ADD `fontType` ENUM('R','B','I','BI') NOT NULL DEFAULT 'R' AFTER `fontPath`;end
ALTER TABLE `gibbonReportTemplateFont` ADD `fontFamily` VARCHAR(60) NULL AFTER `fontType`;end
UPDATE `gibbonReportTemplateFont` SET fontFamily=fontName WHERE fontFamily IS NULL;end
ALTER TABLE `gibbonReportTemplate` ADD `config` TEXT NULL AFTER `flags`;end
ALTER TABLE `gibbonHook` CHANGE `type` `type` ENUM('Public Home Page','Student Profile','Parental Dashboard','Staff Dashboard','Student Dashboard','Report Writing') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;end
ALTER TABLE `gibbonReportingCriteriaType` ADD UNIQUE(`name`);end
ALTER TABLE `gibbonStaffCoverageDate` CHANGE `value` `value` DECIMAL(3,2) NOT NULL DEFAULT '1.00';end
ALTER TABLE `gibbonStaffAbsenceDate` CHANGE `value` `value` DECIMAL(3,2) NOT NULL DEFAULT '1.00';end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('Students', 'applicationFormRefereeRequired', 'Application Form Referee Required', 'Should the referee email address field be required?', 'Y');end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='Students'), 'Withdraw Student', 0, 'Admissions', 'Enables admin to set a student to left and notify other users.', 'student_withdraw.php', 'student_withdraw.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Students' AND gibbonAction.name='Withdraw Student'));end
INSERT INTO `gibbonNotificationEvent` (`event`, `moduleName`, `actionName`, `type`, `scopes`, `active`) VALUES ('Student Withdrawn', 'Students', 'View Student Profile_full', 'Core', 'All,gibbonYearGroupID', 'Y');end
INSERT INTO `gibbonNotificationEvent` (`event`, `moduleName`, `actionName`, `type`, `scopes`, `active`) VALUES ('New Staff', 'Staff', 'Staff Directory_full', 'Core', 'All', 'Y');end
INSERT INTO `gibbonNotificationEvent` (`event`, `moduleName`, `actionName`, `type`, `scopes`, `active`) VALUES ('Staff Left', 'Staff', 'Staff Directory_full', 'Core', 'All', 'Y');end
ALTER TABLE `gibbonTTImport` CHANGE `courseNameShort` `courseNameShort` VARCHAR(12) NOT NULL DEFAULT '';end
ALTER TABLE `gibbonTTImport` CHANGE `classNameShort` `classNameShort` VARCHAR(8) NOT NULL DEFAULT '';end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='Reports'), 'Upload Reports', 0, 'Archive', 'Enables users to upload reports from a ZIP archive.', 'archive_manage_upload.php,archive_manage_uploadPreview.php', 'archive_manage_upload.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Reports' AND gibbonAction.name='Upload Reports'));end
UPDATE `gibbonAction` SET URLList = 'archive_manage.php,archive_manage_add.php,archive_manage_edit.php,archive_manage_delete.php,archive_manage_migrate.php' WHERE name='Manage Archives' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Reports');end
UPDATE `gibbonAction` SET category = 'Data' WHERE name='Import From File ' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin');end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin'), 'View Logs', 0, 'Data', 'Enables users to browse Gibbon\'s event log.', 'logs_view.php', 'logs_view.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='System Admin' AND gibbonAction.name='View Logs'));end
UPDATE gibbonAction SET categoryPermissionStudent='N', categoryPermissionParent='N' WHERE name='Lesson Planner_viewAllEditMyClasses' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Planner');end
";

//v21.0.00
++$count;
$sql[$count][0] = '21.0.00';
$sql[$count][1] = "
UPDATE gibbonAction SET name='View Roll Groups_all', precedence='1' WHERE name='View Roll Groups' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Roll Groups');end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES ((SELECT gibbonModuleID FROM gibbonModule WHERE name='Roll Groups'), 'View Roll Groups_myChildren', 0, 'Roll Groups', 'View the roll groups in which a user\'s children study.', 'rollGroups.php,rollGroups_details.php', 'rollGroups.php', 'Y', 'Y', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'N');end
UPDATE `gibbonSetting` SET nameDisplay='Application Submission Fee', description='The cost of applying to the school. Paid when submitting the application form.' WHERE scope='Application Form' AND name='applicationFee';end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('Application Form', 'applicationProcessFee', 'Application Processing Fee', 'An optional fee that is paid before processing the application form. Sent by staff via the Manage Applications page.', '0');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('Application Form', 'applicationProcessFeeText', 'Application Processing Fee Text', 'A custom message sent to applicants by email when a processing fee needs to be paid.', 'Thank you for your application submission. Please pay the following processing fee before your application is complete. Payment can be made by credit card, using our secure PayPal payment gateway. Click the button below to pay now.');end
ALTER TABLE `gibbonApplicationForm` ADD `paymentMade2` ENUM('N','Y','Exemption') NOT NULL DEFAULT 'N' AFTER `paymentMade`;end
ALTER TABLE `gibbonApplicationForm` ADD `gibbonPaymentID2` INT(14) UNSIGNED ZEROFILL NULL DEFAULT NULL AFTER `gibbonPaymentID`;end
ALTER TABLE `gibbonMessenger` ADD `gibbonSchoolYearID` INT(3) UNSIGNED ZEROFILL NULL DEFAULT NULL AFTER `gibbonMessengerID`;end
UPDATE `gibbonMessenger` SET `gibbonSchoolYearID` = (SELECT y1.gibbonSchoolYearID FROM gibbonSchoolYear AS y1 WHERE gibbonMessenger.timestamp < y1.lastDay AND (gibbonMessenger.timestamp  > (SELECT MAX(y2.lastDay) FROM gibbonSchoolYear as y2 WHERE y2.sequenceNumber<y1.sequenceNumber) OR y1.sequenceNumber=(SELECT MIN(sequenceNumber) FROM gibbonSchoolYear)) LIMIT 1);end
UPDATE `gibbonMessenger` SET `gibbonSchoolYearID` = (SELECT gibbonSchoolYearID FROM gibbonSchoolYear WHERE status='Current' LIMIT 1) WHERE gibbonSchoolYearID IS NULL;end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin'), 'System Overview', 0, 'System', '', 'systemOverview.php', 'systemOverview.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='System Admin' AND gibbonAction.name='System Overview'));end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin'), 'Manage Services', 0, 'Extend & Update', '', 'services_manage.php', 'services_manage.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='System Admin' AND gibbonAction.name='Manage Services'));end
UPDATE `gibbonAction` SET `category`='System' WHERE name='System Check' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin');end
UPDATE `gibbonModule` SET `entryURL`='systemOverview.php' WHERE name='System Admin';end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('Planner', 'parentDailyEmailSummaryIntroduction', 'Parent Daily Email Summary Introduction', 'Information to display at the beginning of the email', '');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('Planner', 'parentDailyEmailSummaryPostScript', 'Parent Daily Email Summary PostScript', 'Information to display at the end of the email', '');end
INSERT INTO `gibbonNotificationEvent` (`event`, `moduleName`, `actionName`, `type`, `scopes`, `active`) VALUES ('Parent Daily Email Summary', 'Planner', 'Parent Daily Email Summary', 'CLI', 'All', 'Y');end
INSERT INTO `gibbonNotificationEvent` (`event`, `moduleName`, `actionName`, `type`, `scopes`, `active`) VALUES ('Tutor Daily Email Summary', 'Planner', 'Tutor Daily Email Summary', 'CLI', 'All', 'Y');end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='School Admin'), 'Email Summary Settings', 0, 'Other', '', 'emailSummarySettings.php', 'emailSummarySettings.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='School Admin' AND gibbonAction.name='Email Summary Settings'));end
UPDATE `gibbonSetting` SET scope='School Admin' WHERE scope='Planner' AND (name='parentWeeklyEmailSummaryIncludeBehaviour' OR name='parentWeeklyEmailSummaryIncludeMarkbook' OR name='parentDailyEmailSummaryIntroduction' OR name='parentDailyEmailSummaryPostScript');end
UPDATE `gibbonNotificationEvent` SET `moduleName` = 'School Admin' WHERE `moduleName` = 'Planner' AND (event='Parent Weekly Email Summary' OR event='Parent Daily Email Summary' OR event='Tutor Daily Email Summary');end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='School Admin'), 'Manage Medical Conditions', 0, 'Other', 'Manage the list of medical conditions that can be attached to student medical records.', 'medicalConditions_manage.php,medicalConditions_manage_add.php,medicalConditions_manage_edit.php,medicalConditions_manage_delete.php', 'medicalConditions_manage.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='School Admin' AND gibbonAction.name='Manage Medical Conditions'));end
UPDATE `gibbonAction` SET category='People' WHERE `name`='Manage Medical Conditions' AND `gibbonModuleID`=(SELECT gibbonModuleID FROM gibbonModule WHERE name='School Admin');end
ALTER TABLE `gibbonAttendanceCode` CHANGE `scope` `scope` ENUM('Onsite','Onsite - Late','Offsite','Offsite - Left','Offsite - Late') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;end
ALTER TABLE `gibbonDiscussion` ADD `tag` VARCHAR(60) NULL DEFAULT NULL AFTER `type`;end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin'), 'Email Templates', 0, 'Settings', '', 'emailTemplates_manage.php,emailTemplates_manage_edit.php', 'emailTemplates_manage.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='System Admin' AND gibbonAction.name='Email Templates'));end
CREATE TABLE `gibbonEmailTemplate` ( `gibbonEmailTemplateID` INT(6) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT , `templateName` VARCHAR(120) NOT NULL , `moduleName` VARCHAR(30) NOT NULL , `templateSubject` VARCHAR(255) NULL , `templateBody` TEXT NULL , `variables` TEXT NULL , `timestamp` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`gibbonEmailTemplateID`), UNIQUE KEY `templateName`(`templateName`)) ENGINE = InnoDB;end
INSERT INTO `gibbonEmailTemplate` (`templateName`, `moduleName`, `templateSubject`, `templateBody`, `variables`, `timestamp`) VALUES ('Send Reports to Parents', 'Reports', '{{reportName|title}} for {{studentPreferredName}} {{studentSurname}}', '<p>Dear {{parentPreferredName}} {{parentSurname}},</p>\r\n<p>This email includes a link to {{studentPreferredName}}\'s {{reportName|title}} created on {{date}}.</p>\r\n<p>Click the button below to download this report. To protect your student\'s security and privacy, this download link will expire after 1 week.</p>\r\n<p>Thank you,<br />{{organisationAdministratorName}}</p>', '{\"reportName\": \"Test Report\", \r\n\"studentPreferredName\": [\"firstName\"],\r\n\"studentSurname\": [\"lastName\"],\r\n\"parentPreferredName\": [\"firstName\"],\r\n\"parentSurname\": [\"lastName\"],\r\n\"date\": [\"date\"]\r\n}', '2020-09-02 16:58:10');end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='Reports'), 'Send Reports', 0, 'Publish', '', 'reports_send.php,reports_send_batch.php', 'reports_send.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Reports' AND gibbonAction.name='Send Reports'));end
ALTER TABLE `gibbonReportArchiveEntry` ADD `timestampSent` TIMESTAMP NULL AFTER `timestampModified`, ADD `timestampAccessExpiry` TIMESTAMP NULL AFTER `timestampAccessed`, ADD `accessToken` VARCHAR(60) NULL AFTER `timestampAccessExpiry`;end
INSERT INTO `gibbonEmailTemplate` (`templateName`, `moduleName`, `templateSubject`, `templateBody`, `variables`, `timestamp`) VALUES ('Send Reports to Students', 'Reports', 'Your {{reportName|title}}', '<p>Dear {{studentPreferredName}} {{studentSurname}},</p>\r\n<p>This email includes a link to your {{reportName|title}} created on {{date}}.</p>\r\n<p>Click the button below to download this report. To protect your security and privacy, this download link will expire after 1 week.</p>\r\n<p>Thank you,<br />{{organisationAdministratorName}}</p>', '{\"reportName\": \"Test Report\", \r\n\"studentPreferredName\": [\"firstName\"],\r\n\"studentSurname\": [\"lastName\"],\r\n\"date\": [\"date\"]\r\n}', '2020-09-02 16:58:10');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('Finance', 'paymentTypeOptions', 'Payment Type Options', 'Which payment types are available for invoicing, as a csv list.', 'Online,Bank Transfer,Cash,Cheque,Credit Card,Other');end
ALTER TABLE `gibbonPayment` CHANGE `type` `type` VARCHAR(60) NOT NULL DEFAULT 'Online';end
ALTER TABLE `gibbonPlannerEntry` CHANGE `homeworkSubmissionRequired` `homeworkSubmissionRequired` enum('Optional','Compulsory','Required') DEFAULT NULL;end
UPDATE `gibbonPlannerEntry` SET homeworkSubmissionRequired='Required' WHERE homeworkSubmissionRequired='Compulsory';end
ALTER TABLE `gibbonPlannerEntry` CHANGE `homeworkSubmissionRequired` `homeworkSubmissionRequired` enum('Optional','Required') DEFAULT NULL;end
ALTER TABLE `gibbonModule` CHANGE `version` `version` VARCHAR(8) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;end
UPDATE gibbonStaff SET type='Support' WHERE NOT type='Teaching';end
ALTER TABLE `gibbonCourseClassPerson` ADD `dateEnrolled` DATE NULL DEFAULT NULL AFTER `role`, ADD `dateUnenrolled` DATE NULL DEFAULT NULL AFTER `dateEnrolled`;end
ALTER TABLE `gibbonRubricColumn` ADD `backgroundColor` VARCHAR(7) NULL DEFAULT NULL AFTER `title`;end
ALTER TABLE `gibbonRubricRow` ADD `backgroundColor` VARCHAR(7) NULL DEFAULT NULL AFTER `title`;end
INSERT INTO `gibboni18n` (`code`, `name`, `version`, `active`, `installed`, `systemDefault`, `dateFormat`, `dateFormatRegEx`, `dateFormatPHP`, `rtl`) VALUES ('af_ZN', 'Afrikaans - Suid-Afrika', '21.0.00', 'Y', 'Y', 'N', 'dd/mm/yyyy', '/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\\d\\d$/i', 'd/m/Y', 'N');end
UPDATE gibboni18n SET code='af_ZA' WHERE code='af_ZN';end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('Students', 'medicalConditionIntro', 'Medical Condition Introductory Text', 'HTML text that will appear above the medical conditions section.', '');end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='Students'), 'My Student History', 0, 'Visualise', '', 'report_myStudentHistory.php', 'report_myStudentHistory.php', 'Y', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Students' AND gibbonAction.name='My Student History'));end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('002', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Students' AND gibbonAction.name='My Student History'));end
DROP TABLE `gibbonPersonMedicalSymptoms`;end
ALTER TABLE `gibbonMedicalCondition` ADD `description` TEXT NULL DEFAULT NULL AFTER `name`;end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin'), 'Server Info', 0, 'System', '', 'serverInfo.php', 'serverInfo.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='System Admin' AND gibbonAction.name='Server Info'));end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin'), 'Cache Manager', 0, 'Utilities', '', 'cacheManager.php', 'cacheManager.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='System Admin' AND gibbonAction.name='Cache Manager'));end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System', 'cachePath', 'Cache Path', 'Relative to the Gibbon root directory.', '/uploads/cache');end
ALTER TABLE `gibbonPersonMedicalCondition` ADD `attachment` VARCHAR(255) NULL DEFAULT NULL AFTER `comment`;end
INSERT INTO `gibbonNotificationEvent` (`event`, `moduleName`, `actionName`, `type`, `scopes`, `active`) VALUES ('Medical Condition', 'Students', 'Manage Medical Forms', 'Core', 'All,gibbonPersonIDStudent,gibbonYearGroupID', 'Y');end
INSERT INTO `gibbonNotificationEvent` (`event`, `moduleName`, `actionName`, `type`, `scopes`, `active`) VALUES ('New Application with SEN/Medical', 'Students', 'Manage Applications', 'Core', 'All,gibbonPersonIDStudent,gibbonYearGroupID', 'Y');end
ALTER TABLE `gibbonPersonMedicalConditionUpdate` ADD `attachment` VARCHAR(255) NULL DEFAULT NULL AFTER `comment`;end
ALTER TABLE `gibbonStaff` ADD `firstAidQualification` VARCHAR(100) NULL DEFAULT NULL AFTER `firstAidQualified`;end
CREATE TABLE `gibbonMigration` ( `gibbonMigrationID` INT(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT , `name` VARCHAR(60) NOT NULL , `version` VARCHAR(8) NOT NULL , `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`gibbonMigrationID`)) ENGINE = InnoDB;end
ALTER TABLE `gibbonPerson` CHANGE `fields` `fields` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'JSON object of custom field values';end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('Planner', 'homeworkNameSingular', 'Homework Name - Singular', 'A name to use for \"Homework\" in the planner. This noun should be in a singular form.', 'Homework');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('Planner', 'homeworkNamePlural', 'Homework Name - Plural', 'A name to use for \"Homework\" in the planner. This noun should be in a plural form.', 'Homework');end
ALTER TABLE `gibbonPlannerEntry` ADD `homeworkTimeCap` INT(3) NULL DEFAULT NULL AFTER `homeworkDetails`;end
ALTER TABLE `gibbonPlannerEntry` ADD `homeworkLocation` ENUM('Out of Class','In Class') NULL DEFAULT NULL AFTER `homeworkTimeCap`;end
ALTER TABLE `gibbonAlertLevel` CHANGE color color varchar(7) NOT NULL COMMENT 'RGB Hex, leading #';end
ALTER TABLE `gibbonAlertLevel` CHANGE colorBG colorBG varchar(7) NOT NULL COMMENT 'RGB Hex, leading #';end
UPDATE `gibbonAlertLevel` SET color=CONCAT('#', color) WHERE NOT color='';end
UPDATE `gibbonAlertLevel` SET colorBG=CONCAT('#', colorBG) WHERE NOT colorBG='';end
ALTER TABLE `gibbonTTDay` CHANGE color color varchar(7) NOT NULL COMMENT 'RGB Hex, leading #';end
ALTER TABLE `gibbonTTDay` CHANGE fontColor fontColor varchar(7) NOT NULL COMMENT 'RGB Hex, leading #';end
UPDATE `gibbonTTDay` SET color=CONCAT('#', color) WHERE NOT color='';end
UPDATE `gibbonTTDay` SET fontColor=CONCAT('#', fontColor) WHERE NOT fontColor='';end
ALTER TABLE `gibbonModule` CHANGE `category` `category` VARCHAR(12) NOT NULL;end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin'), 'Security & Privacy Settings', 0, 'Settings', 'Manage settings related to user security and privacy.', 'privacySettings.php', 'privacySettings.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='System Admin' AND gibbonAction.name='Security & Privacy Settings'));end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System Admin', 'cookieConsentEnabled', 'Ask Users for Cookie Consent?', 'Display a banner for users to accept the use of cookies.', 'Y');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System Admin', 'cookieConsentText', 'Cookie Consent Text', 'The message diplayed to users when they click to give consent.', 'Gibbon uses cookies which are strictly necessary for user account login and basic session data. It does not track or analyze user behaviour. By continuing to use this platform, users accept the use of cookies. Read the privacy policy to find out more.');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System Admin', 'privacyPolicy', 'Privacy Policy', 'Display a privacy policy document and add a link to it from the homepage.', '');end
ALTER TABLE `gibbonPerson` ADD `cookieConsent` ENUM('Y','N') NULL DEFAULT NULL AFTER `receiveNotificationEmails`;end
UPDATE `gibbonSetting` set value='Gibbon uses cookies which are strictly necessary for user account login and basic session data. It does not track or analyze user behaviour. By continuing to use this platform, users accept the use of cookies.' WHERE scope='System Admin' AND name='cookieConsentText';end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System', 'dataRetentionDomains', 'Data Retention Domains', 'A list of areas to pre-select when undertaking data retention work.', 'Student Personal Data,Medical Data,Finance Data,Behaviour Records,Individual Needs,Family Data,Parent Personal Data,Staff Personal Data,Other Users Personal Data,Student Application Forms,Staff Application Forms');end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin'), 'Data Retention', 0, 'Utilities', 'Comply with privacy regulations by flushing older, non-academic, data from the system.', 'dataRetention.php', 'dataRetention.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='System Admin' AND gibbonAction.name='Data Retention'));end
CREATE TABLE `gibbonDataRetention` (`gibbonDataRetentionID` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT,`gibbonPersonID` int(10) unsigned zerofill NOT NULL,`tables` text NOT NULL,`status` enum('Success','Partial Failure') DEFAULT NULL,`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,`gibbonPersonIDOperator` int(10) unsigned zerofill NOT NULL,PRIMARY KEY (`gibbonDataRetentionID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;end
ALTER TABLE `gibbonINPersonDescriptor` CHANGE `gibbonINDescriptorID` `gibbonINDescriptorID` int(3) unsigned zerofill NULL DEFAULT NULL;end
ALTER TABLE `gibbonINPersonDescriptor` CHANGE `gibbonAlertLevelID` `gibbonAlertLevelID` int(3) unsigned zerofill NULL DEFAULT NULL;
ALTER TABLE `gibbonDataRetention` ADD UNIQUE(`gibbonPersonID`);end
ALTER TABLE `gibbonApplicationForm` CHANGE `gender` `gender` ENUM('M','F','Other','Unspecified') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Unspecified';end
ALTER TABLE `gibbonStaffApplicationForm` CHANGE `gender` `gender` ENUM('M','F','Other','Unspecified') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Unspecified';end
UPDATE `gibbonSetting` SET description='Click to select a colour.' WHERE name='browseBGColor' AND scope='Library';end
UPDATE `gibbonSetting` SET value=CONCAT('#', value) WHERE name='browseBGColor' AND scope='Library' AND NOT value='';end
UPDATE `gibbonSetting` SET nameDisplay='Browse Library BG Colour' WHERE name='browseBGColor' AND scope='Library';end
UPDATE `gibbonSetting` SET nameDisplay='Message Bubble Background Colour', description='Message bubble background colour in RGBA (e.g. 100,100,100,0.50). If blank, theme default will be used.' WHERE name='messageBubbleBGColor' AND scope='Messenger';end
UPDATE `gibbonAction` SET `URLList` = 'archive_byReport.php,archive_byStudent.php' WHERE `name`='View Past Reports' AND `gibbonModuleID`=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Reports');end
UPDATE `gibbonAction` SET `URLList` = 'archive_byReport.php,archive_byStudent.php' WHERE `name`='View Draft Reports' AND `gibbonModuleID`=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Reports');end
INSERT INTO `gibboni18n` (`code`, `name`, `version`, `active`, `installed`, `systemDefault`, `dateFormat`, `dateFormatRegEx`, `dateFormatPHP`, `rtl`) VALUES ('uk_UA', 'українська мова - Україна', '21.0.00', 'Y', 'N', 'N', 'dd.mm.yyyy', '/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\\d\\d$/i', 'd.m.Y', 'N');end
";

//v21.0.01
++$count;
$sql[$count][0] = '21.0.01';
$sql[$count][1] = "";

//v22.0.00
++$count;
$sql[$count][0] = '22.0.00';
$sql[$count][1] = "
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System', 'cacheString', 'Front End Cache', '', '1611200873');end
ALTER TABLE `gibbonDiscussion` ADD `gibbonPersonIDTarget` INT(10) UNSIGNED ZEROFILL NULL AFTER `gibbonPersonID`;end
DELETE FROM `gibbonSetting` WHERE scope='Messenger' AND name IN ('messengerLastBubble','messageBubbleBGColor','messageBubbleWidthType','messageBubbleAutoHide');end
INSERT INTO gibbonCountry (`printable_name`, `iddCountryCode`) VALUES ('South Sudan', '211');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('User Admin', 'publicRegistrationAllowedDomains', 'Public Registration Allowed Domains', 'Comma-separated list of email address domains allowed when registering. Leave blank for no restriction.', '');end
ALTER TABLE `gibbonPerson` CHANGE `messengerLastBubble` `messengerLastRead` DATETIME NULL DEFAULT NULL;end
RENAME TABLE `gibbonPersonField` TO `gibbonCustomField`;end
ALTER TABLE `gibbonCustomField` CHANGE `gibbonPersonFieldID` `gibbonCustomFieldID` INT(4) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;end
UPDATE `gibbonAction` SET `category`='Customise', name='Custom Fields', URLList='customFields.php,customFields_add.php,customFields_edit.php,customFields_delete.php', entryURL='customFields.php', gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin') WHERE `name`='Manage Custom Fields' AND `gibbonModuleID`=(SELECT gibbonModuleID FROM gibbonModule WHERE name='User Admin');end
UPDATE `gibbonAction` SET `category`='Customise', name='Notification Events' WHERE `name`='Notification Settings' AND `gibbonModuleID`=(SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin');end
UPDATE `gibbonAction` SET `category`='Customise' WHERE `name`='String Replacement' AND `gibbonModuleID`=(SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin');end
UPDATE `gibbonAction` SET `category`='Customise' WHERE `name`='Email Templates' AND `gibbonModuleID`=(SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin');end
ALTER TABLE `gibbonCustomField` ADD `context` VARCHAR(60) NOT NULL DEFAULT 'Person' AFTER `gibbonCustomFieldID`;end
ALTER TABLE `gibbonCustomField` ADD `sequenceNumber` INT(4) NOT NULL AFTER `required`;end
ALTER TABLE `gibbonCustomField` ADD `heading` VARCHAR(90) NOT NULL AFTER `required`;end
ALTER TABLE `gibbonCustomField` CHANGE `type` `type` ENUM('varchar','text','date','time','url','select','checkboxes','radio','yesno','editor','color','number','image','file') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;end
INSERT INTO gibbonLanguage (name) SELECT * FROM (SELECT 'Somali') AS tmp WHERE NOT EXISTS (SELECT name FROM gibbonLanguage WHERE (name='Somali')) LIMIT 1;end
ALTER TABLE `gibbonPersonMedical` ADD `fields` TEXT NULL AFTER `comment`;end
ALTER TABLE `gibbonPersonMedicalUpdate` ADD `fields` TEXT NULL AFTER `timestamp`;end
DELETE FROM `gibbonSetting` WHERE scope='Students' AND name='extendedBriefProfile';end
ALTER TABLE `gibbonActivitySlot` CHANGE `gibbonSpaceID` `gibbonSpaceID` int(10) UNSIGNED ZEROFILL DEFAULT NULL;end
ALTER TABLE `gibbonCustomField` ADD `hidden` ENUM('Y','N') NULL DEFAULT 'N' AFTER `required`;end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System Admin', 'composerLockHash', 'Composer Update Required', '', '742368e59c40f1eb9b7d8f116f7af49d');end
UPDATE `gibbonAction` SET categoryPermissionParent='N' WHERE `name`='View Markbook_myMarks' AND `gibbonModuleID`=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Markbook');end
DELETE `gibbonPermission` FROM `gibbonPermission` JOIN `gibbonAction` ON (gibbonAction.gibbonActionID=gibbonPermission.gibbonActionID) JOIN gibbonRole ON (gibbonRole.gibbonRoleID=gibbonPermission.gibbonRoleID) WHERE gibbonAction.name='View Markbook_myMarks' AND gibbonRole.category='Parent';end
SELECT NULL;end
UPDATE `gibbonCustomField` SET `heading`='General Information' WHERE `context`='Medical Form' AND (`name`='Blood Type' OR `name`='Tetanus Within Last 10 Years?');end
UPDATE `gibbonCustomField` SET `context`='User' WHERE `context`='Person';end
UPDATE `gibbonPlannerEntry` SET `gibbonUnitID`=NULL WHERE `gibbonUnitID`='0000000000';end
ALTER TABLE `gibbonFirstAid` ADD `fields` TEXT NULL AFTER `timestamp`;end
ALTER TABLE `gibbonStaff` ADD `fields` TEXT NULL AFTER `biographicalGroupingPriority`;end
ALTER TABLE `gibbonCourse` ADD `fields` TEXT NULL AFTER `orderBy`;end
ALTER TABLE `gibbonCourseClass` ADD `fields` TEXT NULL AFTER `gibbonScaleIDTarget`;end
ALTER TABLE `gibbonStaffApplicationForm` ADD `staffFields` TEXT NULL AFTER `fields`;end
ALTER TABLE `gibbonAction` ADD `helpURL` VARCHAR(255) NULL AFTER `description`;end
UPDATE `gibbonAction` SET helpURL='administrators/getting-started/getting-started-with-gibbon/#years-days-times' WHERE `category`='Years, Days & Times' AND `gibbonModuleID`=(SELECT gibbonModuleID FROM gibbonModule WHERE name='School Admin');end
UPDATE `gibbonTheme` SET description='Gibbon\'s 2021 look and feel.', version='1.0.00', author='Sandra Kuipers', url='https://github.com/SKuipers' WHERE name='Default';end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System', 'themeColour', 'Theme Colour', '', 'Purple');end
CREATE TABLE `gibbonStaffUpdate` (`gibbonStaffUpdateID` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,`gibbonSchoolYearID` int(3) UNSIGNED ZEROFILL DEFAULT NULL,`gibbonStaffID` int(10) UNSIGNED ZEROFILL NOT NULL,`status` enum('Pending','Complete') NOT NULL DEFAULT 'Pending',`type` varchar(20) NOT NULL,`initials` varchar(4) DEFAULT NULL,`jobTitle` varchar(100) NOT NULL,`firstAidQualified` enum('','N','Y') NOT NULL DEFAULT '',`firstAidQualification` varchar(100) DEFAULT NULL,`firstAidExpiry` date DEFAULT NULL,`countryOfOrigin` varchar(80) NOT NULL,`qualifications` varchar(255) NOT NULL,`biography` text NOT NULL,`fields` text NOT NULL,`gibbonPersonIDUpdater` int(10) UNSIGNED ZEROFILL NOT NULL,`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(`gibbonStaffUpdateID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `helpURL`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='Data Updater'), 'Update Staff Data_any', 1, 'Request Updates', 'Create staff data update request for any user', NULL, 'data_staff.php', 'data_staff.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES (001, (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Data Updater' AND gibbonAction.name='Update Staff Data_any'));end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `helpURL`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='Data Updater'), 'Update Staff Data_my', 0, 'Request Updates', 'Allows users to create data update request for their staff record.', NULL, 'data_staff.php', 'data_staff.php', 'Y', 'Y', 'Y', 'Y', 'N', 'Y', 'Y', 'Y', 'N', 'Y', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES (001, (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Data Updater' AND gibbonAction.name='Update Staff Data_my'));end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES (002, (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Data Updater' AND gibbonAction.name='Update Staff Data_my'));end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `helpURL`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='Data Updater'), 'Staff Data Updates', 0, 'Manage Updates', 'Manage requests for updates to staff data.', NULL, 'data_staff_manage.php,data_staff_manage_edit.php,data_staff_manage_delete.php', 'data_staff_manage.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES (001, (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Data Updater' AND gibbonAction.name='Staff Data Updates'));end
INSERT INTO `gibbonNotificationEvent` (`event`, `moduleName`, `actionName`, `type`, `scopes`, `active`) VALUES ('Staff Data Updates', 'Data Updater', 'Staff Data Updates', 'Core', 'All', 'Y');end
UPDATE gibbonPerson SET gibbonThemeIDPersonal=(SELECT gibbonThemeID FROM gibbonTheme WHERE name='Default' LIMIT 1) WHERE gibbonThemeIDPersonal=(SELECT gibbonThemeID FROM gibbonTheme WHERE name='2021');end
ALTER TABLE `gibbonInternalAssessmentColumn` CHANGE `name` `name` VARCHAR(30) NOT NULL;end
UPDATE gibbonAction SET name='Manage Form Groups' WHERE name='Manage Roll Groups';end
UPDATE gibbonAction SET name='Class Enrolment by Form Group' WHERE name='Class Enrolment by Roll Group';end
UPDATE gibbonAction SET name='Students by Form Group' WHERE name='Students by Roll Group';end
UPDATE gibbonAction SET name='Activity Spread by Form Group' WHERE name='Activity Spread by Roll Group';end
UPDATE gibbonAction SET name='Activity Type by Form Group' WHERE name='Activity Type by Roll Group';end
UPDATE gibbonAction SET name='Letters Home by Form Group' WHERE name='Letters Home by Roll Group';end
UPDATE gibbonAction SET name='Form Group Summary' WHERE name='Roll Group Summary';end
UPDATE gibbonAction SET name='Form Groups Not Registered' WHERE name='Roll Groups Not Registered';end
UPDATE gibbonAction SET name='Work Summary by Form Group' WHERE name='Work Summary by Roll Group';end
UPDATE gibbonAction SET name='Students By Form Group' WHERE name='Students By Roll Group';end
UPDATE gibbonAction SET description='Bulk email to any of my form groups' WHERE description='Bulk email to any of my roll groups';end
UPDATE gibbonAction SET description='Bulk email to any form group' WHERE description='Bulk email to any roll group';end
UPDATE gibbonAction SET description='Print a report of form groups who have not been registered on a given day' WHERE description='Print a report of roll groups who have not been registered on a given day';end
UPDATE gibbonAction SET description='Print form group lists showing count of various activity types' WHERE description='Print roll group lists showing count of various activity types';end
UPDATE gibbonAction SET description='Print student form group lists' WHERE description='Print student roll group lists';end
UPDATE gibbonAction SET description='Print work summary statistical data by form group' WHERE description='Print work summary statistical data by roll group';end
UPDATE gibbonAction SET description='Show students in form group, less those with an older sibling, so that letters can be carried home by oldest in family.' WHERE description='Show students in roll group, less those with an older sibling, so that letters can be carried home by oldest in family.';end
UPDATE gibbonAction SET description='Summarises gender and number of students across all form groups.' WHERE description='Summarises gender and number of students across all roll groups.';end
UPDATE gibbonAction SET description='Take attendance, one form group at a time' WHERE description='Take attendance, one roll group at a time';end
UPDATE gibbonAction SET description='View a brief profile of form groups in school.' WHERE description='View a brief profile of roll groups in school.';end
UPDATE gibbonAction SET description='View spread of enrolment over terms and days by form group' WHERE description='View spread of enrolment over terms and days by roll group';end
UPDATE gibbonAction SET description='View attendance, by form group and class' WHERE description='View attendance, by roll group and class';end
UPDATE gibbonAction SET name='Attendance By Form Group_all' WHERE name='Attendance By Roll Group_all';end
UPDATE gibbonAction SET name='Attendance By Form Group_myGroups' WHERE name='Attendance By Roll Group_myGroups';end
UPDATE gibbonAction SET name='View Form Groups_all' WHERE name='View Roll Groups_all';end
UPDATE gibbonAction SET name='View Form Groups_myChildren' WHERE name='View Roll Groups_myChildren';end
UPDATE gibbonAction SET description='Shows the number of classes students are enroled in, organised by form group' WHERE description='Shows the number of classes students are enroled in, organised by roll group';end
UPDATE gibbonAction SET name='New Message_formGroups_any' WHERE name='New Message_rollGroups_any';end
UPDATE gibbonAction SET name='New Message_formGroups_my' WHERE name='New Message_rollGroups_my';end
UPDATE gibbonAction SET name='New Message_formGroups_parents' WHERE name='New Message_rollGroups_parents';end
UPDATE gibbonAction SET description='Bulk email to any of my form groups' WHERE description='Bulk email to any of my roll groups';end
UPDATE gibbonAction SET description='Bulk email to any form group' WHERE description='Bulk email to any roll group';end
UPDATE gibbonAction SET name='Activity Choices by Form Group' WHERE name='Activity Choices by Roll Group';end
UPDATE gibbonAction SET description='View all student activity choices in the current year for a given form group.' WHERE description='View all student activity choices in the current year for a given roll group.';end
UPDATE gibbonAction SET description='View the form groups in which a user\'s children study.' WHERE description='View the roll groups in which a user\'s children study.';end
UPDATE gibbonAction SET category='Form Groups' WHERE category='Roll Groups';end
UPDATE gibbonSetting SET nameDisplay='Enable Notifications by Form Group' WHERE nameDisplay='Enable Notifications by Roll Group';end
UPDATE gibbonSetting SET nameDisplay='Default Form Group Attendance Type' WHERE nameDisplay='Default Roll Group Attendance Type';end
UPDATE gibbonSetting SET nameDisplay='Activity Choices by Form Group' WHERE nameDisplay='Activity Choices by Roll Group';end
UPDATE gibbonSetting SET nameDisplay='Enable Notifications for Form Group Tutors' WHERE nameDisplay='Enable Notifications for Roll Group Tutors';end
UPDATE gibbonSetting SET description='Send the school-wide daily attendance report to additional users. Restricted to roles with permission to access Form Groups Not Registered or Classes Not Registered.' WHERE description='Send the school-wide daily attendance report to additional users. Restricted to roles with permission to access Roll Groups Not Registered or Classes Not Registered.';end
UPDATE gibbonSetting SET description='The default selection for attendance type when taking Form Group attendance' WHERE description='The default selection for attendance type when taking Roll Group attendance';end
UPDATE gibbonSetting SET description='View all student activity choices in the current year for a given form group.' WHERE description='View all student activity choices in the current year for a given roll group.';end
UPDATE gibbonSetting SET description='Should the Form Group Tutors of a student be notified of new behaviour records?' WHERE description='Should the Roll Group Tutors of a student be notified of new behaviour records?';end
UPDATE gibbonModule SET description='Allows users to view a listing of form groups' WHERE description='Allows users to view a listing of roll groups';end
UPDATE gibbonNotificationEvent SET actionName='Form Groups Not Registered' WHERE actionName='Roll Groups Not Registered';end
UPDATE gibbonSetting SET name='attendanceCLINotifyByFormGroup' WHERE name='attendanceCLINotifyByRollGroup';end
UPDATE gibbonSetting SET name='defaultFormGroupAttendanceType' WHERE name='defaultRollGroupAttendanceType';end
UPDATE gibbonModule SET name='Form Groups' WHERE name='Roll Groups';end
UPDATE `gibbonSetting` SET value='purple' WHERE value='Purple' AND name='themeColour' AND scope='System';end
UPDATE `gibbonAction` SET URLList='templates_preview.php,templates_manage.php,templates_manage_add.php,templates_manage_edit.php,templates_manage_duplicate.php,templates_manage_delete.php,templates_manage_section_add.php,templates_manage_section_edit.php,templates_manage_section_delete.php,templates_assets.php,templates_assets_components_preview.php,templates_assets_components_add.php,templates_assets_components_edit.php,templates_assets_components_delete.php,templates_assets_components_duplicate.php,templates_assets_fonts_preview.php,templates_assets_fonts_edit.php' WHERE `name`='Template Builder' AND `gibbonModuleID`=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Reports');end
ALTER TABLE gibbonApplicationForm CHANGE `gibbonRollGroupID` `gibbonFormGroupID` int(5) unsigned zerofill DEFAULT NULL;end
ALTER TABLE gibbonAttendanceLogPerson CHANGE `gibbonRollGroupID` `gibbonFormGroupID` int(5) unsigned zerofill DEFAULT NULL;end
ALTER TABLE gibbonAttendanceLogRollGroup CHANGE `gibbonRollGroupID` `gibbonFormGroupID` int(5) unsigned zerofill NOT NULL;end
RENAME TABLE gibbonAttendanceLogRollGroup TO gibbonAttendanceLogFormGroup;end
ALTER TABLE gibbonAttendanceLogFormGroup CHANGE `gibbonAttendanceLogRollGroupID` `gibbonAttendanceLogFormGroupID` int(14) unsigned zerofill NOT NULL AUTO_INCREMENT;end
ALTER TABLE gibbonReportArchiveEntry CHANGE `gibbonRollGroupID` `gibbonFormGroupID` int(5) unsigned zerofill DEFAULT NULL;end
ALTER TABLE gibbonReportingProgress CHANGE `gibbonRollGroupID` `gibbonFormGroupID` int(5) unsigned zerofill DEFAULT NULL;end
ALTER TABLE gibbonRollGroup CHANGE `gibbonRollGroupID` `gibbonFormGroupID` int(5) unsigned zerofill NOT NULL AUTO_INCREMENT;end
ALTER TABLE gibbonRollGroup CHANGE `gibbonRollGroupIDNext` `gibbonFormGroupIDNext` int(5) unsigned zerofill DEFAULT NULL;end
RENAME TABLE gibbonRollGroup TO gibbonFormGroup;end
ALTER TABLE gibbonStudentEnrolment CHANGE `gibbonRollGroupID` `gibbonFormGroupID` int(5) unsigned zerofill NOT NULL;end
DROP INDEX gibbonRollGroupID ON gibbonStudentEnrolment;end
CREATE INDEX `gibbonFormGroupID` ON gibbonStudentEnrolment(gibbonFormGroupID);end
ALTER TABLE gibbonCourseClassMap CHANGE `gibbonRollGroupID` `gibbonFormGroupID` int(5) unsigned zerofill DEFAULT NULL;end
ALTER TABLE gibbonReportingCriteria CHANGE `gibbonRollGroupID` `gibbonFormGroupID` int(5) unsigned zerofill DEFAULT NULL;end
UPDATE gibbonModule SET entryURL='formGroups.php' WHERE name='Form Groups';end
UPDATE gibbonAction SET URLList='formGroups.php,formGroups_details.php', entryURL='formGroups.php' WHERE gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Form Groups') AND name LIKE 'View Form Groups_%';end
UPDATE gibbonAction SET URLList='report_activityChoices_byFormGroup.php', entryURL='report_activityChoices_byFormGroup.php' WHERE gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Activities') AND name='Activity Choices by Form Group';end
UPDATE gibbonAction SET URLList='report_workSummary_byFormGroup.php', entryURL='report_workSummary_byFormGroup.php' WHERE gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Planner') AND name='Work Summary by Form Group';end
UPDATE gibbonAction SET URLList='report_classEnrolment_byFormGroup.php', entryURL='report_classEnrolment_byFormGroup.php' WHERE gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Timetable Admin') AND name='Class Enrolment by Form Group';end
UPDATE gibbonAction SET URLList='attendance_take_byFormGroup.php', entryURL='attendance_take_byFormGroup.php' WHERE gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Attendance') AND name LIKE 'Attendance by Form Group_%';end
UPDATE gibbonAction SET URLList='report_formGroupsNotRegistered_byDate.php,report_formGroupsNotRegistered_byDate_print.php', entryURL='report_formGroupsNotRegistered_byDate.php' WHERE gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Attendance') AND name='Form Groups Not Registered';end
UPDATE gibbonAction SET URLList='formGroup_manage.php,formGroup_manage_edit.php,formGroup_manage_add.php,formGroup_manage_delete.php', entryURL='formGroup_manage.php' WHERE gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='School Admin') AND name='Manage Form Groups';end
UPDATE gibbonAction SET URLList='report_formGroupSummary.php', entryURL='report_formGroupSummary.php' WHERE gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Students') AND name='Form Group Summary';end
UPDATE gibbonAction SET URLList='report_lettersHome_byFormGroup.php', entryURL='report_lettersHome_byFormGroup.php' WHERE gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Students') AND name='Letters Home by Form Group';end
UPDATE gibbonAction SET URLList='report_activityType_formGroup.php', entryURL='report_activityType_formGroup.php' WHERE gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Activities') AND name='Activity Type by Form Group';end
UPDATE gibbonAction SET URLList='report_activitySpread_rollGroup.php', entryURL='report_activitySpread_rollGroup.php' WHERE gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Activities') AND name='Activity Spread by Form Group';end
UPDATE gibbonAction SET URLList='report_students_byFormGroup.php,report_students_byFormGroup_print.php', entryURL='report_students_byFormGroup.php' WHERE gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Students') AND name='Students by Form Group';end
ALTER TABLE gibbonAttendanceLogPerson CHANGE `context` `context` enum('Form Group','Roll Group','Class','Person','Future','Self Registration') DEFAULT NULL;end
UPDATE gibbonAttendanceLogPerson SET context='Form Group' WHERE context='Roll Group';end
ALTER TABLE gibbonAttendanceLogPerson CHANGE `context` `context` enum('Form Group','Class','Person','Future','Self Registration') DEFAULT NULL;end
ALTER TABLE gibbonMessengerReceipt CHANGE `targetType` `targetType` enum('Class','Course','Form Group','Roll Group','Year Group','Activity','Role','Applicants','Individuals','Houses','Role Category','Transport','Attendance','Group') COLLATE utf8_unicode_ci NOT NULL;end
UPDATE gibbonMessengerReceipt SET targetType='Form Group' WHERE targetType='Roll Group';end
ALTER TABLE gibbonMessengerReceipt CHANGE `targetType` `targetType` enum('Class','Course','Form Group','Year Group','Activity','Role','Applicants','Individuals','Houses','Role Category','Transport','Attendance','Group') COLLATE utf8_unicode_ci NOT NULL;end
ALTER TABLE gibbonMessengerTarget CHANGE `type` `type` enum('Class','Course','Form Group','Roll Group','Year Group','Activity','Role','Applicants','Individuals','Houses','Role Category','Transport','Attendance','Group') DEFAULT NULL;end
UPDATE gibbonMessengerTarget SET type='Form Group' WHERE type='Roll Group';end
ALTER TABLE gibbonMessengerTarget CHANGE `type` `type` enum('Class','Course','Form Group','Year Group','Activity','Role','Applicants','Individuals','Houses','Role Category','Transport','Attendance','Group') DEFAULT NULL;end
ALTER TABLE gibbonReportingScope CHANGE `scopeType` `scopeType` enum('Year Group','Form Group','Roll Group','Course') NOT NULL DEFAULT 'Year Group';end
UPDATE gibbonReportingScope SET scopeType='Form Group' WHERE scopeType='Roll Group';end
ALTER TABLE gibbonReportingScope CHANGE `scopeType` `scopeType` enum('Year Group','Form Group','Course') NOT NULL DEFAULT 'Year Group';end
UPDATE `gibbonReportPrototypeSection` SET name=REPLACE(name, 'Roll Group', 'Form Group'), dataSources=REPLACE(dataSources, 'rollGroup', 'formGroup'), templateFile=REPLACE(templateFile, 'rollGroup', 'formGroup');end
UPDATE `gibbonReportPrototypeSection` SET dataSources=REPLACE(dataSources, 'RollGroup', 'FormGroup');end
UPDATE `gibbonReportTemplateSection` SET name=REPLACE(name, 'Roll Group', 'Form Group');end
UPDATE `gibbonNotification` SET actionLink=REPLACE(actionLink, 'gibbonRollGroupID', 'gibbonFormGroupID');end
SELECT NULL;end
ALTER TABLE `gibbonLibraryItem` CHANGE `fields` `fields` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'JSON object';end
ALTER TABLE `gibbonLibraryType` CHANGE `fields` `fields` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'JSON object';end
UPDATE gibbonAction SET URLList='report_activitySpread_formGroup.php', entryURL='report_activitySpread_formGroup.php' WHERE gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Activities') AND name='Activity Spread by Form Group';end
INSERT INTO gibbonLanguage (name) SELECT * FROM (SELECT 'Zulu') AS tmp WHERE NOT EXISTS (SELECT name FROM gibbonLanguage WHERE (name='Zulu')) LIMIT 1;end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('User Admin', 'publicRegistrationAlternateEmail', 'Include Alternate Email?', 'Should the alternate email field be visible in the Public Registration form?', 'N');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('School Admin', 'staffDashboardEnable', 'Enable Staff Dashboard?', 'Should the Staff Dashboard be visible to users?', 'Y');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('School Admin', 'parentDashboardEnable', 'Enable Parent Dashboard?', 'Should the Parent Dashboard be visible to users?', 'Y');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('School Admin', 'studentDashboardEnable', 'Enable Student Dashboard?', 'Should the Student Dashboard be visible to users?', 'Y');end
SELECT NULL;end
CREATE TABLE `gibbonPersonalDocumentType` (`gibbonPersonalDocumentTypeID` INT(3) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,`name` VARCHAR(60) NOT NULL,`description` VARCHAR(255) NOT NULL,`active` ENUM('Y','N') NOT NULL DEFAULT 'Y',`type` ENUM('Core','Additional') NOT NULL DEFAULT 'Additional',`document` ENUM('Passport','ID Card','Document') NOT NULL DEFAULT 'Document',`fields` TEXT NULL, `required` ENUM('Y','N') NOT NULL DEFAULT 'Y', `sequenceNumber` INT(3) NOT NULL DEFAULT 0, `activePersonStudent` TINYINT(1) NOT NULL DEFAULT '0',`activePersonStaff` TINYINT(1) NOT NULL DEFAULT '0',`activePersonParent` TINYINT(1) NOT NULL DEFAULT '0',`activePersonOther` TINYINT(1) NOT NULL DEFAULT '0',`activeApplicationForm` TINYINT(1) NOT NULL DEFAULT '0',`activeDataUpdater` TINYINT(1) NOT NULL DEFAULT '0',PRIMARY KEY (`gibbonPersonalDocumentTypeID`)) ENGINE = InnoDB;end
CREATE TABLE `gibbonPersonalDocument` ( `gibbonPersonalDocumentID` INT(12) UNSIGNED ZEROFILL  NOT NULL AUTO_INCREMENT , `gibbonPersonalDocumentTypeID` INT(3) UNSIGNED ZEROFILL NOT NULL , `foreignTable` VARCHAR(60) NOT NULL , `foreignTableID` INT(12) UNSIGNED ZEROFILL NOT NULL , `documentNumber` VARCHAR(120) NULL , `documentName` VARCHAR(120) NULL , `documentType` VARCHAR(60) NULL , `dateIssue` DATE NULL , `dateExpiry` DATE NULL , `filePath` VARCHAR(255) NULL , `country` VARCHAR(60) NULL , `gibbonPersonIDUpdater` INT(10) UNSIGNED ZEROFILL NULL , `timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`gibbonPersonalDocumentID`), UNIQUE KEY `foreignTableID`( `gibbonPersonalDocumentTypeID`, `foreignTable`, `foreignTableID`)) ENGINE = InnoDB;end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='User Admin'), 'Personal Document Settings', 0, 'User Settings', 'Manage types of personal documents users can upload.', 'personalDocumentSettings.php,personalDocumentSettings_manage_add.php,personalDocumentSettings_manage_edit.php,personalDocumentSettings_manage_delete.php', 'personalDocumentSettings.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='User Admin' AND gibbonAction.name='Personal Document Settings'));end
INSERT INTO `gibbonPersonalDocumentType` (`gibbonPersonalDocumentTypeID`, `name`, `description`, `active`, `type`, `document`, `fields`, `required`, `sequenceNumber`, `activePersonStudent`, `activePersonStaff`, `activePersonParent`, `activePersonOther`, `activeApplicationForm`, `activeDataUpdater`) VALUES(001, 'Primary Passport', '', 'Y', 'Core', 'Passport', '[\"documentName\",\"documentNumber\",\"country\",\"dateIssue\",\"dateExpiry\",\"filePath\"]', 'N', 1, 1, 1, 0, 0, 1, 1);end
INSERT INTO `gibbonPersonalDocumentType` (`gibbonPersonalDocumentTypeID`, `name`, `description`, `active`, `type`, `document`, `fields`, `required`, `sequenceNumber`, `activePersonStudent`, `activePersonStaff`, `activePersonParent`, `activePersonOther`, `activeApplicationForm`, `activeDataUpdater`) VALUES(002, 'Additional Passport', '', 'Y', 'Core', 'Passport', '[\"documentName\",\"documentNumber\",\"country\",\"dateIssue\",\"dateExpiry\",\"filePath\"]', 'N', 2, 1, 1, 0, 0, 0, 1);end
INSERT INTO `gibbonPersonalDocumentType` (`gibbonPersonalDocumentTypeID`, `name`, `description`, `active`, `type`, `document`, `fields`, `required`, `sequenceNumber`, `activePersonStudent`, `activePersonStaff`, `activePersonParent`, `activePersonOther`, `activeApplicationForm`, `activeDataUpdater`) VALUES(003, (SELECT CONCAT(value, ' ID Card') FROM gibbonSetting WHERE scope='System' AND name='country' LIMIT 1), '', 'Y', 'Core', 'ID Card', '[\"documentNumber\",\"filePath\"]', 'N', 3, 1, 1, 1, 1, 1, 1);end
INSERT INTO `gibbonPersonalDocumentType` (`gibbonPersonalDocumentTypeID`, `name`, `description`, `active`, `type`, `document`, `fields`, `required`, `sequenceNumber`, `activePersonStudent`, `activePersonStaff`, `activePersonParent`, `activePersonOther`, `activeApplicationForm`, `activeDataUpdater`) VALUES(004, 'Residency/Visa', '', 'Y', 'Core', 'Document', '[\"documentType\",\"dateExpiry\"]', 'N', 4, 1, 1, 1, 1, 0, 1);end
INSERT INTO `gibbonPersonalDocumentType` (`gibbonPersonalDocumentTypeID`, `name`, `description`, `active`, `type`, `document`, `fields`, `required`, `sequenceNumber`, `activePersonStudent`, `activePersonStaff`, `activePersonParent`, `activePersonOther`, `activeApplicationForm`, `activeDataUpdater`) VALUES(005, 'Birth Certificate', '', 'Y', 'Core', 'Document', '[\"country\",\"filePath\"]', 'N', 5, 1, 1, 1, 1, 0, 1);end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='Students'), 'Personal Document Summary', 0, 'Reports', 'Allows users to view a summary of student personal documents.', 'report_student_personalDocumentSummary.php', 'report_student_personalDocumentSummary.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Students' AND gibbonAction.name='Personal Document Summary'));end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('002', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Students' AND gibbonAction.name='Personal Document Summary'));end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='Reports'), 'Student Name Conflicts', 0, 'Progress', 'Allows users to check report comments for mismatched names.', 'progress_studentNameConflicts.php', 'progress_studentNameConflicts.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Reports' AND gibbonAction.name='Student Name Conflicts'));end
UPDATE gibbonAction SET helpURL = 'administrators/getting-started/getting-started-with-gibbon/#admissions' WHERE name LIKE 'Application Form Settings%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'User Admin');end
UPDATE gibbonAction SET helpURL = 'administrators/getting-started/getting-started-with-gibbon/#user-management-access' WHERE name LIKE 'Manage Permissions%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'User Admin');end
UPDATE gibbonAction SET helpURL = 'administrators/getting-started/getting-started-with-gibbon/#user-management-access' WHERE name LIKE 'Manage Roles%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'User Admin');end
UPDATE gibbonAction SET helpURL = 'administrators/getting-started/getting-started-with-gibbon/#users' WHERE name LIKE 'Manage Users%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'User Admin');end
UPDATE gibbonAction SET helpURL = 'administrators/reports/reporting_cycles/' WHERE name LIKE 'Manage Reporting Cycles%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Reports');end
UPDATE gibbonAction SET helpURL = 'administrators/reports/templates/' WHERE name LIKE 'Template Builder%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Reports');end
UPDATE gibbonAction SET helpURL = 'administrators/timetable/timetabling/' WHERE name LIKE 'Manage Timetables%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Timetable Admin');end
UPDATE gibbonAction SET helpURL = 'administrators/user-admin/data-updater/#family-data' WHERE name LIKE 'Family Data Updates%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Data Updater');end
UPDATE gibbonAction SET helpURL = 'administrators/user-admin/data-updater/#finance-data' WHERE name LIKE 'Finance Data Updates%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Data Updater');end
UPDATE gibbonAction SET helpURL = 'administrators/user-admin/data-updater/#medical-data' WHERE name LIKE 'Medical Form Updates%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Data Updater');end
UPDATE gibbonAction SET helpURL = 'administrators/user-admin/data-updater/#personal-data' WHERE name LIKE 'Personal Data Updates%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Data Updater');end
UPDATE gibbonAction SET helpURL = 'teachers/assess/crowd-assessment/' WHERE name LIKE 'Assess%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Crowd Assessment');end
UPDATE gibbonAction SET helpURL = 'teachers/assess/markbook/' WHERE name LIKE 'Edit Markbook%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Markbook');end
UPDATE gibbonAction SET helpURL = 'teachers/assess/markbook/' WHERE name LIKE 'View Markbook%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Markbook');end
UPDATE gibbonAction SET helpURL = 'teachers/assess/rubrics/' WHERE name LIKE 'View Rubrics%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Rubrics');end
UPDATE gibbonAction SET helpURL = 'teachers/assess/rubrics/#getting-started' WHERE name LIKE 'Manage Rubrics%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Rubrics');end
UPDATE gibbonAction SET helpURL = 'teachers/learn/planner/lesson-planner/' WHERE name LIKE 'Lesson Planner%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Planner');end
UPDATE gibbonAction SET helpURL = 'teachers/other/messenger/' WHERE name LIKE 'View Message Wall%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Messenger');end
UPDATE gibbonAction SET helpURL = 'teachers/other/messenger/#getting-started' WHERE name LIKE 'New Message%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Messenger');end
UPDATE gibbonAction SET helpURL = 'teachers/people/behaviour/' WHERE name LIKE 'Manage Behaviour Records%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Behaviour');end
UPDATE gibbonAction SET helpURL = 'teachers/people/behaviour/' WHERE name LIKE 'View Behaviour Records%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Behaviour');end
UPDATE gibbonAction SET helpURL = 'teachers/people/student-profiles/' WHERE name LIKE 'View Student Profile%' AND gibbonModuleID = (SELECT gibbonModuleID FROM gibbonModule WHERE name = 'Students');end
ALTER TABLE `gibbonBehaviour` ADD `fields` TEXT NULL AFTER `timestamp`;end
ALTER TABLE `gibbonIN` ADD `fields` TEXT NULL AFTER `notes`;end
ALTER TABLE `gibbonINArchive` ADD `fields` TEXT NULL AFTER `archiveTimestamp`;end
UPDATE IGNORE `gibbonApplicationForm` SET `siblingDOB1`=NULL WHERE `siblingDOB1`='0000-00-00';end
UPDATE IGNORE `gibbonApplicationForm` SET `siblingDOB2`=NULL WHERE `siblingDOB2`='0000-00-00';end
UPDATE IGNORE `gibbonApplicationForm` SET `siblingDOB3`=NULL WHERE `siblingDOB3`='0000-00-00';end
UPDATE IGNORE `gibbonFinanceInvoice` SET `paidDate`=NULL WHERE `paidDate`='0000-00-00';end
UPDATE IGNORE `gibbonFinanceInvoice` SET `invoiceDueDate`=NULL WHERE `invoiceDueDate`='0000-00-00';end
UPDATE IGNORE `gibbonLibraryItem` SET `purchaseDate`=NULL WHERE `purchaseDate`='0000-00-00';end
UPDATE IGNORE `gibbonMessengerReceipt` SET `confirmedTimestamp`=NULL WHERE `confirmedTimestamp`='0000-00-00 00:00:00';end
UPDATE IGNORE `gibbonCourse` SET `gibbonDepartmentID`=NULL WHERE `gibbonDepartmentID`=0;end
UPDATE IGNORE `gibbonCrowdAssessDiscuss` SET `gibbonCrowdAssessDiscussIDReplyTo`=NULL WHERE `gibbonCrowdAssessDiscussIDReplyTo`=0;end
UPDATE IGNORE `gibbonFormGroup` SET `gibbonFormGroupIDNext`=NULL WHERE `gibbonFormGroupIDNext`=0;end
UPDATE IGNORE `gibbonInternalAssessmentEntry` SET `gibbonPersonIDLastEdit`=NULL WHERE `gibbonPersonIDLastEdit`=0;end
UPDATE IGNORE `gibbonLibraryItem` SET `gibbonPersonIDReturnAction`=NULL WHERE `gibbonPersonIDReturnAction`=0;end
UPDATE IGNORE `gibbonLibraryItemEvent` SET `gibbonPersonIDReturnAction`=NULL WHERE `gibbonPersonIDReturnAction`=0;end
UPDATE IGNORE `gibbonLibraryItemEvent` SET `gibbonPersonIDStatusResponsible`=NULL WHERE `gibbonPersonIDStatusResponsible`=0;end
UPDATE IGNORE `gibbonMarkbookColumn` SET `gibbonPersonIDCreator`=NULL WHERE `gibbonPersonIDCreator`=0;end
UPDATE IGNORE `gibbonMarkbookColumn` SET `gibbonPersonIDLastEdit`=NULL WHERE `gibbonPersonIDLastEdit`=0;end
UPDATE IGNORE `gibbonPayment` SET `gibbonPersonID`=NULL WHERE `gibbonPersonID`=0;end
UPDATE IGNORE `gibbonPerson` SET `gibboni18nIDPersonal`=NULL WHERE `gibboni18nIDPersonal`=0;end
UPDATE IGNORE `gibbonPerson` SET `gibbonThemeIDPersonal`=NULL WHERE `gibbonThemeIDPersonal`=0;end
UPDATE IGNORE `gibbonPlannerEntry` SET `gibbonUnitID`=NULL WHERE `gibbonUnitID`=0;end
UPDATE IGNORE `gibbonUnit` SET `gibbonPersonIDLastEdit`=gibbonPersonIDCreator WHERE `gibbonPersonIDLastEdit`=0;end
UPDATE IGNORE `gibbonStaffApplicationForm` SET `gibbonPersonID`=NULL WHERE `gibbonPersonID`=0;end
UPDATE IGNORE `gibbonTTDayRowClass` SET `gibbonSpaceID`=NULL WHERE `gibbonSpaceID`=0;end
UPDATE IGNORE `gibbonPerson` SET `dob`=NULL WHERE `dob`='0000-00-00';end
UPDATE IGNORE `gibbonPersonUpdate` SET `dob`=NULL WHERE `dob`='0000-00-00';end
UPDATE `gibbonSetting` SET value='fe4abccf405facac24e05de854d764a6' WHERE scope='System Admin' AND name='composerLockHash';end
UPDATE gibbonNotificationEvent SET actionName='Parent Weekly Email Summary' WHERE event='Parent Daily Email Summary' OR event='Tutor Daily Email Summary';end
SELECT NULL;end
ALTER TABLE `gibbonPersonalDocumentType` CHANGE `document` `document` ENUM('Passport','ID Card','Visa','Document') NOT NULL DEFAULT 'Document';end
ALTER TABLE `gibbonPersonalDocument` ADD `document` ENUM('Passport','ID Card','Visa','Document') NOT NULL DEFAULT 'Document' AFTER `foreignTableID`;end
UPDATE `gibbonPersonalDocumentType` SET `document`='Visa' WHERE `gibbonPersonalDocumentType`.name LIKE '%Visa%';end
UPDATE `gibbonPersonalDocument` SET `document`=(SELECT type.document FROM gibbonPersonalDocumentType as type WHERE type.gibbonPersonalDocumentTypeID=gibbonPersonalDocument.gibbonPersonalDocumentTypeID);end
INSERT IGNORE INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES (006, (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Data Updater' AND gibbonAction.name='Update Staff Data_my'));end
ALTER TABLE `gibbonGroup` CHANGE `name` `name` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;end

";

//v22.0.01
++$count;
$sql[$count][0] = '22.0.01';
$sql[$count][1] = "
UPDATE gibbonPersonalDocument SET document=(SELECT document FROM gibbonPersonalDocumentType WHERE gibbonPersonalDocumentType.gibbonPersonalDocumentTypeID=gibbonPersonalDocument.gibbonPersonalDocumentTypeID);end
";

//v23.0.00
++$count;
$sql[$count][0] = '23.0.00';
$sql[$count][1] = "
UPDATE gibbonPersonalDocument SET document=(SELECT document FROM gibbonPersonalDocumentType WHERE gibbonPersonalDocumentType.gibbonPersonalDocumentTypeID=gibbonPersonalDocument.gibbonPersonalDocumentTypeID);end
ALTER TABLE `gibbonEmailTemplate` CHANGE `templateName` `templateType` VARCHAR(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;end
ALTER TABLE `gibbonEmailTemplate` ADD `templateName` VARCHAR(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `moduleName`;end
UPDATE `gibbonEmailTemplate` SET `templateName`=`templateType` WHERE `templateName`='';end
UPDATE `gibbonAction` SET `URLList` = 'emailTemplates_manage.php,emailTemplates_manage_duplicate.php,emailTemplates_manage_edit.php,emailTemplates_manage_delete.php' WHERE `name`='Email Templates' AND `gibbonModuleID`=(SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin');end
ALTER TABLE `gibbonEmailTemplate` DROP INDEX `templateName`, ADD UNIQUE `moduleTemplate` (`templateName`, `moduleName`) USING BTREE;end
ALTER TABLE `gibbonEmailTemplate` ADD `type` ENUM('Core','Additional','Custom') NOT NULL DEFAULT 'Core' AFTER `gibbonEmailTemplateID`;end
INSERT INTO `gibbonSetting` (`scope` ,`name` ,`nameDisplay` ,`description` ,`value`) VALUES ('System Admin', 'importCustomFolderLocation', 'Custom Imports Folder', 'Path to custom import types folder, relative to uploads.', '/imports');end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='Attendance'), 'Ad Hoc Attendance', 0, 'Take Attendance', 'Allows users to take school-wide attendance for ad hoc groups of students.', 'attendance_take_adHoc.php', 'attendance_take_adHoc.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Attendance' AND gibbonAction.name='Ad Hoc Attendance'));end
ALTER TABLE `gibbonLibraryItem` ADD `cost` decimal(10,2) DEFAULT NULL AFTER `invoiceNumber`;end
UPDATE `gibbonSetting` SET name='paymentAPIUsername', nameDisplay='API Username', description='API details are provided by the payment gateway provider' WHERE scope='System' AND name='paypalAPIUsername';end
UPDATE `gibbonSetting` SET name='paymentAPIPassword', nameDisplay='API Password', description='API details are provided by the payment gateway provider' WHERE scope='System' AND name='paypalAPIPassword';end
UPDATE `gibbonSetting` SET name='paymentAPISignature', nameDisplay='API Signature', description='API details are provided by the payment gateway provider' WHERE scope='System' AND name='paypalAPISignature';end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System', 'paymentAPIKey', 'API Key', 'API details are provided by the payment gateway provider', '');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System', 'paymentGateway', 'Payment Gateway', 'Choose a payment gateway. You must create and configure an account with the selected service to get the required API details.', '');end
ALTER TABLE `gibbonPayment` CHANGE `gateway` `gateway` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;end
UPDATE `gibbonSetting` SET value=REPLACE(value, 'PayPal', 'online') WHERE scope='Application Form' AND name='applicationProcessFeeText';end
UPDATE `gibbonSetting` SET value='PayPal' WHERE scope='System' AND name='paymentGateway' AND value='';end
CREATE TABLE IF NOT EXISTS `gibbonSession` ( `gibbonSessionID` VARCHAR(40) NOT NULL , `gibbonPersonID` INT(10) UNSIGNED ZEROFILL NULL , `sessionData` TEXT NULL , `timestampCreated` TIMESTAMP NULL , `timestampModified` TIMESTAMP NULL , PRIMARY KEY (`gibbonSessionID`)) ENGINE = InnoDB;end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System Admin', 'remoteCLIKey', 'Remote CLI Key', 'Allow command line scripts to be run remotely using a secure key. The key can be passed as a URL parameter called remoteCLIKey.', '');end
ALTER TABLE `gibbonSession` CHANGE `gibbonSessionID` `gibbonSessionID` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin'), 'Active Sessions', 0, 'Utilities', '', 'activeSessions.php', 'activeSessions.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='System Admin' AND gibbonAction.name='Active Sessions'));end
ALTER TABLE `gibbonSession` ADD `gibbonActionID` INT(7) UNSIGNED ZEROFILL NULL DEFAULT NULL AFTER `gibbonPersonID`;end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System Admin', 'maintenanceMode', 'Maintenance Mode', 'Only users with the Administrator role can login during maintenance mode. Enabling this will logout all other users.', 'N');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System Admin', 'maintenanceModeMessage', 'Maintenance Mode Message', 'A message to display on all pages when maintenance mode is active.', 'The system is currently in maintenance mode. Only system administrators will be able to login at this time.');end
ALTER TABLE `gibbonSession` ADD `sessionStatus` VARCHAR(20) DEFAULT NULL AFTER `sessionData`;end
INSERT INTO `gibbonEmailTemplate` (`templateType`, `templateName`, `moduleName`, `templateSubject`, `templateBody`, `variables`, `timestamp`) VALUES ('Negative Behaviour Letter 1', 'Negative Behaviour Letter 1', 'Behaviour', 'Behaviour Letter for {{studentSurname}}, {{studentPreferredName}} ({{studentFormGroup}}) via {{systemName}} at {{organisationName}}', 'Dear Parent/Guardian,<br/><br/>This letter has been automatically generated to alert you to the fact that your child, {{studentPreferredName}}, has reached {{behaviourCount}} negative behaviour incidents. Please see the list below for the details of these incidents:<br/><br/>{{behaviourRecord|raw}}<br/>This letter represents the first communication in a sequence of 3 potential alerts, each of which is more critical than the last.<br/><br/>If you would like more information on this matter, please contact your child\'s tutor.', '{\"behaviourCount\": [\"randomDigit\"], \r\n\"behaviourRecord\": [\"paragraph\"], \r\n\"studentPreferredName\": [\"firstName\"],\r\n\"studentSurname\": [\"lastName\"],\r\n\"studentFormGroup\": \"Y07\",\r\n\"parentPreferredName\": [\"firstNameFemale\"],\r\n\"parentSurname\": [\"lastName\"],\r\n\"parentTitle\": [\"titleFemale\"],\r\n\"formTutorPreferredName\": [\"firstNameMale\"],\r\n\"formTutorSurname\": [\"lastName\"],\r\n\"formTutorTitle\": [\"titleMale\"],\r\n\"formTutorEmail\": [\"safeEmail\"],\r\n\"date\": [\"date\"]\r\n}', '2021-10-20 13:58:10');end
INSERT INTO `gibbonEmailTemplate` (`templateType`, `templateName`, `moduleName`, `templateSubject`, `templateBody`, `variables`, `timestamp`) VALUES ('Negative Behaviour Letter 2', 'Negative Behaviour Letter 2', 'Behaviour', 'Behaviour Letter for {{studentSurname}}, {{studentPreferredName}} ({{studentFormGroup}}) via {{systemName}} at {{organisationName}}', 'Dear Parent/Guardian,<br/><br/>This letter has been automatically generated to alert you to the fact that your child, {{studentPreferredName}}, has reached {{behaviourCount}} negative behaviour incidents. Please see the list below for the details of these incidents:<br/><br/>{{behaviourRecord|raw}}<br/>This letter represents the second communication in a sequence of 3 potential alerts, each of which is more critical than the last.<br/><br/>If you would like more information on this matter, please contact your child\'s tutor.', '{\"behaviourCount\": [\"randomDigit\"], \r\n\"behaviourRecord\": [\"paragraph\"], \r\n\"studentPreferredName\": [\"firstName\"],\r\n\"studentSurname\": [\"lastName\"],\r\n\"studentFormGroup\": \"Y07\",\r\n\"parentPreferredName\": [\"firstNameFemale\"],\r\n\"parentSurname\": [\"lastName\"],\r\n\"parentTitle\": [\"titleFemale\"],\r\n\"formTutorPreferredName\": [\"firstNameMale\"],\r\n\"formTutorSurname\": [\"lastName\"],\r\n\"formTutorTitle\": [\"titleMale\"],\r\n\"formTutorEmail\": [\"safeEmail\"],\r\n\"date\": [\"date\"]\r\n}', '2021-10-20 13:58:10');end
INSERT INTO `gibbonEmailTemplate` (`templateType`, `templateName`, `moduleName`, `templateSubject`, `templateBody`, `variables`, `timestamp`) VALUES ('Negative Behaviour Letter 3', 'Negative Behaviour Letter 3', 'Behaviour', 'Behaviour Letter for {{studentSurname}}, {{studentPreferredName}} ({{studentFormGroup}}) via {{systemName}} at {{organisationName}}', 'Dear Parent/Guardian,<br/><br/>This letter has been automatically generated to alert you to the fact that your child, {{studentPreferredName}}, has reached {{behaviourCount}} negative behaviour incidents. Please see the list below for the details of these incidents:<br/><br/>{{behaviourRecord|raw}}<br/>This letter represents the final communication in a sequence of 3 potential alerts, each of which is more critical than the last.<br/><br/>If you would like more information on this matter, please contact your child\'s tutor.', '{\"behaviourCount\": [\"randomDigit\"], \r\n\"behaviourRecord\": [\"paragraph\"], \r\n\"studentPreferredName\": [\"firstName\"],\r\n\"studentSurname\": [\"lastName\"],\r\n\"studentFormGroup\": \"Y07\",\r\n\"parentPreferredName\": [\"firstNameFemale\"],\r\n\"parentSurname\": [\"lastName\"],\r\n\"parentTitle\": [\"titleFemale\"],\r\n\"formTutorPreferredName\": [\"firstNameMale\"],\r\n\"formTutorSurname\": [\"lastName\"],\r\n\"formTutorTitle\": [\"titleMale\"],\r\n\"formTutorEmail\": [\"safeEmail\"],\r\n\"date\": [\"date\"]\r\n}', '2021-10-20 13:58:10');end
UPDATE `gibbonSetting` SET name='behaviourLettersNegativeLetter1Count', nameDisplay='Negative Letter 1 Count' WHERE scope='Behaviour' AND name='behaviourLettersLetter1Count';end
UPDATE `gibbonSetting` SET name='behaviourLettersNegativeLetter2Count', nameDisplay='Negative Letter 2 Count' WHERE scope='Behaviour' AND name='behaviourLettersLetter2Count';end
UPDATE `gibbonSetting` SET name='behaviourLettersNegativeLetter3Count', nameDisplay='Negative Letter 3 Count' WHERE scope='Behaviour' AND name='behaviourLettersLetter3Count';end
UPDATE `gibbonSetting` SET name='enableNegativeBehaviourLetters', nameDisplay='Enable Negative Behaviour Letters' WHERE scope='Behaviour' AND name='enableBehaviourLetters';end
ALTER TABLE `gibbonPerson` ADD `microsoftAPIRefreshToken` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `googleAPIRefreshToken`;end
ALTER TABLE `gibbonPerson` ADD `genericAPIRefreshToken` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `microsoftAPIRefreshToken`;end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System Admin', 'ssoGoogle', 'Google Integration', '', '{\"enabled\":\"N\"}');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System Admin', 'ssoMicrosoft', 'Microsoft Integration', '', '{\"enabled\":\"N\"}');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('System Admin', 'ssoOther', 'Generic OAuth2 Provider', '', '{\"enabled\":\"N\"}');end
INSERT IGNORE INTO `gibbonSetting` (`gibbonSettingID`, `scope`, `name`, `nameDisplay`, `description`, `value`) VALUES (00328, 'System', 'registerGibbonSupport', 'Receive Support?', 'Join our mailing list and recieve a welcome email from the team.', '');end
ALTER TABLE `gibbonPerson` CHANGE `googleAPIRefreshToken` `googleAPIRefreshToken` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;end
ALTER TABLE `gibbonPerson` CHANGE `microsoftAPIRefreshToken` `microsoftAPIRefreshToken` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;end
ALTER TABLE `gibbonPerson` CHANGE `genericAPIRefreshToken` `genericAPIRefreshToken` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;end
ALTER TABLE `gibbonSession` CHANGE `sessionData` `sessionData` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;end
ALTER TABLE `gibbonPerson` DROP `password`;end
UPDATE `gibbonMarkbookColumn` SET gibbonPlannerEntryID=NULL WHERE gibbonPlannerEntryID=00000000000000;end
UPDATE `gibbonCountry` SET `printable_name` = 'Libya', `iddCountryCode` = '00218' WHERE `gibbonCountry`.`printable_name` LIKE '%Libya%';end
INSERT INTO `gibbonLanguage` (`gibbonLanguageID`, `name`) VALUES (NULL, 'Tamazight');end
ALTER TABLE `gibbonReportingCriteriaType` ADD `defaultValue` VARCHAR(255) DEFAULT NULL AFTER `valueType`;end
CREATE TABLE IF NOT EXISTS `gibbonPersonStatusLog` (`gibbonPersonStatusLogID` int(12) UNSIGNED ZEROFILL NOT NULL, `gibbonPersonID` int(10) UNSIGNED ZEROFILL NOT NULL, `statusOld` enum('Full','Expected','Left','Pending Approval') NOT NULL DEFAULT 'Full', `statusNew` enum('Full','Expected','Left','Pending Approval') NOT NULL DEFAULT 'Full', `reason` text NOT NULL, `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8;end
ALTER TABLE `gibbonPersonStatusLog` ADD PRIMARY KEY (`gibbonPersonStatusLogID`), ADD KEY `gibbonPersonID` (`gibbonPersonID`);end
ALTER TABLE `gibbonPersonStatusLog` MODIFY `gibbonPersonStatusLogID` int(12) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('Behaviour', 'enablePositiveBehaviourLetters', 'Enable Positive Behaviour Letters', 'Should automated behaviour letter functionality be enabled?', 'N');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('Behaviour', 'behaviourLettersPositiveLetter1Count', 'Positive Letter 1 Count', 'After how many positive records should letter 1 be sent?', '3');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('Behaviour', 'behaviourLettersPositiveLetter2Count', 'Positive Letter 2 Count', 'After how many positive records should letter 2 be sent?', '6');end
INSERT INTO `gibbonSetting` (`scope`, `name`, `nameDisplay`, `description`, `value`) VALUES ('Behaviour', 'behaviourLettersPositiveLetter3Count', 'Positive Letter 3 Count', 'After how many positive records should letter 3 be sent?', '9');end
INSERT INTO `gibbonEmailTemplate` (`templateType`, `templateName`, `moduleName`, `templateSubject`, `templateBody`, `variables`, `timestamp`) VALUES ('Positive Behaviour Letter 1', 'Positive Behaviour Letter 1', 'Behaviour', 'Positive Behaviour Letter for {{studentSurname}}, {{studentPreferredName}} ({{studentFormGroup}}) via {{systemName}} at {{organisationName}}', '', '{\"behaviourCount\": [\"randomDigit\"], \r\n\"behaviourRecord\": [\"paragraph\"], \r\n\"studentPreferredName\": [\"firstName\"],\r\n\"studentSurname\": [\"lastName\"],\r\n\"studentFormGroup\": \"Y07\",\r\n\"parentPreferredName\": [\"firstNameFemale\"],\r\n\"parentSurname\": [\"lastName\"],\r\n\"parentTitle\": [\"titleFemale\"],\r\n\"formTutorPreferredName\": [\"firstNameMale\"],\r\n\"formTutorSurname\": [\"lastName\"],\r\n\"formTutorTitle\": [\"titleMale\"],\r\n\"formTutorEmail\": [\"safeEmail\"],\r\n\"date\": [\"date\"]\r\n}', '2021-10-20 13:58:10');end
INSERT INTO `gibbonEmailTemplate` (`templateType`, `templateName`, `moduleName`, `templateSubject`, `templateBody`, `variables`, `timestamp`) VALUES ('Positive Behaviour Letter 2', 'Positive Behaviour Letter 2', 'Behaviour', 'Positive Behaviour Letter for {{studentSurname}}, {{studentPreferredName}} ({{studentFormGroup}}) via {{systemName}} at {{organisationName}}', '', '{\"behaviourCount\": [\"randomDigit\"], \r\n\"behaviourRecord\": [\"paragraph\"], \r\n\"studentPreferredName\": [\"firstName\"],\r\n\"studentSurname\": [\"lastName\"],\r\n\"studentFormGroup\": \"Y07\",\r\n\"parentPreferredName\": [\"firstNameFemale\"],\r\n\"parentSurname\": [\"lastName\"],\r\n\"parentTitle\": [\"titleFemale\"],\r\n\"formTutorPreferredName\": [\"firstNameMale\"],\r\n\"formTutorSurname\": [\"lastName\"],\r\n\"formTutorTitle\": [\"titleMale\"],\r\n\"formTutorEmail\": [\"safeEmail\"],\r\n\"date\": [\"date\"]\r\n}', '2021-10-20 13:58:10');end
INSERT INTO `gibbonEmailTemplate` (`templateType`, `templateName`, `moduleName`, `templateSubject`, `templateBody`, `variables`, `timestamp`) VALUES ('Positive Behaviour Letter 3', 'Positive Behaviour Letter 3', 'Behaviour', 'Positive Behaviour Letter for {{studentSurname}}, {{studentPreferredName}} ({{studentFormGroup}}) via {{systemName}} at {{organisationName}}', '', '{\"behaviourCount\": [\"randomDigit\"], \r\n\"behaviourRecord\": [\"paragraph\"], \r\n\"studentPreferredName\": [\"firstName\"],\r\n\"studentSurname\": [\"lastName\"],\r\n\"studentFormGroup\": \"Y07\",\r\n\"parentPreferredName\": [\"firstNameFemale\"],\r\n\"parentSurname\": [\"lastName\"],\r\n\"parentTitle\": [\"titleFemale\"],\r\n\"formTutorPreferredName\": [\"firstNameMale\"],\r\n\"formTutorSurname\": [\"lastName\"],\r\n\"formTutorTitle\": [\"titleMale\"],\r\n\"formTutorEmail\": [\"safeEmail\"],\r\n\"date\": [\"date\"]\r\n}', '2021-10-20 13:58:10');end
ALTER TABLE `gibbonBehaviourLetter` ADD `type` ENUM('Negative','Positive') NOT NULL DEFAULT 'Negative' AFTER `status`;end
CREATE TABLE `gibbonActivityType` ( `gibbonActivityTypeID` INT(6) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT , `name` VARCHAR(60) NULL, `description` TEXT NULL , `access` ENUM('None','View','Register') NULL DEFAULT 'Register', `enrolmentType` ENUM('Competitive','Selection') NULL DEFAULT 'Competitive', `maxPerStudent` INT(3) NOT NULL DEFAULT '0' , `waitingList` ENUM('Y','N') NULL DEFAULT 'Y', `backupChoice` ENUM('Y','N') NULL DEFAULT 'Y', PRIMARY KEY (`gibbonActivityTypeID`), UNIQUE KEY (`name`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;end
UPDATE `gibbonAction` SET `URLList` = 'activitySettings.php,activitySettings_type_add.php,activitySettings_type_edit.php,activitySettings_type_delete.php' WHERE `name`='Activity Settings' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='School Admin');end
UPDATE gibbonSetting SET value='br[style],strong[style],b[style],em[style],span[style],p[style],address[style],pre[style|class],h1[style],h2[style],h3[style],h4[style],h5[style],h6[style],table[style],thead[style],tbody[style],tfoot[style],tr[style],td[style|colspan|rowspan],ol[style],ul[style],li[style],blockquote[style],a[style|target|href],img[style|class|src|width|height],video[style],source[style],hr[style],iframe[style|width|height|src|frameborder|allowfullscreen],embed[style],div[style],sup[style],sub[style],code[style|class],details[style|class],summary[style|class]' WHERE name='allowableHTML' AND scope='System';end
SELECT 'Hello, this blank line is important, nothing to see here.';end
";

//v23.0.01
++$count;
$sql[$count][0] = '23.0.01';
$sql[$count][1] = "
INSERT INTO `gibboni18n` (`code`, `name`, `version`, `active`, `installed`, `systemDefault`, `dateFormat`, `dateFormatRegEx`, `dateFormatPHP`, `rtl`) VALUES ('es_DO', 'Español - República Dominicana', '23.0.01', 'Y', 'Y', 'N', 'dd/mm/yyyy', '/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\\d\\d$/i', 'd/m/Y', 'N');end
";

//v23.0.02
++$count;
$sql[$count][0] = '23.0.02';
$sql[$count][1] = "";

//v24.0.00
++$count;
$sql[$count][0] = '24.0.00';
$sql[$count][1] = "
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='System Admin'), 'Upload Photos & Files', 0, 'Data', '', 'file_upload.php', 'file_upload.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='System Admin' AND gibbonAction.name='Upload Photos & Files'));end
DELETE `gibbonAction`, `gibbonPermission` FROM `gibbonAction` JOIN `gibbonPermission` ON (gibbonAction.gibbonActionID=gibbonPermission.gibbonActionID) WHERE gibbonAction.name='Import User Photos' AND gibbonAction.gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='User Admin');end
ALTER TABLE `gibbonString` CHANGE `original` `original` VARCHAR(255) NOT NULL, CHANGE `replacement` `replacement` VARCHAR(255) NOT NULL;end
INSERT INTO `gibbonModule` (`gibbonModuleID`, `name`, `description`, `entryURL`, `type`, `active`, `category`, `version`, `author`, `url`) VALUES (NULL, 'Admissions', '', 'studentEnrolment_manage.php', 'Core', 'Y', 'People', '', 'Sandra Kuipers', 'https://github.com/SKuipers');end
UPDATE `gibbonAction` SET category='Current Students', gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Admissions') WHERE name='Student Enrolment' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Students');end
UPDATE `gibbonAction` SET category='Current Students', gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Admissions') WHERE name='Withdraw Student' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Students');end
UPDATE `gibbonAction` SET category='Reports', gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Admissions') WHERE name='New Students' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Students');end
UPDATE `gibbonAction` SET category='Reports', gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Admissions') WHERE name='Left Students' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Students');end
UPDATE `gibbonAction` SET category='Visualise', gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Admissions') WHERE name='Student Enrolment Trends' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Students');end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='Admissions'), 'Admissions Inquiries', 0, 'Prospective Students', '', 'admissions_manage.php', 'admissions_manage.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Admissions' AND gibbonAction.name='Admissions Inquiries'));end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='Admissions'), 'Manage Applications', 0, 'Prospective Students', '', 'applications_manage.php', 'applications_manage.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Admissions' AND gibbonAction.name='Manage Applications'));end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES((SELECT gibbonModuleID FROM gibbonModule WHERE name='Admissions'), 'Manage Other Forms', 0, 'Prospective Students', '', 'forms_manage.php', 'forms_manage.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Admissions' AND gibbonAction.name='Manage Other Forms'));end
UPDATE gibbonCountry SET iddCountryCode='218' WHERE printable_name='Libya';end
UPDATE gibbonSetting SET value=CONCAT(value, ',pagebreak,columnbreak') WHERE name='allowableHTML' AND scope='System';end
UPDATE gibbonPlannerEntry SET homeworkSubmissionDrafts=NULL WHERE homeworkSubmissionDrafts='N';end
ALTER TABLE `gibbonDiscussion` ADD INDEX(`foreignTable`, `foreignTableID`);end
ALTER TABLE `gibbonDiscussion` ADD INDEX(`gibbonPersonID`);end
INSERT INTO `gibbonNotificationEvent` (`event`, `moduleName`, `actionName`, `type`, `scopes`, `active`) VALUES ('Updated Timetable Subscriber', 'Timetable', 'View Timetable by Person', 'Core', 'All', 'Y');end
ALTER TABLE gibbonPerson ADD `mfaSecret` VARCHAR(16) DEFAULT NULL AFTER `receiveNotificationEmails`;end
ALTER TABLE gibbonPerson ADD `mfaToken` TEXT DEFAULT NULL AFTER `mfaSecret`;end
";
