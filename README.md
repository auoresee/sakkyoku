# 準備

## SQLの準備

1. MySQLのインストール

```sudo apt install mysql```


2. ユーザー、データベースの作成

```mysql> CREATE USER 'noverdi'@'%' IDENTIFIED BY 'password';```

※'password'の部分は適当なパスワードに置き換えてください

```mysql> CREATE DATABASE noverdi```

```mysql> GRANT ALL ON noverdi.* TO noverdi;```



3. パスワードファイルの作成

phpフォルダの中に sqlpassword.php というファイル名で以下の内容のファイルを作成してください。

```
<?php

$SQL_PASSWORD = 'password';

?>
```

※'password'の部分は2.で設定したパスワードに置き換えてください


## データベースの構築

サーバを立てた後、admintool/songadmin.htmlからデータベースを再構築してください。




Piano Roll with ability to make multi-track songs. Read more and check it out here: http://www.oliphaunts.com/pianoroll-js/