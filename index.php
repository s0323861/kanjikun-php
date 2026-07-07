<?php
header('Content-Type: text/html; charset=UTF-8');

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : '';

// 言語設定の取得（デフォルトは日本語 'ja'）
$lang = $_GET['lang'] ?? 'ja';

// 安全対策：許可する言語コードのみに制限（ディレクトリトラバーサルなどの脆弱性防止）
if (!in_array($lang, ['ja', 'en'], true)) {
    $lang = 'ja';
}

// 言語ファイルの読み込み（該当言語の配列をダイレクトに $text に格納）
$text = require __DIR__ . "/lang/{$lang}.php";

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
  <title><?= htmlspecialchars($text['title'], ENT_QUOTES, 'UTF-8') ?> - イベントの出欠管理・スケジュール調整ツール</title>
  <link rel="shortcut icon" href="favicon.ico">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
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
      <a href="./?lang=<?= urlencode($lang) ?>" class="navbar-brand fw-bold text-dark"><i class="fa fa-calendar-o text-primary"></i> <?= htmlspecialchars($text['title'], ENT_QUOTES, 'UTF-8') ?></a>
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
        <span class="fw-bold text-muted small" id="progress-text"><?= htmlspecialchars($text['step'], ENT_QUOTES, 'UTF-8') ?> 1 / 3</span>
        <span class="fw-bold text-muted small" id="progress-percent">33%</span>
      </div>
      <div class="progress">
        <div class="progress-bar" id="main-progress" role="progressbar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
    </div>

    <div class="tab-content" id="wizardTabContent">
        
        <div class="tab-pane fade show active" id="step1" role="tabpanel">
            <div class="step-indicator">Welcome</div>
            <h3 class="form-title"><?= htmlspecialchars($text['welcome'], ENT_QUOTES, 'UTF-8') ?> <span class="text-danger"><i class="fa fa-heart"></i></span></h3>
            <p class="text-secondary leading-relaxed mb-5">
                <?= $text['description'] // HTMLタグを含むためそのまま出力 ?>
            </p>
  
            <div class="d-flex justify-content-end mt-4">
                <button type="button" class="btn btn-action btn-next next-step"><?= htmlspecialchars($text['start'], ENT_QUOTES, 'UTF-8') ?> <i class="fa fa-chevron-right ms-2"></i></button>
            </div>
        </div>

        <div class="tab-pane fade" id="step2" role="tabpanel">
            <div class="step-indicator">Step 01</div>
            <h3 class="form-title"><?= htmlspecialchars($text['basic_info'], ENT_QUOTES, 'UTF-8') ?></h3>

            <form role="form">
                <div class="mb-4">
                    <label for="inputName" class="form-label"><?= htmlspecialchars($text['event_title_label'], ENT_QUOTES, 'UTF-8') ?> <span class="text-danger">*</span></label>
                    <input type="text" maxlength="50" class="form-control" id="inputName" name="name" placeholder="<?= htmlspecialchars($text['event_placeholder'], ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="mb-4">
                    <label for="textArea" class="form-label"><?= htmlspecialchars($text['memo_label'], ENT_QUOTES, 'UTF-8') ?></label>
                    <textarea class="form-control" rows="4" id="textArea" maxlength="200" name="memo" placeholder="<?= htmlspecialchars($text['memo_placeholder'], ENT_QUOTES, 'UTF-8') ?>"></textarea>
                </div>

                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-action btn-prev prev-step"><i class="fa fa-chevron-left me-2"></i> <?= htmlspecialchars($text['prev'], ENT_QUOTES, 'UTF-8') ?></button>
                    <button type="button" class="btn btn-action btn-next next-step" id="stp1btn"><?= htmlspecialchars($text['next'], ENT_QUOTES, 'UTF-8') ?> <i class="fa fa-chevron-right ms-2"></i></button>
                </div>
            </form>
        </div>

        <div class="tab-pane fade" id="step3" role="tabpanel">
            <div class="step-indicator">Step 02</div>
            <h3 class="form-title"><?= htmlspecialchars($text['candidate_dates'], ENT_QUOTES, 'UTF-8') ?></h3>

            <form role="form">
                <div class="mb-4">
                    <label for="date1" class="form-label"><?= htmlspecialchars($text['candidate'], ENT_QUOTES, 'UTF-8') ?>1 <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control date-1" id="date1" name="date1" required>
                </div>

                <div class="mb-4">
                    <label for="date2" class="form-label"><?= htmlspecialchars($text['candidate'], ENT_QUOTES, 'UTF-8') ?>2<?= htmlspecialchars($text['optional'], ENT_QUOTES, 'UTF-8') ?></label>
                    <input type="datetime-local" class="form-control date-2" id="date2" name="date2">
                </div>

                <div class="mb-4">
                    <label for="date3" class="form-label"><?= htmlspecialchars($text['candidate'], ENT_QUOTES, 'UTF-8') ?>3<?= htmlspecialchars($text['optional'], ENT_QUOTES, 'UTF-8') ?></label>
                    <input type="datetime-local" class="form-control date-3" id="date3" name="date3">
                </div>

                <input type="hidden" name="id" value="<?php echo htmlspecialchars($rand_str, ENT_QUOTES, 'UTF-8'); ?>" id="eventid">
                <input type="hidden" id="current_lang" value="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">

                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-action btn-prev prev-step"><i class="fa fa-chevron-left me-2"></i> <?= htmlspecialchars($text['prev'], ENT_QUOTES, 'UTF-8') ?></button>
                    <button type="button" class="btn btn-action btn-success bg-success text-white" id="stp2btn"><i class="fa fa-paper-plane me-2"></i> <?= htmlspecialchars($text['create_table'], ENT_QUOTES, 'UTF-8') ?></button>
                </div>
            </form>
        </div>

        <div class="tab-pane fade" id="complete" role="tabpanel">
            <div class="step-indicator text-success">Success</div>
            <h3 class="form-title"><?= htmlspecialchars($text['success_title'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p class="text-secondary mb-4">
                <?= $text['success_desc'] ?>
            </p>

            <div class="url-box text-center mb-4">
                <div id="result1"><?= htmlspecialchars($text['generating'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>

            <!-- 【追加】QRコードの表示先コンテナ -->
            <div id="qrcode-container" class="text-center mb-4 d-none">
                <p class="text-muted small mb-2"><i class="fa fa-mobile me-1"></i> スマホでの共有・アクセス用 QRコード</p>
                <img id="qrcode-img" src="" alt="Event QR Code" class="img-thumbnail shadow-sm" style="width: 150px; height: 150px;">
            </div>

            <div class="text-center mt-3">
                <div id="result2"></div>
            </div>
        </div>
    </div>
  </div>

  <footer class="text-center">
      <p><?= htmlspecialchars($text['developed_by'], ENT_QUOTES, 'UTF-8') ?> <a href="https://github.com/s0323861" target="_blank">Akira Mukai</a> 2021-2026</p>
  </footer>
</main>

<!-- 【追加】Bootstrap標準のToast通知表示用コンテナ（右下に小さくポップアップします） -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="copyToast" class="toast align-items-center text-white bg-dark border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
    <div class="d-flex">
      <div class="toast-body">
        <i class="fa fa-check-circle text-success me-2"></i> URLをクリップボードにコピーしました！
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="./js/default.js"></script>
<script src="./js/validator.js"></script>

</body>
</html>