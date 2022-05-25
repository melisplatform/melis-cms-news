SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `melis_cms_news_texts`
-- -----------------------------------------------------
  ALTER TABLE `melis_cms_news_texts`
  ADD `cnews_paragraph5` LONGTEXT NULL AFTER `cnews_paragraph4`,
  ADD `cnews_paragraph6` LONGTEXT NULL AFTER `cnews_paragraph5`,
  ADD `cnews_paragraph7` LONGTEXT NULL AFTER `cnews_paragraph6`,
  ADD `cnews_paragraph8` LONGTEXT NULL AFTER `cnews_paragraph7`,
  ADD `cnews_paragraph9` LONGTEXT NULL AFTER `cnews_paragraph8`,
  ADD `cnews_paragraph10` LONGTEXT NULL AFTER `cnews_paragraph9`,
  ADD `cnews_paragraph_order` VARCHAR(255) NULL AFTER `cnews_paragraph10`;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
