<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Web service functions for local_classcards.
 *
 * The function is added to the official mobile service, so the app uses the
 * same token it received from admin/tool/mobile/launch.php.
 *
 * @package    local_classcards
 * @copyright  2026 ClassCards (TCC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_classcards_get_firebase_token' => [
        'classname'   => 'local_classcards\external\get_firebase_token',
        'description' => 'Returns a Firebase custom token for the current user, with their courses and role per course.',
        'type'        => 'read',
        'ajax'        => false,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_classcards_get_status' => [
        'classname'   => 'local_classcards\external\get_status',
        'description' => 'ClassCards status for the current user: terms, acceptance and, per course, role and authorisation.',
        'type'        => 'read',
        'ajax'        => false,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_classcards_set_course_authorization' => [
        'classname'    => 'local_classcards\external\set_course_authorization',
        'description'  => 'A teacher allows (accepting the terms) or stops ClassCards from using the materials of a course.',
        'type'         => 'write',
        'ajax'         => false,
        'capabilities' => 'local/classcards:autorizarcurso',
        'services'     => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_classcards_get_materials' => [
        'classname'   => 'local_classcards\external\get_materials',
        'description' => 'PDF materials of a course that ClassCards may send to the AI.',
        'type'        => 'read',
        'ajax'        => false,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
];
