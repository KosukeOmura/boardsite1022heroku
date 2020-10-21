<?php

// データベースの接続情報
// define( 'DB_HOST', 'localhost');
// define( 'DB_USER', 'kosuke');
// define( 'DB_PASS', 'komazawataxidesu');
// define( 'DB_NAME', 'board');
define( 'DB_HOST', 'us-cdbr-east-02.cleardb.com');
define( 'DB_USER', 'baad3d3f5e8bb4');
define( 'DB_PASS', '018815c8');
define( 'DB_NAME', 'heroku_c66346a7c074732');

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$message_id = null;
$mysqli = null;
$sql = null;
$res = null;
$error_message = array();
$message_data = array();


session_start();

// 管理者としてログインしているか確認
if( empty($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true ) {
	
	// ログインページへリダイレクト
	header("Location: ./admin.php");
}

if( !empty($_GET['message_id']) && empty($_POST['message_id']) ) {

	$message_id = (int)htmlspecialchars($_GET['message_id'], ENT_QUOTES);

	// データベースに接続
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
	
	// 接続エラーの確認
	if( $mysqli->connect_errno ) {
		$error_message[] = 'データベースの接続に失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
	} else {
	
		// データの読み込み
		$sql = "SELECT * FROM message WHERE id = $message_id";
		$res = $mysqli->query($sql);
		
		if( $res ) {
			$message_data = $res->fetch_assoc();
		} else {
		
			// データが読み込めなかったら一覧に戻る
			header("Location: ./admin.php");
		}
		
		$mysqli->close();
	}
} elseif( !empty($_POST['message_id'])) {

    $message_id = (int)htmlspecialchars( $_POST['message_id'], ENT_QUOTES);
    
    //データベースに接続
    $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

    //接続エラーの確認
    if( $mysqli->connect_errno) {
        $error_message[] = 'データベースの接続に失敗しました。エラー番号' .$mysqli->connect_errno.
        ':' . $mysqli->connect_error;
    } else {
        $sql = "DELETE FROM message WHERE id = $message_id";
        $res = $mysqli->query($sql);
    }

    $mysqli->close();

    //更新に成功したら一覧に戻る
    if( $res ) {
        header( "Location: admin.php");
    }
}

?>



<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ひと言掲示板　管理ページ(投稿の削除)</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
<h1>ひと言掲示板　管理ページ(投稿の削除)</h1>

<?php if( !empty($error_message) ): ?>
    <ul class="error_message">
		<?php foreach( $error_message as $value ): ?>
            <li>・<?php echo $value; ?></li>
		<?php endforeach; ?>
    </ul>
<?php endif; ?>
<p class="text-confirm">以下の投稿を削除します。<br>よろしければ「削除」ボタンを押してください</p>
<form method="post">
	<div>
		<label for="view_name">表示名</label>
		<input id="view_name" type="text" name="view_name" value="<?php if( !empty($message_data['view_name']) ){ echo $message_data['view_name']; } ?>">
	</div>
	<div>
		<label for="message">ひと言メッセージ</label>
		<textarea id="message" name="message"　disabled>
        <?php if( !empty($message_data['message']) ){ echo $message_data['message']; } ?>
        </textarea>
    </div>
    <a class="btn_cancel" href="admin.php">キャンセル</a>
    <input type="submit" name="btn_submit" value="削除">
    <input type="hidden" name="message_id" value="<?php echo $message_data['id']; ?>">
</form>

</body>
</html>