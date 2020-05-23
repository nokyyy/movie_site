<?php
session_start();

header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
if ($_POST['token'] != $_SESSION['token']){
	echo "不正アクセスの可能性あり";
	exit();
}

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//データベース接続
require_once("db.php");
$dbh = db_connect();

//エラーメッセージの初期化
$errors = array();

if(empty($_POST)) {
	header("Location: registration_mail_form.php");
	exit();
}else{
	//POSTされたデータを変数に入れる
	$mail = isset($_POST['mail']) ? $_POST['mail'] : NULL;

	//メール入力判定
	if ($mail == ''){
		$errors['mail'] = "メールが入力されていません。";
	}else{
		if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
			$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
		}

		/*
		ここで本登録用のmemberテーブルにすでに登録されているmailかどうかをチェックする。
		$errors['member_check'] = "このメールアドレスはすでに利用されております。";
		*/
	}
}

if (count($errors) === 0){

	$urltoken = hash('sha256',uniqid(rand(),1));
	// 本登録をするためのページへ遷移するためのurl + hash
	$url = "url_of_register_page"."?urltoken=".$urltoken;
	// メールで送るので「http~」のurlを書く

	// localで確認する用
  //$url = "../fillout/registration_form.php"."?urltoken=".$urltoken;

	//ここでurlとtokenをデータベースに登録する
	try{
		//例外処理を投げる（スロー）ようにする
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$statement = $dbh->prepare("INSERT INTO pre_member (urltoken,mail,date) VALUES (:urltoken,:mail,now() )");

		//プレースホルダへ実際の値を設定する
		$statement->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
		$statement->bindValue(':mail', $mail, PDO::PARAM_STR);
		$statement->execute();

		//データベース接続切断
		$dbh = null;

	}catch (PDOException $e){
		print('Error:'.$e->getMessage());
		die();
	}

//以下メールの本文内にURL//ヒアドキュメント構文(長い文字列を変数に入れたり出力する時に使う)
$body = <<< EOM
24時間以内に下記のURLからご登録下さい。
{$url}
EOM;// echo <<<EOM ~~~ EOM; [End Of Message]

//もしphpmailerじゃなくて、mbのメールを使うなら
/*
	mb_language('ja');
	mb_internal_encoding('UTF-8');

	//Fromヘッダーを作成
	$header = 'From: ' . mb_encode_mimeheader($name). ' <' . $mail. '>';
*/
	//if (mb_send_mail($mailTo, $subject, $body, $header, '-f'. $returnMail)) {
  if(true){

      if(isset($_POST['token'])){
        require 'src/Exception.php';
        require 'src/PHPMailer.php';
        require 'src/SMTP.php';
        require 'setting.php';

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
            $mail->addAddress($_POST['mail'], '受信者さん'); //受信者（送信先）を追加する
        //    $mail->addReplyTo('xxxxxxxxxx@xxxxxxxxxx','返信先');
        //    $mail->addCC('xxxxxxxxxx@xxxxxxxxxx'); // CCで追加
        //    $mail->addBcc('xxxxxxxxxx@xxxxxxxxxx'); // BCCで追加
            $mail->Subject = MAIL_SUBJECT; // メールタイトル
            $mail->isHTML(true);    // HTMLフォーマットの場合はコチラを設定します
            //$body = 'メールの中身';
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
    //セッション変数を全て解除
    $_SESSION = array();
		//クッキーの削除
		if (isset($_COOKIE["PHPSESSID"])) {
			setcookie("PHPSESSID", '', time() - 1800, '/');
		}

 		//セッションを破棄する
 		session_destroy();

 		$message = "メールをお送りしました。24時間以内にメールに記載されたURLからご登録下さい。";

	 } else {
		$errors['mail_error'] = "メールの送信に失敗しました。";
	}
}

?>

<!DOCTYPE html>
<html>
<head>
<title>メール確認画面</title>
<meta charset="utf-8">
</head>
<body>
<h1>メール確認画面</h1>

<?php if (count($errors) === 0): ?>

<p><?=$message?></p>

<p>↓このURLが記載されたメールが届きます。下記のURLから新規登録しても大丈夫です</p>
<p>------------------------------メール本文-----------------------------------------------</p>
<a href="<?=$url?>"><?=$url?></a>
<!--<aタグ内は表示しない><aタグで挟まっているのは表示>--->

<?php elseif(count($errors) > 0): ?>

<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>

<input type="button" value="戻る" onClick="history.back()">

<?php endif; ?>
<br><br>

<p>---------------------------------以下、携帯でユーザー登録した人向け-----------------------------------------</p>
<a href="../login/loginPage.php">{携帯からユーザー登録をしたらここをクリックしてログイン画面へとんでください！！}</a>

</body>
</html>

<?php
// mbじゃGメールでスパムに弾かれたりするから、phpmailerを使う
 ?>
