CREATE DATABASE IF NOT EXISTS `training`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_bin;
GRANT ALL PRIVILEGES ON `training`.* TO 'myapp'@'%';
