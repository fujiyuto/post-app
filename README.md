# 環境構築

### 前提
[MySQL用のコンテナ](https://github.com/fujiyuto/sql-container)の構築を行っておく。
### 1.
WindowsならWSL、Macならzshで下記コマンドを実行し、コンテナの起動と設定を行う。
```
$ docker coompose up -d
$ docker compose exec laravel.text composer install
$ docker compose down
$ ./vendor/bin/sail up -d
```
### 2.
初回構築時はデータベースがないので.envのDB_DATABASEに指定した名前のデータベースを作成する。

### 3
マイグレーションを実行しデータベースにテーブルを作成する。
```
$ sail artisan migrate
```
