# hakoniwa

## 概要
[箱庭諸島 S.E ver23_r09](http://hakoniwa.symphonic-net.com/) の改造版

## 目的
* ナウい環境で実行できるようにする
 * ver.23_r09 は 2013-06-29 で止まっているので古い環境でしか動かない
* 機能はできるだけシンプルにする
 * マニュアルの削除 → 興味のある人は"ググる"だろう
 * 画像のローカル設定の削除 → インターネット環境の向上のため
 * 「観光者通信」の削除 → メッセージのやりとりはSNSなどを活用する（だろう）

## 実行環境
* Nginx 1.9
* PHP 5.6

## 変更点
* バグフィックス
* その他 最適化
 * Chrome, Firefox でも動くようにJavaScriptを修正

* jcode.phps を廃止 → マルチバイト対応のPHPの関数を利用する

* コマンドの追加
 * 「浅瀬埋め立て自動入力」
