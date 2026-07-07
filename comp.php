<?php

// --- 多言語設定の追加 ---
$lang = $_POST['lang'] ?? $_GET['lang'] ?? 'ja';

// 多言語文言配列の定義
$text = [
    'ja' => [
        'title' => '幹事くん',
        'start' => '始める',
        'event_name' => 'イベントの名前を入力してください',
        'top' => 'Top',
        'back' => '戻る',
        'error' => 'エラー',
        'id_error' => 'idが取得できませんでした。',
        'file_error' => 'ファイルが存在しません。',
        'already_exists' => '既にファイルが存在します。',
        'bad_id' => '不適切なIDです。',
        'write_error' => 'ファイルの書き込みに失敗しました。',
        'developed_by' => 'Developed by',
        'delete' => '削除する',
        'update' => '更新する',
        'confirm_delete' => 'イベントを削除してよろしいですか？',
        'cancel' => 'キャンセル',
        'ok' => 'OK',
        // index.php用
        'welcome' => '「幹事くん」にようこそ',
        'description' => '「幹事くん」はイベント・歓送迎会・忘年会・新年会・同窓会などの日程調整＆出欠確認を行うツールです。<br>無料・登録不要・使い捨て型のWebサービスです！まずめるボタンを押してイベントを作成しましょう。',
        'step' => 'ステップ',
        'basic_info' => 'イベントの基本情報',
        'event_title_label' => 'イベント名',
        'event_placeholder' => '例: ○○部 忘年会2026',
        'memo_label' => 'メモ・詳細（任意）',
        'memo_placeholder' => '場所の候補や会費、伝達事項などがあれば入力してください',
        'next' => '次へ',
        'prev' => '前へ',
        'candidate_dates' => '候補日程の選択',
        'candidate' => '候補日程',
        'optional' => '（任意）',
        'create_table' => '出欠表をつくる',
        'success_title' => '出欠調整ページが完成しました！ 🎉',
        'success_desc' => '下記の生成されたURLをコピーして、参加メンバーに共有してください。<br>以後、このURLページからメンバーがそれぞれの出欠回答を入力できるようになります。',
        'generating' => 'URL生成中...',
        // detail.php用
        're_edit' => 'イベントを再編集する',
        'attendance_status' => '出欠状況・回答一覧',
        'name_header' => 'お名前',
        'comment_header' => 'コメント',
        'no_answers' => 'まだ出欠回答がありません。下のフォームから最初の回答を入力しましょう！',
        'action_delete' => '削除',
        'action_change' => '変更',
        'confirm_answer_delete' => 'この回答を削除してもよろしいですか？',
        'share_url_title' => 'このイベントの共有URL',
        'share_url_desc' => '参加メンバーにこのURLを連絡して、出欠を入力してもらってください。',
        'copy' => 'コピー',
        'copy_success' => 'URLをクリップボードにコピーしました！',
        'form_section_title' => '出欠を入力・更新する',
        'participant_name' => '参加者のお名前',
        'name_example' => '例: 山田太郎',
        'answers_label' => '各日程の出欠回答',
        'status_yes' => '◯ 行ける',
        'status_maybe' => '△ 微妙',
        'status_no' => '✕ 無理',
        'comment_optional' => 'コメント（任意）',
        'comment_example' => '例: 遅れて参加します！',
        'register_attendance' => '出欠を登録する',
        'back_to_top' => 'トップへ戻る',
        // change.php用
        'enter_attendance' => '出欠を入力する',
        'display_name' => '表示名',
        // edit.php用
        'event_re_edit' => 'イベント再編集',
        'danger_zone' => '危険エリア',
        'danger_desc' => '※一度削除すると復旧はできません。ご注意ください。',
        'delete_event_btn' => 'イベントを削除する',
        'confirm_title' => '確認画面'
    ],
    'en' => [
        'title' => 'Kanjikun',
        'start' => 'Start',
        'event_name' => 'Enter the event name',
        'top' => 'Top',
        'back' => 'Back',
        'error' => 'Error',
        'id_error' => 'Failed to retrieve the ID.',
        'file_error' => 'The file does not exist.',
        'already_exists' => 'The file already exists.',
        'bad_id' => 'Invalid ID.',
        'write_error' => 'Failed to write to the file.',
        'developed_by' => 'Developed by',
        'delete' => 'Delete',
        'update' => 'Update',
        'confirm_delete' => 'Are you sure you want to delete this event?',
        'cancel' => 'Cancel',
        'ok' => 'OK',
        // index.php
        'welcome' => 'Welcome to Kanjikun',
        'description' => 'Kanjikun is a tool for scheduling events, welcome/farewell parties, year-end/New Year parties, alumni associations, and managing attendance.<br>It is a free, registration-free, and disposable web service! Press the start button to create your event.',
        'step' => 'Step',
        'basic_info' => 'Basic Event Information',
        'event_title_label' => 'Event Name',
        'event_placeholder' => 'e.g., Year-end Party 2026',
        'memo_label' => 'Memo / Details (Optional)',
        'memo_placeholder' => 'Enter location candidates, membership fees, or any notes here.',
        'next' => 'Next',
        'prev' => 'Prev',
        'candidate_dates' => 'Select Candidate Dates',
        'candidate' => 'Candidate Date',
        'optional' => ' (Optional)',
        'create_table' => 'Create Attendance Table',
        'success_title' => 'Attendance page has been created! 🎉',
        'success_desc' => 'Copy the generated URL below and share it with the participants.<br>From now on, members can enter their attendance from this URL page.',
        'generating' => 'Generating URL...',
        // detail.php
        're_edit' => 'Edit Event',
        'attendance_status' => 'Attendance Status / Responses',
        'name_header' => 'Name',
        'comment_header' => 'Comment',
        'no_answers' => 'No responses yet. Let\'s enter the first response using the form below!',
        'action_delete' => 'Delete',
        'action_change' => 'Change',
        'confirm_answer_delete' => 'Are you sure you want to delete this response?',
        'share_url_title' => 'Share URL for this Event',
        'share_url_desc' => 'Please send this URL to the participants to have them enter their attendance.',
        'copy' => 'Copy',
        'copy_success' => 'URL copied to clipboard!',
        'form_section_title' => 'Enter / Update Attendance',
        'participant_name' => 'Participant Name',
        'name_example' => 'e.g., John Doe',
        'answers_label' => 'Attendance for Each Date',
        'status_yes' => '◯ Available',
        'status_maybe' => '△ Tentative',
        'status_no' => '✕ Unavailable',
        'comment_optional' => 'Comment (Optional)',
        'comment_example' => 'e.g., I will be arriving late!',
        'register_attendance' => 'Submit Attendance',
        'back_to_top' => 'Back to Top',
        // change.php
        'enter_attendance' => 'Enter Attendance',
        'display_name' => 'Display Name',
        // edit.php
        'event_re_edit' => 'Edit Event',
        'danger_zone' => 'Danger Zone',
        'danger_desc' => '*Once deleted, it cannot be recovered. Please be careful.',
        'delete_event_btn' => 'Delete Event',
        'confirm_title' => 'Confirmation'
    ]
];

// 1. 入力値の取得と初期化（未定義エラー防止）
$name  = $_POST['name'] ?? '';
$memo  = $_POST['memo'] ?? '';
$date1 = $_POST['date1'] ?? '';
$date2 = $_POST['date2'] ?? '';
$date3 = $_POST['date3'] ?? '';
$id    = $_POST['id'] ?? '';

$id = basename($id);
if ($id === '' || $id === '.' || $id === '..') {
    header('HTTP/1.1 400 Bad Request');
    exit(json_encode(['error' => $text[$lang]['bad_id']]));
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
	touch( $filename );
}else{
    header('HTTP/1.1 400 Bad Request');
	exit(json_encode(['error' => $text[$lang]['already_exists']]));
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
    exit(json_encode(['error' => $text[$lang]['write_error']]));
}

header('Content-Type: application/json');

$uri = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
// 生成URLの末尾に現在の言語設定を付与
$url = substr($uri, 0, strrpos($uri, "/")) . "/detail.php?id=" . urlencode($id) . "&lang=" . urlencode($lang);

$safe_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
$data1 = "<a href=\"" . $safe_url . "\" class=\"alert-link\" target=\"_blank\">" . $safe_url . "</a>";
$data2 = "<a href=\"" . $safe_url . "\" class=\"btn btn-primary\" target=\"_blank\"><i class=\"fa fa-external-link\"></i> " . htmlspecialchars($lang === 'ja' ? 'イベントページを表示する' : 'View Event Page', ENT_QUOTES, 'UTF-8') . "</a>";

echo json_encode(compact('data1','data2'));

?>