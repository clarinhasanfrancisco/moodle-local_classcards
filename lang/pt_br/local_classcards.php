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
 * Textos em português (Brasil) do local_classcards.
 *
 * @package    local_classcards
 * @copyright  2026 ClassCards (TCC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['aviso_aluno'] = 'O PDF escolhido será enviado ao serviço de inteligência artificial Gemini, do Google, apenas para gerar os seus flashcards. No plano gratuito usado nesta versão, o Google pode usar o conteúdo enviado para melhorar os produtos dele. Use os flashcards só para estudo e não redistribua o material.';
$string['classcards:autorizarcurso'] = 'Autorizar o uso dos materiais do curso no ClassCards';
$string['enabled'] = 'Habilitar o ClassCards';
$string['enabled_desc'] = 'Permite que o app ClassCards faça o login dos usuários com a conta do Moodle.';
$string['error_cursonaoautorizado'] = 'O professor ainda não autorizou o uso dos materiais deste curso no ClassCards.';
$string['error_keyinvalid'] = 'O arquivo da conta de serviço do Firebase não é válido.';
$string['error_keynotconfigured'] = 'O arquivo da conta de serviço do Firebase não foi encontrado. Peça ao administrador do site para configurar o ClassCards.';
$string['error_naoinscrito'] = 'Você não está inscrito neste curso.';
$string['error_notenabled'] = 'O ClassCards não está habilitado neste site.';
$string['error_projectmismatch'] = 'A conta de serviço do Firebase pertence a outro projeto Firebase.';
$string['error_termosdesatualizados'] = 'O termo foi atualizado. Leia a nova versão antes de aceitar.';
$string['keyfile'] = 'Arquivo da conta de serviço';
$string['keyfile_desc'] = 'Caminho completo do arquivo JSON da conta de serviço do Firebase, fora da pasta pública do site. Deixe vazio para usar moodledata/local_classcards/firebase-service-account.json.';
$string['pluginname'] = 'ClassCards';
$string['privacy:metadata:aceite'] = 'Registro dos professores que aceitaram o termo de autorização de uso dos materiais no ClassCards.';
$string['privacy:metadata:aceite:timecreated'] = 'Quando o termo foi aceito.';
$string['privacy:metadata:aceite:userid'] = 'O professor que aceitou o termo.';
$string['privacy:metadata:aceite:versao'] = 'A versão do termo que foi aceita.';
$string['privacy:metadata:curso'] = 'Cursos cujos materiais um professor autorizou o ClassCards a usar.';
$string['privacy:metadata:curso:timemodified'] = 'Quando a autorização foi alterada.';
$string['privacy:metadata:curso:usermodified'] = 'O professor que alterou a autorização por último.';
$string['privacy:metadata:curso:versaotermo'] = 'A versão do termo aceita ao autorizar o curso.';
$string['privacy:metadata:firebase'] = 'Para fazer o login do usuário no app ClassCards, é emitido um token com estes dados para o projeto Firebase do app.';
$string['privacy:metadata:firebase:courses'] = 'Os ids dos cursos em que o usuário está inscrito e o papel dele em cada um.';
$string['privacy:metadata:firebase:email'] = 'O endereço de e-mail do usuário.';
$string['privacy:metadata:firebase:fullname'] = 'O nome completo do usuário.';
$string['privacy:metadata:firebase:userid'] = 'O id do usuário no Moodle.';
$string['privacy:metadata:gemini'] = 'Quando o próprio usuário pede, o app ClassCards envia o PDF escolhido ao serviço de IA Gemini (Google), pelo Firebase, para gerar flashcards.';
$string['privacy:metadata:gemini:material'] = 'O arquivo PDF do material escolhido.';
$string['projectid'] = 'Id do projeto Firebase';
$string['projectid_desc'] = 'Opcional. Se preenchido, a conta de serviço precisa ser deste projeto Firebase.';
$string['termos'] = 'Termo de autorização dos materiais';
$string['termos_desc'] = 'Texto que o professor lê e aceita no app ao autorizar o uso dos materiais de um curso. Sempre que mudar o texto, aumente a versão: as autorizações antigas deixam de valer até o professor aceitar a nova versão.';
$string['termos_padrao'] = 'Termo de autorização de uso dos materiais no ClassCards

Ao autorizar, você permite que os alunos e professores inscritos neste curso usem os arquivos PDF publicados nele para gerar flashcards no ClassCards. Quando alguém escolhe um PDF, o arquivo é enviado ao serviço de inteligência artificial Gemini, do Google, pelo Firebase, com a única finalidade de gerar flashcards para estudo.

Ao autorizar, você declara que:

1. Tem direito sobre os materiais deste curso ou autorização da instituição para permitir esse uso.

2. Está ciente de que, nesta versão, o serviço de IA do Google é usado no plano gratuito. Nesse plano, o Google pode usar o material enviado e as respostas geradas para melhorar os produtos dele, e revisores humanos podem lê-los. Por isso, não autorize cursos cujos PDFs contenham dados pessoais (de alunos, por exemplo) ou informações confidenciais.

3. Sabe que o ClassCards registra no Moodle quem autorizou, a versão deste termo e a data.

Você pode retirar a autorização quando quiser. A partir daí, os materiais deixam de ficar disponíveis no ClassCards (os flashcards já criados não são apagados). Se este termo mudar, a autorização precisará ser renovada.';
$string['termostexto'] = 'Texto do termo';
$string['termostexto_desc'] = 'Deixe vazio para usar o texto padrão do plugin.';
$string['termosversao'] = 'Versão do termo';
$string['termosversao_desc'] = 'Aumente este número sempre que o texto mudar.';
