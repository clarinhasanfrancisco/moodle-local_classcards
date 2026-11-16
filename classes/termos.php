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

namespace local_classcards;

/**
 * Terms a TEACHER accepts when allowing ClassCards to send a course's materials to the AI:
 * current text, version and acceptance. Students only see a short notice (aviso_aluno).
 *
 * @package    local_classcards
 * @copyright  2026 ClassCards (TCC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class termos {

    /**
     * Current version of the terms (the administrator increases it whenever the text changes).
     *
     * @return int
     */
    public static function versao(): int {
        return max(1, (int) get_config('local_classcards', 'termosversao'));
    }

    /**
     * Current text of the terms.
     *
     * @return string
     */
    public static function texto(): string {
        $texto = trim((string) get_config('local_classcards', 'termostexto'));
        return $texto !== '' ? $texto : get_string('termos_padrao', 'local_classcards');
    }

    /**
     * Short notice shown to students the first time they use a material (not an acceptance).
     *
     * @return string
     */
    public static function aviso_aluno(): string {
        return get_string('aviso_aluno', 'local_classcards');
    }

    /**
     * Has the user accepted the current version?
     *
     * @param int $userid
     * @return bool
     */
    public static function aceitou(int $userid): bool {
        global $DB;
        return $DB->record_exists('local_classcards_aceite', ['userid' => $userid, 'versao' => self::versao()]);
    }

    /**
     * Records the acceptance of the current version (only once per version).
     *
     * @param int $userid
     * @return void
     */
    public static function aceitar(int $userid): void {
        global $DB;
        if (!self::aceitou($userid)) {
            $DB->insert_record('local_classcards_aceite', (object) [
                'userid' => $userid,
                'versao' => self::versao(),
                'timecreated' => time(),
            ]);
        }
    }
}
