define(['jquery', 'core/ajax'], function($, Ajax) {
    function loadScreenQtypeAudio($type) {
        if ($type == 1) {
            return `
            <div class="qtype_audio">
                <div class="start_audio">
                    <div class="icon_lestening">
                        <i class="fa-solid fa-headphones fa-5xl" style="color: #ffffff;"></i>
                    </div>
                    <div class="description">
                        <p>You will be listening to an audio clip during this test. You will not be permitted to pause or rewind the audio while answering the questions.</p>
                        <p>To continue, click Play.</p>
                        <button class="btn btn-play-audio" type="button">
                            <i class="fa fa-play-circle fa-xl" aria-hidden="true"></i>
                            <span class="caption">Play</span>
                        </button>
                    </div>
                </div>
            </div>`;
        } else if ($type == 2) {
            return `
            <div class="qtype_audio click_to_continue">
                <div class="start_audio">
                <div class="icon_lestening">
                        <i class="fa-solid fa-headphones fa-5xl" style="color: #ffffff;"></i>
                    </div>
                    <div class="description">
                        <p>Click anywhere to continue</p>
                    </div>
                </div>
            </div>
            `;
        }
        return null;
    }
    return {
        init: function() {
            const audio = document.getElementById('audio_in_page');
            
            // Lấy thông tin cần thiết từ trang
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            const cmid = urlParams.get('cmid');
            const attemptid = urlParams.get('attempt')
            
            $body = $('body');
            $isAttepmt = $body.is('#page-mod-quiz-attempt')
            // Nếu không phải trang attempt thì không chạy
            if (!$isAttepmt) {
                return;
            }

            $html = '';

            function toogleOffScloll($status) {
                if ($status && $isAttepmt) {
                    $body.addClass('overflow-hidden');
                } else {
                    $body.removeClass('overflow-hidden');
                }
            }
        

            // Biến để theo dõi interval
            let saveTimeInterval;

            // Hàm lưu thời gian phát hiện tại vào database qua API
            function saveAudioTime(currentTime) {
                Ajax.call([{
                    methodname: 'qtype_audio_update_playback_time',
                    args: {
                        cmid: parseInt(cmid),
                        attemptid: parseInt(attemptid),
                        audio_current_time: currentTime
                    },
                    done: function(response) {
                        if (response.status) {
                            // console.log('Audio time updated: ' + currentTime);
                        } else {
                            console.error('Failed to update audio time: ' + response.message);
                        }
                    },
                    fail: function(error) {
                        console.error('AJAX error: ' + error.message);
                    }
                }]);
            }

            // Hàm lấy thời gian đã lưu từ database
            function getAudioTime() {
                return new Promise((resolve, reject) => {
                    Ajax.call([{
                        methodname: 'qtype_audio_get_playback_time', // Bạn cần tạo thêm API này
                        args: {
                            cmid: parseInt(cmid),
                            attemptid: parseInt(attemptid)
                        },
                        done: function(response) {
                            if (response && response.status) {
                                resolve(response.audio_current_time);
                            } else {
                                resolve(0); // Mặc định là 0 nếu không có dữ liệu
                            }
                        },
                        fail: function(error) {
                            console.error('AJAX error: ' + error.message);
                            resolve(0); // Mặc định là 0 nếu có lỗi
                        }
                    }]);
                });
            }

            // Hàm bắt đầu lưu thời gian định kỳ
            function startSavingTime() {
                saveTimeInterval = setInterval(() => {
                    saveAudioTime(audio.currentTime);
                }, 5000); // Lưu mỗi 5 giây để tránh quá nhiều request
            }
            function addStatusAudioPlaying() {
                if (audio.paused) {
                    $('.audio-playing').remove();
                } else {
                    if ($('.audio-playing').length == 0)
                        $('.quiz-timer-inner').append('<span style="font-weight: normal; font-size: medium;" class="audio-playing"><i class="fa fa-volume-up" aria-hidden="true"></i>Audio is playing</span>');
                }
            }

            // Lưu thời gian khi người dùng tạm dừng hoặc kết thúc audio
            $(audio).on('pause ended play', function() {
                saveAudioTime(audio.currentTime);
                addStatusAudioPlaying();
            });

            // Khởi tạo giao diện và xử lý sự kiện
            getAudioTime().then(savedTime => {
                if (savedTime > 0) { // Đã có thời gian lưu trước đó
                    audio.currentTime = parseFloat(savedTime);
                    // Nếu thời gian lưu lớn hơn thời gian audio thì không cho phát
                    if (savedTime < Math.round(audio.duration)) {
                        toogleOffScloll(true);
                        $html = loadScreenQtypeAudio(2);
                        $body.append($html);
                        $('.click_to_continue').on('click', function() {
                            audio.play();
                            toogleOffScloll(false);
                            startSavingTime();
                            $('.qtype_audio').remove();
                            addStatusAudioPlaying();
                        });
                    }
                    return;
                } else { // Chưa có thời gian lưu trước đó
                    toogleOffScloll(true);
                    $html = loadScreenQtypeAudio(1);
                    $body.append($html);
                    $('.btn-play-audio').on('click', function() {
                        audio.play();
                        startSavingTime();
                        toogleOffScloll(false);
                        $('.qtype_audio').remove();
                        addStatusAudioPlaying();
                    });
                }
            });
        }
    };
});