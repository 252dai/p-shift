# P-shift
シフト管理Webアプリケーション

## 概要
P-shiftは、アルバイトや小規模事業者向けの
シフト提出・管理・確定・チャット・給与管理を一元化できるWebアプリです。

Laravel 12を用いて開発され、一般ユーザーと管理者の2つのロールを持ちます。

## 動作環境
- windows10/11
- Apache(XAMPP)
- PHP8.2以上
- Laravel12

## システム構築手順
### 1.リポジトリをクローン
git clone https://github.com/kcg-c-seminar-34/2025C24.git
cd p-shift

### 2.ライブラリのインストール
composer install
npm install
npm run build

### 3.環境ファイル作成
cp .env.example .env
php artisan key:generate

### 4.データベース設定(XAMPP)
.envを編集

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pshift
DB_USERNAME=root
DB_PASSWORD=

phpMyAdminでpshiftデータベースを作成

### 5.マイグレーション
php artisan migrate

### 6.サーバ起動
php artisan serve

ブラウザでhttp://127.0.0.1:8000

## 機能一覧
### 👤アルバイト
- 新規登録/ログイン
- ログアウト
- 希望シフト提出
- 確定シフト閲覧
- チャット
- 給与確認

### 👤社員
- 新規登録/ログイン
- ログアウト
- ユーザー管理
- ユーザー招待
- 希望シフト一覧確認
- 確定シフト作成・編集・削除
- チャット

## 開発者
京都コンピュータ学院
情報科学科
チーム『P-shift』
