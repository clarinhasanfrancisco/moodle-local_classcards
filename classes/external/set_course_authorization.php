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
use core_external\external_single_structure;
use core_external\external_value;
use local_classcards\materiais;
use local_classcards\termos;

/**
 * A teacher allows (or stops) ClassCards from using a course's materials.
 *
 * Allowing means accepting the terms: the app sends the version of the terms it showed, and if
 * the terms changed meanwhile nothing is recorded. The permission is checked here, on the server.
 *
 * @package    local_classcards
 * @copyright  2026 ClassCards (TCC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_course_authorization extends external_api {

    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course id'),
            'autorizado' => new external_value(PARAM_BOOL, 'true to allow (accepting the terms), false to stop'),
            'versao' => new external_value(PARAM_INT, 'Version of the terms shown to the teacher (needed to allow)',
                VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Changes the authorisation.
     *
     * @param int $courseid
     * @param bool $autorizado
     * @param int $versao
     * @return array
     */
    public static function execute(int $courseid, bool $autorizado, int $versao = 0): array {
        global $USER;
        $params = self::validate_parameters(self::execute_parameters(),
            ['courseid' => $courseid, 'autorizado' => $autorizado, 'versao' => $versao]);

        if (!get_config('local_classcards', 'enabled')) {
            throw new \moodle_exception('error_notenabled', 'local_classcards');
        }
        $context = \context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability(materiais::CAP_AUTORIZAR, $context);

        if ($params['autorizado'] && $params['versao'] !== termos::versao()) {
            throw new \moodle_exception('error_termosdesatualizados', 'local_classcards');
        }

        materiais::definir_autorizacao($params['courseid'], $params['autorizado'], (int) $USER->id);
        return [
            'courseid' => $params['courseid'],
            'autorizado' => materiais::autorizado($params['courseid']),
            'versao' => termos::versao(),
        ];
    }

    /**
     * Describes the response.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'courseid' => new external_value(PARAM_INT, 'Course id'),
            'autorizado' => new external_value(PARAM_BOOL, 'Current authorisation'),
            'versao' => new external_value(PARAM_INT, 'Current version of the terms'),
        ]);
    }
}
