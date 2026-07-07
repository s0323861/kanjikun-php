$(function () {
    // ステップ進行状態とプログレスバーの更新用関数
    function updateProgressBar() {
        var $panes = $('.tab-content .tab-pane');
        var activeIndex = $panes.index($('.tab-content .tab-pane.active'));
        
        // 全体のステップ数を「3」に固定（ウェルカム、基本情報、候補日程）
        var totalSteps = 3; 
        
        // 完成画面（4番目のパネル）に達した、または#completeがアクティブな場合
        if (activeIndex === 3 || $('.tab-content .tab-pane#complete').hasClass('active')) {
            $('#progress-text').text('完了');
            $('#progress-percent').text('100%');
            $('#main-progress').css('width', '100%').attr('aria-valuenow', 100);
            $('.progress-track').fadeOut(); // 完成時はプログレスバーを隠す
            return;
        }
        
        // 現在のステップ番号（1, 2, 3）
        var currentStep = activeIndex + 1;
        var percent = Math.round((currentStep / totalSteps) * 100);
        
        $('#progress-text').text('ステップ ' + currentStep + ' / ' + totalSteps);
        $('#progress-percent').text(percent + '%');
        $('#main-progress').css('width', percent + '%').attr('aria-valuenow', percent);
    }

    $(".next-step").click(function (e) {
        var $active = $('.tab-content .tab-pane.active');
        var $next = $active.next('.tab-pane');
        
        if ($next.length) {
            $active.removeClass('show active');
            $next.addClass('show active');
            updateProgressBar();
        }
    });

    $(".prev-step").click(function (e) {
        var $active = $('.tab-content .tab-pane.active');
        var $prev = $active.prev('.tab-pane');
        
        if ($prev.length) {
            $active.removeClass('show active');
            $prev.addClass('show active');
            updateProgressBar();
        }
    });

    // 入力チェック（イベント名）
    if ($("#inputName").val().length == 0) {
        $('#stp1btn').prop('disabled', true);
    }
    $("#inputName").on('keydown keyup keypress change input', function() {
        if ($(this).val().length > 0) {
            $('#stp1btn').prop('disabled', false);
        } else {
            $('#stp1btn').prop('disabled', true);
        }
    });

    // 入力チェック（日時1）
    if ($(".date-1").val().length == 0) {
        $('#stp2btn').prop('disabled', true);
    }
    $(".date-1").on('keydown keyup keypress change input', function() {
        if ($(this).val().length > 0) {
            $('#stp2btn').prop('disabled', false);
        } else {
            $('#stp2btn').prop('disabled', true);
        }
    });

    // 非同期通信を行ない結果を表示する
    $('#stp2btn').click(function () {
        var currentLang = $('#current_lang').val() || 'ja';
        
        // 連打防止のため、一時的にボタンを無効化
        var $btn = $(this);
        $btn.prop('disabled', true);

        $.ajax({
            type: "POST",
            url: "comp.php",
            dataType: 'json',
            data: {
                name: $('#inputName').val(), 
                memo: $('#textArea').val(), 
                date1: $(".date-1").val(), 
                date2: $(".date-2").val(), 
                date3: $(".date-3").val(), 
                id: $('#eventid').val(),
                lang: currentLang
            }
        })
.done(function (response) {
            var btnText = (currentLang === 'en') ? 'Go to Event Page' : 'イベントページに移動する';
            var copyBtnText = (currentLang === 'en') ? 'Copy URL' : 'URLをコピー';

            var resultHtml = 
                '<div class="input-group font-monospace">' +
                '  <input type="text" class="form-control bg-white text-center fw-bold text-success border-success" id="created-url" value="' + response.url + '" readonly>' +
                '  <button class="btn btn-success" type="button" onclick="copyToClipboard(\'' + response.url + '\')"><i class="fa fa-clipboard"></i> ' + copyBtnText + '</button>' +
                '</div>';
            
            $('#result1').html(resultHtml);

            $('#result2').html(
                '<a href="' + response.url + '" class="btn btn-primary btn-lg px-5 fw-bold rounded-3 shadow-sm">' +
                '  <i class="fa fa-calendar-check-o me-2"></i>' + btnText +
                '</a>'
            );

            // 【修正】未定義のエラーを回避するため、すでに取得済みの response.url をそのまま利用します
            var eventUrl = response.url;

            // QRコード生成APIのURLを組み立てて画像要素にセット
            var qrApiUrl = 'https://quickchart.io/chart?cht=qr&chs=150x150&chl=' + encodeURIComponent(eventUrl);
            $('#qrcode-img').attr('src', qrApiUrl);
            $('#qrcode-container').removeClass('d-none'); // コンテナを表示

            // 通信が成功したタイミングで初めて「完了画面」へ遷移させる
            var $active = $('.tab-content .tab-pane.active');
            var $next = $('#complete');
            $active.removeClass('show active');
            $next.addClass('show active');
            updateProgressBar();
        })
        .fail(function (xhr, status, error) {
            // エラー時はボタンを元に戻す
            $btn.prop('disabled', false);
            
            var errorMsg = 'エラーが発生しました。';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            } else {
                errorMsg += ' (Status: ' + xhr.status + ', ' + error + ')';
            }
            alert("エラー: " + errorMsg);
            console.error(xhr);
        });
    });
});

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // BootstrapのToastインスタンスを作成して表示
        var toastEl = document.getElementById('copyToast');
        var toast = new bootstrap.Toast(toastEl);
        toast.show();
    }).catch(function(err) {
        console.error('コピー失敗: ', err);
    });
}