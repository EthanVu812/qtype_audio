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
 * External services definition for qtype_audio
 *
 * @package    qtype_audio
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'qtype_audio_update_playback_time' => array(
        'classname'   => 'qtype_audio_external',
        'methodname'  => 'update_playback_time',
        'classpath'   => 'question/type/audio/externallib.php',
        'description' => 'Cập nhật thời gian phát hiện tại của audio trong bài làm',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'mod/quiz:attempt'
    ),
    'qtype_audio_get_playback_time' => array(
        'classname'   => 'qtype_audio_external',
        'methodname'  => 'get_playback_time',
        'classpath'   => 'question/type/audio/externallib.php',
        'description' => 'Lấy thời gian phát hiện tại của audio từ bài làm',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities'=> 'mod/quiz:attempt'
    )
);

$services = array(
    'Audio Playback Service' => array(
        'functions' => array('qtype_audio_update_playback_time', 'qtype_audio_get_playback_time'),
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);