<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * External Web Service API for qtype_audio
 *
 * @package    qtype_audio
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * External functions for qtype_audio
 *
 * @package    qtype_audio
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_audio_external extends external_api {

    /**
     * Định nghĩa tham số đầu vào
     * @return external_function_parameters
     */
    public static function update_playback_time_parameters() {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'Course module ID', VALUE_REQUIRED),
                'attemptid' => new external_value(PARAM_INT, 'Attempt ID', VALUE_REQUIRED),
                'audio_current_time' => new external_value(PARAM_FLOAT, 'Current audio time in seconds', VALUE_REQUIRED)
            )
        );
    }

    /**
     * Cập nhật thời gian phát audio
     *
     * @param int $cmid Course module ID
     * @param int $attemptid Attempt ID
     * @param float $audio_current_time Current audio time in seconds
     * @return array Status of update operation
     */
    public static function update_playback_time($cmid, $attemptid, $audio_current_time) {
        global $DB, $USER;

        // Validate parameters
        $params = self::validate_parameters(
            self::update_playback_time_parameters(),
            array(
                'cmid' => $cmid,
                'attemptid' => $attemptid,
                'audio_current_time' => $audio_current_time
            )
        );

        // Kiểm tra xem course module có tồn tại không
        $cm = get_coursemodule_from_id('quiz', $params['cmid'], 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/quiz:attempt', $context);

        // Kiểm tra xem lần làm bài có tồn tại và thuộc về người dùng hiện tại
        $attempt = $DB->get_record('quiz_attempts', array('id' => $params['attemptid']), '*', MUST_EXIST);
        if ($attempt->userid != $USER->id) {
            throw new moodle_exception('nopermissions', 'error', '', 'update audio time');
        }

        // Tìm kiếm bản ghi hiện có
        $record = $DB->get_record('qtype_audio_attempt_user', array(
            'cmid' => $params['cmid'],
            'attemptid' => $params['attemptid'],
            'userid' => $USER->id
        ));

        $result = array(
            'status' => false,
            'message' => '',
            'id' => 0
        );

        try {
            if ($record) {
                // Cập nhật bản ghi hiện có
                $record->audio_current_time = $params['audio_current_time'];
                $record->timemodified = time();
                $DB->update_record('qtype_audio_attempt_user', $record);
                $result['id'] = $record->id;
                $result['status'] = true;
                $result['message'] = 'Cập nhật thành công';
            } else {
                // Tạo bản ghi mới
                $record = new stdClass();
                $record->cmid = $params['cmid'];
                $record->attemptid = $params['attemptid'];
                $record->userid = $USER->id;
                $record->audio_current_time = $params['audio_current_time'];
                $record->timecreated = time();
                $record->timemodified = time();
                $result['id'] = $DB->insert_record('qtype_audio_attempt_user', $record);
                $result['status'] = true;
                $result['message'] = 'Tạo mới thành công';
            }
        } catch (Exception $e) {
            $result['status'] = false;
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Định nghĩa tham số đầu ra
     * @return external_description
     */
    public static function update_playback_time_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Status of the operation'),
                'message' => new external_value(PARAM_TEXT, 'Status message'),
                'id' => new external_value(PARAM_INT, 'ID of the record')
            )
        );
    }


    // API get_playback_time

    /**
     * Định nghĩa tham số đầu vào cho hàm lấy thời gian phát
     * @return external_function_parameters
     */
    public static function get_playback_time_parameters() {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'Course module ID', VALUE_REQUIRED),
                'attemptid' => new external_value(PARAM_INT, 'Attempt ID', VALUE_REQUIRED)
            )
        );
    }

    /**
     * Lấy thời gian phát audio đã lưu
     *
     * @param int $cmid Course module ID
     * @param int $attemptid Attempt ID
     * @return array Status and current audio time
     */
    public static function get_playback_time($cmid, $attemptid) {
        global $DB, $USER;

        // Validate parameters
        $params = self::validate_parameters(
            self::get_playback_time_parameters(),
            array(
                'cmid' => $cmid,
                'attemptid' => $attemptid
            )
        );

        // Kiểm tra xem course module có tồn tại không
        $cm = get_coursemodule_from_id('quiz', $params['cmid'], 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/quiz:attempt', $context);

        // Kiểm tra xem lần làm bài có tồn tại và thuộc về người dùng hiện tại
        $attempt = $DB->get_record('quiz_attempts', array('id' => $params['attemptid']), '*', MUST_EXIST);
        if ($attempt->userid != $USER->id) {
            throw new moodle_exception('nopermissions', 'error', '', 'get audio time');
        }

        // Tìm kiếm bản ghi hiện có
        $record = $DB->get_record('qtype_audio_attempt_user', array(
            'cmid' => $params['cmid'],
            'attemptid' => $params['attemptid'],
            'userid' => $USER->id
        ));

        $result = array(
            'status' => false,
            'audio_current_time' => 0
        );

        if ($record) {
            $result['status'] = true;
            $result['audio_current_time'] = $record->audio_current_time;
        }

        return $result;
    }

    /**
     * Định nghĩa tham số đầu ra
     * @return external_description
     */
    public static function get_playback_time_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Status of the operation'),
                'audio_current_time' => new external_value(PARAM_FLOAT, 'Current audio time in seconds')
            )
        );
    }
}