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
use Firebase\JWT\JWT;

/**
 * Issues a Firebase custom token for the user who owns the web service token.
 *
 * Moodle is the source of truth for who the user is and what role they have in
 * each course; the Firebase rules can then trust the claims in the token.
 *
 * @package    local_classcards
 * @copyright  2026 ClassCards (TCC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_firebase_token extends external_api {

    /** Audience required by Firebase for custom tokens. */
    const AUDIENCE = 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit';

    /** Firebase accepts custom tokens valid for at most one hour. */
    const LIFETIME = 3600;

    /** Capability that makes someone a "professor" in a course (editing teachers and managers have it). */
    const TEACHER_CAPABILITY = 'moodle/course:manageactivities';

    /** Firebase limits developer claims to 1000 bytes; keep a margin. */
    const MAX_CLAIMS_BYTES = 900;

    /**
     * No parameters: the user is the owner of the web service token.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Builds and signs the Firebase custom token.
     *
     * @return array
     */
    public static function execute(): array {
        global $USER;

        $context = \context_system::instance();
        self::validate_context($context);

        if (!get_config('local_classcards', 'enabled')) {
            throw new \moodle_exception('error_notenabled', 'local_classcards');
        }

        $key = self::load_service_account();

        $cursos = [];
        $professor = [];
        $aluno = [];
        foreach (enrol_get_users_courses($USER->id, true, 'id, fullname, shortname') as $course) {
            $coursecontext = \context_course::instance($course->id);
            $isteacher = has_capability(self::TEACHER_CAPABILITY, $coursecontext, $USER->id);
            if ($isteacher) {
                $professor[] = (int) $course->id;
            } else {
                $aluno[] = (int) $course->id;
            }
            $cursos[] = [
                'id' => (int) $course->id,
                'fullname' => \core_external\util::format_string($course->fullname, $coursecontext),
                'shortname' => \core_external\util::format_string($course->shortname, $coursecontext),
                'papel' => $isteacher ? 'professor' : 'aluno',
            ];
        }

        $siteid = self::site_id();
        $uid = 'moodle-' . $siteid . '-' . $USER->id;

        $claims = ['moodle' => [
            'site' => $siteid,
            'userid' => (int) $USER->id,
            'prof' => $professor,
            'aluno' => $aluno,
        ]];
        // Very large enrolments would exceed the Firebase claim limit: keep the teacher list, which guards privileges.
        if (strlen(json_encode($claims)) > self::MAX_CLAIMS_BYTES) {
            $claims['moodle']['aluno'] = [];
            $claims['moodle']['incompleto'] = true;
        }

        $now = time();
        $payload = [
            'iss' => $key['client_email'],
            'sub' => $key['client_email'],
            'aud' => self::AUDIENCE,
            'iat' => $now,
            'exp' => $now + self::LIFETIME,
            'uid' => $uid,
            'claims' => $claims,
        ];

        $token = JWT::encode($payload, $key['private_key'], 'RS256', $key['private_key_id'] ?? null);

        return [
            'token' => $token,
            'uid' => $uid,
            'expiresat' => $now + self::LIFETIME,
            'userid' => (int) $USER->id,
            'fullname' => fullname($USER),
            'email' => (string) $USER->email,
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
            'token' => new external_value(PARAM_RAW, 'Firebase custom token (JWT), valid for one hour'),
            'uid' => new external_value(PARAM_RAW, 'Firebase uid for this Moodle user'),
            'expiresat' => new external_value(PARAM_INT, 'Unix time when the custom token expires'),
            'userid' => new external_value(PARAM_INT, 'Moodle user id'),
            'fullname' => new external_value(PARAM_TEXT, 'User full name'),
            'email' => new external_value(PARAM_RAW, 'User email'),
            'cursos' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Course id'),
                    'fullname' => new external_value(PARAM_TEXT, 'Course full name'),
                    'shortname' => new external_value(PARAM_TEXT, 'Course short name'),
                    'papel' => new external_value(PARAM_ALPHA, 'professor or aluno'),
                ])
            ),
        ]);
    }

    /**
     * Short, stable identifier of this Moodle site (part of the Firebase uid).
     *
     * @return string
     */
    public static function site_id(): string {
        global $CFG;
        return substr(sha1($CFG->wwwroot), 0, 10);
    }

    /**
     * Path of the service account file. Defaults to a folder inside moodledata (outside the web root).
     *
     * @return string
     */
    public static function key_path(): string {
        global $CFG;
        $path = trim((string) get_config('local_classcards', 'keyfile'));
        return $path !== '' ? $path : $CFG->dataroot . '/local_classcards/firebase-service-account.json';
    }

    /**
     * Reads and checks the Firebase service account file.
     *
     * @return array
     */
    protected static function load_service_account(): array {
        $path = self::key_path();
        if (!is_readable($path)) {
            throw new \moodle_exception('error_keynotconfigured', 'local_classcards');
        }
        $key = json_decode((string) file_get_contents($path), true);
        if (!is_array($key) || ($key['type'] ?? '') !== 'service_account'
                || empty($key['client_email']) || empty($key['private_key'])) {
            throw new \moodle_exception('error_keyinvalid', 'local_classcards');
        }
        $projectid = trim((string) get_config('local_classcards', 'projectid'));
        if ($projectid !== '' && ($key['project_id'] ?? '') !== $projectid) {
            throw new \moodle_exception('error_projectmismatch', 'local_classcards');
        }
        return $key;
    }
}
