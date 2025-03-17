<?php
// plugin file 
function qtype_audio_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $DB, $CFG;

    // Kiểm tra context
    if ($context->contextlevel != CONTEXT_SYSTEM && 
        $context->contextlevel != CONTEXT_COURSE && 
        $context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    // Kiểm tra filearea có phải là 'audiofile' không
    if ($filearea !== 'audiofile') {
        return false;
    }

    // Lấy question id từ args
    $questionid = (int)array_shift($args);

    // Kiểm tra question có tồn tại không
    if (!$question = $DB->get_record('question', array('id' => $questionid))) {
        return false;
    }

    // Kiểm tra quyền truy cập
    require_once($CFG->libdir . '/questionlib.php');

    // Nếu là context course hoặc module, kiểm tra xem học viên có ghi danh vào khóa học hay không
    if ($context->contextlevel == CONTEXT_COURSE) {
        require_course_login($course, true, null, false);
    } elseif ($context->contextlevel == CONTEXT_MODULE) {
        require_course_login($course, true, $cm, false);
    } else {
        // Với context system, vẫn kiểm tra quyền xem câu hỏi
        question_require_capability_on($question, 'view');
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/qtype_audio/$filearea/$questionid/$relativepath";

    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    // Gửi file về cho client
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}