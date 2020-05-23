
<?php
session_start();
//login・継続処理
if(isset($_SESSION['account'])){
  $account = $_SESSION['account'];
}else{
  header("Location: ../login/loginPage.php");
  exit();
}
//logout処理
if(isset($_POST['logout'])){
  session_unset();
  header('Location: ../login/loginPage.php');
  exit();
}

  /*重要設定 [post_max_size,upload_max_filesize,memory_limit,--max_allowed_packet]*/
  //post_max_sizeがクリアできないと$_FILE[]に送られてこないよ！
try{
    //データベース接続
  require_once("../phpmailer/db.php");
  $dbh = db_connect();

    //ファイルアップロードがあったとき
  if (isset($_FILES['upfile']['error']) && is_int($_FILES['upfile']['error']) && $_FILES["upfile"]["name"] !== ""){
    switch ($_FILES['upfile']['error']) {
        case UPLOAD_ERR_OK: // OK時
            break;
        case UPLOAD_ERR_NO_FILE:   // ファイル未選択
            throw new RuntimeException('ファイルが選択されていません', 400);
        case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
            //php.iniのpost_max_size はクリアでupload_max_filesizeが超過した時
            throw new RuntimeException('ファイルサイズが大きすぎます', 400);
        default:
            throw new RuntimeException('その他のエラーが発生しました', 500);
    }

      //拡張子を判定
      $tmp = pathinfo($_FILES["upfile"]["name"]);
      $extension = $tmp["extension"];
      if($extension === "jpg" || $extension === "jpeg" || $extension === "JPG" || $extension === "JPEG"){
          $extension = "jpeg";
      }
      elseif($extension === "png" || $extension === "PNG"){
          $extension = "png";
      }
      elseif($extension === "gif" || $extension === "GIF"){
          $extension = "gif";
      }
      elseif($extension === "mp4" || $extension === "MP4"){
          $extension = "mp4";
      }
      else{
          echo "非対応ファイルです．<br/>";
          echo ("<a href=\"index.php\">戻る</a><br/>");
          exit(1);
      }

      /* pathinfo()
      $path_parts = pathinfo('/www/htdocs/inc/lib.inc.php');
      echo $path_parts['dirname'], "\n"; // /www/htdocs/inc
      echo $path_parts['basename'], "\n"; // lib.inc.php
      echo $path_parts['extension'], "\n"; // php
      echo $path_parts['filename'], "\n"; // lib.inc
      */

      //画像・動画をバイナリデータにする．
      $raw_data = file_get_contents($_FILES['upfile']['tmp_name']);
      //php.iniのmemori_limitが小さいと変数にそのメモリ以上入れられない
      //ディフォで128M(具体的には、動画540pxで6minくらいのデータ量)

      //DBに格納するファイルネーム設定
      //こっからサーバー側の一時的なファイルネームと取得時刻を結合した文字列にsha256をかけるコード
      $date = getdate();
      //var_dump($_FILES["upfile"]["tmp_name"]); ->string(36) "/Applications/MAMP/tmp/php/phpXcfzHX"
      //upfileのtmp_nameを取得
      $fname = $_FILES["upfile"]["tmp_name"].$date["year"].$date["mon"].$date["mday"].$date["hours"].$date["minutes"].$date["seconds"];
      //echo "<br><br>".$fname;
      $fname = hash("sha256", $fname);//fileの名前をハッシュ化
    if(!empty($_POST['title'])){
      $title = $_POST['title'];
      //画像・動画をDBに格納．
      $sql = "INSERT INTO media(account, title, fname, extension, raw_data)
              VALUES (:account, :title, :fname, :extension, :raw_data);";
      $stmt = $dbh->prepare($sql);
      $stmt -> bindValue(":account",$account, PDO::PARAM_STR);
      $stmt -> bindValue(":title",$title, PDO::PARAM_STR);
      $stmt -> bindValue(":fname",$fname, PDO::PARAM_STR); //file名前(hash化済み)
      $stmt -> bindValue(":extension",$extension, PDO::PARAM_STR); //拡張子
      $stmt -> bindValue(":raw_data",$raw_data, PDO::PARAM_STR); //動画バイナリデータ
      ini_set('memory_limit', '-1');//php.iniファイルのpost_max_sizeを無制限(-1)にする
      $stmt -> execute();
    }else{
      echo "<script>alert('タイトルが入力されていません');</script>";
    }
  }//画像・動画投稿終了

  //こっからDELETE処理
  if($_POST){
  if(isset($_POST['delid'])&&$_POST['delid']!==""){
    $delid = $_POST['delid'];
    $sql = "SELECT id FROM media WHERE id = $delid";
    $result = $dbh->query($sql);
    $row=$result->fetch();
      if(!empty($row)){
        $sql = 'DELETE FROM media WHERE id=:id';
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $delid, PDO::PARAM_INT);
        $result = $stmt->execute();
      }else{
        echo "番号が存在しません";
      }
    }
  }
}catch(PDOException $e){
    echo("<p>500 Inertnal Server Error</p>");
    exit($e->getMessage());
}
?>

<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>media</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="index.css">
</head>
<body>
  <div id="base">
    <div id="head">
      <h1>みんなの画像・動画投稿サイト</h1>
    </div>
    <div id="container" class="clearfix">
      <div id="right">
        <img id="user" src="./user.png" alt="">
        <p><?php echo $account ?></p>
        <div id="open" >
          画像・動画投稿
        </div>
        <div id="mask" class="hidden"></div>
        <section id="modal" class="hidden">
          <form action="index.php" enctype="multipart/form-data" method="post">
              <label>画像/動画アップロード</label>
              <input type="text" style="padding: 5px;" name="title" placeholder="ここにタイトルを入力!!">
              <input type="file" name="upfile">
              <br>
              ※画像はjpeg方式，png方式，gif方式に対応しています．動画はmp4方式のみ対応しています．<br>
              <input type="submit" value="アップロード">
          </form>
          <div id="close">
            閉じる
          </div>
        </section>
        <div id="open2">
          削除
        </div>
        <div id="mask2" class="hidden"></div>
        <section id="modal2" class="hidden">
          <label>削除</label>
          <br>
          番号を指定してください
          <form action="index.php" enctype="multipart/form-data" method="post">
            <input type="text" name="delid">
            <input type="submit" value="削除">
          </form>
          <div id="close2">
            閉じる
          </div>
        </section>
        <form action="" method="post">
          <button id="open3" type="submit" name="logout">
            ログアウト
          </button>
        </form>
      </div>
      <!---~~~~~~~~~~~~~~~~~~~~~~画像・動画表示部分~~~~~~~~~~~~~~~~~~~~~~~~--->
      <div id="center">
        <div class="display">
  <?php
  //DBから取得して表示する．
  $sql = "SELECT * FROM media ORDER BY id;";
  $stmt = $dbh->prepare($sql);
  ini_set('memory_limit', '-1');
  $stmt -> execute();
  //こっからやっていることはidの表示とsrcパスでの画像・動画表示
  while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
    echo "<div class='contents'>";
      echo $row["id"]."　".$row['account']." さん  [タイトル: ".$row['title']."]";
      $target = $row["fname"];
      if($row["extension"] == "mp4"){
          echo ("<video src=\"./import_media.php?target=$target\" controls></video>");

      }
      elseif($row["extension"] == "jpeg" || $row["extension"] == "png" || $row["extension"] == "gif"){
          //echo ("<img style='width:300px' src='./import_media.php?target=$target'>");
          echo ("<img src='./import_media.php?target=$target'>");

      }
      echo "</div>";
  }
  ?>
      </div>
    </div>
  </div>
  <script src="script.js"></script>
</div>
</body>
</html>
