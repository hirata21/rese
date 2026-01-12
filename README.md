# rese（飲食店予約サービス）

## 1. アプリ概要・目的

### ■ アプリ概要
ある企業のグループ会社の飲食店予約サービス
rese は、飲食店の検索・予約・決済・来店管理をオンラインで一元管理できる飲食店予約サービスです。
ユーザーは店舗を検索してコースを選び、事前決済付きで予約できます。
店舗代表者は予約状況を管理し、QRコードによる来店チェックインや来店完了メールの送信ができます。

### ■ 作成した目的
外部の飲食店予約サービスは手数料を取られるので自社で予約サービスを持ちたい。
飲食店の予約業務を効率化し、利用者にとってもスムーズで安心な予約体験を提供することを目的として開発しました。

### ■ 商品一覧（店舗一覧）画面
![店舗一覧画面](docs/shop_list.png)

## アプリケーションURL

・開発環境: http://localhost/

・phpMyAdmin: http://localhost:8080

・Mailhog: http://localhost:8025

## 機能一覧

### 【認証・ユーザー管理】
- 会員登録
- ログイン
- ログアウト
- ユーザー情報取得

### 【飲食店閲覧・検索】
- 飲食店一覧取得
- 飲食店詳細取得
- エリアで検索
- ジャンルで検索
- 店名で検索

### 【お気に入り機能】
- ユーザー飲食店お気に入り一覧取得
- 飲食店お気に入り追加
- 飲食店お気に入り削除

### 【予約機能】
- 飲食店予約情報追加（コース・日時・人数指定）
- 飲食店予約情報削除（キャンセル）
- 予約変更機能
- ユーザー飲食店予約情報取得

### 【決済・来店管理】
- Stripeによるオンライン決済
- QRコードによる来店チェックイン
- 来店完了処理

### 【評価・口コミ】
- 来店後の評価
- コメント付き口コミ投稿

### 【管理・運営】
- 管理画面（管理者）
- 店舗代表者（オーナー）管理
- 店舗情報管理

### 【通知・メール】
- お知らせメール送信
- リマインダーメール送信

### 【システム】
- バリデーション（入力チェック）
- 認証（Laravel Fortify）
- ストレージ（画像アップロード）
- レスポンシブデザイン（PC / スマホ対応


## 使用技術(実行環境)
- Laravel 8.75
- PHP 7.4.9
- MySQL 8.0.26
- Nginx 1.21.1
- phpMyAdmin（ポート8080で接続）
- Docker / Docker Compose v3.8


## テーブル設計書


## ER図
![ER図](./images/er.png)

## 環境構築

### Docker ビルド

1.git clone https://github.com/hirata21/rese.git

2.DockerDesktop アプリを立ち上げる

3.docker-compose up -d --build

### Laravel 環境構築

1.docker-compose exec php bash

2.composer install

3.「.env.example」ファイルを 「.env」ファイルに命名を変更。

4..env に以下の環境変数を追加

DB_CONNECTION=mysql

DB_HOST=mysql

DB_PORT=3306

DB_DATABASE=laravel_db

DB_USERNAME=laravel_user

DB_PASSWORD=laravel_pass

5.アプリケーションキーの作成

php artisan key:generate

6.マイグレーションの実行

php artisan migrate

7.シーディングの実行

php artisan db:seed

8.ストレージリンクの作成

php artisan storage:link

9.権限（必要なとき）

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache