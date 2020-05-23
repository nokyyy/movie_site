<?php
// 画像・動画投稿サイトへログインするためのページです
// 今回は同じメールアドレスを使って登録できないことを想定
header("Content-type: text/html; charset=utf-8");
require_once('../phpmailer/db.php');
$dbh = db_connect();

// 変数初期化
$errors = array();
$check="";
$difmail="";
// 打ち込み値(メール)の形式チェック
if(isset($_POST['user'])){
  $mail = isset($_POST['mail']) ? $_POST['mail'] : NULL;
  //メール入力判定
  if ($mail == ''){
    $errors['mail'] = "メールが入力されていません";
  }else{
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
      $errors['mail_check'] = "メールアドレスの形式が正しくありません";
    }
  }


  //DB処理
  if(count($errors) === 0){
    $pass = $_POST['pass'];
    try{
      $sql = "SELECT * FROM member WHERE mail=:mail;";
      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if($row){
        # hash化から戻す
        if(!password_verify($pass,$row["password"])){
          $check="パスワードが違います";
        }
      }else{;
          $difmail="メールアドレスが違っています";
      }
      if(empty($check)&&empty($difmail)){
        session_start();
        $_SESSION['account'] = $row['account'];
        //クロスサイトリクエストフォージェリ（CSRF）対策
        $_SESSION['token'] = str_replace('+', ' ',base64_encode(openssl_random_pseudo_bytes(32)));
        //$token = $_SESSION['token'];
        //header("Location: ../home/index.php?token=".$token);
        header("Location: ../home/index.php");
        exit();
      }
    }catch (PDOException $e){
    	//トランザクション取り消し（ロールバック）
    	$dbh->rollBack();
    	$errors['error'] = "もう一度やりなおして下さい。";
    	print('Error:'.$e->getMessage());
    }
  }
}

//テスト段階で別ページでデモユーザーを消すのは面倒なので、
//今回はユーザー削除も同ページに作りました
//--------------DELETE処理---------------------------//
if(isset($_POST['delmit'])){
  // echo $_POST['delete']."<br>";
  // echo $_POST['delpass'];
  $delid = $_POST['delid'];
  $delpass = $_POST['delpass'];
  try{
    //$sql = 'update tbtest2 set name=:name,comment=:comment where id=:id';
    $sql = "SELECT * FROM member WHERE id=:id;";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $delid, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $checks=array();
    if($row){
      if(empty($delpass)){
          echo "<script>window.alert('パスワードを入力してください')</script>";
      }elseif(!password_verify($delpass,$row["password"])){
        echo "<script>window.alert('パスワードが違います')</script>";
        $checks['pass']="パスワードが違います";
      }
    }else{
          echo "<script>window.alert('idが違います')</script>";
        $checks[]="id不一致";
    }
    if(count($checks) === 0){
      $sql = 'DELETE FROM member WHERE id=:id';
      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':id', $delid, PDO::PARAM_INT);
      $result = $stmt->execute();
        if($result==false){
          echo "削除エラー";
        }
      }
  }catch (PDOException $e){
    //トランザクション取り消し（ロールバック）
    $dbh->rollBack();
    $errors['error'] = "もう一度やりなおして下さい。";
    print('Error:'.$e->getMessage());
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>login</title>
<link rel="stylesheet" type="text/css" href="./login.css">
<link rel="stylesheet" type="text/css" href="">
</head>
<body>
<div class="wrapper">
  <div id="entire">
  <h1>Welcome</h1>
  <form action="" method="post">
    <input type="text" name="mail" placeholder="MailAddress" class="exbtn">
    <?php
      foreach ($errors as $err) {echo $err;}
      echo $difmail;
     ?>
    <input type="text" name="pass" placeholder="Password" class=exbtn>
    <?php echo $check; ?>
    <div id="cen">
      <button type="submit" name="user">Login</button>
    </div>
  </form>
  <div id="cen">
    <button onclick="location.href='../phpmailer/registration_mail_form.php'" id="new">新規登録</button>
    <br>
<!--        以下、特設でつけたユーザー管理画面です        --->
    <button class="btn" type="button" onclick="clickDisplay()" >ユーザー表示・削除</button>
  </div>
  <?php
  try{
    $sql = "SELECT * FROM member ORDER BY id;";
    $stmt = $dbh->prepare($sql);
    //ini_set('memory_limit', '-1');
    $stmt -> execute();
    }catch (PDOException $e){
      //トランザクション取り消し（ロールバック）
      $dbh->rollBack();
      $errors['error'] = "もう一度やりなおして下さい。";
      print('Error:'.$e->getMessage());
    }
    $i=0;
    ?>
    <table id="userTable">
      <tr>
        <th>ID</th>
        <th>名前</th>
        <th>パスワード</th>
        <th>削除</th>
      </tr>
      <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): $i++ ?>
      <tr>
        <td><?php echo $row["id"] ?></td>
        <td><?php echo $row["account"] ?></td>
          <form method="POST" action="" onSubmit="return deleteCheck()">
          <td>
            <input type="text" name="delpass" placeholder="Password" id="delpass">
            <!--?php echo "<script> var i=".$i."</script>"?-->
          </td>
          <td>
            <input type="hidden" name="delid" value="<?php echo $row["id"] ?>">
            <input type="submit" name="delmit" id="del" value="削除">
          </td>
          </form>
      </tr>
    <?php endwhile; ?>
    <!--<input type="button" value="登録ユーザーを表示" onclick="" />--->
    </table>
  </div>
</div>
<script src="./login.js" charset="utf-8"></script>
</body>
</html>
