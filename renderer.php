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
 * Description 'question' renderer class.
 *
 * @package    qtype
 * @subpackage description
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for description 'question's.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_audio_renderer extends qtype_renderer {
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {
        global $PAGE, $DB;
        
        $question = $qa->get_question();
        
        $html = html_writer::start_div('qtype_audio');
        
        // Thêm phần start_audio
        $html .= '
        <div class="start_audio">
            <div class="icon_lestening">
                <i class="fa-solid fa-headphones fa-2xl" style="color: #ffffff;"></i>
            </div>
            <div class="description">
                <p>You will be listening to an audio clip during this test. You will not be permitted to pause or rewind the audio while answering the questions.</p>
                <p>To continue, click Play.</p>
                <button class="btn btn-play-audio" type="button">
                    <i class="fa fa-play-circle fa-xl" aria-hidden="true"></i>
                    <span class="caption">Play</span>
                </button>
            </div>
        </div>';
        $html .= html_writer::end_div(); // Đóng div.qtype_audio
        
        // Lấy file âm thanh
        $fs = get_file_storage();
        $files = $fs->get_area_files($question->contextid, 'qtype_audio', 'audiofile', 
                                    $question->id);
        $audiosrc = '';
        foreach ($files as $file) {
            if ($file->get_filename() !== '.') {
                $url = moodle_url::make_pluginfile_url(
                    $question->contextid,
                    'qtype_audio',
                    'audiofile',
                    $question->id,
                    '/',
                    $file->get_filename()
                );
                
                $audiosrc = $url->out();
            }
        }
        $controlaudio = $DB->get_field('qtype_audio', 'controlaudio', ['questionid' => $question->id])? 'controls': '';
        
        // Load JavaScript module
        $PAGE->requires->js_call_amd('qtype_audio/player', 'init');
        
        return "<audio $controlaudio data-questionId='".$question->id."' id='audio_in_page' src='$audiosrc' ></audio>";
    }

    public function formulation_heading() {
        return get_string('informationtext', 'qtype_audio');
    }
}
