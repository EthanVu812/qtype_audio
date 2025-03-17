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
 * Question type class for the description 'question' type.
 *
 * @package    qtype
 * @subpackage description
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');


/**
 * The description 'question' type.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_audio extends question_type {
    public function is_real_question_type() {
        return false;
    }

    public function is_usable_by_random() {
        return false;
    }

    public function can_analyse_responses() {
        return false;
    }

    public function save_question($question, $form) {
        // Make very sure that descriptions can'e be created with a grade of
        // anything other than 0.
        $form->defaultmark = 0;
        return parent::save_question($question, $form);
    }

    public function actual_number_of_questions($question) {
        // Used for the feature number-of-questions-per-page
        // to determine the actual number of questions wrapped by this question.
        // The question type description is not even a question
        // in itself so it will return ZERO!
        return 0;
    }

    public function get_random_guess_score($questiondata) {
        return null;
    }
    public function save_question_options($question) {
        global $DB;

        $context = $question->context;
        $oldaudio = $DB->get_record('qtype_audio', array('questionid' => $question->id));

         // Lưu file mới
        file_save_draft_area_files($question->audiofile, $context->id,
        'qtype_audio', 'audiofile', $question->id);
        
        $options = new stdClass();
        $options->questionid = $question->id;
        $options->audiofile = $question->audiofile;
        $options->controlaudio = $question->controlaudio;

        if ($oldaudio) {
            $options->id = $oldaudio->id;
            $DB->update_record('qtype_audio', $options);
        } else {
            $DB->insert_record('qtype_audio', $options);
        }
        
        return true;
    }
    public function edit_form_from_instance(question_edit_form $form, question $question) {
        $options = $this->get_question_options($question);
        $form->set_data($options);
    }
    
    public function get_question_options($question) {
        global $DB;
        
        $options = $DB->get_record('qtype_audio', array('questionid' => $question->id));
        if ($options) {
            $question->audiofile = $options->audiofile;
        }
        return true;
    }
}
