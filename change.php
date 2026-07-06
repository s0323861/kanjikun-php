<?php
// 1. 入力値の取得と初期化（クエリパラメータおよびPOSTデータ）
$id   = $_GET['id']   ?? $_POST['id']   ?? '';
$sid  = $_GET['sid']  ?? $_POST['sid']  ?? '';
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
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>幹事くん</title>
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
  <!-- フォームセクション -->
  <section id="enter" class="my-4">
    <div class="row">
      <div class="col-12">
        <div class="page-header mb-4">
          <h1 id="forms" class="display-6">出欠を入力する</h1>
          <a class="btn btn-secondary" href="./detail.php?id=<?= h($id) ?>"><i class="fa-solid fa-chevron-left"></i> 戻る</a>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <!-- BS5ではwellが廃止されたためcardで再現 -->
        <div class="card bg-light p-4 shadow-sm">
          <form method="post" action="change.php?id=<?= h($id) ?>&sid=<?= h($sid) ?>">
            <input type="hidden" name="edit" value="go">
            <input type="hidden" name="id" value="<?= h($id) ?>">
            <input type="hidden" name="sid" value="<?= h($sid) ?>">
            
            <fieldset>
              <!-- 表示名 -->
              <div class="row mb-3 align-items-center">
                <label for="inputName" class="col-lg-2 col-form-label fw-bold">表示名</label>
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
                <label for="inputComment" class="col-lg-2 col-form-label fw-bold">コメント</label>
                <div class="col-lg-10">
                  <input type="text" class="form-control" name="comment" id="inputComment" value="<?= h($com) ?>">
                </div>
              </div>

              <!-- アクションボタン -->
              <div class="row mb-3">
                <div class="col-lg-10 offset-lg-2">
                  <button type="submit" class="btn btn-primary"><i class="fa-solid fa-rotate"></i> 更新する</button>
                </div>
              </div>

              <hr>

              <div class="row">
                <div class="col-lg-10 offset-lg-2">
                  <!-- detail.php の削除仕様に合わせて調整してください -->
                  <a href="detail.php?id=<?= h($id) ?>&sid=<?= h($sid) ?>&edit=delete" class="btn btn-danger"><i class="fa-solid fa-trash-can"></i> 削除する</a>
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
    <p class="text-muted">Developed by <a href="https://github.com/s0323861" class="text-decoration-none" target="_blank" rel="noopener">Akira Mukai</a> 2021</p>
  </footer>

</div>

<!-- Bootstrap 5 JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>