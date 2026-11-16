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
 * Admin settings for local_classcards.
 *
 * @package    local_classcards
 * @copyright  2026 ClassCards (TCC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_classcards', new lang_string('pluginname', 'local_classcards'));

    $settings->add(new admin_setting_configcheckbox(
        'local_classcards/enabled',
        new lang_string('enabled', 'local_classcards'),
        new lang_string('enabled_desc', 'local_classcards'),
        0
    ));

    $settings->add(new admin_setting_configtext(
        'local_classcards/projectid',
        new lang_string('projectid', 'local_classcards'),
        new lang_string('projectid_desc', 'local_classcards'),
        '',
        PARAM_ALPHANUMEXT
    ));

    // Path only (never the key itself): the key file stays outside the web root and outside Git.
    $settings->add(new admin_setting_configtext(
        'local_classcards/keyfile',
        new lang_string('keyfile', 'local_classcards'),
        new lang_string('keyfile_desc', 'local_classcards'),
        '',
        PARAM_RAW_TRIMMED
    ));

    // Terms shown in the app before the first use of course materials with the AI.
    $settings->add(new admin_setting_heading(
        'local_classcards/termos',
        new lang_string('termos', 'local_classcards'),
        new lang_string('termos_desc', 'local_classcards')
    ));
    $settings->add(new admin_setting_configtextarea(
        'local_classcards/termostexto',
        new lang_string('termostexto', 'local_classcards'),
        new lang_string('termostexto_desc', 'local_classcards'),
        '',
        PARAM_RAW,
        60,
        16
    ));
    $settings->add(new admin_setting_configtext(
        'local_classcards/termosversao',
        new lang_string('termosversao', 'local_classcards'),
        new lang_string('termosversao_desc', 'local_classcards'),
        1,
        PARAM_INT
    ));

    $ADMIN->add('localplugins', $settings);
}
