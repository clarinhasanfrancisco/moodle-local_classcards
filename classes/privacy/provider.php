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

namespace local_classcards\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy provider for local_classcards.
 *
 * Stored data: acceptances of the terms (system context) and the teacher who last changed a course
 * authorisation (course context). Data sent elsewhere: Firebase (sign-in) and Gemini (PDF chosen by the user).
 *
 * @package    local_classcards
 * @copyright  2026 ClassCards (TCC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider,
        \core_privacy\local\request\core_userlist_provider {

    /**
     * Describes the data.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('local_classcards_aceite', [
            'userid' => 'privacy:metadata:aceite:userid',
            'versao' => 'privacy:metadata:aceite:versao',
            'timecreated' => 'privacy:metadata:aceite:timecreated',
        ], 'privacy:metadata:aceite');
        $collection->add_database_table('local_classcards_curso', [
            'usermodified' => 'privacy:metadata:curso:usermodified',
            'versaotermo' => 'privacy:metadata:curso:versaotermo',
            'timemodified' => 'privacy:metadata:curso:timemodified',
        ], 'privacy:metadata:curso');
        $collection->add_external_location_link('firebase', [
            'userid' => 'privacy:metadata:firebase:userid',
            'fullname' => 'privacy:metadata:firebase:fullname',
            'email' => 'privacy:metadata:firebase:email',
            'courses' => 'privacy:metadata:firebase:courses',
        ], 'privacy:metadata:firebase');
        $collection->add_external_location_link('gemini', [
            'material' => 'privacy:metadata:gemini:material',
        ], 'privacy:metadata:gemini');
        return $collection;
    }

    /**
     * Contexts with data for the user.
     *
     * @param int $userid
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        $contextlist->add_from_sql(
            "SELECT ctx.id
               FROM {context} ctx
              WHERE ctx.contextlevel = :nivel
                AND EXISTS (SELECT 1 FROM {local_classcards_aceite} a WHERE a.userid = :userid)",
            ['nivel' => CONTEXT_SYSTEM, 'userid' => $userid]);
        $contextlist->add_from_sql(
            "SELECT ctx.id
               FROM {local_classcards_curso} c
               JOIN {context} ctx ON ctx.instanceid = c.courseid AND ctx.contextlevel = :nivel
              WHERE c.usermodified = :userid",
            ['nivel' => CONTEXT_COURSE, 'userid' => $userid]);
        return $contextlist;
    }

    /**
     * Users with data in a context.
     *
     * @param userlist $userlist
     * @return void
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();
        if ($context->contextlevel == CONTEXT_SYSTEM) {
            $userlist->add_from_sql('userid', "SELECT userid FROM {local_classcards_aceite}", []);
        } else if ($context->contextlevel == CONTEXT_COURSE) {
            $userlist->add_from_sql('usermodified',
                "SELECT usermodified FROM {local_classcards_curso} WHERE courseid = :courseid AND usermodified > 0",
                ['courseid' => $context->instanceid]);
        }
    }

    /**
     * Exports the user's data.
     *
     * @param approved_contextlist $contextlist
     * @return void
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;
        $pasta = [get_string('pluginname', 'local_classcards')];
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel == CONTEXT_SYSTEM) {
                $aceites = [];
                foreach ($DB->get_records('local_classcards_aceite', ['userid' => $userid], 'versao') as $a) {
                    $aceites[] = ['versao' => $a->versao, 'timecreated' => transform::datetime($a->timecreated)];
                }
                if ($aceites) {
                    writer::with_context($context)->export_data($pasta, (object) ['aceites' => $aceites]);
                }
            } else if ($context->contextlevel == CONTEXT_COURSE) {
                $c = $DB->get_record('local_classcards_curso',
                    ['courseid' => $context->instanceid, 'usermodified' => $userid]);
                if ($c) {
                    writer::with_context($context)->export_data($pasta, (object) [
                        'autorizado' => transform::yesno($c->autorizado),
                        'versaotermo' => $c->versaotermo,
                        'timemodified' => transform::datetime($c->timemodified),
                    ]);
                }
            }
        }
    }

    /**
     * Deletes all users' data in a context. A course authorisation is a course setting, so it is kept,
     * only without the teacher who changed it.
     *
     * @param \context $context
     * @return void
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        if ($context->contextlevel == CONTEXT_SYSTEM) {
            $DB->delete_records('local_classcards_aceite');
        } else if ($context->contextlevel == CONTEXT_COURSE) {
            $DB->set_field('local_classcards_curso', 'usermodified', 0, ['courseid' => $context->instanceid]);
        }
    }

    /**
     * Deletes one user's data.
     *
     * @param approved_contextlist $contextlist
     * @return void
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel == CONTEXT_SYSTEM) {
                $DB->delete_records('local_classcards_aceite', ['userid' => $userid]);
            } else if ($context->contextlevel == CONTEXT_COURSE) {
                $DB->set_field('local_classcards_curso', 'usermodified', 0,
                    ['courseid' => $context->instanceid, 'usermodified' => $userid]);
            }
        }
    }

    /**
     * Deletes several users' data in a context.
     *
     * @param approved_userlist $userlist
     * @return void
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();
        $userids = $userlist->get_userids();
        if (!$userids) {
            return;
        }
        [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        if ($context->contextlevel == CONTEXT_SYSTEM) {
            $DB->delete_records_select('local_classcards_aceite', "userid $insql", $params);
        } else if ($context->contextlevel == CONTEXT_COURSE) {
            $params['courseid'] = $context->instanceid;
            $DB->set_field_select('local_classcards_curso', 'usermodified', 0,
                "courseid = :courseid AND usermodified $insql", $params);
        }
    }
}
