SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `melis_cms_news_seo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `melis_cms_news_seo`;
CREATE TABLE IF NOT EXISTS `melis_cms_news_seo` (
  `cnews_seo_id` int(11) NOT NULL AUTO_INCREMENT,
  `cnews_id` int(11) NOT NULL,
  `cnews_seo_lang_id` int(11) NOT NULL,
  `cnews_seo_url` varchar(255) DEFAULT NULL,
  `cnews_seo_url_redirect` varchar(255) DEFAULT NULL,
  `cnews_seo_url_301` varchar(255) DEFAULT NULL,
  `cnews_seo_meta_title` text DEFAULT NULL,
  `cnews_seo_meta_description` text DEFAULT NULL,
  `cnews_seo_canonical` text DEFAULT NULL,
  PRIMARY KEY (`cnews_seo_id`)) 
  ENGINE=InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
