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
 * Course authorisation by teachers and the list of PDF materials ClassCards may use.
 *
 * @package    local_classcards
 * @copyright  2026 ClassCards (TCC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class materiais {

    /** Capability that lets a teacher allow or stop the use of a course's materials. */
    const CAP_AUTORIZAR = 'local/classcards:autorizarcurso';

    /** Only PDFs for now (decision of the project). */
    const MIMETYPE = 'application/pdf';

    /**
     * Has a teacher allowed this course under the CURRENT version of the terms?
     * Courses start blocked (opt-in), and a new version of the terms requires a new authorisation.
     *
     * @param int $courseid
     * @return bool
     */
    public static function autorizado(int $courseid): bool {
        global $DB;
        $registro = $DB->get_record('local_classcards_curso', ['courseid' => $courseid], 'autorizado, versaotermo');
        return $registro && $registro->autorizado && (int) $registro->versaotermo === termos::versao();
    }

    /**
     * Allows or stops the use of a course's materials. Allowing also records that the teacher
     * accepted the current version of the terms.
     *
     * @param int $courseid
     * @param bool $autorizado
     * @param int $userid teacher making the change
     * @return void
     */
    public static function definir_autorizacao(int $courseid, bool $autorizado, int $userid): void {
        global $DB;
        if ($autorizado) {
            termos::aceitar($userid);
        }
        $dados = [
            'autorizado' => (int) $autorizado,
            'versaotermo' => $autorizado ? termos::versao() : 0,
            'usermodified' => $userid,
            'timemodified' => time(),
        ];
        $registro = $DB->get_record('local_classcards_curso', ['courseid' => $courseid]);
        if ($registro) {
            $DB->update_record('local_classcards_curso', (object) (['id' => $registro->id] + $dados));
        } else {
            $DB->insert_record('local_classcards_curso', (object) (['courseid' => $courseid] + $dados));
        }
    }

    /**
     * PDF files of the course resources the user can see, with their web service download URL.
     *
     * @param \stdClass $course
     * @return array
     */
    public static function listar(\stdClass $course): array {
        global $DB;
        $lista = [];
        $fs = get_file_storage();
        $modinfo = get_fast_modinfo($course);
        foreach ($modinfo->get_instances_of('resource') as $cm) {
            if (!$cm->uservisible) {
                continue;
            }
            $context = \context_module::instance($cm->id);
            if (!has_capability('mod/resource:view', $context)) {
                continue;
            }
            $revision = (int) $DB->get_field('resource', 'revision', ['id' => $cm->instance]);
            $files = $fs->get_area_files($context->id, 'mod_resource', 'content', 0, 'sortorder DESC, id ASC', false);
            foreach ($files as $file) {
                if ($file->get_mimetype() !== self::MIMETYPE) {
                    continue;
                }
                $url = \moodle_url::make_webservice_pluginfile_url(
                    $context->id, 'mod_resource', 'content', $revision, $file->get_filepath(), $file->get_filename());
                $lista[] = [
                    'cmid' => (int) $cm->id,
                    'nome' => \core_external\util::format_string($cm->name, $context),
                    'arquivo' => $file->get_filename(),
                    'tamanho' => (int) $file->get_filesize(),
                    'mimetype' => $file->get_mimetype(),
                    'fileurl' => $url->out(false),
                ];
                // One file per resource is enough (the main file).
                break;
            }
        }
        return $lista;
    }
}
