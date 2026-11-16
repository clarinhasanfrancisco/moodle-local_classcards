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
use local_classcards\termos;

/**
 * ClassCards status for the current user: terms, acceptance and, per course, role and authorisation.
 *
 * @package    local_classcards
 * @copyright  2026 ClassCards (TCC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_status extends external_api {

    /**
     * No parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Returns the status.
     *
     * @return array
     */
    public static function execute(): array {
        global $USER;
        self::validate_context(\context_system::instance());

        $habilitado = (bool) get_config('local_classcards', 'enabled');
        $cursos = [];
        if ($habilitado) {
            foreach (enrol_get_users_courses($USER->id, true, 'id, fullname') as $course) {
                $context = \context_course::instance($course->id);
                $cursos[] = [
                    'id' => (int) $course->id,
                    'fullname' => \core_external\util::format_string($course->fullname, $context),
                    'papel' => has_capability(get_firebase_token::TEACHER_CAPABILITY, $context) ? 'professor' : 'aluno',
                    'autorizado' => materiais::autorizado((int) $course->id),
                    'podeautorizar' => has_capability(materiais::CAP_AUTORIZAR, $context),
                ];
            }
        }

        return [
            'habilitado' => $habilitado,
            'termosversao' => termos::versao(),
            'termostexto' => termos::texto(),
            'aceitou' => termos::aceitou((int) $USER->id),
            'avisoaluno' => termos::aviso_aluno(),
            'cursos' => $cursos,
        ];
    }

    /**
     * Describes the response.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'habilitado' => new external_value(PARAM_BOOL, 'Is ClassCards enabled on this site'),
            'termosversao' => new external_value(PARAM_INT, 'Current version of the terms'),
            'termostexto' => new external_value(PARAM_RAW, 'Current text of the terms teachers accept (plain text)'),
            'aceitou' => new external_value(PARAM_BOOL, 'Has the user (as a teacher) accepted the current version'),
            'avisoaluno' => new external_value(PARAM_RAW, 'Short notice for students before the first use (plain text)'),
            'cursos' => new external_multiple_structure(new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Course id'),
                'fullname' => new external_value(PARAM_TEXT, 'Course name'),
                'papel' => new external_value(PARAM_ALPHA, 'professor or aluno'),
                'autorizado' => new external_value(PARAM_BOOL, 'Has a teacher allowed the materials under the current terms'),
                'podeautorizar' => new external_value(PARAM_BOOL, 'Can the user allow or stop it'),
            ])),
        ]);
    }
}
