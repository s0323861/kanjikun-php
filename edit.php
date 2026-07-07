<?php
// 1. 入力値の取得と初期化
$id   = $_GET['id']   ?? $_POST['id']   ?? '';
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

// 2. IDのバリデーション
if (empty($id)) {
    $msg = $text['id_error'];
} elseif (!preg_match('/^[a-zA-Z0-9]+$/', $id)) {
    $msg = $text['bad_id'];
}

$file = "./data/" . $id . ".txt";

if (empty($msg) && !file_exists($file)) {
    $msg = $text['file_error'];
}

// 3. 更新処理（POST時かつエラーがない場合）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $edit === 'update' && empty($msg)) {
    $name  = $_POST['name']  ?? '';
    $memo  = $_POST['memo']  ?? '';
    $date1 = $_POST['date1'] ?? '';
    $date2 = $_POST['date2'] ?? '';
    $date3 = $_POST['date3'] ?? '';

    // バリデーション：イベント名と候補日程1は必須
    if (empty($name) || empty($date1)) {
        $msg = $text['bad_id']; // または適切なエラー文言
    } else {
        // サニタイズ（タブや改行の除去・置換）
        $name  = str_replace(["\t", "\n", "\r"], [" ", "", ""], strip_tags($name));
        $memo  = str_replace(["\t"], [" "], strip_tags($memo)); // メモ内の改行は維持
        $date1 = str_replace(["\t", "\n", "\r"], [" ", "", ""], strip_tags($date1));
        $date2 = str_replace(["\t", "\n", "\r"], [" ", "", ""], strip_tags($date2));
        $date3 = str_replace(["\t", "\n", "\r"], [" ", "", ""], strip_tags($date3));

        // 既存ファイルの読み込み（出席者データを保持するため）
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        
        // 新しいヘッダー部分（1〜3行目）を作成
        $new_lines = [];
        $new_lines[0] = $name;
        $new_lines[1] = $memo;
        $new_lines[2] = $date1 . "\t" . $date2 . "\t" . $date3;

        // 4行目以降の出席者データがあればそのまま引き継ぐ
        if ($lines && count($lines) > 3) {
            for ($i = 3; $i < count($lines); $i++) {
                $new_lines[] = $lines[$i];
            }
        }

        // ファイルへの書き込み
        if (file_put_contents($file, implode("\n", $new_lines) . "\n", LOCK_EX) === false) {
            $msg = $text['write_error'];
        } else {
            // 更新成功時は詳細画面へリダイレクト
            header("Location: detail.php?id=" . urlencode($id) . "&lang=" . urlencode($lang));
            exit;
        }
    }
}

// 4. 既存データの読み込み（編集画面の初期値用）
$current_name  = '';
$current_memo  = '';
$current_date1 = '';
$current_date2 = '';
$current_date3 = '';

if (empty($msg) && file_exists($file)) {
    // FILE_SKIP_EMPTY_LINES を削除して、空行もスキップせずに読み込む
    $lines = file($file, FILE_IGNORE_NEW_LINES);
    if ($lines) {
        $current_name = $lines[0] ?? '';
        $current_memo = $lines[1] ?? '';
        if (isset($lines[2])) {
            $dates = explode("\t", $lines[2]);
            $current_date1 = $dates[0] ?? '';
            $current_date2 = $dates[1] ?? '';
            $current_date3 = $dates[2] ?? '';
        }
    }
}

// エスケープ用関数
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="<?= h($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($text['event_re_edit']) ?> - <?= h($text['title']) ?></title>
  
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
    <h1 class="h3 mb-4 fw-bold text-secondary border-bottom pb-2">
      <i class="fa-solid fa-pen-to-square me-2"></i><?= h($text['event_re_edit']) ?>
    </h1>
    
    <form method="post" action="edit.php?id=<?= h($id) ?>&edit=update&lang=<?= h($lang) ?>" class="needs-validation" novalidate>
      
      <div class="mb-4">
        <label for="name" class="form-label fw-bold"><?= h($text['event_title_label']) ?> <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="name" name="name" maxlength="50" value="<?= h($current_name) ?>" required>
      </div>
      
      <div class="mb-4">
        <label for="memo" class="form-label fw-bold"><?= h($text['memo_label']) ?></label>
        <textarea class="form-control" id="memo" name="memo" rows="4" maxlength="200"><?= h($current_memo) ?></textarea>
      </div>
      
      <div class="card bg-light p-3 mb-4">
        <h2 class="h5 fw-bold mb-3 text-muted"><i class="fa-regular fa-clock me-2"></i><?= h($text['candidate_dates']) ?></h2>
        
        <div class="mb-3">
          <label for="date1" class="form-label small fw-bold"><?= h($text['candidate']) ?> 1 <span class="text-danger">*</span></label>
          <input type="datetime-local" class="form-control" id="date1" name="date1" value="<?= h($current_date1) ?>" required>
        </div>
        
        <div class="mb-3">
          <label for="date2" class="form-label small fw-bold"><?= h($text['candidate']) ?> 2</label>
          <input type="datetime-local" class="form-control" id="date2" name="date2" value="<?= h($current_date2) ?>">
        </div>
        
        <div class="mb-0">
          <label for="date3" class="form-label small fw-bold"><?= h($text['candidate']) ?> 3</label>
          <input type="datetime-local" class="form-control" id="date3" name="date3" value="<?= h($current_date3) ?>">
        </div>
      </div>
      
      <div class="d-flex justify-content-between align-items-center">
        <a href="detail.php?id=<?= h($id) ?>&lang=<?= h($lang) ?>" class="btn btn-outline-secondary px-4">
          <i class="fa-solid fa-arrow-left me-2"></i><?= h($text['back']) ?>
        </a>
        <button type="submit" class="btn btn-primary px-4">
          <i class="fa-solid fa-check me-2"></i><?= h($text['update']) ?>
        </button>
      </div>
    </form>
  </div>

  <div class="card shadow-custom p-4 bg-white border border-danger-subtle">
    <h2 class="h5 text-danger fw-bold mb-3">
      <i class="fa-solid fa-triangle-exclamation me-2"></i><?= h($text['danger_zone']) ?>
    </h2>
    <p class="text-muted small mb-3"><?= h($text['danger_desc']) ?></p>
    <div>
      <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
        <i class="fa-solid fa-trash-can me-2"></i><?= h($text['delete_event_btn']) ?>
      </button>
    </div>
  </div>

  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle"><?= h($text['confirm_title']) ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss=\"modal\" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-0"><?= h($text['confirm_delete']) ?></p>
        </div>
        <div class="modal-footer">
          <a href="./?cmd=delete&id=<?= h($id) ?>&lang=<?= h($lang) ?>" class="btn btn-danger"><?= h($text['ok']) ?></a>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= h($text['cancel']) ?></button>
        </div>
      </div>
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
<script>
  // Bootstrap 5 のフォームバリデーションスタイル適用
  (() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
  })()
</script>
</body>
</html>