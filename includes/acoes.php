<?php
// Tratamento dos POST: valida, grava em data/db.json e redireciona.
// Cada função recebe o "banco" por referência e devolve os erros de validação por campo (ou redireciona).
// BACKEND: trocar por controllers que gravem no banco de dados.

function ir(string $rota, array $params = [], ?array $flash = null): never {
  if ($flash) $_SESSION['flash'] = $flash;
  header('Location: ' . url($rota, $params));
  exit;
}

function texto(string $campo): string { return trim((string) ($_POST[$campo] ?? '')); }

function digitos(string $v): string { return preg_replace('/\D/', '', $v); }

function obrigatorios(array $campos): array {
  $erros = [];
  foreach ($campos as $campo => $rotulo) if (texto($campo) === '') $erros[$campo] = "Informe $rotulo.";
  return $erros;
}

function validar_senha(bool $obrigatoria = true): array {
  $senha = (string) ($_POST['senha'] ?? '');
  if ($senha === '' && !$obrigatoria) return [];
  if (mb_strlen($senha) < 8) return ['senha' => 'A senha deve ter pelo menos 8 caracteres.'];
  if ($senha !== (string) ($_POST['senha_confirmacao'] ?? '')) return ['senha_confirmacao' => 'As senhas não conferem.'];
  return [];
}

function excluir_pet(array &$db, int $pet_id): void {
  foreach ($db['exames'] as $id => $x) if ($x['pet_id'] === $pet_id) { @unlink(UPLOADS . '/' . basename($x['arquivo'])); unset($db['exames'][$id]); }
  foreach ($db['consultas'] as $id => $c) if ($c['pet_id'] === $pet_id) unset($db['consultas'][$id]);
  unset($db['pets'][$pet_id]);
}

// Inativar/reativar (e excluir, quando permitido) em listas.
function acao_lista(array &$db, string $tabela, string $rota, string $artigo): array {
  $id = (int) ($_POST['id'] ?? 0);
  $acao = $_POST['acao'] ?? '';
  if (!isset($db[$tabela][$id])) ir($rota, [], ['tipo' => 'erro', 'msg' => 'Cadastro não encontrado.']);
  $nome = $db[$tabela][$id]['nome'];
  if ($acao === 'inativar' || $acao === 'reativar') {
    global $usuario;
    if ($tabela === 'funcionarios' && $id === $usuario['id'] && $acao === 'inativar') ir($rota, [], ['tipo' => 'erro', 'msg' => 'Você não pode inativar o próprio acesso.']);
    $db[$tabela][$id]['ativo'] = $acao === 'reativar';
    $msg = "$nome " . ($acao === 'reativar' ? 'reativad' : 'inativad') . $artigo . '.';
  } elseif ($acao === 'excluir' && $tabela === 'clientes') {
    foreach ($db['pets'] as $pid => $p) if ($p['tutor_id'] === $id) excluir_pet($db, $pid);
    unset($db['clientes'][$id]);
    $msg = "Cliente $nome excluído, com os pets, consultas e exames.";
  } elseif ($acao === 'excluir' && $tabela === 'pets') {
    excluir_pet($db, $id);
    $msg = "Pet $nome excluído, com todo o histórico.";
  } else {
    ir($rota);
  }
  salvar_db($db);
  ir($rota, [], ['tipo' => 'sucesso', 'msg' => $msg]);
}

// ---------- Autenticação ----------

function post_login(array &$db): array {
  $email = mb_strtolower(texto('email'));
  foreach ($db['funcionarios'] as $id => $f) {
    if (mb_strtolower($f['email']) !== $email) continue;
    if ($f['ativo'] && $f['senha_hash'] !== '' && password_verify((string) ($_POST['senha'] ?? ''), $f['senha_hash'])) {
      session_regenerate_id(true);
      $_SESSION['uid'] = $id;
      ir('painel');
    }
    break;
  }
  return ['senha' => 'E-mail ou senha inválidos.'];
}

// Sem envio de e-mail neste protótipo: o link de redefinição é aberto direto.
function post_recuperar(array &$db): array {
  $email = mb_strtolower(texto('email'));
  foreach ($db['funcionarios'] as $f) {
    if (mb_strtolower($f['email']) !== $email || !$f['ativo']) continue;
    $token = bin2hex(random_bytes(16));
    $db['resets'][$token] = ['email' => $email, 'expira' => time() + 3600];
    salvar_db($db);
    ir('redefinir-senha', ['token' => $token], ['tipo' => 'sucesso', 'msg' => 'Neste protótipo não há envio de e-mail: o link de redefinição foi aberto direto.']);
  }
  ir('login', [], ['tipo' => 'sucesso', 'msg' => 'Se o e-mail estiver cadastrado, enviaremos o link de redefinição.']);
}

function post_redefinir(array &$db): array {
  $token = texto('token');
  $reset = $db['resets'][$token] ?? null;
  if (!$reset || $reset['expira'] < time()) ir('recuperar-senha', [], ['tipo' => 'erro', 'msg' => 'Link inválido ou expirado. Peça um novo.']);
  if ($erros = validar_senha()) return $erros;
  foreach ($db['funcionarios'] as $id => $f) if (mb_strtolower($f['email']) === $reset['email']) $db['funcionarios'][$id]['senha_hash'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
  unset($db['resets'][$token]);
  salvar_db($db);
  ir('login', [], ['tipo' => 'sucesso', 'msg' => 'Senha alterada. Entre com a nova senha.']);
}

// ---------- Clínicas ----------

function post_clinicas(array &$db): array { return acao_lista($db, 'clinicas', 'clinicas', 'a'); }

function post_clinicas_form(array &$db): array {
  $id = (int) ($_POST['id'] ?? 0);
  $novo = !isset($db['clinicas'][$id]);
  $erros = obrigatorios(['nome' => 'o nome', 'cnpj' => 'o CNPJ', 'telefone' => 'o telefone', 'email' => 'o e-mail', 'endereco' => 'o endereço', 'cidade' => 'a cidade', 'uf' => 'a UF']);
  if (!isset($erros['email']) && !filter_var(texto('email'), FILTER_VALIDATE_EMAIL)) $erros['email'] = 'E-mail inválido.';
  if (!isset($erros['uf']) && !preg_match('/^[A-Za-z]{2}$/', texto('uf'))) $erros['uf'] = 'Use a sigla com 2 letras.';
  if ($erros) return $erros;
  if ($novo) $id = novo_id($db['clinicas']);
  $db['clinicas'][$id] = array_merge($db['clinicas'][$id] ?? ['id' => $id, 'ativo' => true, 'acesso' => ($_POST['acesso'] ?? '') === 'google' ? 'google' : 'email'], [
    'nome' => texto('nome'), 'cnpj' => texto('cnpj'), 'email' => texto('email'), 'telefone' => texto('telefone'),
    'endereco' => texto('endereco'), 'cidade' => texto('cidade'), 'uf' => strtoupper(texto('uf')),
  ]);
  salvar_db($db);
  ir('clinicas', [], ['tipo' => 'sucesso', 'msg' => $novo ? 'Clínica cadastrada.' : 'Clínica atualizada.']);
}

// ---------- Funcionários (com login) ----------

function post_funcionarios(array &$db): array { return acao_lista($db, 'funcionarios', 'funcionarios', 'o'); }

function post_funcionarios_form(array &$db): array {
  $id = (int) ($_POST['id'] ?? 0);
  $novo = !isset($db['funcionarios'][$id]);
  $acesso = $novo ? (($_POST['acesso'] ?? '') === 'google' ? 'google' : 'email') : $db['funcionarios'][$id]['acesso'];
  $erros = obrigatorios(['nome' => 'o nome', 'email' => 'o e-mail', 'cpf' => 'o CPF', 'telefone' => 'o telefone', 'perfil' => 'o perfil']);
  if (!isset($erros['email']) && !filter_var(texto('email'), FILTER_VALIDATE_EMAIL)) $erros['email'] = 'E-mail inválido.';
  if (!isset($erros['perfil']) && !isset(PERFIS[texto('perfil')])) $erros['perfil'] = 'Perfil inválido.';
  $clinicas = array_values(array_intersect(array_map('intval', (array) ($_POST['clinicas'] ?? [])), array_keys($db['clinicas'])));
  if (!$clinicas) $erros['clinicas'] = 'Vincule o funcionário a pelo menos uma clínica.';
  foreach ($db['funcionarios'] as $fid => $f) {
    if ($fid === $id) continue;
    if (mb_strtolower($f['email']) === mb_strtolower(texto('email'))) $erros['email'] = 'Este e-mail já está cadastrado para outro funcionário.';
    if ($f['cpf'] !== '' && digitos($f['cpf']) === digitos(texto('cpf'))) $erros['cpf'] = 'Este CPF já está cadastrado para outro funcionário.';
  }
  if ($acesso === 'email') $erros += validar_senha($novo);
  if ($erros) return $erros;
  if ($novo) $id = novo_id($db['funcionarios']);
  $reg = array_merge($db['funcionarios'][$id] ?? ['id' => $id, 'ativo' => true, 'acesso' => $acesso, 'senha_hash' => ''], [
    'nome' => texto('nome'), 'email' => texto('email'), 'cpf' => texto('cpf'), 'telefone' => texto('telefone'),
    'especialidade' => in_array(texto('especialidade'), ESPECIALIDADES, true) ? texto('especialidade') : '',
    'perfil' => texto('perfil'), 'clinicas' => $clinicas,
  ]);
  if ($acesso === 'email' && ($_POST['senha'] ?? '') !== '') $reg['senha_hash'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
  $db['funcionarios'][$id] = $reg;
  salvar_db($db);
  ir('funcionarios', [], ['tipo' => 'sucesso', 'msg' => $novo ? 'Funcionário cadastrado. O login é o e-mail informado.' : 'Funcionário atualizado.']);
}

// ---------- Clientes e pets ----------

function post_clientes(array &$db): array { return acao_lista($db, 'clientes', 'clientes', 'o'); }

function post_clientes_form(array &$db): array {
  $id = (int) ($_POST['id'] ?? 0);
  $novo = !isset($db['clientes'][$id]);
  $erros = obrigatorios(['nome' => 'o nome', 'cpf' => 'o CPF', 'email' => 'o e-mail', 'telefone' => 'o telefone']);
  if (!isset($erros['email']) && !filter_var(texto('email'), FILTER_VALIDATE_EMAIL)) $erros['email'] = 'E-mail inválido.';
  if ($erros) return $erros;
  if ($novo) $id = novo_id($db['clientes']);
  $db['clientes'][$id] = array_merge($db['clientes'][$id] ?? ['id' => $id, 'ativo' => true, 'acesso' => ($_POST['acesso'] ?? '') === 'google' ? 'google' : 'email'], [
    'nome' => texto('nome'), 'cpf' => texto('cpf'), 'email' => texto('email'), 'telefone' => texto('telefone'), 'endereco' => texto('endereco'),
  ]);
  salvar_db($db);
  ir('clientes', [], ['tipo' => 'sucesso', 'msg' => $novo ? 'Cliente cadastrado.' : 'Cliente atualizado.']);
}

function post_pets(array &$db): array { return acao_lista($db, 'pets', 'pets', 'o'); }

function post_pets_form(array &$db): array {
  $id = (int) ($_POST['id'] ?? 0);
  $novo = !isset($db['pets'][$id]);
  $erros = obrigatorios(['cliente_id' => 'o cliente', 'nome' => 'o nome do pet', 'especie' => 'a espécie']);
  if (!isset($erros['cliente_id']) && !isset($db['clientes'][(int) $_POST['cliente_id']])) $erros['cliente_id'] = 'Cliente não encontrado.';
  $peso = str_replace(',', '.', texto('peso'));
  if ($peso !== '' && (!is_numeric($peso) || $peso < 0 || $peso > 999.99)) $erros['peso'] = 'Informe um peso entre 0 e 999,99 kg.';
  if ($erros) return $erros;
  if ($novo) $id = novo_id($db['pets']);
  $db['pets'][$id] = array_merge($db['pets'][$id] ?? ['id' => $id, 'ativo' => true], [
    'tutor_id' => (int) $_POST['cliente_id'], 'nome' => texto('nome'), 'especie' => texto('especie'), 'raca' => texto('raca'), 'peso' => $peso === '' ? 0.0 : round((float) $peso, 2),
  ]);
  salvar_db($db);
  ir('pets', ['cliente_id' => $db['pets'][$id]['tutor_id']], ['tipo' => 'sucesso', 'msg' => $novo ? 'Pet cadastrado.' : 'Pet atualizado.']);
}

// ---------- Consultas e exames ----------

function post_consultas(array &$db): array {
  $id = (int) ($_POST['id'] ?? 0);
  $acao = $_POST['acao'] ?? '';
  if (!isset($db['consultas'][$id])) ir('consultas', [], ['tipo' => 'erro', 'msg' => 'Consulta não encontrada.']);
  if ($acao === 'cancelar') $novo = 'Cancelada';
  elseif ($acao === 'status' && in_array($_POST['status'] ?? '', STATUS_CONSULTA, true)) $novo = $_POST['status'];
  else ir('consultas');
  $db['consultas'][$id]['status'] = $novo;
  salvar_db($db);
  ir('consultas', [], ['tipo' => 'sucesso', 'msg' => $novo === 'Cancelada' ? 'Consulta cancelada.' : "Status alterado para $novo."]);
}

function post_consultas_form(array &$db): array {
  $id = (int) ($_POST['id'] ?? 0);
  $novo = !isset($db['consultas'][$id]);
  $erros = obrigatorios(['clinica_id' => 'a clínica', 'veterinario_id' => 'o veterinário', 'data' => 'a data', 'hora' => 'o horário']);
  if ($novo) {
    $erros += obrigatorios(['cliente_id' => 'o cliente', 'pet_id' => 'o pet']);
    $pet = $db['pets'][(int) ($_POST['pet_id'] ?? 0)] ?? null;
    if (!isset($erros['pet_id']) && (!$pet || (string) $pet['tutor_id'] !== texto('cliente_id'))) $erros['pet_id'] = 'Escolha um pet do cliente selecionado.';
  }
  if (!isset($erros['clinica_id']) && !isset($db['clinicas'][(int) $_POST['clinica_id']])) $erros['clinica_id'] = 'Clínica não encontrada.';
  $vet = $db['funcionarios'][(int) ($_POST['veterinario_id'] ?? 0)] ?? null;
  if (!isset($erros['veterinario_id']) && (!$vet || $vet['perfil'] !== 'veterinario' || !$vet['ativo'])) $erros['veterinario_id'] = 'Escolha um veterinário ativo.';
  if (!isset($erros['data']) && !DateTime::createFromFormat('!Y-m-d', texto('data'))) $erros['data'] = 'Data inválida.';
  if (!isset($erros['hora']) && !DateTime::createFromFormat('!H:i', texto('hora'))) $erros['hora'] = 'Horário inválido.';
  if ($erros) return $erros;
  if ($novo) $id = novo_id($db['consultas']);
  $db['consultas'][$id] = array_merge($db['consultas'][$id] ?? ['id' => $id, 'pet_id' => (int) $_POST['pet_id'], 'status' => 'Pendente'], [
    'clinica_id' => (int) $_POST['clinica_id'], 'veterinario_id' => (int) $_POST['veterinario_id'], 'data' => texto('data'), 'hora' => texto('hora'), 'motivo' => texto('motivo'),
  ]);
  salvar_db($db);
  ir('consultas', [], ['tipo' => 'sucesso', 'msg' => $novo ? 'Consulta agendada.' : 'Consulta reagendada.']);
}

function post_exames_form(array &$db): array {
  $consulta_id = (int) ($_POST['consulta_id'] ?? 0);
  $voltar = fn(string $msg) => ir('exames-form', ['consulta_id' => $consulta_id], ['tipo' => 'erro', 'msg' => $msg]);
  $consulta = $db['consultas'][$consulta_id] ?? null;
  if (!$consulta || $consulta['status'] !== 'Concluída') $voltar('Só é possível adicionar exames a consultas concluídas.');
  $nomes = (array) ($_POST['nome_exame'] ?? []);
  $arq = $_FILES['arquivo_exame'] ?? ['name' => [], 'tmp_name' => [], 'error' => [], 'size' => []];
  $novos = [];
  foreach ($nomes as $i => $nome) {
    $ext = strtolower(pathinfo((string) ($arq['name'][$i] ?? ''), PATHINFO_EXTENSION));
    if (trim($nome) === '' || ($arq['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) $voltar('Informe o nome e anexe o arquivo de cada exame.');
    if (!in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'gif'], true)) $voltar('Formatos aceitos: PDF e imagens (JPG, PNG, WEBP, GIF).');
    if ($arq['size'][$i] > 10 * 1024 * 1024) $voltar('Cada arquivo pode ter no máximo 10 MB.');
    $novos[] = [trim($nome), $ext, $arq['tmp_name'][$i], basename($arq['name'][$i]), trim((string) ($_POST['obs_exame'][$i] ?? ''))];
  }
  if (!$novos) $voltar('Adicione ao menos um exame.');
  if (!is_dir(UPLOADS)) mkdir(UPLOADS, 0775, true);
  foreach ($novos as [$nome, $ext, $tmp, $original, $obs]) {
    $guardado = bin2hex(random_bytes(8)) . '.' . $ext;
    if (!move_uploaded_file($tmp, UPLOADS . '/' . $guardado)) $voltar('Não foi possível salvar o arquivo. Verifique a permissão da pasta uploads/.');
    $eid = novo_id($db['exames']);
    $db['exames'][$eid] = ['id' => $eid, 'consulta_id' => $consulta_id, 'pet_id' => $consulta['pet_id'], 'nome' => $nome, 'arquivo' => $guardado, 'arquivo_nome' => $original, 'obs' => $obs];
  }
  salvar_db($db);
  ir('exames', ['pet_id' => $consulta['pet_id']], ['tipo' => 'sucesso', 'msg' => count($novos) . ' exame(s) adicionado(s).']);
}

function tratar_post(string $rota, array &$db): array {
  static $mapa = [
    'login' => 'post_login', 'recuperar-senha' => 'post_recuperar', 'redefinir-senha' => 'post_redefinir',
    'clinicas' => 'post_clinicas', 'clinicas-form' => 'post_clinicas_form',
    'funcionarios' => 'post_funcionarios', 'funcionarios-form' => 'post_funcionarios_form',
    'clientes' => 'post_clientes', 'clientes-form' => 'post_clientes_form',
    'pets' => 'post_pets', 'pets-form' => 'post_pets_form',
    'consultas' => 'post_consultas', 'consultas-form' => 'post_consultas_form', 'exames-form' => 'post_exames_form',
  ];
  return isset($mapa[$rota]) ? $mapa[$rota]($db) : [];
}
