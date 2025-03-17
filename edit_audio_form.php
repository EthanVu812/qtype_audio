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
 * Defines the editing form for the description question type.
 *
 * @package    qtype
 * @subpackage description
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Description editing form definition.
 *
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_audio_edit_form extends question_edit_form {
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    protected function definition_inner($mform) {
        // We don't need this default element.
        $mform->removeElement('defaultmark');
        $mform->addElement('hidden', 'defaultmark', 0);
        $mform->setType('defaultmark', PARAM_RAW);

        // Add the file picker for the audio file.
        $mform->addElement('filepicker', 'audiofile', get_string('audiofile', 'qtype_audio'), null, ['accepted_types' => 'mp3,ogg']);
        $mform->addHelpButton('audiofile', 'audiofile', 'qtype_audio');
        $mform->addRule('audiofile', null, 'required');

        // Add the checkbox controls for the audio player.
        $mform->addElement('checkbox', 'controlaudio', get_string('controlaudio', 'qtype_audio'));
        $mform->setDefault('controlaudio', 0);
    }

    public function qtype() {
        return 'audio';
    }
    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        global $DB;
        $draftitemid = file_get_submitted_draft_itemid('audiofile');
        file_prepare_draft_area(
            $draftitemid,
            $this->context->id,
            'qtype_audio',
            'audiofile',
            !empty($question->id) ? $question->id : null,
            ['subdirs' => 0, 'maxfiles' => 1]
        );
        $question->audiofile = $draftitemid;
        $qa = $DB->get_record('qtype_audio', ['questionid' => $question->id]);
        $question->controlaudio = $qa->controlaudio;
        return $question;
    }
    
}
