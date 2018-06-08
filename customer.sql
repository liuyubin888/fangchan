/*
Navicat MySQL Data Transfer

Source Server         : this
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : yuefanghui

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2018-06-07 23:20:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for customer
-- ----------------------------
DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(255) NOT NULL,
  `share_identification` varchar(255) NOT NULL COMMENT '个人分享唯一标识',
  `name` varchar(255) NOT NULL COMMENT '姓名',
  `created` datetime NOT NULL COMMENT '创建时间',
  `contact_status` int(11) NOT NULL DEFAULT '1' COMMENT '联系状态:1--未联系，2--已联系',
  `receive_status` int(11) NOT NULL DEFAULT '1' COMMENT '发放奖品状态：1--未发放，2--已发放',
  `share_user_identification` varchar(255) DEFAULT NULL COMMENT '分享人标识',
  `wx_info_id` int(11) DEFAULT NULL COMMENT '微信用户信息记录ID',
  `prize_grant_date` datetime DEFAULT NULL COMMENT '奖品发放时间',
  `contact_date` datetime DEFAULT NULL COMMENT '联系时间',
  `prize_name` varchar(255) DEFAULT NULL COMMENT '奖品名称',
  `contact_name` text COMMENT '联系结果',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_mobile` (`mobile`),
  UNIQUE KEY `unique_share_bs` (`share_identification`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of customer
-- ----------------------------
INSERT INTO `customer` VALUES ('1', '18126788811', 'dsfdsf231sd231', 'fdds', '2018-01-08 16:06:15', '2', '1', 'sdf48654', null, '2005-06-03 22:59:44', '2005-06-07 22:08:47', '奖品为IPHONE X一台', '联系结果');
INSERT INTO `customer` VALUES ('2', '17688445071', 'sdadasdsadas', '5071', '2018-01-24 16:06:15', '2', '2', 'dsfdsf231sd231', null, '2005-06-01 22:58:40', '2005-05-02 22:12:58', 'sasasasa', 'dsfgdgdd');
