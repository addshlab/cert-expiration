![NO SUPPORT](http://add.sh/images/no-support.png) ![NO GUARANTEE](http://add.sh/images/no-guarantee.png)

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

# Cert Expiration Widget

## 注意

このソフトウェアはサポート無し・動作保証無しです。第三者からの要望は受け付けていません。

## 説明

サイトで TLS を使用していた場合、ドメイン名・有効期限・発行者・署名アルゴリズムを表示するウィジェットです。

## 仕組み

1. サイトで TLS を使用しているとみられる場合、その証明書の情報を取得して表示する

## 仕様

* デザインが作者のサイト用にハードコードされている

## データベースの使用
* Transient API を利用して、取得した TLS の情報を一定期間保存
* これらの情報は、プラグイン削除の有無に関わらず一定期間で削除される

