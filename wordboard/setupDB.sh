#! /bin/sh

# MySQLクライアントを起動し、データベース(wordboard_db)を作成
## sql文の実行
CMD_MySQL="mysql --socket=/Applications/MAMP/tmp/mysql/mysql.sock -u root -proot"
$CMD_MySQL -e "CREATE DATABASE wordboard_db DEFAULT CHARACTER SET utf8;"
$CMD_MySQL -e "SHOW DATABASES;"
$CMD_MySQL -e "quit"

# MySWLクライアントを起動し、データベース(wordboard_db)を開き、テーブル(post)を作成
## sql文の実行
MySQL_wordboard_db="mysql --socket=/Applications/MAMP/tmp/mysql/mysql.sock -u root -proot wordboard_db"
$MySQL_wordboard_db -e "CREATE TABLE post (
id INTEGER NOT NULL AUTO_INCREMENT,
name VARCHAR(40),
comment VARCHAR(200),
created_at DATETIME,
PRIMARY KEY(id)
) ENGINE = INNODB;"
$MySQL_wordboard_db -e "quit"
