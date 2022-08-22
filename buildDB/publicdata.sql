/*
 Navicat Premium Data Transfer

 Source Server         : WHPU algorithm
 Source Server Type    : MySQL
 Source Server Version : 80029
 Source Host           : localhost:3306
 Source Schema         : publicdata

 Target Server Type    : MySQL
 Target Server Version : 80029
 File Encoding         : 65001

 Date: 22/08/2022 15:03:10
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for homepage
-- ----------------------------
DROP TABLE IF EXISTS `homepage`;
CREATE TABLE `homepage`  (
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of homepage
-- ----------------------------
INSERT INTO `homepage` VALUES ('leetCodeContest1', 'weekly-contest-306');
INSERT INTO `homepage` VALUES ('leetCodeContest2', 'weekly-contest-305');
INSERT INTO `homepage` VALUES ('solveAll', '0');
INSERT INTO `homepage` VALUES ('todayVisted', '21');

-- ----------------------------
-- Table structure for rankinglist
-- ----------------------------
DROP TABLE IF EXISTS `rankinglist`;
CREATE TABLE `rankinglist`  (
  `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `leetCodeIntegral` smallint NULL DEFAULT NULL,
  `solveProblems` smallint NULL DEFAULT NULL,
  `leetCodeContest1` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `leetCodeContest2` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `codeForcesIntegralNOW` smallint NULL DEFAULT NULL,
  `codeForcesIntegralMAX` smallint NULL DEFAULT NULL,
  `AcWingIntegral` smallint NULL DEFAULT NULL,
  `username_LC` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `username_CF` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `username_AW` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`username`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of rankinglist
-- ----------------------------
INSERT INTO `rankinglist` VALUES ('Dayangtuo247', 1317, 120, '[ X ]', '[ √ ]', 373, 373, NULL, 'dayangtuo247', 'DaYangtuo247', NULL);
INSERT INTO `rankinglist` VALUES ('kuan525', 2270, 677, '[ √ ]', '[ √ ]', 1776, 1861, NULL, 'kuan525', 'kuan525', NULL);
INSERT INTO `rankinglist` VALUES ('wzy', 1293, 77, '[ X ]', '[ X ]', 568, 568, NULL, 'zao-men-tan-zhi-lang', 'wzy666', NULL);

SET FOREIGN_KEY_CHECKS = 1;
