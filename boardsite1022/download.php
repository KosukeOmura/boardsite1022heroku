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

// 変数の初期化
$csv_data = null;
$sql = null;
$res = null;
$message_array = array();
$limit = null;

session_start();

if( !empty($_GET['btn_logout']) ) {
	unset($_SESSION['admin_login']);
}

// 取得件数
if( !empty($_GET['limit']) ) {

	if( $_GET['limit'] === "10" ) {
		$limit = 10;
	} elseif( $_GET['limit'] === "30" ) {
		$limit = 30;
	}
}


if( !empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true ) {

    // 出力の設定
    //パスを指定
    header("Content-Type: application/octet-stream");
    //メッセージデータ.csvでダウンロードされるように指定
    header("Content-Disposition: attachment; filename=メッセージデータ.csv");
    header("Content-Transfer-Encoding: binary");

    //データベースに接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    //接続エラーの確認
    if( !$mysqli->connect_errno) {

        if( !empty($limit) ) {
			$sql = "SELECT * FROM message ORDER BY post_date ASC LIMIT $limit";
		} else {
			$sql = "SELECT * FROM message ORDER BY post_date ASC";
		}

        //$sql = "SELECT * FROM message ORDER BY post_date ASC";

        $sql = "SELECT * FROM message ORDER BY post_date ASC";
        $res = $mysqli->query($sql);

        if( $res ){
            $message_array = $res->fetch_all(MYSQLI_ASSOC);
        }

        $mysqli->close();
    }

    // CSVデータを作成
    //if文でファイルとして出力する投稿データがあるか確認あればラベル作成生成
    if( !empty($message_array)) {

        //1行目のラベル作成
        $csv_data .= '"ID","表示名","メッセージ","投稿日時"'."\n";

        //foreachで投稿データの数だけループ処理してファイル内容を$csv_dateに追記
        foreach( $message_array as $value) {

            //データを1行ずつCSVファイルに書き込む
             // \nは改行コード　行の終わりを指定する
            $csv_data .= '"' . $value['id'] . '","' . $value['view_name'] . '","' . $value['message'] . '","' . $value['post_date'] . "\"\n";
        }
    }

    //ファイルを出力
    //ファイル内容を書き込んできたcsv_dataをecho関数で出力するだけでファイルとして出力
    echo $csv_data;

} else {

	// ログインページへリダイレクト
	header("Location: ./admin.php");
}

return;
?>