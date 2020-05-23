<?php
    //index.phpページからのsrc先です
    /*ロジックとしてはこのページにデータベースから取得したバイナリデータを
    表示させてそれをindex.phpページからsrcするって感じです*/
    if(isset($_GET["target"]) && $_GET["target"] !== ""){
        $target = $_GET["target"];
    }
    else{
        header("Location: index.php");//
    }
    $MIMETypes = array(
        'png' => 'image/png',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'mp4' => 'video/mp4'
    );
    try {
        require_once("../phpmailer/db.php");
        $dbh = db_connect();
        $sql = "SELECT * FROM media WHERE fname = :target;";
        $stmt = $dbh->prepare($sql);
        $stmt -> bindValue(":target", $target, PDO::PARAM_STR);
        $stmt -> execute();
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);

        header("Content-Type: ".$MIMETypes[$row["extension"]]);//MIMEタイプを指定
        //このファイルのタイプを指定し、HTTPレスポンスヘッダに送信
        //もし指定しないとサーバーによってはvideo/mp4などと認識せずtext/plainタイプとかで認識されてしまう
        //そうなるとブラウザは無視か、あかんことになる
        //echo "helloworld";textタイプが入ると読み込めなくなる->おそらくmultipart/form-data型で返すんじゃね
        //あくまでvideo/mp4タイプでこのページに表示したい
        echo ($row["raw_data"]);
    }
    catch (PDOException $e) {
        echo("<p>500 Inertnal Server Error</p>");
        exit($e->getMessage());
    }
?>
