<?php
// 1. 入力値の取得と初期化
$id   = $_GET['id']   ?? $_POST['id']   ?? '';
$edit = $_GET['edit'] ?? $_POST['edit'] ?? '';

$msg = '';

// --- 多言語設定の追加 ---
$lang = $_GET['lang'] ?? $_POST['lang'] ?? 'ja';

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

// ディレクトリトラバーサル対策
if ($id === '' || $id === '.' || $id === '..') {
    $msg = $text[$lang]['id_error'];
}

// URLの組み立て
$uri  = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$base_dir = substr($uri, 0, strrpos($uri, "/"));
$url  = $base_dir . "/detail.php?id=" . urlencode($id) . "&lang=" . urlencode($lang);

// ファイルの存在確認
$file = "./data/" . $id . ".txt";
if ($msg === '') {
    if (!file_exists($file)) {
        $msg = $text[$lang]['file_error'];
    }
}

// 変数の初期化
$name = $memo = $date1 = $date2 = $date3 = '';

// エラーがなければメイン処理を実行
if ($msg === '') {

    // 【パターンA】「更新する」ボタンを押して送信されてきた場合 (POST)
    if ($edit === 'go') {
        $name  = $_POST['name'] ?? '';
        $memo  = $_POST['memo'] ?? '';
        
        // datetime-local からは "YYYY-MM-DDTHH:mm" 形式で届きます
        $date1 = $_POST['date1'] ?? '';
        $date2 = $_POST['date2'] ?? '';
        $date3 = $_POST['date3'] ?? '';

        // 禁則文字の置換とタグ除去
        $name = str_replace(['$', '?', '.', "\t"], ['＄', '？', '．', ' '], strip_tags($name));
        $memo = str_replace(['$', '?', '.', "\t"], ['＄', '？', '．', ' '], strip_tags($memo));
        $memo = str_replace(["\r\n", "\r", "\n"], '<br>', $memo);

        // 既存のファイルを読み込んで全行保持する[cite: 9]
        if ($fp = fopen($file, 'r')) {
            flock($fp, LOCK_SH);
            $all = [];
            while (($line = fgets($fp)) !== false) {
                $all[] = $line;
            }
            flock($fp, LOCK_UN);
            fclose($fp);
        }

        // ファイルへの書き込み（最初の3行を更新）[cite: 9]
        if ($fp = fopen($file, 'w')) {
            flock($fp, LOCK_EX);
            fwrite($fp, $name . "\n");
            fwrite($fp, $memo . "\n");
            fwrite($fp, $date1 . "\t" . $date2 . "\t" . $date3 . "\n");

            // 4行目以降の出欠データを書き戻す[cite: 9]
            foreach ($all as $cnt => $line) {
                if ($cnt > 2) {
                    fwrite($fp, $line);
                }
            }
            flock($fp, LOCK_UN);
            fclose($fp);
        }

        // 画面表示用に <br> を改行コードに戻す[cite: 9]
        $memo = str_replace('<br>', "\r\n", $memo);

        // リダイレクト部分（保存完了時）
        header("Location: edit.php?id=" . urlencode($id) . "&lang=" . urlencode($lang));
        exit;

    // 【パターンB】画面遷移だけでページを開いた場合 (GET)[cite: 9]
    } else {
        if ($fp = fopen($file, 'r')) {
            flock($fp, LOCK_SH);
            $i = 0;
            while (($line = fgets($fp)) !== false) {
                $line = rtrim($line, "\r\n");
                if ($i === 0) {
                    $name = $line;
                } elseif ($i === 1) {
                    $memo = $line;
                    $memo = str_replace('<br>', "\r\n", $memo);
                } elseif ($i === 2) {
                    list($date1, $date2, $date3) = array_pad(explode("\t", $line), 3, '');
                }
                $i++;
            }
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

    // 既存データ（古いPerl版の形式など）を HTML5の datetime-local 形式 (YYYY-MM-DDTHH:mm) に変換
    auto_format_to_html5_date($date1);
    auto_format_to_html5_date($date2);
    auto_format_to_html5_date($date3);
}

/**
 * 古いファイルの日付形式を HTML5 (YYYY-MM-DDTHH:mm) 形式に変換するヘルパー関数
 */
function auto_format_to_html5_date(&$date_str) {
    if (empty($date_str)) return;

    // すでに YYYY-MM-DDTHH:mm 形式（新仕様で保存されたデータ）なら何もしない
    if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $date_str)) {
        return;
    }

    // 旧形式 1: "2021/10/25(月) 18:00" や "2021/10/25 18:00" などの場合
    if (preg_match('/^(\d{4})\/(\d{1,2})\/(\d{1,2})(?:\([^)]+\))?\s+(\d{2}:\d{2})/', $date_str, $matches)) {
        $year  = $matches[1];
        $month = sprintf('%02d', $matches[2]);
        $day   = sprintf('%02d', $matches[3]);
        $time  = $matches[4];
        $date_str = "{$year}-{$month}-{$day}T{$time}";
        return;
    }

    // 旧形式 2: 前回のコードで変換されていた "MM/DD/YYYY HH:mm" の場合[cite: 9]
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}:\d{2})/', $date_str, $matches)) {
        $date_str = "{$matches[3]}-{$matches[1]}-{$matches[2]}T{$matches[4]}";
        return;
    }
}

// XSS対策
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="<?= h($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($text[$lang]['title']) ?></title>
  <link rel="shortcut icon" href="favicon.ico">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style type="text/css">
    body { padding-top: 80px; }
  </style>
</head>
<body>

<header>
  <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <div class="container">
      <a href="./" class="navbar-brand"><i class="fa-solid fa-calendar-days"></i> 幹事くん</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-main" aria-controls="navbar-main" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbar-main">
        <ul class="navbar-nav me-auto mb-2 mb-md-0">
          <li class="nav-item">
            <a class="nav-link" href="<?= h($url) ?>"><i class="fa-solid fa-house"></i> Top</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>

<div class="container">

<?php if ($msg === ''): ?>
    <div class="row">
      <div class="col-12">
        <div class="page-header mb-4">
          <h1 class="display-6"><?= h($text[$lang]['event_re_edit']) ?></h1>
          <a class="btn btn-secondary" href="./detail.php?id=<?= h($id) ?>&lang=<?= h($lang) ?>"><i class="fa-solid fa-chevron-left"></i> <?= h($text[$lang]['back']) ?></a>
        </div>
      </div>
    </div>

    <!-- フォーム -->
    <form method="post" action="edit.php?id=<?= h($id) ?>&lang=<?= h($lang) ?>">
      <input type="hidden" name="edit" value="go">
      <input type="hidden" name="id" value="<?= h($id) ?>">
      
      <div class="row g-4">
        <!-- 左半分：イベント名とメモ -->
        <div class="col-lg-6">
          <div class="card bg-light p-4 shadow-sm">
            <fieldset>
              <div class="mb-3">
                <label for="inputName" class="form-label fw-bold">イベント名</label>
                <input type="text" maxlength="50" class="form-control" id="inputName" name="name" value="<?= h($name) ?>" required>
              </div>

              <div class="mb-3">
                <label for="textArea" class="form-label fw-bold">メモ</label>
                <textarea class="form-control" rows="3" id="textArea" name="memo"><?= h($memo) ?></textarea>
              </div>
            </fieldset>
          </div>
        </div>

        <!-- 右半分：候補日程（HTML5標準 input type="datetime-local" を使用） -->
        <div class="col-lg-5 ms-auto">
          <div class="p-2">
            <fieldset>
              <!-- 候補日程1 -->
              <div class="mb-3">
                <label for="date1" class="form-label fw-bold">候補日程1</label>
                <input type="datetime-local" class="form-control" id="date1" name="date1" value="<?= h($date1) ?>" required>
              </div>

              <!-- 候補日程2 -->
              <div class="mb-3">
                <label for="date2" class="form-label fw-bold">候補日程2（任意）</label>
                <input type="datetime-local" class="form-control" id="date2" name="date2" value="<?= h($date2) ?>">
              </div>

              <!-- 候補日程3 -->
              <div class="mb-3">
                <label for="date3" class="form-label fw-bold">候補日程3（任意）</label>
                <input type="datetime-local" class="form-control" id="date3" name="date3" value="<?= h($date3) ?>">
              </div>

              <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-rotate"></i> 更新する</button>
              </div>
            </fieldset>
          </div>
        </div>
      </div>
    </form>

    <!-- 削除エリア -->
    <div class="row mt-5">
      <div class="col-12">
        <div class="card border-danger p-4 bg-light-subtle">
          <h5 class="text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> <?= h($text[$lang]['danger_zone']) ?></h5>
          <p class="text-muted mb-3"><?= h($text[$lang]['danger_desc']) ?></p>
          <div>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#myModal"><i class="fa-solid fa-trash-can"></i> <?= h($text[$lang]['delete_event_btn']) ?></button>
          </div>
        </div>
      </div>
    </div>

    <!-- モーダル -->
    <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"><?= h($text[$lang]['confirm_title']) ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="mb-0"><?= h($text[$lang]['confirm_delete']) ?></p>
          </div>
          <div class="modal-footer">
            <a href="./?cmd=delete&id=<?= h($id) ?>&lang=<?= h($lang) ?>" class="btn btn-danger"><?= h($text[$lang]['ok']) ?></a>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= h($text[$lang]['cancel']) ?></button>
          </div>
        </div>
      </div>
    </div>

<?php else: ?>
  <!-- エラーセクション -->
  <div class="row my-5">
    <div class="col-12 text-center">
      <div class="alert alert-danger" role="alert">
        <h1 class="alert-heading h3"><i class="fa-solid fa-triangle-exclamation"></i> エラー</h1>
        <p class="lead mb-0"><?= h($msg) ?></p>
      </div>
    </div>
  </div>
<?php endif; ?>

  <hr class="mt-5">

  <!-- フッター -->
  <footer class="py-3 my-4">
    <p class="text-muted">Developed by <a href="https://github.com/s0323861" class="text-decoration-none" target="_blank" rel="noopener">Akira Mukai</a> 2021-2026</p>
  </footer>

</div>

<!-- Bootstrap 5 JavaScript Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>