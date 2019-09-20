<?php
// データベース情報
define('DB_NAME', 'wordboard_db');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');

$dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST . ';charset:utf8mb4';
$driver_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    // データベースに接続
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $driver_options);

    /* データベースから値を取ってきたり、データを挿入したりする処理 */

} catch (PDOException $e) {
    /**
     * エラーが発生時は、「500 Internal Server Error」でテキスト表示し終了
     * もし手抜きしたくない場合は、普通にHTMLの表示を継続
     * 商用環境ではログファイルに記録し、Webブラウザにはエラーを出さないのを推奨
     */
    header('Content-Type: text/plain; charset=utf-8', true, 500);
    exit('データベースの接続に失敗しました。' . $e->getMessage());

}

/**
 * Webブラウザにこれから表示するのが、UTF-8によるHTMLであることを明示
 * （または、<meta charset="utf-8"> のどちらか1つあればOK。両方あってもOK。
 */
header('Content-Type: text/html; charset=utf-8');



$errors = array();

// POSTなら保存処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 名前が正しく入力されているかチェック
    $name = null;
    if (!isset($_POST['name']) || !strlen($_POST['name'])) {
        $errors['name'] = '名前を入力してください。';
    } else if (strlen($_POST['name']) > 40) {
        $errors['name'] = '名前は40文字以内で入力してください。';
    } else {
        $name = $_POST['name'];
    }

    // ひとことが正しく入力されているかチェック
    $comment = null;
    if (!isset($_POST['comment']) || !strlen($_POST['comment'])) {
        $errors['comment'] = 'ひとことを入力してください。';
    } else if (strlen($_POST['comment']) > 200) {
        $errors['comment'] = 'ひとことは200文字以内で入力してください。';
    } else {
        $comment = $_POST['comment'];
    }

    // エラーがなければ保存
    if (count($errors) === 0) {
        // 保存するためのSQL文を作成
        $stmt = $pdo -> prepare("INSERT INTO post (name, comment, created_at) VALUES (:name, :comment, :created_at)");

        $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
        $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt -> bindParam(':created_at', date('y-m-d H:i:s'), PDO::PARAM_STR);

        // 保存する
        $stmt -> execute();

        header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }
}

// 投稿された内容を取得するSQLを作成して結果を取得
$stmt = $pdo -> query("SELECT * FROM `post` ORDER BY `created_at` DESC");

// 取得した結果を$postsに格納
$posts = array();
if ($stmt !== false && $stmt -> rowCount()) {
    while ($post = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $posts[] = $post;
    }
}

// 接続を閉じる
$stmt = null;
unset($pdo);

include 'views/bbs_view.php';

 ?>
