SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table structure for table `melis_cms_news_workflow`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `melis_cms_news_workflow`;
CREATE TABLE IF NOT EXISTS `melis_cms_news_workflow` (
  `cnews_wf_id` int(11) NOT NULL AUTO_INCREMENT,
  `cnews_wf_process_finished` int(11) NOT NULL DEFAULT 0,
  `cnews_wf_item_key_id` varchar(20) NOT NULL,
  `cnews_wf_type` varchar(10) NOT NULL,
  `cnews_wf_details` varchar(255) DEFAULT NULL,
  `cnews_wf_opening_js` varchar(255) NOT NULL,
  `cnews_wf_date` datetime NOT NULL,
  PRIMARY KEY (`cnews_wf_id`))
ENGINE=InnoDB;


-- ---------------------------------------------------------
-- Table structure for table `melis_cms_news_workflow_events`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `melis_cms_news_workflow_events`;
CREATE TABLE `melis_cms_news_workflow_events` (
  `cnews_wfe_id` int(11) NOT NULL AUTO_INCREMENT,
  `cnews_wfe_action` enum('VALIDATION','VALIDATED','REFUSED','') NOT NULL,
  `cnews_wfe_wf_id` int(11) NOT NULL,
  `cnews_wfe_from_user_id` int(11) NOT NULL,
  `cnews_wfe_to_user_id` int(11) NOT NULL,
  `cnews_wfe_to_role_id` int(11) DEFAULT NULL,
  `cnews_wfe_date` datetime NOT NULL,
  PRIMARY KEY (`cnews_wfe_id`))
ENGINE=InnoDB;

ALTER TABLE `melis_cms_news_workflow_events`
  ADD KEY `cnews_wf_id_idx` (`cnews_wfe_wf_id`);



-- ---------------------------------------------------------
-- Table structure for table `melis_cms_news_workflow_comment`
-- ---------------------------------------------------------
CREATE TABLE `melis_cms_news_workflow_comment` (
  `cnews_com_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'News comment Id',
  `cnews_com_type_id` int(11) NOT NULL COMMENT 'Type Id of the comment',
  `cnews_com_user_id` int(11) NOT NULL COMMENT 'BO user Id',
  `cnews_com_news_id` int(11) NOT NULL,
  `cnews_com_date` datetime NOT NULL COMMENT 'Date of the comment',
  `cnews_com_title` varchar(255) DEFAULT NULL COMMENT 'Title of the comment if needed',
  `cnews_com_text` text NOT NULL COMMENT 'Text of the comment',
  PRIMARY KEY (`cnews_com_id`))
ENGINE=InnoDB;

ALTER TABLE `melis_cms_news_workflow_comment` 
  ADD KEY `cnews_com_type_idx` (`cnews_com_type_id`);


-- ---------------------------------------------------------------
-- Table structure for table `melis_cms_news_workflow_comment_type`
-- ---------------------------------------------------------------
CREATE TABLE `melis_cms_news_workflow_comment_type` (
  `cnews_comt_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Type comment Id for news',
  `cnews_comt_translate_key` varchar(45) NOT NULL COMMENT 'Translation for the type name to be found in module translations files',
  PRIMARY KEY (`cnews_comt_id`))
ENGINE=InnoDB;


-- ---------------------------------------------------------------
-- Data for `melis_cms_news_workflow_comment_type`
-- ---------------------------------------------------------------
START TRANSACTION;
INSERT INTO `melis_cms_news_workflow_comment_type` (`cnews_comt_id`, `cnews_comt_translate_key`) VALUES
(1, 'tr_meliscmsnews_type_PAGE'),
(2, 'tr_meliscmsnews_type_WORKFLOW');
COMMIT;

-- ---------------------------------------------------------------
-- New data for `melis_core_log_type`
-- ---------------------------------------------------------------
START TRANSACTION;
INSERT INTO `melis_core_log_type` ( `logt_code`) VALUES
('NEWS_WORKFLOW_DEMAND'),
('NEWS_WORKFLOW_VALIDATED'),
('NEWS_WORKFLOW_REFUSED');
COMMIT;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
