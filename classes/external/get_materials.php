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

namespace local_classcards\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_classcards\materiais;

/**
 * PDF materials of a course that ClassCards may send to the AI.
 *
 * Only when: ClassCards is enabled, the user is enrolled in the course, and a teacher allowed the
 * course under the current terms. Moodle's own visibility rules still apply.
 *
 * @package    local_classcards
 * @copyright  2026 ClassCards (TCC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_materials extends external_api {

    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course id'),
        ]);
    }

    /**
     * Lists the materials.
     *
     * @param int $courseid
     * @return array
     */
    public static function execute(int $courseid): array {
        global $USER;
        ['courseid' => $courseid] = self::validate_parameters(self::execute_parameters(), ['courseid' => $courseid]);

        if (!get_config('local_classcards', 'enabled')) {
            throw new \moodle_exception('error_notenabled', 'local_classcards');
        }
        $context = \context_course::instance($courseid);
        // Also rejects users who are not enrolled (require_login on the course).
        self::validate_context($context);
        if (!is_enrolled($context, $USER, '', true)) {
            throw new \moodle_exception('error_naoinscrito', 'local_classcards');
        }
        // The teacher accepted the terms when allowing the course; students only see a notice in the app.
        if (!materiais::autorizado($courseid)) {
            throw new \moodle_exception('error_cursonaoautorizado', 'local_classcards');
        }

        return ['materiais' => materiais::listar(get_course($courseid))];
    }

    /**
     * Describes the response.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'materiais' => new external_multiple_structure(new external_single_structure([
                'cmid' => new external_value(PARAM_INT, 'Course module id'),
                'nome' => new external_value(PARAM_TEXT, 'Name of the resource in the course'),
                'arquivo' => new external_value(PARAM_FILE, 'File name'),
                'tamanho' => new external_value(PARAM_INT, 'File size in bytes'),
                'mimetype' => new external_value(PARAM_RAW, 'MIME type'),
                'fileurl' => new external_value(PARAM_URL, 'Download URL (append token=... to download)'),
            ])),
        ]);
    }
}
