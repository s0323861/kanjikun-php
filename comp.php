<?php

// --- 多言語設定の追加 ---
$lang = $_POST['lang'] ?? $_GET['lang'] ?? 'ja';

// 安全対策：許可する言語コードのみに制限（ディレクトリトラバーサル防止）
if (!in_array($lang, ['ja', 'en'], true)) {
    $lang = 'ja';
}

// 言語ファイルの読み込み（選択された言語の配列をダイレクトに格納）
$text = require __DIR__ . "/lang/{$lang}.php";

$id    = $_POST['id']    ?? '';
$name  = $_POST['name']  ?? '';
$memo  = $_POST['memo']  ?? '';
$date1 = $_POST['date1'] ?? '';
$date2 = $_POST['date2'] ?? '';
$date3 = $_POST['date3'] ?? '';

// 文字列を置換する
$name = trim($name);
$memo = str_replace(array("\r\n","\r","\n"), '<br>', $memo);
$memo = str_replace('\t', '', $memo);
$memo = trim($memo);

// ファイルの名前
$filename = "./data/" . $id . ".txt";

// ファイルの存在確認
if( !file_exists($filename) ){
	touch( $filename );
}else{
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json; charset=UTF-8');
	exit(json_encode(['error' => $text['already_exists']]));
}

if ($handle = fopen( $filename, 'a' )) {
    flock($handle, LOCK_EX);
    fwrite( $handle, $name . "\n" );
    fwrite( $handle, $memo . "\n" );
    fwrite( $handle, $date1 . "\t" . $date2 . "\t" . $date3 . "\n" );
    flock($handle, LOCK_UN);
    fclose($handle);
} else {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json; charset=UTF-8');
    exit(json_encode(['error' => $text['write_error']]));
}

header('Content-Type: application/json; charset=UTF-8');

$uri = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
// 生成URLの末尾に現在の言語設定を付与
$url = substr($uri, 0, strrpos($uri, "/")) . "/detail.php?id=" . urlencode($id) . "&lang=" . urlencode($lang);

echo json_encode(array('url' => $url));

?>