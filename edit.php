<?php
// 1. 入力値の取得と初期化
$id   = $_GET['id']   ?? $_POST['id']   ?? '';
$edit = $_GET['edit'] ?? $_POST['edit'] ?? '';

$msg = '';

// ディレクトリトラバーサル対策
$id = basename($id);
if ($id === '' || $id === '.' || $id === '..') {
    $msg = "idが取得できませんでした。";
}

// URLの組み立て
$uri  = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$base_dir = substr($uri, 0, strrpos($uri, "/"));
$url  = $base_dir . "/detail.php?id=" . urlencode($id);

// ファイルの存在確認
$file = "./data/" . $id . ".txt";
if ($msg === '') {
    if (!file_exists($file)) {
        $msg = "ファイルが存在しません。";
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
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>幹事くん</title>
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
          <h1 class="display-6">イベント再編集</h1>
          <a class="btn btn-secondary" href="./detail.php?id=<?= h($id) ?>"><i class="fa-solid fa-chevron-left"></i> 戻る</a>
        </div>
      </div>
    </div>

    <!-- フォーム -->
    <form method="post" action="edit.php?id=<?= h($id) ?>">
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
          <h5 class="text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> 危険エリア</h5>
          <p class="text-muted mb-3">※一度削除すると復旧はできません。ご注意ください。</p>
          <div>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#myModal"><i class="fa-solid fa-trash-can"></i> イベントを削除する</button>
          </div>
        </div>
      </div>
    </div>

    <!-- モーダル -->
    <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">確認画面</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="mb-0">イベントを削除してよろしいですか？</p>
          </div>
          <div class="modal-footer">
            <a href="./?cmd=delete&id=<?= h($id) ?>" class="btn btn-danger">OK</a>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
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
    <p class="text-muted">Developed by <a href="https://github.com/s0323861" class="text-decoration-none" target="_blank" rel="noopener">Akira Mukai</a> 2021</p>
  </footer>

</div>

<!-- Bootstrap 5 JavaScript Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>