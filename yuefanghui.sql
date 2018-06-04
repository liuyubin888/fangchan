/*
Navicat MySQL Data Transfer

Source Server         : this
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : yuefanghui

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2018-06-03 04:27:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL COMMENT '帐号',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `salt` varchar(255) NOT NULL COMMENT '加密盐',
  `status` varchar(255) NOT NULL,
  `groupid` int(11) NOT NULL,
  `created` datetime NOT NULL COMMENT '创建时间',
  `lastvisit` datetime NOT NULL COMMENT '最近一次登录时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', '18126788811', 'ef9169144e811171dd9ef2a0c2279023', 'C3v7gj', '0', '1', '0000-00-00 00:00:00', '2018-06-02 21:41:28');
INSERT INTO `admin` VALUES ('2', '17688445073', 'abbdac1047f138857d4675bac9681d64', 'N0Q0tA', '1', '1', '0000-00-00 00:00:00', '2018-06-02 21:41:20');
INSERT INTO `admin` VALUES ('23', 'wenwen', 'eb397026c83ef6dda58340a82e8924d6', 'dR119v', '1', '1', '2018-06-02 21:41:42', '2018-06-02 21:41:42');
INSERT INTO `admin` VALUES ('24', 'binbin', '9f4c4ab0dc0d036d610e580cbfd458a0', 'u3tU3t', '1', '1', '2018-06-02 21:43:38', '2018-06-02 21:43:38');

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of customer
-- ----------------------------
