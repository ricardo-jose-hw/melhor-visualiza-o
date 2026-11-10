
**Acesso inicial:** `admin@exemplo.com.br` / `admin123`. Os demais funcionários entram com o e-mail e a senha
definidos no cadastro. 

## Estrutura
- `index.php` — sessão, login obrigatório, permissão por perfil e roteador (`?p=<rota>`)
- `includes/store.php` — leitura/gravação de `data/db.json` (provisório)
- `includes/acoes.php` — tratamento de todos os `POST`: validação, gravação e redirecionamento (provisório)
- `includes/config.php` — rotas, menu por perfil e listas fixas
- `includes/helpers.php` — funções que geram campos, ícones, status e botões
- `data/seed.php` — dados iniciais (mesma forma da modelagem: tutor, pet, funcionário, clínica, consulta, exame)
- `pages/` — uma tela por arquivo (o nome do arquivo é a rota)
- `assets/` — `css/style.css`, `js/app.js`, fontes e logos do site

## Contrato dos formulários
Todo formulário envia `POST` para a própria rota. Telas de cadastro/edição usam `?id=` para edição.
Ações em listas enviam `acao` + `id` (`inativar`, `reativar`, `cancelar`, `status`, `excluir`).

| Rota | Requisitos | Campos enviados |
|---|
| `login`, `recuperar-senha`, `redefinir-senha` | RF-01 a RF-03 | `email`, `senha` · `email` · `token`, `senha`, `senha_confirmacao` (login Google: `?p=login&google=1`) |
| `clinicas`, `clinicas-form` | RF-05 a RF-09 | `nome`, `cnpj`, `email`, `telefone`, `endereco`, `cidade`, `uf`, `acesso` (`email`/`google`, só no cadastro) |
| `funcionarios`, `funcionarios-form` | RF-10 a RF-15 | `nome`, `email` (é o login), `cpf`, `telefone`, `especialidade`, `perfil`, `clinicas[]`, `acesso`, `senha`, `senha_confirmacao` |
| `clientes`, `clientes-form` | RF-16 a RF-20 | `nome`, `cpf`, `email`, `telefone`, `endereco`, `acesso` |
| `pets`, `pets-form`, `pets-historico` | RF-21 a RF-25 | `cliente_id`, `nome`, `especie`, `raca`, `peso` |
| `consultas`, `consultas-form` | RF-26 a RF-32 | `clinica_id`, `veterinario_id`, `cliente_id`, `pet_id`, `data`, `hora`, `motivo` · filtros por GET: `data`, `cliente`, `pet`, `clinica_id`, `status` |
| `exames`, `exames-form` | RF-33 a RF-35 | `consulta_id`, `nome_exame[]`, `obs_exame[]`, `arquivo_exame[]` (multipart) |

## Login e permissões
- O funcionário é cadastrado com e-mail (login) e senha (hash com `password_hash`); "Esqueci minha senha" abre direto o link de redefinição, pois o protótipo não envia e-mail.
- Perfis (RF-04): `administrador`, `veterinario`, `funcionario` (cliente não usa este painel). Funcionários e Clínicas são só do administrador.
- O veterinário vê apenas as próprias consultas (painel e lista). Funcionário inativo não entra.
- Login com Google e envio de e-mail dependem do back-end.

## Exclusão
- **Excluir cliente** apaga também os pets, as consultas e os exames (com os arquivos). **Excluir pet** apaga o histórico dele.
- Inativar continua disponível e mantém o cadastro e o histórico.

## Pontos para o back-end
- Trocar `includes/store.php` e `includes/acoes.php` pelo banco de dados e pelos controllers reais; as telas em `pages/` não dependem de como os dados são gravados.
- Regras já aplicadas no protótipo: CPF e e-mail únicos entre funcionários, senha com 8+ caracteres, exames só em consultas `Concluída` (PDF/imagem, até 10 MB).
- Faltam no protótipo: CSRF, limite de tentativas de login, envio de e-mail, notificação ao cliente (RF-32), logs de auditoria (RNF-10), HTTPS e LGPD.
- Mensagens de sucesso/erro usam `$_SESSION['flash']`; erros de campo usam `$erros['campo']`.

## Convenções do código
- Cores, raios e fontes ficam nas variáveis no topo de `assets/css/style.css`
