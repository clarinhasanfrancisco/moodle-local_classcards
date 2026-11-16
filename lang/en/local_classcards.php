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
 * English strings for local_classcards.
 *
 * @package    local_classcards
 * @copyright  2026 ClassCards (TCC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['aviso_aluno'] = 'The PDF you choose will be sent to Google\'s Gemini artificial intelligence service only to generate your flashcards. On the free plan used in this version, Google may use the content sent to improve its products. Use the flashcards only for study and do not redistribute the material.';
$string['classcards:autorizarcurso'] = 'Allow ClassCards to use the course materials';
$string['enabled'] = 'Enable ClassCards';
$string['enabled_desc'] = 'Allow the ClassCards app to sign users in with their Moodle account.';
$string['error_cursonaoautorizado'] = 'The teacher has not allowed ClassCards to use the materials of this course yet.';
$string['error_keyinvalid'] = 'The Firebase service account file is not valid.';
$string['error_keynotconfigured'] = 'The Firebase service account file was not found. Ask the site administrator to configure ClassCards.';
$string['error_naoinscrito'] = 'You are not enrolled in this course.';
$string['error_notenabled'] = 'ClassCards is not enabled on this site.';
$string['error_projectmismatch'] = 'The Firebase service account belongs to a different Firebase project.';
$string['error_termosdesatualizados'] = 'The terms have changed. Read the new version before accepting.';
$string['keyfile'] = 'Service account file';
$string['keyfile_desc'] = 'Full path to the Firebase service account JSON file, outside the web root. Leave empty to use moodledata/local_classcards/firebase-service-account.json.';
$string['pluginname'] = 'ClassCards';
$string['privacy:metadata:aceite'] = 'Record of the teachers who accepted the terms for allowing ClassCards to use course materials.';
$string['privacy:metadata:aceite:timecreated'] = 'When the terms were accepted.';
$string['privacy:metadata:aceite:userid'] = 'The teacher who accepted the terms.';
$string['privacy:metadata:aceite:versao'] = 'The version of the terms that was accepted.';
$string['privacy:metadata:curso'] = 'Courses whose materials a teacher allowed ClassCards to use.';
$string['privacy:metadata:curso:timemodified'] = 'When the authorisation was changed.';
$string['privacy:metadata:curso:usermodified'] = 'The teacher who last changed the authorisation.';
$string['privacy:metadata:curso:versaotermo'] = 'The version of the terms accepted when allowing the course.';
$string['privacy:metadata:firebase'] = 'To sign the user in to the ClassCards app, a token with these data is issued for the app\'s Firebase project.';
$string['privacy:metadata:firebase:courses'] = 'The ids of the courses the user is enrolled in and their role in each one.';
$string['privacy:metadata:firebase:email'] = 'The user\'s email address.';
$string['privacy:metadata:firebase:fullname'] = 'The user\'s full name.';
$string['privacy:metadata:firebase:userid'] = 'The user\'s Moodle id.';
$string['privacy:metadata:gemini'] = 'When the user asks for it, the ClassCards app sends the chosen PDF to Google\'s Gemini AI service, through Firebase, to generate flashcards.';
$string['privacy:metadata:gemini:material'] = 'The PDF file of the chosen material.';
$string['projectid'] = 'Firebase project id';
$string['projectid_desc'] = 'Optional. If set, the service account must belong to this Firebase project.';
$string['termos'] = 'Terms for allowing materials';
$string['termos_desc'] = 'Text the teacher reads and accepts in the app when allowing the use of a course\'s materials. Whenever you change the text, increase the version: old authorisations stop working until the teacher accepts the new version.';
$string['termos_padrao'] = 'Terms for allowing the use of materials in ClassCards

By allowing it, you let the students and teachers enrolled in this course use the PDF files published in it to generate flashcards in ClassCards. When someone chooses a PDF, the file is sent to Google\'s Gemini artificial intelligence service, through Firebase, with the sole purpose of generating flashcards for study.

By allowing it, you declare that:

1. You hold the rights to the materials of this course, or have the institution\'s permission to allow this use.

2. You are aware that, in this version, Google\'s AI service is used on the free plan. On that plan, Google may use the material sent and the generated responses to improve its products, and human reviewers may read them. Therefore, do not allow courses whose PDFs contain personal data (of students, for example) or confidential information.

3. You know that ClassCards records in Moodle who allowed it, the version of these terms and the date.

You can withdraw the authorisation at any time. From then on, the materials are no longer available in ClassCards (flashcards already created are not deleted). If these terms change, the authorisation will have to be renewed.';
$string['termostexto'] = 'Terms text';
$string['termostexto_desc'] = 'Leave empty to use the plugin\'s default text.';
$string['termosversao'] = 'Terms version';
$string['termosversao_desc'] = 'Increase this number whenever the text changes.';
