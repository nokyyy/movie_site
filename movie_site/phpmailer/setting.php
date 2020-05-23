<?php

// メール情報
// メールホスト名・gmailでは smtp.gmail.com
define('MAIL_HOST','smtp.gmail.com');

// メールユーザー名・アカウント名・メールアドレスを@込でフル記述
// ここに送信させるメールアドレスをうつ
define('MAIL_USERNAME','exam@aaaa.ex');

// メールパスワード・上で記述したメールアドレスに即したパスワード
// そのメールアドレスにログインするためのパスワード
define('MAIL_PASSWORD','********');

// SMTPプロトコル(sslまたはtls)
define('MAIL_ENCRPT','ssl');

// 送信ポート(ssl:465, tls:587)
define('SMTP_PORT', 465);

// メールアドレス・ここではメールユーザー名と同じでOK
define('MAIL_FROM','exam@aaaa.ex');

// 表示名
define('MAIL_FROM_NAME','送信者の名前');

// メールタイトル
define('MAIL_SUBJECT','お問い合わせいただきありがとうございます');
//define('MAIL_SUBJECT',$_POST['title']); ポスト送信でタイトルを送る場合
