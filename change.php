<?php
// 1. 入力値の取得と初期化（クエリパラメータおよびPOSTデータ）
$id   = $_GET['id']   ?? $_POST['id']   ?? '';
$sid  = $_GET['sid']  ?? $_POST['sid']  ?? '';
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

// エラー判定変更
if ($id === '' || $id === '.' || $id === '..') {
    $msg = $text[$lang]['id_error'];
}

// URLの組み立てに言語を含める
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
$disp = $ans1 = $ans2 = $ans3 = $com = '';

// エラーがなければメイン処理を実行
if ($msg === '') {

    // 【パターンA】編集して送信されてきた場合 (POST)
    if ($edit === 'go') {
        $disp = $_POST['display'] ?? '';
        $ans1 = $_POST['date1'] ?? '';
        $ans2 = $_POST['date2'] ?? '';
        $ans3 = $_POST['date3'] ?? '';
        $com  = $_POST['comment'] ?? '';

        // 禁則文字の置換とタグ除去
        $disp = str_replace(['$', '?', '.', "\t"], ['＄', '？', '．', ' '], strip_tags($disp));
        $com  = str_replace(['$', '?', '.', "\t"], ['＄', '？', '．', ' '], strip_tags($com));

        // ファイルを読み込んで配列化
        if ($fp = fopen($file, 'r')) {
            flock($fp, LOCK_SH);
            $all = [];
            while (($line = fgets($fp)) !== false) {
                $all[] = $line;
            }
            flock($fp, LOCK_UN);
            fclose($fp);
        }

        // 配ラーの書き換え
        $new = [];
        foreach ($all as $i => $line) {
            $line = rtrim($line, "\r\n");
            if ($i === 0) {
                $name = $line;
            } elseif ($i === 1) {
                $memo = $line;
            } elseif ($i === 2) {
                list($date1, $date2, $date3) = array_pad(explode("\t", $line), 3, '');
            } else {
                $cols = explode("\t", $line);
                $tmp5 = $cols[4] ?? '';
                if ($tmp5 === $sid) {
                    // 該当するセッションIDのデータを更新
                    $line = implode("\t", [$disp, $ans1, $ans2, $ans3, $sid, $com]);
                }
            }
            $new[] = $line . "\n";
        }

        // ファイルに書き戻し（排他ロック）
        if ($fp = fopen($file, 'w')) {
            flock($fp, LOCK_EX);
            foreach ($new as $line) {
                fwrite($fp, $line);
            }
            flock($fp, LOCK_UN);
            fclose($fp);
        }

    // 【パターンB】最初にページを開いた場合 (GET)
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
                } elseif ($i === 2) {
                    list($date1, $date2, $date3) = array_pad(explode("\t", $line), 3, '');
                } else {
                    $cols = explode("\t", $line);
                    $tmp1 = $cols[0] ?? '';
                    $tmp2 = $cols[1] ?? '';
                    $tmp3 = $cols[2] ?? '';
                    $tmp4 = $cols[3] ?? '';
                    $tmp5 = $cols[4] ?? '';
                    $tmp6 = $cols[5] ?? '';

                    if ($tmp5 === $sid) {
                        $disp = $tmp1;
                        $ans1 = $tmp2;
                        $ans2 = $tmp3;
                        $ans3 = $tmp4;
                        $com  = $tmp6;
                    }
                }
                $i++;
            }
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }
}

// XSS対策（HTML出力用エスケープ関数の定義）
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
  <!-- Font Awesome 6 (最新の標準CDN) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style type="text/css">
    body { padding-top: 80px; }
  </style>
</head>
<body>

<header>
  <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <div class="container">
      <a href="./?lang=<?= h($lang) ?>" class="navbar-brand"><i class="fa-solid fa-calendar-days"></i> <?= h($text[$lang]['title']) ?></a>
      <div class="collapse navbar-collapse" id="navbar-main">
        <ul class="navbar-nav me-auto mb-2 mb-md-0">
          <li class="nav-item">
            <a class="nav-link" href="<?= h($url) ?>"><i class="fa-solid fa-house"></i> <?= h($text[$lang]['top']) ?></a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>

<div class="container">

<?php if ($msg === ''): ?>
  <!-- フォームセクション -->
  <section id="enter" class="my-4">
    <div class="row">
      <div class="col-12">
        <div class="page-header mb-4">
          <h1 id="forms" class="display-6"><?= h($text[$lang]['enter_attendance']) ?></h1>
          <a class="btn btn-secondary" href="./detail.php?id=<?= h($id) ?>&lang=<?= h($lang) ?>"><i class="fa-solid fa-chevron-left"></i> <?= h($text[$lang]['back']) ?></a>        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <!-- BS5ではwellが廃止されたためcardで再現 -->
        <div class="card bg-light p-4 shadow-sm">
          <form method="post" action="change.php?id=<?= h($id) ?>&sid=<?= h($sid) ?>&lang=<?= h($lang) ?>">
            <input type="hidden" name="edit" value="go">
            <input type="hidden" name="id" value="<?= h($id) ?>">
            <input type="hidden" name="sid" value="<?= h($sid) ?>">
            
            <fieldset>
              <!-- 表示名 -->
              <div class="row mb-3 align-items-center">
                <label for="inputName" class="col-lg-2 col-form-label fw-bold"><?= h($text[$lang]['display_name']) ?></label>
                <div class="col-lg-10">
                  <input type="text" class="form-control" id="inputName" name="display" value="<?= h($disp) ?>" required>
                </div>
              </div>

              <!-- 日にち候補テーブル -->
              <div class="row mb-3">
                <label class="col-lg-2 col-form-label fw-bold">日にち候補</label>
                <div class="col-lg-10">
                  <table class="table table-striped table-hover align-middle">
                    <tbody>
                      <!-- 候補1 -->
                      <tr>
                        <td><?= h($date1) ?></td>
                        <td>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="date1" id="date1_yes" value="yes" <?= $ans1 === 'yes' ? 'checked' : '' ?>>
                            <label class="form-check-label" Bres for="date1_yes"><i class="fa-regular fa-circle text-success"></i></label>
                          </div>
                        </td>
                        <td>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="date1" id="date1_notyet" value="notyet" <?= $ans1 === 'notyet' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="date1_notyet"><i class="fa-solid fa-question text-warning"></i></label>
                          </div>
                        </td>
                        <td>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="date1" id="date1_nono" value="nono" <?= $ans1 === 'nono' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="date1_nono"><i class="fa-solid fa-xmark text-danger"></i></label>
                          </div>
                        </td>
                      </tr>

                      <!-- 候補2 -->
                      <?php if ($date2 !== ''): ?>
                      <tr>
                        <td><?= h($date2) ?></td>
                        <td>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="date2" id="date2_yes" value="yes" <?= $ans2 === 'yes' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="date2_yes"><i class="fa-regular fa-circle text-success"></i></label>
                          </div>
                        </td>
                        <td>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="date2" id="date2_notyet" value="notyet" <?= $ans2 === 'notyet' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="date2_notyet"><i class="fa-solid fa-question text-warning"></i></label>
                          </div>
                        </td>
                        <td>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="date2" id="date2_nono" value="nono" <?= $ans2 === 'nono' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="date2_nono"><i class="fa-solid fa-xmark text-danger"></i></label>
                          </div>
                        </td>
                      </tr>
                      <?php endif; ?>

                      <!-- 候補3 -->
                      <?php if ($date3 !== ''): ?>
                      <tr>
                        <td><?= h($date3) ?></td>
                        <td>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="date3" id="date3_yes" value="yes" <?= $ans3 === 'yes' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="date3_yes"><i class="fa-regular fa-circle text-success"></i></label>
                          </div>
                        </td>
                        <td>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="date3" id="date3_notyet" value="notyet" <?= $ans3 === 'notyet' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="date3_notyet"><i class="fa-solid fa-question text-warning"></i></label>
                          </div>
                        </td>
                        <td>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="date3" id="date3_nono" value="nono" <?= $ans3 === 'nono' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="date3_nono"><i class="fa-solid fa-xmark text-danger"></i></label>
                          </div>
                        </td>
                      </tr>
                      <?php endif; ?>
                    </tbody>
                  </table> 
                </div>
              </div>

              <!-- コメント -->
              <div class="row mb-4 align-items-center">
                <label for="inputComment" class="col-lg-2 col-form-label fw-bold"><?= h($text[$lang]['comment_header']) ?></label>
                <div class="col-lg-10">
                  <input type="text" class="form-control" name="comment" id="inputComment" value="<?= h($com) ?>">
                </div>
              </div>

              <!-- アクションボタン -->
              <div class="row mb-3">
                <div class="col-lg-10 offset-lg-2">
                  <button type="submit" class="btn btn-primary"><i class="fa-solid fa-rotate"></i> <?= h($text[$lang]['update']) ?></button>
                </div>
              </div>

              <hr>

              <div class="row">
                <div class="col-lg-10 offset-lg-2">
                  <!-- detail.php の削除仕様に合わせて調整してください -->
                  <a href="detail.php?id=<?= h($id) ?>&sid=<?= h($sid) ?>&lang=<?= h($lang) ?>&edit=delete" class="btn btn-danger"><i class="fa-solid fa-trash-can"></i> <?= h($text[$lang]['delete']) ?></a>
                </div>
              </div>

            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </section>

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

  <hr>

  <!-- フッター -->
  <footer class="py-3 my-4">
    <p class="text-muted">Developed by <a href="https://github.com/s0323861" class="text-decoration-none" target="_blank" rel="noopener">Akira Mukai</a> 2021-2026</p>
  </footer>

</div>

<!-- Bootstrap 5 JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>