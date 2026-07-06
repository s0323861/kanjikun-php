<?php
header('Content-Type: text/html; charset=UTF-8');

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : '';

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
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="keywords" content="出欠管理,ツール,使い捨て,webサービス">
  <meta name="description" content="結婚式の２次会、同窓会、歓送迎会、忘年会、新年会、飲み会、オフ会などの参加者の出欠をとるwebサービスです。">
  <title>幹事くん - イベントの出欠管理・スケジュール調整ツール</title>
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
      <a href="./" class="navbar-brand fw-bold text-dark"><i class="fa fa-calendar-o text-primary"></i> 幹事くん</a>
    </div>
  </nav>
</header>

<main class="container main-container" style="padding-top: 60px;">
  <div class="form-card">
    
    <!-- Designmodo風のトッププログレスバー -->
    <div class="progress-track">
      <div class="d-flex justify-content-between mb-2">
        <span class="fw-bold text-muted small" id="progress-text">ステップ 1 / 3</span>
        <span class="fw-bold text-muted small" id="progress-percent">33%</span>
      </div>
      <div class="progress">
        <div class="progress-bar" id="main-progress" role="progressbar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
    </div>

    <!-- コンテンツ表示用のダミータブコンテンツ（JSでインデックス切り替え） -->
    <div class="tab-content" id="wizardTabContent">
        
        <!-- ステップ1: ウェルカム -->
        <div class="tab-pane fade show active" id="step1" role="tabpanel">
            <div class="step-indicator">Welcome</div>
            <h3 class="form-title">「幹事くん」にようこそ <span class="text-danger"><i class="fa fa-heart"></i></span></h3>
            <p class="text-secondary leading-relaxed mb-5">
                「幹事くん」はイベント・歓送迎会・忘年会・新年会・同窓会などの日程調整＆出欠確認を行うツールです。<br>
                無料・登録不要・使い捨て型のWebサービスです！まずめるボタンを押してイベントを作成しましょう。
            </p>
  
            <div class="d-flex justify-content-end mt-4">
                <button type="button" class="btn btn-action btn-next next-step">始める <i class="fa fa-chevron-right ms-2"></i></button>
            </div>
        </div>

        <!-- ステップ2: イベント情報入力 -->
        <div class="tab-pane fade" id="step2" role="tabpanel">
            <div class="step-indicator">Step 01</div>
            <h3 class="form-title">イベントの基本情報</h3>

            <form role="form">
                <div class="mb-4">
                    <label for="inputName" class="form-label">イベント名 <span class="text-danger">*</span></label>
                    <input type="text" maxlength="50" class="form-control" id="inputName" name="name" placeholder="例: ○○部 忘年会2026">
                </div>

                <div class="mb-4">
                    <label for="textArea" class="form-label">メモ・詳細（任意）</label>
                    <textarea class="form-control" rows="4" id="textArea" maxlength="200" name="memo" placeholder="場所の候補や会費、伝達事項などがあれば入力してください"></textarea>
                </div>

                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-action btn-prev prev-step"><i class="fa fa-chevron-left me-2"></i> 前へ</button>
                    <button type="button" class="btn btn-action btn-next next-step" id="stp1btn">次へ <i class="fa fa-chevron-right ms-2"></i></button>
                </div>
            </form>
        </div>

        <!-- ステップ3: 日程候補 -->
        <!-- 第三画面 (Step 2) -->
        <div class="tab-pane fade" id="step3" role="tabpanel">
            <div class="step-indicator">Step 02</div>
            <h3 class="form-title">候補日程の選択</h3>

            <form role="form">
                <div class="mb-4">
                    <label for="date1" class="form-label">候補日程1 <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control date-1" id="date1" name="date1" required>
                </div>

                <div class="mb-4">
                    <label for="date2" class="form-label">候補日程2（任意）</label>
                    <input type="datetime-local" class="form-control date-2" id="date2" name="date2">
                </div>

                <div class="mb-4">
                    <label for="date3" class="form-label">候補日程3（任意）</label>
                    <input type="datetime-local" class="form-control date-3" id="date3" name="date3">
                </div>

                <input type="hidden" name="id" value="<?php echo htmlspecialchars($rand_str, ENT_QUOTES, 'UTF-8'); ?>" id="eventid">

                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-action btn-prev prev-step"><i class="fa fa-chevron-left me-2"></i> 前へ</button>
                    <button type="button" class="btn btn-action btn-next btn-success bg-success text-white border-none" id="stp2btn"><i class="fa fa-paper-plane me-2"></i> 出欠表をつくる</button>
                </div>
            </form>
        </div>

        <!-- ステップ4: 完成 -->
        <div class="tab-pane fade" id="complete" role="tabpanel">
            <div class="step-indicator text-success">Success</div>
            <h3 class="form-title">出欠調整ページが完成しました！ 🎉</h3>
            <p class="text-secondary mb-4">
                下記の生成されたURLをコピーして、参加メンバーに共有してください。<br>
                以後、このURLページからメンバーがそれぞれの出欠回答を入力できるようになります。
            </p>

            <div class="url-box text-center mb-4">
                <div id="result1">URL生成中...</div>
            </div>

            <div class="text-center mt-3">
                <div id="result2"></div>
            </div>
        </div>
    </div>
  </div>

  <footer class="text-center">
      <p>Developed by <a href="https://github.com/s0323861" target="_blank">Akira Mukai</a> 2021-2026</p>
  </footer>
</main>

<!-- JS Script -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="./js/default.js"></script>
<script src="./js/validator.js"></script>

</body>
</html>
