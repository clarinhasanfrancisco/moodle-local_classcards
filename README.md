# local_classcards (plugin do Moodle para o app ClassCards)

Plugin do tipo `local` que integra o app Android **ClassCards** (flashcards com IA) ao Moodle,
sem alterar o núcleo do Moodle. Desenvolvido para o Moodle **5.3** (estrutura com `public/`).

## O que ele faz

| Função (web service, serviço `moodle_mobile_app`) | Para quê |
|---|---|
| `local_classcards_get_firebase_token` | Emite um *custom token* do Firebase para o dono do token do Moodle (login no app pelo Moodle), com os cursos e o papel em cada um (`professor`/`aluno`) |
| `local_classcards_get_status` | Situação: termo (texto + versão), se o usuário já aceitou como professor, aviso para alunos e, por curso, papel, se está autorizado e se o usuário pode autorizar |
| `local_classcards_set_course_authorization` | O professor autoriza (aceitando o termo) ou retira o uso dos materiais do curso |
| `local_classcards_get_materials` | PDFs do curso, só se o usuário está inscrito e o curso está autorizado na versão atual do termo |

Regras importantes:

- O login no app usa o fluxo oficial `admin/tool/mobile/launch.php` (o app nunca vê a senha).
- Os materiais começam **bloqueados**; só quem tem `local/classcards:autorizarcurso` (Professor, Gerente) libera.
- Mudou a versão do termo → as autorizações antigas deixam de valer até o professor aceitar de novo.
- Privacy API implementada (exportação e exclusão de dados).

## Configuração

1. Copiar para `public/local/classcards` e rodar `php admin/cli/upgrade.php`.
2. *Administração do site → Plugins → ClassCards*: habilitar, informar o id do projeto Firebase e, se quiser, editar o termo.
3. Colocar a chave da conta de serviço do Firebase em
   `moodledata/local_classcards/firebase-service-account.json` (permissão 600, fora da pasta pública).
   **Nunca** versionar essa chave.

## Licença

GNU GPL v3 ou posterior (como o Moodle).
