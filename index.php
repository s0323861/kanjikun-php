<?php
header('Content-Type: text/html; charset=UTF-8');

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : '';

// 言語設定の取得（デフォルトは日本語 'ja'）
$lang = $_GET['lang'] ?? 'ja';

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

$file = "./data/" . $id . ".txt";

if ($cmd === "delete") {
    if (file_exists($file)) {
        unlink($file);
    }
}

function randstr($length = 10) {
    $chars = array_merge(range('a', 'z'), range(0, 9));
    $rand_str_tmp = '';
    $max_idx = count($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $rand_str_tmp .= $chars[mt_rand(0, $max_idx)];
    }
    return $rand_str_tmp;
}

$rand_str = randstr(10);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($text[$lang]['title'], ENT_QUOTES, 'UTF-8') ?> - イベントの出欠管理・スケジュール調整ツール</title>
  <link rel="shortcut icon" href="favicon.ico">
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome 4.4.0 -->
  <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <!-- カレンダーCSS -->
  <link rel="stylesheet" type="text/css" href="./css/bootstrap-datetimepicker.css">
  
  <style type="text/css">
    body {
      background-color: #f3f4f7;
      font-family: 'Helvetica Neue', Arial, sans-serif;
      color: #333;
    }
    
    /* Designmodoスタイルのすっきりしたコンテナとカードデザイン */
    .main-container {
      max-width: 800px;
      margin: 60px auto;
    }
    
    .form-card {
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      border: none;
      padding: 40px;
      transition: all 0.3s ease;
    }
    
    /* プログレスバーのカスタマイズ */
    .progress-track {
      margin-bottom: 40px;
    }
    .progress {
      height: 8px;
      border-radius: 4px;
      background-color: #e9ecef;
    }
    .progress-bar {
      background-color: #4f46e5; /* モダンなインディゴブルー */
      transition: width 0.4s ease;
    }
    
    /* ステップ表示テキスト */
    .step-indicator {
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #4f46e5;
      font-weight: 700;
      margin-bottom: 10px;
    }
    
    .form-title {
      font-size: 1.75rem;
      font-weight: 700;
      color: #111827;
      margin-bottom: 30px;
    }
    
    /* フォームコントロールのモダン化 */
    .form-control {
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      padding: 12px 16px;
      font-size: 1rem;
      transition: all 0.2s ease;
    }
    .form-control:focus {
      border-color: #4f46e5;
      box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
      color: #111827;
    }
    
    .form-label {
      font-weight: 600;
      color: #374151;
      margin-bottom: 8px;
    }
    
    .input-group-text {
      background-color: #f9fafb;
      border: 2px solid #e5e7eb;
      border-right: none;
      border-radius: 10px 0 0 10px;
      color: #6b7280;
    }
    .input-group .form-control {
      border-left: none;
      border-radius: 0 10px 10px 0;
    }
    .input-group .form-control:focus {
      border-left: 2px solid #4f46e5;
    }
    
    /* モダンなボタン */
    .btn-action {
      padding: 12px 30px;
      font-weight: 600;
      border-radius: 10px;
      transition: all 0.2s ease;
    }
    .btn-next {
      background-color: #4f46e5;
      color: white;
      border: none;
    }
    .btn-next:hover:not(:disabled) {
      background-color: #4338ca;
      color: white;
    }
    .btn-next:disabled {
      background-color: #c7d2fe;
      border: none;
    }
    .btn-prev {
      background-color: #f3f4f6;
      color: #4b5563;
      border: none;
    }
    .btn-prev:hover {
      background-color: #e5e7eb;
      color: #1f2937;
    }
    
    /* URL表示エリア */
    .url-box {
      background-color: #f0fdf4;
      border: 2px dashed #bbf7d0;
      border-radius: 12px;
      padding: 20px;
      color: #166534;
      font-weight: 600;
      word-break: break-all;
    }
    
    /* フッター */
    footer {
      margin-top: 60px;
      color: #9ca3af;
      font-size: 0.9rem;
    }
    footer a {
      color: #6b7280;
      text-decoration: none;
    }
    footer a:hover {
      text-decoration: underline;
    }
    
    /* ウィザードのタブ自体は非表示にし、進捗バーとボタンで制御 */
    .wizard-inner, .nav-tabs {
      display: none !important;
    }
  </style>
</head>
<body>

<header>
  <nav class="navbar navbar-expand-lg navbar-white bg-white border-bottom fixed-top shadow-sm py-3">
    <div class="container">
      <a href="./?lang=<?= urlencode($lang) ?>" class="navbar-brand fw-bold text-dark"><i class="fa fa-calendar-o text-primary"></i> <?= htmlspecialchars($text[$lang]['title'], ENT_QUOTES, 'UTF-8') ?></a>
      <!-- 言語切り替えリンクの設置例 -->
      <div class="ms-auto">
        <a href="?lang=ja" class="btn btn-sm <?= $lang === 'ja' ? 'btn-secondary' : 'btn-outline-secondary' ?>">JA</a>
        <a href="?lang=en" class="btn btn-sm <?= $lang === 'en' ? 'btn-secondary' : 'btn-outline-secondary' ?>">EN</a>
      </div>
    </div>
  </nav>
</header>

<main class="container main-container" style="padding-top: 60px;">
  <div class="form-card">
    
    <div class="progress-track">
      <div class="d-flex justify-content-between mb-2">
        <span class="fw-bold text-muted small" id="progress-text"><?= htmlspecialchars($text[$lang]['step'], ENT_QUOTES, 'UTF-8') ?> 1 / 3</span>
        <span class="fw-bold text-muted small" id="progress-percent">33%</span>
      </div>
      <div class="progress">
        <div class="progress-bar" id="main-progress" role="progressbar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
    </div>

    <div class="tab-content" id="wizardTabContent">
        
        <!-- ステップ1: ウェルカム -->
        <div class="tab-pane fade show active" id="step1" role="tabpanel">
            <div class="step-indicator">Welcome</div>
            <h3 class="form-title"><?= htmlspecialchars($text[$lang]['welcome'], ENT_QUOTES, 'UTF-8') ?> <span class="text-danger"><i class="fa fa-heart"></i></span></h3>
            <p class="text-secondary leading-relaxed mb-5">
                <?= $text[$lang]['description'] // HTMLタグを含むためそのまま出力 ?>
            </p>
  
            <div class="d-flex justify-content-end mt-4">
                <button type="button" class="btn btn-action btn-next next-step"><?= htmlspecialchars($text[$lang]['start'], ENT_QUOTES, 'UTF-8') ?> <i class="fa fa-chevron-right ms-2"></i></button>
            </div>
        </div>

        <!-- ステップ2: イベント情報入力 -->
        <div class="tab-pane fade" id="step2" role="tabpanel">
            <div class="step-indicator">Step 01</div>
            <h3 class="form-title"><?= htmlspecialchars($text[$lang]['basic_info'], ENT_QUOTES, 'UTF-8') ?></h3>

            <form role="form">
                <div class="mb-4">
                    <label for="inputName" class="form-label"><?= htmlspecialchars($text[$lang]['event_title_label'], ENT_QUOTES, 'UTF-8') ?> <span class="text-danger">*</span></label>
                    <input type="text" maxlength="50" class="form-control" id="inputName" name="name" placeholder="<?= htmlspecialchars($text[$lang]['event_placeholder'], ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="mb-4">
                    <label for="textArea" class="form-label"><?= htmlspecialchars($text[$lang]['memo_label'], ENT_QUOTES, 'UTF-8') ?></label>
                    <textarea class="form-control" rows="4" id="textArea" maxlength="200" name="memo" placeholder="<?= htmlspecialchars($text[$lang]['memo_placeholder'], ENT_QUOTES, 'UTF-8') ?>"></textarea>
                </div>

                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-action btn-prev prev-step"><i class="fa fa-chevron-left me-2"></i> <?= htmlspecialchars($text[$lang]['prev'], ENT_QUOTES, 'UTF-8') ?></button>
                    <button type="button" class="btn btn-action btn-next next-step" id="stp1btn"><?= htmlspecialchars($text[$lang]['next'], ENT_QUOTES, 'UTF-8') ?> <i class="fa fa-chevron-right ms-2"></i></button>
                </div>
            </form>
        </div>

        <!-- ステップ3: 日程候補 -->
        <div class="tab-pane fade" id="step3" role="tabpanel">
            <div class="step-indicator">Step 02</div>
            <h3 class="form-title"><?= htmlspecialchars($text[$lang]['candidate_dates'], ENT_QUOTES, 'UTF-8') ?></h3>

            <form role="form">
                <div class="mb-4">
                    <label for="date1" class="form-label"><?= htmlspecialchars($text[$lang]['candidate'], ENT_QUOTES, 'UTF-8') ?>1 <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control date-1" id="date1" name="date1" required>
                </div>

                <div class="mb-4">
                    <label for="date2" class="form-label"><?= htmlspecialchars($text[$lang]['candidate'], ENT_QUOTES, 'UTF-8') ?>2<?= htmlspecialchars($text[$lang]['optional'], ENT_QUOTES, 'UTF-8') ?></label>
                    <input type="datetime-local" class="form-control date-2" id="date2" name="date2">
                </div>

                <div class="mb-4">
                    <label for="date3" class="form-label"><?= htmlspecialchars($text[$lang]['candidate'], ENT_QUOTES, 'UTF-8') ?>3<?= htmlspecialchars($text[$lang]['optional'], ENT_QUOTES, 'UTF-8') ?></label>
                    <input type="datetime-local" class="form-control date-3" id="date3" name="date3">
                </div>

                <input type="hidden" name="id" value="<?php echo htmlspecialchars($rand_str, ENT_QUOTES, 'UTF-8'); ?>" id="eventid">
                <!-- JSに言語設定を渡すための隠しフィールド -->
                <input type="hidden" id="current_lang" value="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">

                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-action btn-prev prev-step"><i class="fa fa-chevron-left me-2"></i> <?= htmlspecialchars($text[$lang]['prev'], ENT_QUOTES, 'UTF-8') ?></button>
                    <button type="button" class="btn btn-action btn-next btn-success bg-success text-white border-none" id="stp2btn"><i class="fa fa-paper-plane me-2"></i> <?= htmlspecialchars($text[$lang]['create_table'], ENT_QUOTES, 'UTF-8') ?></button>
                </div>
            </form>
        </div>

        <!-- ステップ4: 完成 -->
        <div class="tab-pane fade" id="complete" role="tabpanel">
            <div class="step-indicator text-success">Success</div>
            <h3 class="form-title"><?= htmlspecialchars($text[$lang]['success_title'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p class="text-secondary mb-4">
                <?= $text[$lang]['success_desc'] ?>
            </p>

            <div class="url-box text-center mb-4">
                <div id="result1"><?= htmlspecialchars($text[$lang]['generating'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>

            <div class="text-center mt-3">
                <div id="result2"></div>
            </div>
        </div>
    </div>
  </div>

  <footer class="text-center">
      <p><?= htmlspecialchars($text[$lang]['developed_by'], ENT_QUOTES, 'UTF-8') ?> <a href="https://github.com/s0323861" target="_blank">Akira Mukai</a> 2021-2026</p>
  </footer>
</main>

<!-- JS Script -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="./js/default.js"></script>
<script src="./js/validator.js"></script>

</body>
</html>
