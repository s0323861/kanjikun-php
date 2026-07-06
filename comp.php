<?php

// 1. 入力値の取得と初期化（未定義エラー防止）
$name  = $_POST['name'] ?? '';
$memo  = $_POST['memo'] ?? '';
$date1 = $_POST['date1'] ?? '';
$date2 = $_POST['date2'] ?? '';
$date3 = $_POST['date3'] ?? '';
$id    = $_POST['id'] ?? '';

// 2. ディレクトリトラバーサル対策（ファイル名の安全化）
$id = basename($id);
if ($id === '' || $id === '.' || $id === '..') {
    header('HTTP/1.1 400 Bad Request');
    exit(json_encode(['error' => '不適切なIDです。']));
}

// 文字列を置換する
$name = trim($name);
$memo = str_replace(array("\r\n","\r","\n"), '<br>', $memo);
$memo = str_replace('\t', '', $memo);
$memo = trim($memo);

// ファイルの名前
$filename = "./data/" . $id . ".txt";

// ファイルの存在確認
if( !file_exists($filename) ){
	// ファイル作成
	touch( $filename );
}else{
	// すでにファイルが存在する為エラーとする
    header('HTTP/1.1 400 Bad Request');
	exit(json_encode(['error' => '既にファイルが存在します。']));
}

// 3. ファイル書き込み（排他ロックの追加）
if ($handle = fopen( $filename, 'a' )) {
    flock($handle, LOCK_EX); // ロック開始
    fwrite( $handle, $name . "\n" );
    fwrite( $handle, $memo . "\n" );
    fwrite( $handle, $date1 . "\t" . $date2 . "\t" . $date3 . "\n" );
    flock($handle, LOCK_UN); // ロック解除
    fclose($handle);
} else {
    header('HTTP/1.1 500 Internal Server Error');
    exit(json_encode(['error' => 'ファイルの書き込みに失敗しました。']));
}

// Content-TypeをJSONに指定する
header('Content-Type: application/json');

// URLの組み立て
$uri = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$url = substr($uri, 0, strrpos($uri, "/")) . "/detail.php?id=" . urlencode($id);

// 4. XSS対策（HTMLエスケープ）
$safe_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

$data1 = "<a href=\"" . $safe_url . "\" class=\"alert-link\" target=\"_blank\">" . $safe_url . "</a>";

$data2 = "<a href=\"" . $safe_url . "\" class=\"btn btn-primary\" target=\"_blank\"><i class=\"fa fa-external-link\"></i> イベントページを表示する</a>";

echo json_encode(compact('data1','data2'));

?>