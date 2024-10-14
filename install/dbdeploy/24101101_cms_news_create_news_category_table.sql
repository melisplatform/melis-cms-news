DROP TABLE IF EXISTS `melis_cms_news_category`;

CREATE TABLE `melis_cms_news_category` (
  `cnc_id` int(11) NOT NULL AUTO_INCREMENT,
  `cnc_cnews_id` int(11) NOT NULL,
  `cnc_cat2_id` int(11) NOT NULL,
  `cnc_order` int NULL,
  PRIMARY KEY (`cnc_id`),
  KEY `cnc_cnews_id` (`cnc_cnews_id`),
  KEY `cnc_cat2_id` (`cnc_cat2_id`),
  CONSTRAINT `melis_cms_news_category_ibfk_1` FOREIGN KEY (`cnc_cnews_id`) REFERENCES `melis_cms_news` (`cnews_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
