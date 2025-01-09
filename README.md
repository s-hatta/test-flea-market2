# coachtechフリマ

## 環境構築

0.  注意事項  
    本アプリはStripeによる決済処理をテスト環境にておこないます。  
    事前にホストマシンへのStripeCLIのインストールをお願いします。
	（参考：[https://docs.stripe.com/stripe-cli](https://docs.stripe.com/stripe-cli)）
    
1.  はじめに
```
$ コマンドラインで実行するコマンドであることをあらわす
/var/www# PHPコンテナで実行するコマンドであることをあらわす
```

2.  リポジトリのコピー
```
$ git clone git@github.com:s-hatta/test-flea-market.git
```

3.  .envファイルの設定
```
$ cd test-flea-market/
$ cp src/.env.example src/.env
```

4.  .envファイルを編集（+は追加する行、-は削除する行）
```
// 前略
- APP_TIMEZONE=Asia_Tokyo
+ APP_TIMEZONE=Asia/Tokyo
// 中略
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
//中略
- STRIPE_SECRET_KEY=sk_test_~~~
- STRIPE_PUBLIC_KEY=pk_test_~~~
- STRIPE_WEBHOOK_SECRET=whsec_~~~
+ STRIPE_SECRET_KEY=環境に合わせて記述
+ STRIPE_PUBLIC_KEY=環境に合わせて記述
+ STRIPE_WEBHOOK_SECRET=環境に合わせて記述
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
- phpMyAdmin 5.2.1
- nginx 1.26.2
- Stripe CLI
- Selenium

## ER図
![er drawio](https://github.com/user-attachments/assets/6067458c-c9cb-4873-bf26-dcc0fbddacf5)

## URL
- 開発環境：http://localhost/
- phpMyAdmin：http://localhost:8080/
- MailHog：http://localhost:8025/

## テスト用ユーザー
 - メールアドレス：test@example.com
 - パスワード：password

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
