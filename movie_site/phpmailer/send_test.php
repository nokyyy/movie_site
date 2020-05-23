<?php

if(isset($_POST['to'])){
  require 'src/Exception.php';
  require 'src/PHPMailer.php';
  require 'src/SMTP.php';
  require 'setting.php';
  //require先でも$_POSTいけるで！！

  // PHPMailerのインスタンス生成
      $mail = new PHPMailer\PHPMailer\PHPMailer();

      $mail->isSMTP(); // SMTPを使うようにメーラーを設定する
      $mail->SMTPAuth = true;
      $mail->Host = MAIL_HOST; // メインのSMTPサーバー（メールホスト名）を指定
      $mail->Username = MAIL_USERNAME; // SMTPユーザー名（メールユーザー名）
      $mail->Password = MAIL_PASSWORD; // SMTPパスワード（メールパスワード）
      $mail->SMTPSecure = MAIL_ENCRPT; // TLS暗号化を有効にし、「SSL」も受け入れます
      $mail->Port = SMTP_PORT; // 接続するTCPポート

      // メール内容設定
      $mail->CharSet = "UTF-8";
      $mail->Encoding = "base64";
      $mail->setFrom(MAIL_FROM,MAIL_FROM_NAME);
      $mail->addAddress($_POST['to'], '受信者さん'); //受信者（送信先）を追加する
  //    $mail->addReplyTo('xxxxxxxxxx@xxxxxxxxxx','返信先');
  //    $mail->addCC('xxxxxxxxxx@xxxxxxxxxx'); // CCで追加
  //    $mail->addBcc('xxxxxxxxxx@xxxxxxxxxx'); // BCCで追加
      $mail->Subject = MAIL_SUBJECT; // メールタイトル
      $mail->isHTML(true);    // HTMLフォーマットの場合はコチラを設定します
      $body = 'メールの中身';
      //$body = $_POST['content']; 内容も送る場合
      /*ここでトークンを発行する*/


      $mail->Body  = $body; // メール本文
      // メール送信の実行
      if(!$mail->send()) {
      	echo 'メッセージは送られませんでした！';
      	echo 'Mailer Error: ' . $mail->ErrorInfo;
      } else {
      	echo '送信完了！';
      }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <script type="text/javascript" charset="UTF-8"></script>
  </head>
  <body>
    <p>
      <h2>メール送信フォーム</h2>
    </p>
    <form action="" method="post">
      <p>
        送信先
      </p>
      <input type="text" name="to">
      <p>
        メールのタイトル
      </p>
      <input type="text" name="title">
      <p>
        本文
      </p>
      <textarea name="content" cols="50" rows="5"></textarea>
      <p>
        <input type="submit" name="send" value="送信">
      </p>
    </form>
  </body>
</html>
