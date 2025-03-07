# coachtechフリマ

## 環境構築

1.  はじめに
```
$ コマンドラインで実行するコマンドであることをあらわす
/var/www# PHPコンテナで実行するコマンドであることをあらわす
```

2.  リポジトリのコピー
```
$ git clone git@github.com:s-hatta/test-flea-market2.git
```

3.  .envファイルの設定
```
$ cd test-flea-market2/
$ cp src/.env.example src/.env
```

4.  .envファイルを編集（+は追加する行、-は削除する行）
```
// 前略
- # DB_HOST=127.0.0.1
- # DB_PORT=3306
- # DB_DATABASE=laravel
- # DB_USERNAME=root
- # DB_PASSWORD=
+ DB_HOST=mysql
+ DB_PORT=3306
+ DB_DATABASE=laravel_db
+ DB_USERNAME=laravel_user
+ DB_PASSWORD=laravel_pass
// 後略
```

5.  Dockerビルド
```
$ docker compose up -d --build
```

6.  Laravelのパッケージインストール
```
$ docker compose exec php bash
/var/www# composer install
```

7.  Laravel Duskのインストール
```
/var/www# composer require laravel/dusk --dev
```

8.  アプリケーションキー作成
```
/var/www# php artisan key:generate
```

9.  シンボリックリンクの設定
```
/var/www# php artisan storage:link
```

１０.  マイグレーション＆シーディング
```
/var/www# php artisan migrate:fresh --seed
/var/www# exit
```

## 使用技術(実行環境)
- PHP 8.3.13
- Lalavel Framework 11.32.0
    - fortify 1.24.5
    - Laravel Dusk
- MySQL 8.0.40
- phpMyAdmin 5.2.2
- nginx 1.26.2
- Selenium

## ER図
![er2 drawio](https://github.com/user-attachments/assets/c4caaa07-9ef8-4cd5-bb7b-3a4e6ce1e195)

## URL
- 開発環境：http://localhost/
- phpMyAdmin：http://localhost:8080/
- MailHog：http://localhost:8025/

## テスト用ユーザー
 - メールアドレス：test@example.com
 - パスワード：password

## ダミー商品データ
|商品ID|商品名|価格|商品説明|コンディション|
|-----|-----|-----|-----|-----|
|CO01|腕時計|15,000|スタイリッシュなデザインのメンズ腕時計|良好|
|CO02|HDD|5,000|高速で信頼性の高いハードディスク|目立った傷や汚れなし|
|CO03|玉ねぎ3束|300|新鮮な玉ねぎ3束のセット|やや傷や汚れあり|
|CO04|革靴|4,000|クラシックなデザインの革靴|状態が悪い|
|CO05|ノートPC|45,000|高性能なノートパソコン|良好|
|CO06|マイク|8,000|高音質のレコーディング用マイク|目立った傷や汚れなし|
|CO07|ショルダーバッグ|3,500|おしゃれなショルダーバッグ|やや傷や汚れあり|
|CO08|タンブラー|500|使いやすいタンブラー|状態が悪い|
|CO09|コーヒーミル|4,000|手動のコーヒーミル|良好|
|CO10|メイクセット|2,500|便利なメイクアップセット|目立った傷や汚れなし|

## ダミーユーザーデータ
|ユーザー名|メールアドレス|パスワード|備考|
|-----|-----|-----|-----|
|seller001|seller001@example.com|password|CO01とCO05を出品している|
|seller002|seller002@example.com|password|CO02とCO06を出品している|
|seller003|seller003@example.com|password|どの商品も出品しておらず紐づけされていない|

## テストの実行手順
※テスト実行後はマイグレーションとシーディングをおこなうこと
1.  PHPUnit  
```
/var/www# php artisan test
```

2. Laravel Dusk(動的ページのテスト用)
```
/var/www# php artisan dusk
```
