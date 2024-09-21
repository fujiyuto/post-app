# 環境構築

### 前提
[mysql-post-app](https://github.com/Tomoya969/mysql-post-app)のコンテナの構築を行っておく。
### 1.
WindowsならWSL、Macならzshで下記コマンドを実行し、コンテナの起動と設定を行う。
```
$ docker coompose up -d
$ docker compose exec laravel.text composer install
$ docker compose down
$ ./vendor/bin/sail up -d
```
### 2.

マイグレーションを実行しデータベースにテーブルを作成する。
```
$ sail artisan migrate
```
