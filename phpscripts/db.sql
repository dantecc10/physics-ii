/*
 Navicat Premium Data Transfer

 Source Server         : SGI - DB
 Source Server Type    : MySQL
 Source Server Version : 100616 (10.6.16-MariaDB-0ubuntu0.22.04.1)
 Source Host           : sgi.castelancarpinteyro.com:3306
 Source Schema         : sgi-system

 Target Server Type    : MySQL
 Target Server Version : 100616 (10.6.16-MariaDB-0ubuntu0.22.04.1)
 File Encoding         : 65001

 Date: 11/02/2024 15:51:55
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for access_history
-- ----------------------------
DROP TABLE IF EXISTS `access_history`;
CREATE TABLE `access_history`  (
  `id_access` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `rfid_tag_access` int UNSIGNED NOT NULL,
  `sensor_id_access` int UNSIGNED NOT NULL,
  `timestamp_access` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_access`) USING BTREE,
  INDEX `rfid_tag_access_access_history_fk`(`rfid_tag_access` ASC) USING BTREE,
  INDEX `sensor_id_access_access_history_fk`(`sensor_id_access` ASC) USING BTREE,
  CONSTRAINT `rfid_tag_access_access_history_fk` FOREIGN KEY (`rfid_tag_access`) REFERENCES `rfid_tags` (`id_tag`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `sensor_id_access_access_history_fk` FOREIGN KEY (`sensor_id_access`) REFERENCES `sensors` (`id_sensor`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of access_history
-- ----------------------------

-- ----------------------------
-- Table structure for administrators
-- ----------------------------
DROP TABLE IF EXISTS `administrators`;
CREATE TABLE `administrators`  (
  `id_administrator` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_administrator` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `last_names_administrator` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `mobile_administrator` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `email_administrator` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `password_administrator` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `icon_img_administrator` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `rfid_tag_administrator` int UNSIGNED NOT NULL,
  `status_administrator` smallint NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_administrator`) USING BTREE,
  INDEX `rfid_tag_administrator_administrators_fk`(`rfid_tag_administrator` ASC) USING BTREE,
  CONSTRAINT `rfid_tag_administrator_administrators_fk` FOREIGN KEY (`rfid_tag_administrator`) REFERENCES `rfid_tags` (`id_tag`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of administrators
-- ----------------------------

-- ----------------------------
-- Table structure for condominiums
-- ----------------------------
DROP TABLE IF EXISTS `condominiums`;
CREATE TABLE `condominiums`  (
  `id_condominium` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_condominium` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `address_condominium` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  PRIMARY KEY (`id_condominium`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of condominiums
-- ----------------------------

-- ----------------------------
-- Table structure for houses
-- ----------------------------
DROP TABLE IF EXISTS `houses`;
CREATE TABLE `houses`  (
  `id_house` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `condominium_house` int UNSIGNED NOT NULL,
  `address_house` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  PRIMARY KEY (`id_house`) USING BTREE,
  INDEX `condominium_house_houses_fk`(`condominium_house` ASC) USING BTREE,
  CONSTRAINT `condominium_house_houses_fk` FOREIGN KEY (`condominium_house`) REFERENCES `condominiums` (`id_condominium`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of houses
-- ----------------------------

-- ----------------------------
-- Table structure for residents
-- ----------------------------
DROP TABLE IF EXISTS `residents`;
CREATE TABLE `residents`  (
  `id_resident` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_resident` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `last_names_resident` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `phone_number_resident` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `email_resident` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `password_resident` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'unset',
  `icon_img_resident` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `house_resident` int UNSIGNED NOT NULL,
  `condominium_resident` int UNSIGNED NOT NULL,
  `rfid_tag_resident` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_resident`) USING BTREE,
  INDEX `house_resident_residents_fk`(`house_resident` ASC) USING BTREE,
  INDEX `condominium_resident_residents_fk`(`condominium_resident` ASC) USING BTREE,
  INDEX `rfid_tag_resident_residents_fk`(`rfid_tag_resident` ASC) USING BTREE,
  CONSTRAINT `condominium_resident_residents_fk` FOREIGN KEY (`condominium_resident`) REFERENCES `condominiums` (`id_condominium`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `house_resident_residents_fk` FOREIGN KEY (`house_resident`) REFERENCES `houses` (`id_house`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `rfid_tag_resident_residents_fk` FOREIGN KEY (`rfid_tag_resident`) REFERENCES `rfid_tags` (`id_tag`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of residents
-- ----------------------------

-- ----------------------------
-- Table structure for rfid_tags
-- ----------------------------
DROP TABLE IF EXISTS `rfid_tags`;
CREATE TABLE `rfid_tags`  (
  `id_tag` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `rfid_key_tag` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `house_tag` int UNSIGNED NOT NULL,
  `condominium_tag` int UNSIGNED NOT NULL,
  `status_tag` smallint UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_tag`) USING BTREE,
  INDEX `house_tag_tags_fk`(`house_tag` ASC) USING BTREE,
  INDEX `condominium_tag_tags_fk`(`condominium_tag` ASC) USING BTREE,
  CONSTRAINT `condominium_tag_tags_fk` FOREIGN KEY (`condominium_tag`) REFERENCES `condominiums` (`id_condominium`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `house_tag_tags_fk` FOREIGN KEY (`house_tag`) REFERENCES `houses` (`id_house`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of rfid_tags
-- ----------------------------

-- ----------------------------
-- Table structure for sensors
-- ----------------------------
DROP TABLE IF EXISTS `sensors`;
CREATE TABLE `sensors`  (
  `id_sensor` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `location_sensor` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `type_sensor` int UNSIGNED NOT NULL,
  `condominium_sensor` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_sensor`) USING BTREE,
  INDEX `condominium_sensor_sensors_fk`(`condominium_sensor` ASC) USING BTREE,
  CONSTRAINT `condominium_sensor_sensors_fk` FOREIGN KEY (`condominium_sensor`) REFERENCES `condominiums` (`id_condominium`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sensors
-- ----------------------------

-- ----------------------------
-- Table structure for vehicles
-- ----------------------------
DROP TABLE IF EXISTS `vehicles`;
CREATE TABLE `vehicles`  (
  `id_vehicle` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `brand_vehicle` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `model_vehicle` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `legal_id_vehicle` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `rfid_tag_vehicle` int UNSIGNED NOT NULL,
  `resident_vehicle` int UNSIGNED NOT NULL,
  `house_vehicle` int UNSIGNED NOT NULL,
  `condominium_vehicle` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_vehicle`) USING BTREE,
  INDEX `rfid_tag_vehicle_vehicles_fk`(`rfid_tag_vehicle` ASC) USING BTREE,
  INDEX `resident_vehicle_vehicles_fk`(`resident_vehicle` ASC) USING BTREE,
  INDEX `house_vehicle_vehicles_fk`(`house_vehicle` ASC) USING BTREE,
  INDEX `condominium_vehicle_vehicles_fk`(`condominium_vehicle` ASC) USING BTREE,
  CONSTRAINT `condominium_vehicle_vehicles_fk` FOREIGN KEY (`condominium_vehicle`) REFERENCES `condominiums` (`id_condominium`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `house_vehicle_vehicles_fk` FOREIGN KEY (`house_vehicle`) REFERENCES `houses` (`id_house`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `resident_vehicle_vehicles_fk` FOREIGN KEY (`resident_vehicle`) REFERENCES `residents` (`id_resident`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `rfid_tag_vehicle_vehicles_fk` FOREIGN KEY (`rfid_tag_vehicle`) REFERENCES `rfid_tags` (`id_tag`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of vehicles
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
