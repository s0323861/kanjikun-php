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
    if ($('#inputName').val().length == 0) {
        $('#stp1btn').prop('disabled', true);
    }
    $('#inputName').on('keydown keyup keypress change', function() {
        if ($(this).val().length > 0) {
            $('#stp1btn').prop('disabled', false);
        } else {
            $('#stp1btn').prop('disabled', true);
        }
    });

    // 入力チェック（日時1） - 標準のdatetime-localでもこのイベントで完全に動作します
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
                id: $('#eventid').val()
            }
        })
        .done(function (response) {
            $('#result1').html(response.data1);
            $('#result2').html(response.data2);
        })
        .fail(function () {
            $('#result1').html('失敗');
            $('#result2').html('エラーが発生しました。設定を確認してください。');
        });
        
        var $active = $('.tab-content .tab-pane.active');
        var $next = $('#complete');
        $active.removeClass('show active');
        $next.addClass('show active');
        updateProgressBar();
    });
});