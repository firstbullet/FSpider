SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for fs_content
-- ----------------------------
DROP TABLE IF EXISTS `fs_content`;
CREATE TABLE `fs_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `url` varchar(100) NOT NULL COMMENT '信息源地址',
  `img` varchar(255) NOT NULL COMMENT '信息配图',
  `add_time` varchar(50) DEFAULT NULL,
  `from` varchar(150) DEFAULT NULL COMMENT '信息出处',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2628 DEFAULT CHARSET=utf8;
