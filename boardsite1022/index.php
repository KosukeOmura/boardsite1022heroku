<?php
// エラー表示を有効にする
ini_set( 'display_errors', 1 );

// 全てのエラーを表示する
ini_set('error_reporting', E_ALL);

// データベースの接続情報
// define( 'DB_HOST', 'localhost');
// define( 'DB_USER', 'kosuke');
// define( 'DB_PASS', 'komazawataxidesu');
// define( 'DB_NAME', 'board');

define( 'DB_USER', 'baad3d3f5e8bb4');
define( 'DB_PASS', '018815c8');
define( 'DB_NAME', 'heroku_c66346a7c074732');
define('PDO_DSN','mysql:host=us-cdbr-east-02.cleardb.com;dbname=' . DB_NAME);

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$now_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();
$clean = array();

session_start();


if( !empty($_POST['btn_submit']) ) {
	
	// 表示名の入力チェック
	if( empty($_POST['view_name']) ) {
		$error_message[] = '表示名を入力してください。';
	} else {
		$clean['view_name'] = htmlspecialchars( $_POST['view_name'], ENT_QUOTES);
		
		//セッションに表示名を保存
		$_SESSION['view_name'] = $clean['view_name'];
	}
	// メッセージの入力チェック
	if( empty($_POST['message']) ) {
		$error_message[] = 'ひと言メッセージを入力してください。';
	} else {
		$clean['message'] = htmlspecialchars( $_POST['message'], ENT_QUOTES);
	}

	if( empty($error_message) ) {
		
		// データベースに接続
		$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
		
		// 接続エラーの確認
		if( $mysqli->connect_errno ) {
			$error_message[] = '書き込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
		} else {

			// 文字コード設定
			$mysqli->set_charset('utf8');
			
			// 書き込み日時を取得
			$now_date = date("Y-m-d H:i:s");
			
			// データを登録するSQL作成
			$sql = "INSERT INTO message (view_name, message, post_date) VALUES ( '$clean[view_name]', '$clean[message]', '$now_date')";
			
			// データを登録
			$res = $mysqli->query($sql);
		
			if( $res ) {
				$_SESSION['success_message'] = 'メッセージを書き込みました。';
			} else {
				$error_message[] = '書き込みに失敗しました。';
			}
		
			// データベースの接続を閉じる
			$mysqli->close();
		}

		header('Location:./');
	}
}

// データベースに接続
$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 接続エラーの確認
if( $mysqli->connect_errno ) {
	$error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {

	$sql = "SELECT view_name,message,post_date FROM message ORDER BY post_date DESC";
	$res = $mysqli->query($sql);

    if( $res ) {
		$message_array = $res->fetch_all(MYSQLI_ASSOC);
    }

    $mysqli->close();
}


?>



<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
<h1>ひと言掲示板</h1>
<?php if( empty($_POST['btn_submit']) && !empty($_SESSION['success_message']) ): ?>
	<p class="success_message"><?php echo $_SESSION['success_message']; ?></p>
	<?php unset($_SESSION['success_message']); ?>
<?php endif; ?>
<?php if( !empty($error_message) ): ?>
    <ul class="error_message">
		<?php foreach( $error_message as $value ): ?>
            <li>・<?php echo $value; ?></li>
		<?php endforeach; ?>
    </ul>
<?php endif; ?>
<form method="post">
	<div>
		<label for="view_name">表示名</label>
		<input id="view_name" type="text" name="view_name" value="<?php if( !empty($_SESSION['view_name']) ){ echo $_SESSION['view_name']; } ?>">
	</div>
	<div>
		<label for="message">ひと言メッセージ</label>
		<textarea id="message" name="message"></textarea>
	</div>
	<input type="submit" name="btn_submit" value="書き込む">
</form>
<hr>
<section>
<?php if( !empty($message_array) ){ ?>
<?php foreach( $message_array as $value ){ ?>
<article>
    <div class="info">
        <h2><?php echo $value['view_name']; ?></h2>
        <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
    </div>
    <p><?php echo $value['message']; ?></p>
</article>
<?php } ?>
<?php } ?>
</section>
</body>
</html>