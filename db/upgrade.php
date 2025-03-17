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
 * Audio question type upgrade code.
 *
 * @package    qtype
 * @subpackage audio
 * @copyright  2025 Ethan Vu - Vloom (vvthe8102002@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade code for the calculated question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_audio_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2024042201) {

        // Define table qtype_audio_attempt_user to be created.
        $table = new xmldb_table('qtype_audio_attempt_user');

        // Adding fields to table qtype_audio_attempt_user.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('attemptid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('audio_current_time', XMLDB_TYPE_INTEGER, '20', null, null, null, null);

        // Adding keys to table qtype_audio_attempt_user.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for qtype_audio_attempt_user.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Audio savepoint reached.
        upgrade_plugin_savepoint(true, 2024042201, 'qtype', 'audio');
    }
    if ($oldversion < 2024042204) {

        // Define field controlaudio to be added to qtype_audio.
        $table = new xmldb_table('qtype_audio');
        $field = new xmldb_field('controlaudio', XMLDB_TYPE_INTEGER, '5', null, null, null, null, 'audiofile');

        // Conditionally launch add field controlaudio.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Audio savepoint reached.
        upgrade_plugin_savepoint(true, 2024042204, 'qtype', 'audio');
    }



    return true;
}
