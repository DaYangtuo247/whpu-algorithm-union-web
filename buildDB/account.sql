/*
 Navicat Premium Data Transfer

 Source Server         : WHPU algorithm
 Source Server Type    : MySQL
 Source Server Version : 80029
 Source Host           : localhost:3306
 Source Schema         : account

 Target Server Type    : MySQL
 Target Server Version : 80029
 File Encoding         : 65001

 Date: 22/08/2022 15:01:44
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for todaylike
-- ----------------------------
DROP TABLE IF EXISTS `todaylike`;
CREATE TABLE `todaylike`  (
  `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `likeuser` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of todaylike
-- ----------------------------
INSERT INTO `todaylike` VALUES ('Dayangtuo247', 'wzy');
INSERT INTO `todaylike` VALUES ('Dayangtuo247', 'kuan525');
INSERT INTO `todaylike` VALUES ('kuan525', 'Dayangtuo247');
INSERT INTO `todaylike` VALUES ('kuan525', 'kuan525');
INSERT INTO `todaylike` VALUES ('wzy', 'wzy');
INSERT INTO `todaylike` VALUES ('wzy', 'Dayangtuo247');
INSERT INTO `todaylike` VALUES ('wzy', 'kuan525');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `introduce` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `like` int NULL DEFAULT NULL,
  `leetCodePage` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `AcWingPage` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `codeforcesPage` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `headImg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `lastLoginTime` int NULL DEFAULT NULL,
  `online` tinyint NULL DEFAULT NULL,
  `registrationDate` int NULL DEFAULT NULL,
  PRIMARY KEY (`username`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('Dayangtuo247', '$2y$10$KPubeV4g2XZpcRGVwbyqP.4uh5iurohAgJDaiz8sW6f2QrGIZSevW', '$2y$10$KPubeV4g2XZpcRGVwbyqP.4uh5iurohAgJDaiz8sW6f2QrGIZSevW', 101, 'https://leetcode.cn/u/dayangtuo247/', '4523245345', 'https://codeforces.com/profile/DaYangtuo247', '../userinfo/userPictures/yangTuo.gif', 1660638219, 1, 1657874412);
INSERT INTO `user` VALUES ('kuan525', '$2y$10$KPubeV4g2XZpcRGVwbyqP.4uh5iurohAgJDaiz8sW6f2QrGIZSevW', '$2y$10$KPubeV4g2XZpcRGVwbyqP.4uh5iurohAgJDaiz8sW6f2QrGIZSevW', 3, 'https://leetcode.cn/u/kuan525/', '345345', 'https://codeforces.com/profile/kuan525', '../userinfo/userPictures/default/11.jpg', 1660550307, 1, 1657874412);
INSERT INTO `user` VALUES ('wzy', '$2y$10$KPubeV4g2XZpcRGVwbyqP.4uh5iurohAgJDaiz8sW6f2QrGIZSevW', '$2y$10$KPubeV4g2XZpcRGVwbyqP.4uh5iurohAgJDaiz8sW6f2QrGIZSevW', 2, 'https://leetcode.cn/u/zao-men-tan-zhi-lang/', '45345345345', 'https://codeforces.com/profile/wzy666', '../userinfo/userPictures/default/1.jpg', 1657811513, 0, 1657874412);

SET FOREIGN_KEY_CHECKS = 1;
