<?php
// 下記のsqlを使ってデータベースにテーブル等を作ってください

//データベース接続
require_once("./phpmailer/db.php");
$dbh = db_connect();

//-------------------<pre_member table用>---------------------
// CREATE TABLE pre_member (
// id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
// urltoken VARCHAR(128) NOT NULL,
// mail VARCHAR(50) NOT NULL,
// date DATETIME NOT NULL,
// flag TINYINT(1) NOT NULL DEFAULT 0
// )ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;
$sql = "CREATE TABLE IF NOT EXISTS pre_member"//テーブル名
." ("
. "id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
. "urltoken VARCHAR(128) NOT NULL,"
. "mail VARCHAR(50) NOT NULL,"
. "date DATETIME NOT NULL,"
. "flag TINYINT(1) NOT NULL DEFAULT 0"
.")ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
$stmt = $dbh->query($sql);

$sql ='SHOW CREATE TABLE pre_member';
  $result = $dbh -> query($sql);
  foreach ($result as $row){
    echo $row[1];
  }
  echo "<hr>";

//-------------------<member table用>---------------------
// CREATE TABLE member (
// id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
// account VARCHAR(50) NOT NULL,
// mail VARCHAR(50) NOT NULL,
// password VARCHAR(128) NOT NULL,
// flag TINYINT(1) NOT NULL DEFAULT 1
// )ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;
$sql = "CREATE TABLE IF NOT EXISTS member"//テーブル名
." ("
. "id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
. "account VARCHAR(50) NOT NULL,"
. "mail VARCHAR(50) NOT NULL,"
. "password VARCHAR(128) NOT NULL,"
. "flag TINYINT(1) NOT NULL DEFAULT 1"
.")ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
$stmt = $dbh->query($sql);

$sql ='SHOW CREATE TABLE member';
  $result = $dbh -> query($sql);
  foreach ($result as $row){
    echo $row[1];
  }
  echo "<hr>";

//-------------------------<media用>-----------------------------
/*いちいち面倒な書き方をしなくてもできるバージョン*/
//   CREATE TABLE
// `mediatest`.`media` ( `id` INT NOT NULL AUTO_INCREMENT ,
// `fname` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
// `extension` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
// `raw_data` LONGBLOB NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
$sql = "CREATE TABLE IF NOT EXISTS `media` (
`id` INT NOT NULL AUTO_INCREMENT ,
`account` VARCHAR(50) NOT NULL,
`title` VARCHAR(30) NOT NULL,
`fname` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`extension` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`raw_data` LONGBLOB NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;";
$stmt = $dbh->query($sql);

$sql ='SHOW CREATE TABLE media';
  $result = $dbh -> query($sql);
  foreach ($result as $row){
    echo $row[1];
  }
  echo "<hr>";

  //--------------- DROP TABLE ----------------
  // $sql = "DROP TABLE media";
  // $stmt = $dbh->query($sql);

  //--------------- show table ------------------
  $sql ='SHOW TABLES';
  $result = $dbh -> query($sql);
  foreach ($result as $row){
    echo $row[0];
    echo '<br>';
  }
  echo "<hr>";
