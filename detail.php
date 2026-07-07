<?php
header('Content-Type: text/html; charset=UTF-8');

// 言語設定の取得（デフォルトは日本語 'ja'）
$lang = $_REQUEST['lang'] ?? 'ja';

// 安全対策：許可する言語コードのみに制限（ディレクトリトラバーサル防止）
if (!in_array($lang, ['ja', 'en'], true)) {
    $lang = 'ja';
}

// 言語ファイルの読み込み（選択された言語の配列をダイレクトに格納）
$text = require __DIR__ . "/lang/{$lang}.php";

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$sid = isset($_REQUEST['sid']) ? $_REQUEST['sid'] : '';
$edit = isset($_REQUEST['edit']) ? $_REQUEST['edit'] : '';

$msg = '';
if ($id === '') {
    $msg = $text['id_error'];
}

$file = "./data/" . $id . ".txt";
if ($msg === '' && !file_exists($file)) {
    $msg = $text['file_error'];
}

// 4桁のランダム文字列生成関数
function randstr($length = 4) {
    $chars = array_merge(range('a', 'z'), range(0, 9));
    $rand_str_tmp = '';
    $max_idx = count($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $rand_str_tmp .= $chars[mt_rand(0, $max_idx)];
    }
    return $rand_str_tmp;
}

// 編集・削除処理
if ($msg === '') {
    if ($edit === 'go') {
        $disp = isset($_POST['display']) ? $_POST['display'] : '';
        $ans1 = isset($_POST['date1']) ? $_POST['date1'] : 'notyet';
        $ans2 = isset($_POST['date2']) ? $_POST['date2'] : 'notyet';
        $ans3 = isset($_POST['date3']) ? $_POST['date3'] : 'notyet';
        $com = isset($_POST['comment']) ? $_POST['comment'] : '';

        // エスケープおよびタブ除去処理
        $disp = str_replace(["$", "?", ".", "\\t", "\\n", "\\r"], ["＄", "？", "．", " ", "", ""], $disp);
        $disp = strip_tags($disp);
        $com = str_replace(["$", "?", ".", "\\t", "\\n", "\\r"], ["＄", "？", "．", " ", "", ""], $com);
        $com = strip_tags($com);
        
        $rand_str = randstr(4);

        // ファイル追記
        $line = $disp . "\t" . $ans1 . "\t" . $ans2 . "\t" . $ans3 . "\t" . $rand_str . "\t" . $com . "\n";
        file_put_contents($file, $line, FILE_APPEND | LOCK_EX);

        // 再読み込みのためのリダイレクト（二重送信防止）
        header("Location: detail.php?id=" . urlencode($id) . "&lang=" . urlencode($lang));
        exit;

    } elseif ($edit === 'delete' && $sid !== '') {
        if (file_exists($file)) {
            // FILE_SKIP_EMPTY_LINES を削除
            $lines = file($file, FILE_IGNORE_NEW_LINES);
            $new_lines = [];
            
            for ($i = 0; $i < count($lines); $i++) {
                if ($i <= 2) {
                    $new_lines[] = $lines[$i];
                } else {
                    $parts = explode("\t", $lines[$i]);
                    if (isset($parts[4]) && $parts[4] !== $sid) {
                        $new_lines[] = $lines[$i];
                    }
                }
            }
            file_put_contents($file, implode("\n", $new_lines) . "\n", LOCK_EX);
        }
        header("Location: detail.php?id=" . urlencode($id) . "&lang=" . urlencode($lang));
        exit;
    }
}

// データの読み込み
$name = '';
$memo = '';
$date1 = '';
$date2 = '';
$date3 = '';
$people = [];

if ($msg === '' && file_exists($file)) {
    // FILE_SKIP_EMPTY_LINES を削除し、空行もスキップせずに読み込む
    $lines = file($file, FILE_IGNORE_NEW_LINES);
    if (isset($lines[0])) $name = $lines[0];
    if (isset($lines[1])) $memo = $lines[1];
    if (isset($lines[2])) {
        $dates = explode("\t", $lines[2]);
        $date1 = isset($dates[0]) ? $dates[0] : '';
        $date2 = isset($dates[1]) ? $dates[1] : '';
        $date3 = isset($dates[2]) ? $dates[2] : '';
    }
    
    for ($i = 3; $i < count($lines); $i++) {
        $people[] = $lines[$i];
    }
}

// 現在のURLの取得
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?id=" . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "&lang=" . htmlspecialchars($lang, ENT_QUOTES, 'UTF-8');

// 出欠ステータステキストの多言語対応
$maru_label = $lang === 'ja' ? '◯ 行ける' : '◯ Yes';
$batsu_label = $lang === 'ja' ? '✕ 無理' : '✕ No';
$sankaku_label = $lang === 'ja' ? '△ 微妙' : '△ Maybe';

$maru = '<span class="badge bg-success text-white rounded-pill px-3 py-1"><i class="fa fa-check"></i> ' . $maru_label . '</span>';
$batsu = '<span class="badge bg-danger text-white rounded-pill px-3 py-1"><i class="fa fa-times"></i> ' . $batsu_label . '</span>';
$sankaku = '<span class="badge bg-warning text-dark rounded-pill px-3 py-1"><i class="fa fa-exclamation-triangle"></i> ' . $sankaku_label . '</span>';

function getStatusIcon($status, $m, $b, $s) {
    if ($status === 'yes') return $m;
    if ($status === 'nono') return $b;
    return $s;
}

// datetime-local の表示形式をすっきり整える関数
function formatEventDate($dateStr) {
    if (empty($dateStr)) return '';
    if (strpos($dateStr, 'T') !== false) {
        return str_replace('T', ' ', $dateStr);
    }
    return $dateStr;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($name ? $name . " - " . $text['title'] : $text['error'] . " - " . $text['title'], ENT_QUOTES, 'UTF-8'); ?></title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  
  <style type="text/css">
    body {
      background-color: #f3f4f7;
      font-family: 'Helvetica Neue', Arial, sans-serif;
      color: #333;
      padding-top: 80px;
    }
    .main-container {
      max-width: 900px;
      margin: 30px auto 60px auto;
    }
    .event-card {
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      border: none;
      padding: 35px;
      margin-bottom: 30px;
    }
    .event-title {
      font-size: 2rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 15px;
    }
    .event-memo {
      font-size: 1.05rem;
      color: #4b5563;
      line-height: 1.6;
      background: #f9fafb;
      padding: 20px;
      border-radius: 12px;
      border-left: 4px solid #4f46e5;
    }
    .section-title {
      font-size: 1.35rem;
      font-weight: 700;
      color: #111827;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
    }
    .section-title i {
      color: #4f46e5;
      margin-right: 10px;
    }
    .table-responsive {
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid #e5e7eb;
    }
    .table {
      margin-bottom: 0;
    }
    .table th {
      background-color: #f8fafc;
      color: #475569;
      font-weight: 600;
      border-bottom: 2px solid #e2e8f0;
      padding: 14px;
    }
    .table td {
      padding: 14px;
      vertical-align: middle;
    }
    .form-control {
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      padding: 12px 16px;
      transition: all 0.2s ease;
    }
    .form-control:focus {
      border-color: #4f46e5;
      box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }
    .status-radio-group {
      display: inline-flex;
      background: #f3f4f6;
      padding: 4px;
      border-radius: 10px;
      width: 100%;
      max-width: 320px;
    }
    .status-radio-btn {
      flex: 1;
      text-align: center;
    }
    .status-radio-btn input[type="radio"] {
      display: none;
    }
    .status-radio-btn label {
      display: block;
      padding: 8px 12px;
      cursor: pointer;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.9rem;
      color: #6b7280;
      transition: all 0.15s ease;
    }
    .radio-yes input[type="radio"]:checked + label {
      background: #dcfce7;
      color: #166534;
    }
    .radio-notyet input[type="radio"]:checked + label {
      background: #fef9c3;
      color: #854d0e;
    }
    .radio-nono input[type="radio"]:checked + label {
      background: #fee2e2;
      color: #991b1b;
    }
    .btn-custom {
      padding: 10px 24px;
      font-weight: 600;
      border-radius: 10px;
      transition: all 0.2s ease;
    }
    .btn-indigo {
      background-color: #4f46e5;
      color: white;
      border: none;
    }
    .btn-indigo:hover {
      background-color: #4338ca;
      color: white;
    }
    .url-box {
      background-color: #f0fdf4;
      border: 2px dashed #bbf7d0;
      border-radius: 12px;
      padding: 15px 20px;
      color: #166534;
      font-weight: 600;
    }
    footer {
      margin-top: 60px;
      color: #9ca3af;
      font-size: 0.9rem;
    }
    footer a {
      color: #6b7280;
      text-decoration: none;
    }
  </style>
</head>
<body>

<header>
  <nav class="navbar navbar-expand-lg navbar-white bg-white border-bottom fixed-top shadow-sm py-3">
    <div class="container">
      <a href="./?lang=<?= urlencode($lang) ?>" class="navbar-brand fw-bold text-dark"><i class="fa fa-calendar-o text-primary"></i> <?= htmlspecialchars($text['title'], ENT_QUOTES, 'UTF-8') ?></a>
    </div>
  </nav>
</header>

<main class="container main-container">

  <?php if ($msg !== ''): ?>
    <div class="alert alert-danger rounded-4 p-4 shadow-sm" role="alert">
        <h4 class="alert-heading fw-bold"><i class="fa fa-exclamation-triangle"></i> <?= htmlspecialchars($text['error'], ENT_QUOTES, 'UTF-8') ?></h4>
        <p class="mb-0"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></p>
        <hr>
        <a href="./?lang=<?= urlencode($lang) ?>" class="btn btn-outline-danger btn-custom mt-2"><?= htmlspecialchars($text['back_to_top'], ENT_QUOTES, 'UTF-8') ?></a>
    </div>
  <?php else: ?>

    <div class="event-card">
        <h1 class="event-title"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></h1>
        <?php if (!empty($memo)): ?>
            <p class="event-memo"><?php echo nl2br(htmlspecialchars($memo, ENT_QUOTES, 'UTF-8')); ?></p>
        <?php endif; ?>
        
        <div class="mt-4">
            <a href="./edit.php?id=<?php echo urlencode($id); ?>&lang=<?= urlencode($lang) ?>" class="btn btn-outline-secondary btn-custom btn-sm">
                <i class="fa fa-pencil-square-o"></i> <?= htmlspecialchars($text['re_edit'], ENT_QUOTES, 'UTF-8') ?>
            </a>
        </div>
    </div>

    <div class="event-card">
        <h2 class="section-title"><i class="fa fa-users"></i> <?= htmlspecialchars($text['attendance_status'], ENT_QUOTES, 'UTF-8') ?></h2>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th><?= htmlspecialchars($text['name_header'], ENT_QUOTES, 'UTF-8') ?></th>
                        <?php if (!empty($date1)): ?>
                            <th><?php echo htmlspecialchars(formatEventDate($date1), ENT_QUOTES, 'UTF-8'); ?></th>
                        <?php endif; ?>
                        <?php if (!empty($date2)): ?>
                            <th><?php echo htmlspecialchars(formatEventDate($date2), ENT_QUOTES, 'UTF-8'); ?></th>
                        <?php endif; ?>
                        <?php if (!empty($date3)): ?>
                            <th><?php echo htmlspecialchars(formatEventDate($date3), ENT_QUOTES, 'UTF-8'); ?></th>
                        <?php endif; ?>
                        <th><?= htmlspecialchars($text['comment_header'], ENT_QUOTES, 'UTF-8') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($people)): ?>
                        <tr>
                            <?php
                            // 存在する日程の数に応じて動的にcolspanを計算
                            $col_count = 2 + (!empty($date1)?1:0) + (!empty($date2)?1:0) + (!empty($date3)?1:0);
                            ?>
                            <td colspan="<?php echo $col_count; ?>" class="text-center text-muted py-4">
                                <?= htmlspecialchars($text['no_answers'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($people as $p): 
                            $parts = explode("\t", $p);
                            $p_name = isset($parts[0]) ? $parts[0] : '';
                            $a1 = isset($parts[1]) ? $parts[1] : 'notyet';
                            $a2 = isset($parts[2]) ? $parts[2] : 'notyet';
                            $a3 = isset($parts[3]) ? $parts[3] : 'notyet';
                            $p_sid = isset($parts[4]) ? $parts[4] : '';
                            $p_com = isset($parts[5]) ? $parts[5] : '';
                        ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark"><?php echo htmlspecialchars($p_name, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <div class="small mt-1">
                                        <a href="detail.php?id=<?php echo urlencode($id); ?>&edit=delete&sid=<?php echo urlencode($p_sid); ?>&lang=<?= urlencode($lang) ?>" 
                                           class="text-danger text-decoration-none me-2" onclick="return confirm('<?= htmlspecialchars($text['confirm_answer_delete'], ENT_QUOTES, 'UTF-8') ?>');">
                                            <i class="fa fa-trash-o"></i> <?= htmlspecialchars($text['action_delete'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                        <a href="./change.php?id=<?php echo urlencode($id); ?>&sid=<?php echo urlencode($p_sid); ?>&lang=<?= urlencode($lang) ?>" class="text-muted text-decoration-none">
                                            <i class="fa fa-pencil"></i> <?= htmlspecialchars($text['action_change'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </div>
                                </td>
                                <?php if (!empty($date1)): ?>
                                    <td><?php echo getStatusIcon($a1, $maru, $batsu, $sankaku); ?></td>
                                <?php endif; ?>
                                <?php if (!empty($date2)): ?>
                                    <td><?php echo getStatusIcon($a2, $maru, $batsu, $sankaku); ?></td>
                                <?php endif; ?>
                                <?php if (!empty($date3)): ?>
                                    <td><?php echo getStatusIcon($a3, $maru, $batsu, $sankaku); ?></td>
                                <?php endif; ?>
                                <td class="text-secondary small"><?php echo htmlspecialchars($p_com, ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="event-card">
        <h2 class="section-title"><i class="fa fa-link"></i> <?= htmlspecialchars($text['share_url_title'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="text-muted small"><?= htmlspecialchars($text['share_url_desc'], ENT_QUOTES, 'UTF-8') ?></p>
        <div class="input-group">
            <input type="text" class="form-control url-box font-monospace" id="share-url" value="<?php echo $url; ?>" readonly>
            <button class="btn btn-outline-primary btn-custom" type="button" onclick="copyUrl()"><i class="fa fa-clipboard"></i> <?= htmlspecialchars($text['copy'], ENT_QUOTES, 'UTF-8') ?></button>
        </div>
    </div>

    <div class="event-card" id="enter-form">
        <h2 class="section-title"><i class="fa fa-edit"></i> <?= htmlspecialchars($text['form_section_title'], ENT_QUOTES, 'UTF-8') ?></h2>
        
        <form method="post" action="detail.php?id=<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>&lang=<?= urlencode($lang) ?>">
            <input type="hidden" name="edit" value="go">
            
            <div class="mb-4">
                <label for="inputDisplayName" class="form-label fw-bold"><?= htmlspecialchars($text['participant_name'], ENT_QUOTES, 'UTF-8') ?> <span class="text-danger">*</span></label>
                <input type="text" maxlength="15" class="form-control" id="inputDisplayName" name="display" placeholder="<?= htmlspecialchars($text['name_example'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold mb-3"><?= htmlspecialchars($text['answers_label'], ENT_QUOTES, 'UTF-8') ?></label>
                
                <div class="card p-3 mb-3 border border-light-subtle bg-light-subtle rounded-3">
                    <div class="row align-items-center">
                        <div class="col-md-5 mb-2 mb-md-0 fw-bold text-secondary">
                            <?php echo htmlspecialchars(formatEventDate($date1), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="col-md-7">
                            <div class="status-radio-group">
                                <div class="status-radio-btn radio-yes">
                                    <input type="radio" name="date1" id="date1_yes" value="yes">
                                    <label for="date1_yes"><?= htmlspecialchars($text['status_yes'], ENT_QUOTES, 'UTF-8') ?></label>
                                </div>
                                <div class="status-radio-btn radio-notyet">
                                    <input type="radio" name="date1" id="date1_maybe" value="notyet" checked>
                                    <label for="date1_maybe"><?= htmlspecialchars($text['status_maybe'], ENT_QUOTES, 'UTF-8') ?></label>
                                </div>
                                <div class="status-radio-btn radio-nono">
                                    <input type="radio" name="date1" id="date1_no" value="nono">
                                    <label for="date1_no"><?= htmlspecialchars($text['status_no'], ENT_QUOTES, 'UTF-8') ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($date2)): ?>
                <div class="card p-3 mb-3 border border-light-subtle bg-light-subtle rounded-3">
                    <div class="row align-items-center">
                        <div class="col-md-5 mb-2 mb-md-0 fw-bold text-secondary">
                            <?php echo htmlspecialchars(formatEventDate($date2), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="col-md-7">
                            <div class="status-radio-group">
                                <div class="status-radio-btn radio-yes">
                                    <input type="radio" name="date2" id="date2_yes" value="yes">
                                    <label for="date2_yes"><?= htmlspecialchars($text['status_yes'], ENT_QUOTES, 'UTF-8') ?></label>
                                </div>
                                <div class="status-radio-btn radio-notyet">
                                    <input type="radio" name="date2" id="date2_maybe" value="notyet" checked>
                                    <label for="date2_maybe"><?= htmlspecialchars($text['status_maybe'], ENT_QUOTES, 'UTF-8') ?></label>
                                </div>
                                <div class="status-radio-btn radio-nono">
                                    <input type="radio" name="date2" id="date2_no" value="nono">
                                    <label for="date2_no"><?= htmlspecialchars($text['status_no'], ENT_QUOTES, 'UTF-8') ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($date3)): ?>
                <div class="card p-3 mb-3 border border-light-subtle bg-light-subtle rounded-3">
                    <div class="row align-items-center">
                        <div class="col-md-5 mb-2 mb-md-0 fw-bold text-secondary">
                            <?php echo htmlspecialchars(formatEventDate($date3), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="col-md-7">
                            <div class="status-radio-group">
                                <div class="status-radio-btn radio-yes">
                                    <input type="radio" name="date3" id="date3_yes" value="yes">
                                    <label for="date3_yes"><?= htmlspecialchars($text['status_yes'], ENT_QUOTES, 'UTF-8') ?></label>
                                </div>
                                <div class="status-radio-btn radio-notyet">
                                    <input type="radio" name="date3" id="date3_maybe" value="notyet" checked>
                                    <label for="date3_maybe"><?= htmlspecialchars($text['status_maybe'], ENT_QUOTES, 'UTF-8') ?></label>
                                </div>
                                <div class="status-radio-btn radio-nono">
                                    <input type="radio" name="date3" id="date3_no" value="nono">
                                    <label for="date3_no"><?= htmlspecialchars($text['status_no'], ENT_QUOTES, 'UTF-8') ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="inputComment" class="form-label fw-bold"><?= htmlspecialchars($text['comment_optional'], ENT_QUOTES, 'UTF-8') ?></label>
                <input type="text" class="form-control" maxlength="20" name="comment" id="inputComment" placeholder="<?= htmlspecialchars($text['comment_example'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-indigo btn-custom w-100 py-2"><i class="fa fa-user-plus me-1"></i> <?= htmlspecialchars($text['register_attendance'], ENT_QUOTES, 'UTF-8') ?></button>
            </div>
        </form>
    </div>

  <?php endif; ?>

  <footer class="text-center">
      <p><?= htmlspecialchars($text['developed_by'], ENT_QUOTES, 'UTF-8') ?> <a href="https://github.com/s0323861" target="_blank">Akira Mukai</a> 2021-2026</p>
  </footer>
</main>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="copyToast" class="toast align-items-center text-bg-success border-0 rounded-3 shadow" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        <i class="fa fa-check-circle me-2"></i> <?= htmlspecialchars($text['copy_success'], ENT_QUOTES, 'UTF-8') ?>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function copyUrl() {
    var copyText = document.getElementById("share-url");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value).then(function() {
        // Bootstrap 5 の Toast インスタンスを取得して表示
        var toastEl = document.getElementById('copyToast');
        var toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
    }).catch(function(err) {
        // 万が一クリップボードAPIが失敗した場合のフォールバック
        alert("<?= htmlspecialchars($text['copy_success'], ENT_QUOTES, 'UTF-8') ?>");
    });
}
</script>
</body>
</html>