# movie_site
 
# Features
 
メールアドレスを入力し、そこに「url+hash」が届き、そのurlをクリックするとユーザー登録画面に入れるのでメールアドレスとパスワードを入力し登録。
ログイン画面で登録したメールアドレスとパスワードを打ち込むと、画像・動画投稿サイトに遷移します。
投稿したものをDBにバイナリデータとして保存し、保存したものを表示するためにDBからそのデータを読み込むようにしています。(だいぶ処理が重くなりました)

-> mp4はデータ変換してDBに保存するのではなく、普通に動画ファイルのまま別のフォルダー階層に保存して、HTMLのvideoタグで読み込んだほうが断然速かったです

webアプリケーションの写真は "movie_site/テスト写真" に入っています
 
# Requirement
 
* phpmailer
* php7.3.8
 
# Installation

下記のサイトより「phpmailer」クローン 

git clone https://github.com/PHPMailer/PHPMailer.git
 
# Usage

setsqlフォルダーに「setsql.php」が入っていて、そこにsqlを書いたので
まずサーバーでDBにテーブル等を作ってください

phpmailerフォルダーの中に「db.php」があるのでデータベース接続情報を記入してください(接続はPDO)

「registration_mail_check.php」の48行目の「url_of_register_page」に 「http:~  ~registration_mail_form.php」を書いてください

最後に「setting.php」の9、13、22行目を変更してください(詳細はコメントアウトしてあります)
これで動くと思います

# Note
 
一先ず作った感じなので変数とかわかりにくかったりします。
 
# Author

nokyyy
 
# License
 
 nokyyy owns copyright on these sites nokyyy uploaded
