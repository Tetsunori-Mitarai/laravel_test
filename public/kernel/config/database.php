<?php

define(KERNEL_SITE, 'poyopoko');

define(DB_HOSTNAME, 'localhost');
define(DB_USERNAME, 'tetsu_database');
define(DB_PASSWORD, 'vdx824cy');

define(DB_NAME, 'tetsu_database');

// セッション情報管理用データベースの名前
define(SESSION_DB_NAME, "tetsu_database");

// セッション情報管理用テーブルの名前
define(SESSION_TABLE, "kernel_session");

// セッションID保管用項目名。
define(SESSION_SID_FIELDNAME, "session_id");

// セッション認証用MD5ハッシュ
define(SESSION_KEY_FIELDNAME, "session_key");

// シリアライズされたセッションデータ保管用項目名
define(SESSION_RAWDATA_FIELDNAME, "rawdata");

// セッション登録日保管用項目名。
define(SESSION_RDATE_FIELDNAME, "timestamp");

// 不要なセッションを何日残しておくか？
define(SESSION_GC_DAYS, "7");

?>