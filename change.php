<?php
// 1. 入力値の取得と初期化（クエリパラメータおよびPOSTデータ）
$id   = $_GET['id']   ?? $_POST['id']   ?? '';
$sid  = $_GET['sid']  ?? $_POST['sid']  ?? '';
$edit = $_GET['edit'] ?? $_POST['edit'] ?? '';

$msg = '';

// --- 多言語設定の追加 ---
$lang = $_GET['lang'] ?? $_POST['lang'] ?? 'ja';

// 安全対策：許可する言語コードのみに制限（ディレクトリトラバーサル防止）
if (!in_array($lang, ['ja', 'en'], true)) {
    $lang = 'ja';
}

// 言語ファイルの読み込み（選択された言語の配列をダイレクトに格納）
$text = require __DIR__ . "/lang/{$lang}.php";

// 2. バリデーション
if (empty($id) || empty($sid)) {
    $msg = $text['id_error'];
}

$file = "./data/" . $id . ".txt";

if (empty($msg) && !file_exists($file)) {
    $msg = $text['file_error'];
}

// 3. 更新処理（POST時かつ $edit === 'update' の場合）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $edit === 'update' && empty($msg)) {
    $disp = $_POST['display'] ?? '';
    $ans1 = $_POST['date1']   ?? 'notyet';
    $ans2 = $_POST['date2']   ?? 'notyet';
    $ans3 = $_POST['date3']   ?? 'notyet';
    $com  = $_POST['comment'] ?? '';

    // タブ文字や改行のサニタイズ処理
    $disp = str_replace(["$", "?", ".", "\t", "\n", "\r"], ["＄", "？", "．", " ", "", ""], $disp);
    $disp = strip_tags($disp);
    $com  = str_replace(["$", "?", ".", "\t", "\n", "\r"], ["＄", "？", "．", " ", "", ""], $com);
    $com  = strip_tags($com);

    if (file_exists($file)) {
        // 【修正】FILE_SKIP_EMPTY_LINES を削除し、空のメモ行を保持させる
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $new_lines = [];

        for ($i = 0; $i < count($lines); $i++) {
            // ヘッダー行（イベント名、メモ、日程）はそのまま保持
            if ($i <= 2) {
                $new_lines[] = $lines[$i];
            } else {
                $parts = explode("\t", $lines[$i]);
                // 一致するセッションID(sid)の行を見つけたら新しいデータに置き換える
                if (isset($parts[4]) && $parts[4] === $sid) {
                    $new_lines[] = $disp . "\t" . $ans1 . "\t" . $ans2 . "\t" . $ans3 . "\t" . $sid . "\t" . $com;
                } else {
                    $new_lines[] = $lines[$i];
                }
            }
        }
        
        // ファイルへ保存し、詳細画面へ遷移
        if (file_put_contents($file, implode("\n", $new_lines) . "\n", LOCK_EX) === false) {
            $msg = $text['write_error'];
        } else {
            header("Location: detail.php?id=" . urlencode($id) . "&lang=" . urlencode($lang));
            exit;
        }
    }
}

// 4. 既存データの読み込み（フォームの初期値用）
$event_name = '';
$date1 = '';
$date2 = '';
$date3 = '';

$current_display = '';
$current_ans1    = 'notyet';
$current_ans2    = 'notyet';
$current_ans3    = 'notyet';
$current_comment = '';

if (empty($msg) && file_exists($file)) {
    // 【修正】FILE_SKIP_EMPTY_LINES を削除し、行のズレを防ぐ
    $lines = file($file, FILE_IGNORE_NEW_LINES);
    
    // イベント情報の取得
    $event_name = $lines[0] ?? '';
    if (isset($lines[2])) {
        $dates = explode("\t", $lines[2]);
        $date1 = $dates[0] ?? '';
        $date2 = $dates[1] ?? '';
        $date3 = $dates[2] ?? '';
    }

    // 対象ユーザーの回答データの取得
    for ($i = 3; $i < count($lines); $i++) {
        $parts = explode("\t", $lines[$i]);
        if (isset($parts[4]) && $parts[4] === $sid) {
            $current_display = $parts[0] ?? '';
            $current_ans1    = $parts[1] ?? 'notyet';
            $current_ans2    = $parts[2] ?? 'notyet';
            $current_ans3    = $parts[3] ?? 'notyet';
            $current_comment = $parts[5] ?? '';
            break;
        }
    }
}

// エスケープ用関数
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// 日時表示の整形関数
function formatEventDate($dateStr) {
    if (empty($dateStr)) return '';
    return str_replace('T', ' ', $dateStr);
}
?>
<!DOCTYPE html>
<html lang="<?= h($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($text['enter_attendance']) ?> - <?= h($text['title']) ?></title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    body {
      background-color: #f8f9fa;
    }
    .main-container {
      max-width: 700px;
      margin: 50px auto;
    }
    .card {
      border: none;
      border-radius: 12px;
    }
    .shadow-custom {
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
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
  </style>
</head>
<body>

<header class="mb-4">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="./?lang=<?= h($lang) ?>"><i class="fa-solid fa-calendar-days me-2"></i><?= h($text['title']) ?></a>
    </div>
  </nav>
</header>

<main class="container main-container">
<?php if (empty($msg)): ?>

  <div class="card shadow-custom p-4 p-md-5 bg-white mb-4">
    <h1 class="h3 mb-2 fw-bold text-dark"><?= h($event_name) ?></h1>
    <h2 class="h5 mb-4 text-secondary border-bottom pb-2">
      <i class="fa-solid fa-user-pen me-2"></i><?= h($text['form_section_title']) ?>
    </h2>
    
    <form method="post" action="change.php?id=<?= h($id) ?>&sid=<?= h($sid) ?>&edit=update&lang=<?= h($lang) ?>">
      
      <div class="mb-4">
        <label for="display" class="form-label fw-bold"><?= h($text['participant_name']) ?> <span class="text-danger">*</span></label>
        <input type="text" class="form-control py-2" id="display" name="display" maxlength="15" value="<?= h($current_display) ?>" required>
      </div>
      
      <div class="mb-4">
        <label class="form-label fw-bold mb-3"><?= h($text['answers_label']) ?></label>
        
        <div class="card p-3 mb-3 border bg-light rounded-3">
          <div class="row align-items-center">
            <div class="col-md-5 mb-2 mb-md-0 fw-bold text-secondary">
              <?= h(formatEventDate($date1)) ?>
            </div>
            <div class="col-md-7">
              <div class="status-radio-group">
                <div class="status-radio-btn radio-yes">
                  <input type="radio" name="date1" id="date1_yes" value="yes" <?= $current_ans1 === 'yes' ? 'checked' : '' ?>>
                  <label for="date1_yes"><?= h($text['status_yes']) ?></label>
                </div>
                <div class="status-radio-btn radio-notyet">
                  <input type="radio" name="date1" id="date1_maybe" value="notyet" <?= $current_ans1 === 'notyet' ? 'checked' : '' ?>>
                  <label for="date1_maybe"><?= h($text['status_maybe']) ?></label>
                </div>
                <div class="status-radio-btn radio-nono">
                  <input type="radio" name="date1" id="date1_no" value="nono" <?= $current_ans1 === 'nono' ? 'checked' : '' ?>>
                  <label for="date1_no"><?= h($text['status_no']) ?></label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php if (!empty($date2)): ?>
        <div class="card p-3 mb-3 border bg-light rounded-3">
          <div class="row align-items-center">
            <div class="col-md-5 mb-2 mb-md-0 fw-bold text-secondary">
              <?= h(formatEventDate($date2)) ?>
            </div>
            <div class="col-md-7">
              <div class="status-radio-group">
                <div class="status-radio-btn radio-yes">
                  <input type="radio" name="date2" id="date2_yes" value="yes" <?= $current_ans2 === 'yes' ? 'checked' : '' ?>>
                  <label for="date2_yes"><?= h($text['status_yes']) ?></label>
                </div>
                <div class="status-radio-btn radio-notyet">
                  <input type="radio" name="date2" id="date2_maybe" value="notyet" <?= $current_ans2 === 'notyet' ? 'checked' : '' ?>>
                  <label for="date2_maybe"><?= h($text['status_maybe']) ?></label>
                </div>
                <div class="status-radio-btn radio-nono">
                  <input type="radio" name="date2" id="date2_no" value="nono" <?= $current_ans2 === 'nono' ? 'checked' : '' ?>>
                  <label for="date2_no"><?= h($text['status_no']) ?></label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($date3)): ?>
        <div class="card p-3 mb-3 border bg-light rounded-3">
          <div class="row align-items-center">
            <div class="col-md-5 mb-2 mb-md-0 fw-bold text-secondary">
              <?= h(formatEventDate($date3)) ?>
            </div>
            <div class="col-md-7">
              <div class="status-radio-group">
                <div class="status-radio-btn radio-yes">
                  <input type="radio" name="date3" id="date3_yes" value="yes" <?= $current_ans3 === 'yes' ? 'checked' : '' ?>>
                  <label for="date3_yes"><?= h($text['status_yes']) ?></label>
                </div>
                <div class="status-radio-btn radio-notyet">
                  <input type="radio" name="date3" id="date3_maybe" value="notyet" <?= $current_ans3 === 'notyet' ? 'checked' : '' ?>>
                  <label for="date3_maybe"><?= h($text['status_maybe']) ?></label>
                </div>
                <div class="status-radio-btn radio-nono">
                  <input type="radio" name="date3" id="date3_no" value="nono" <?= $current_ans3 === 'nono' ? 'checked' : '' ?>>
                  <label for="date3_no"><?= h($text['status_no']) ?></label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <div class="mb-4">
        <label for="comment" class="form-label fw-bold"><?= h($text['comment_optional']) ?></label>
        <input type="text" class="form-control py-2" id="comment" name="comment" maxlength="20" value="<?= h($current_comment) ?>" placeholder="<?= h($text['comment_example']) ?>">
      </div>

      <div class="d-flex justify-content-between align-items-center mt-4">
        <a href="detail.php?id=<?= h($id) ?>&lang=<?= h($lang) ?>" class="btn btn-outline-secondary px-4 py-2 fw-bold">
          <i class="fa-solid fa-arrow-left me-2"></i><?= h($text['back']) ?>
        </a>
        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
          <i class="fa-solid fa-rotate me-2"></i><?= h($text['update']) ?>
        </button>
      </div>
    </form>
    
    <hr class="my-4">
    <div class="text-end">
      <a href="detail.php?id=<?= h($id) ?>&sid=<?= h($sid) ?>&lang=<?= h($lang) ?>&edit=delete" 
         class="btn btn-outline-danger px-4 py-2 fw-bold" 
         onclick="return confirm('<?= h($text['confirm_answer_delete']) ?>');">
        <i class="fa-solid fa-trash-can me-2"></i><?= h($text['delete']) ?>
      </a>
    </div>

  </div>

<?php else: ?>
  <div class="row my-5">
    <div class="col-12 text-center">
      <div class="alert alert-danger shadow-custom" role="alert">
        <h1 class="alert-heading h3 fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= h($text['error']) ?></h1>
        <p class="lead mb-0"><?= h($msg) ?></p>
      </div>
      <a href="./?lang=<?= h($lang) ?>" class="btn btn-outline-secondary mt-3"><i class="fa-solid fa-house me-2"></i><?= h($text['top']) ?></a>
    </div>
  </div>
<?php endif; ?>

  <hr class="mt-5">

  <footer class="py-3 my-4 text-center">
    <p class="text-muted"><?= h($text['developed_by']) ?> <a href="https://github.com/s0323861" target="_blank" class="text-decoration-none">Akira Mukai</a> 2021-2026</p>
  </footer>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>