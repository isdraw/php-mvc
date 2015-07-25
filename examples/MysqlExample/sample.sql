/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2015-07-25 16:06:45
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `sample`
-- ----------------------------
DROP TABLE IF EXISTS `sample`;
CREATE TABLE `sample` (
  `sample_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sample_name` tinytext,
  `sample_text` text,
  `sample_createtime` datetime DEFAULT NULL,
  PRIMARY KEY (`sample_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of sample
-- ----------------------------
