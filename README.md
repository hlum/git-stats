# 📊 git-stats

[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-3178C6?logo=typescript&logoColor=white)](https://www.typescriptlang.org/)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Express](https://img.shields.io/badge/Express-5.x-000000?logo=express&logoColor=white)](https://expressjs.com/)
[![License: ISC](https://img.shields.io/badge/License-ISC-blue.svg)](https://opensource.org/licenses/ISC)

GitHub の README に表示できる**統計カード SVG**を生成する API サーバーです。  
TypeScript（Express）と PHP の 2 種類の実装があります。

---

## 📸 デモ

| GitHub Stats | Top Languages |
|:---:|:---:|
| <img src="https://24cm0138.main.jp/php/public/index.php/api/stats?username=hlum&theme=dark&hide_title=true&hide_border=true" /> | <img src="https://24cm0138.main.jp/php/public/index.php/api/top-langs?username=hlum&theme=dark&hide_title=true&hide_border=true" /> |

---

## 🎯 このプロジェクトについて

このプロジェクトは **学習目的** で作成したものです。  
[anuraghazra/github-readme-stats](https://github.com/anuraghazra/github-readme-stats) を参考にしながら、以下のことを学んでいます。

- GitHub GraphQL API を使ったデータ取得の仕組み
- SVG カードの動的生成方法
- TypeScript と PHP 両方での実装
- Express / PHP 組み込みサーバーを使った API サーバーの構築

> 💡 元のプロジェクトは [@anuraghazra](https://github.com/anuraghazra) 氏が作成した素晴らしいオープンソースプロジェクトです。ぜひオリジナルもチェックしてみてください！

---

## ✨ 機能

- 📈 **統計カード** — コミット数・PR 数・Issue 数・Star 数などを表示
- 🌐 **使用言語カード** — リポジトリの使用言語の割合を表示
- 🎨 **テーマ対応** — ライト / ダークテーマ切り替え
- 🖌️ **カスタムカラー** — タイトル・テキスト・背景など個別に色指定可能
- 📐 **レイアウト調整** — カード幅・フォントサイズ・パディングなど
- 🏆 **ランク表示** — コントリビューション量に応じた S+ 〜 C ランク

---

## 🛠 技術スタック

| 実装 | 言語 | フレームワーク | HTTP クライアント |
|------|------|--------------|----------------|
| TypeScript 版 | TypeScript 5.x / Node.js | Express 5.x | Axios |
| PHP 版 | PHP 8.1+ | 組み込みサーバー | Guzzle 7.x |

---

## 🚀 クイックスタート

### 共通の事前準備

**GitHub Personal Access Token** が必要です。

1. [https://github.com/settings/tokens](https://github.com/settings/tokens) にアクセス
2. 「Generate new token (classic)」をクリック
3. `read:user` と `repo` スコープにチェックを入れて生成
4. 生成されたトークンをコピーしておく

---

### TypeScript 版

**必要なもの**: Node.js 18+、npm

```bash
# リポジトリをクローン
git clone https://github.com/hlum/git-stats.git
cd git-stats

# 依存関係のインストール
npm install

# 環境変数ファイルの作成
cp .env.example .env   # または手動で .env を作成
# .env に以下を記述:
#   GITHUB_TOKEN=your_token_here
#   PORT=3000

# 開発サーバーの起動
npm run dev
```

ブラウザで確認:

```
http://localhost:3000/api/stats?username=hlum
http://localhost:3000/api/top-langs?username=hlum
```

**利用可能なスクリプト:**

| コマンド | 説明 |
|---------|------|
| `npm run dev` | 開発サーバー起動（ts-node） |
| `npm run build` | TypeScript のコンパイル |
| `npm test` | テストの実行（Jest） |
| `npm run test:watch` | テストのウォッチモード |

---

### PHP 版

**必要なもの**: PHP 8.1+、Composer

```bash
cd php

# 依存関係のインストール
composer install

# 環境変数ファイルの作成
cp .env.example .env
# .env に以下を記述:
#   GITHUB_TOKEN=your_token_here

# 開発サーバーの起動（localhost:3000）
composer dev
```

ブラウザで確認:

```
http://localhost:3000/api/stats?username=hlum
http://localhost:3000/api/top-langs?username=hlum
```

**利用可能なスクリプト:**

| コマンド | 説明 |
|---------|------|
| `composer dev` | 開発サーバー起動（PHP 組み込みサーバー） |
| `composer test` | テストの実行（PHPUnit） |
| `./vendor/bin/phpunit --filter=StatsCard` | 特定テストクラスのみ実行 |

---

## 📡 API リファレンス

### エンドポイント

| エンドポイント | 説明 |
|--------------|------|
| `GET /api/stats?username=USER` | 統計カード（SVG）を返す |
| `GET /api/top-langs?username=USER` | 使用言語カード（SVG）を返す |

### パラメータ一覧

#### 共通

| パラメータ | デフォルト | 説明 |
|----------|-----------|------|
| `username` | 必須 | GitHub ユーザー名 |
| `theme` | `light` | テーマ: `light` または `dark` |
| `custom_title` | 自動 | カードタイトルを上書き |
| `hide_title` | `false` | タイトルを非表示 |
| `hide_border` | `false` | ボーダーを非表示 |
| `disable_animations` | `false` | アニメーションを無効化 |
| `card_width` | `450` | カード幅（px） |
| `card_height` | 自動計算 | カード高さ（px） |
| `border_radius` | `5` | 角丸の半径（px） |
| `line_height` | `25` | 行間（px） |
| `padding_x` | `25` | 左右パディング |
| `padding_y` | `20` | 上下パディング |
| `title_offset_y` | `35` | タイトルの縦位置 |
| `title_font_size` | `18` | タイトルフォントサイズ |
| `text_font_size` | `14` | 本文フォントサイズ |

#### 統計カード専用

| パラメータ | デフォルト | 説明 |
|----------|-----------|------|
| `hide` | なし | 非表示にする項目（カンマ区切り）: `stars,commits,prs,issues,contribs` |
| `hide_rank` | `false` | ランク円を非表示 |

#### 使用言語カード専用

| パラメータ | デフォルト | 説明 |
|----------|-----------|------|
| `langs_count` | `5` | 表示する言語数（1〜10） |

#### カラーパラメータ（PHP 版）

`#` あり・なし両方の hex カラー、または名前付きカラーが使用可能です。

| パラメータ | 説明 |
|----------|------|
| `bg_color` | 背景色 |
| `title_color` | タイトル色 |
| `text_color` | 本文色 |
| `icon_color` | アイコン色 |
| `border_color` | ボーダー色 |
| `ring_color` | ランク円の色 |

使用可能な名前付きカラー: `github-dark`, `github-light`, `dracula-bg`, `dracula-pink`, `monokai-bg`, `nord-bg` など。

---

## 🖥 VPS へのデプロイ

### TypeScript 版（PM2 + Nginx）

**1. サーバーに Node.js と PM2 をインストール**

```bash
# Node.js (Ubuntu/Debian の場合)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

# PM2 のインストール
npm install -g pm2
```

**2. リポジトリをクローン・ビルド**

```bash
git clone https://github.com/hlum/git-stats.git
cd git-stats
npm install
npm run build

# .env を作成
echo "GITHUB_TOKEN=your_token_here" > .env
echo "PORT=3000" >> .env
```

**3. PM2 でプロセスを常時起動**

```bash
pm2 start dist/api/index.js --name git-stats
pm2 save
pm2 startup   # OS 起動時に自動起動
```

**4. Nginx のリバースプロキシ設定**

```nginx
server {
    listen 80;
    server_name your-domain.com;

    location / {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

```bash
sudo nginx -t && sudo systemctl reload nginx
```

---

### PHP 版（PHP-FPM + Nginx）

**1. PHP と PHP-FPM のインストール**

```bash
sudo apt-get install -y php8.1-fpm php8.1-curl php8.1-mbstring

# Composer のインストール
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

**2. リポジトリをクローン・依存関係インストール**

```bash
git clone https://github.com/hlum/git-stats.git
cd git-stats/php
composer install --no-dev --optimize-autoloader

# .env を作成
echo "GITHUB_TOKEN=your_token_here" > .env
```

**3. Nginx の設定**

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/git-stats/php/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

```bash
sudo nginx -t && sudo systemctl reload nginx
```

**4. ファイルの権限設定**

```bash
sudo chown -R www-data:www-data /var/www/git-stats/php
sudo chmod -R 755 /var/www/git-stats/php
```

---

### HTTPS の設定（共通）

```bash
# Let's Encrypt を使った SSL 証明書の取得
sudo apt-get install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

---

## 📁 プロジェクト構成

```
git-stats/
├── src/                        # TypeScript ソース
│   ├── api/                    # Express エンドポイント
│   │   ├── index.ts            # サーバーエントリーポイント
│   │   └── stats.ts            # /api/stats ルート
│   ├── fetchers/               # GitHub GraphQL データ取得
│   ├── renderers/              # SVG カード生成
│   │   ├── card.ts             # 基本カードテンプレート
│   │   └── stats-card.ts       # 統計カードレンダラー
│   ├── types/                  # TypeScript 型定義
│   └── utils/                  # ユーティリティ（http, rank, formatter など）
├── dist/                       # コンパイル済み JS（git 管理外）
├── php/                        # PHP 版
│   ├── src/
│   │   ├── Api/                # コントローラー
│   │   ├── Fetchers/           # GitHub API フェッチャー
│   │   ├── Renderers/          # SVG レンダラー
│   │   ├── Types/              # 型クラス（Theme など）
│   │   └── Utils/              # ユーティリティ
│   ├── public/                 # ドキュメントルート（index.php）
│   └── tests/                  # PHPUnit テスト
├── .env                        # 環境変数（git 管理外）
├── instructions.md             # 実装ガイド（学習メモ）
├── package.json
└── tsconfig.json
```

---

## 📖 学習リソース

このプロジェクトを作るにあたって参考にしたリソースです。

- 🌟 [anuraghazra/github-readme-stats](https://github.com/anuraghazra/github-readme-stats) — このプロジェクトのインスピレーション元
- 📚 [GitHub GraphQL API ドキュメント](https://docs.github.com/ja/graphql)
- 📘 [TypeScript ハンドブック](https://www.typescriptlang.org/docs/handbook/intro.html)
- ⚡ [Express.js ドキュメント](https://expressjs.com/)
- 🐘 [PHP マニュアル](https://www.php.net/manual/ja/)

---

## ❓ トラブルシューティング

**`GITHUB_TOKEN is required` エラー**
→ `.env` ファイルが存在するか、`GITHUB_TOKEN` が正しく設定されているか確認してください。

**GraphQL エラーが返ってくる**
→ トークンのスコープ（`read:user`, `repo`）が正しいか確認してください。

**SVG が表示されない**
→ ブラウザで直接 URL にアクセスして、エラーメッセージが含まれる SVG が返っていないか確認してください。

---

*このプロジェクトは学習目的で作成しています。元の素晴らしいプロジェクト [anuraghazra/github-readme-stats](https://github.com/anuraghazra/github-readme-stats) に感謝します。*
