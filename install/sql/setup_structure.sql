-- MySQL Script generated by MySQL Workbench
-- Wed Feb 28 12:21:10 2018
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `mydb` ;

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`melis_cms_news`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`melis_cms_news` ;

CREATE TABLE IF NOT EXISTS `mydb`.`melis_cms_news` (
  `cnews_id` INT NOT NULL AUTO_INCREMENT,
  `cnews_status` TINYINT(4) NOT NULL,
  `cnews_image1` LONGTEXT NULL,
  `cnews_image2` LONGTEXT NULL,
  `cnews_image3` LONGTEXT NULL,
  `cnews_documents1` LONGTEXT NULL,
  `cnews_documents2` LONGTEXT NULL,
  `cnews_documents3` LONGTEXT NULL,
  `cnews_creation_date` DATETIME NOT NULL,
  `cnews_publish_date` DATETIME NULL,
  `cnews_unpublish_date` DATETIME NULL,
  `cnews_slider_id` INT NULL,
  `cnews_site_id` INT NULL,
  PRIMARY KEY (`cnews_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`melis_cms_news_texts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`melis_cms_news_texts` ;

CREATE TABLE IF NOT EXISTS `mydb`.`melis_cms_news_texts` (
  `cnews_text_id` INT NOT NULL AUTO_INCREMENT,
  `cnews_title` VARCHAR(255) NULL,
  `cnews_subtitle` VARCHAR(255) NULL,
  `cnews_paragraph1` LONGTEXT NULL,
  `cnews_paragraph2` LONGTEXT NULL,
  `cnews_paragraph3` LONGTEXT NULL,
  `cnews_paragraph4` LONGTEXT NULL,
  `cnews_id` INT(11) NULL,
  `cnews_lang_id` INT(11) NULL,
  PRIMARY KEY (`cnews_text_id`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
