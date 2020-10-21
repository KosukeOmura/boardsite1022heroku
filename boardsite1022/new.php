<?php 

var_dump($_POST);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>掲示板</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <h1>ひと言掲示板</h1>

    <form method="post">
        <div>
            <label for="view_name">表示名</label>
            <input id="view_name" type="text" name="view_name" value="">
        </div>
        <div>
            <label for="message">ひと言メッセージ</label>
            <textarea id="message"  name="message"></textarea>
        </div>
        <input type="submit" name="btn_submit" value="投稿">
    </form>
</body>
</html>