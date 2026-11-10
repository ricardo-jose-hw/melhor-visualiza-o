<?php
// Funções de apoio para montar o HTML das telas.

function e($v): string { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }

function url(string $rota, array $params = []): string { return 'index.php?' . http_build_query(['p' => $rota] + $params); }

function icone(string $nome): string {
  static $d = [
    'painel'       => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>',
    'calendario'   => '<path d="M8 2v4M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>',
    'clientes'     => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
    'pet'          => '<circle cx="11" cy="4" r="2"/><circle cx="18" cy="8" r="2"/><circle cx="20" cy="16" r="2"/><path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z"/>',
    'exames'       => '<path d="M10 2v7.5a2 2 0 0 1-.2.9L4.7 20.5a1 1 0 0 0 .9 1.5h12.8a1 1 0 0 0 .9-1.5l-5.1-10.1a2 2 0 0 1-.2-.9V2M8.5 2h7M7 16h10"/>',
    'funcionarios' => '<path d="M11 2v2M5 2v2M5 3H4a2 2 0 0 0-2 2v4a6 6 0 0 0 12 0V5a2 2 0 0 0-2-2h-1"/><path d="M8 15a6 6 0 0 0 12 0v-3"/><circle cx="20" cy="10" r="2"/>',
    'clinicas'     => '<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2M10 6h4M10 10h4M10 14h4M10 18h4"/>',
    'sair'         => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
    'menu'         => '<path d="M4 6h16M4 12h16M4 18h16"/>',
    'mais'         => '<path d="M5 12h14M12 5v14"/>',
    'editar'       => '<path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>',
    'inativar'     => '<circle cx="12" cy="12" r="10"/><path d="m4.9 4.9 14.2 14.2"/>',
    'reativar'     => '<path d="M12 2v10"/><path d="M18.4 6.6a9 9 0 1 1-12.8 0"/>',
    'historico'    => '<path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5Z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/>',
    'reagendar'    => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>',
    'cancelar'     => '<circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/>',
    'relogio'      => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
    'sino'         => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9M10.3 21a1.94 1.94 0 0 0 3.4 0"/>',
    'arquivo'      => '<path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/>',
    'lixo'         => '<path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
  ];
  return '<svg class="icone" viewBox="0 0 24 24" aria-hidden="true">' . $d[$nome] . '</svg>';
}

// <option> a partir de lista simples (valor = rótulo) ou de [valor => rótulo].
function opcoes(array $lista, $sel = '', ?string $vazio = null): string {
  if (array_is_list($lista)) $lista = array_combine($lista, $lista);
  $h = $vazio === null ? '' : '<option value="">' . e($vazio) . '</option>';
  foreach ($lista as $v => $r) $h .= '<option value="' . e($v) . '"' . ((string) $v === (string) $sel ? ' selected' : '') . '>' . e($r) . '</option>';
  return $h;
}

function datalist(string $id, array $lista): string {
  return '<datalist id="' . $id . '">' . implode('', array_map(fn($v) => '<option value="' . e($v) . '"></option>', $lista)) . '</datalist>';
}

function erro(array $erros, string $campo): string { return isset($erros[$campo]) ? '<small class="erro">' . e($erros[$campo]) . '</small>' : ''; }

function atributos(array $attr): string {
  $h = '';
  foreach ($attr as $k => $v) if ($v !== false && $v !== null) $h .= $v === true ? " $k" : " $k=\"" . e($v) . '"';
  return $h;
}

// Campo de texto (input) com rótulo e mensagem de erro. $attr: type, required, classe e qualquer atributo HTML.
function campo(string $nome, string $rotulo, array $reg = [], array $erros = [], array $attr = []): string {
  $tipo = $attr['type'] ?? 'text';
  $classe = $attr['classe'] ?? '';
  unset($attr['type'], $attr['classe']);
  return '<div class="campo ' . $classe . '"><label for="' . $nome . '">' . e($rotulo) . '</label><input id="' . $nome . '" name="' . $nome . '" type="' . $tipo . '" value="' . e($tipo === 'password' ? '' : ($_POST[$nome] ?? $reg[$nome] ?? '')) . '"' . atributos($attr) . '>' . erro($erros, $nome) . '</div>';
}

function campo_select(string $nome, string $rotulo, array $lista, $sel = '', ?string $vazio = null, array $erros = [], array $attr = []): string {
  $classe = $attr['classe'] ?? '';
  unset($attr['classe']);
  $sel = $_POST[$nome] ?? $sel;
  return '<div class="campo ' . $classe . '"><label for="' . $nome . '">' . e($rotulo) . '</label><select id="' . $nome . '" name="' . $nome . '"' . atributos($attr) . '>' . opcoes($lista, $sel, $vazio) . '</select>' . erro($erros, $nome) . '</div>';
}

// Cadastro "via e-mail" ou "via Google" (RF-05/06, RF-10/11, RF-16/17). Só aparece em cadastros novos.
function forma_acesso(string $sel = 'email'): string {
  return '<fieldset class="campo campo-full"><legend>Forma de acesso</legend><div class="opcoes"><label><input type="radio" name="acesso" value="email"' . ($sel === 'email' ? ' checked' : '') . '> E-mail e senha</label><label><input type="radio" name="acesso" value="google"' . ($sel === 'google' ? ' checked' : '') . '> Conta Google</label></div><small class="dica">E-mail e senha: o e-mail informado é o login, com senha própria. Conta Google: entra com a conta do e-mail informado.</small></fieldset>';
}

const CLASSE_STATUS = ['Pendente' => 'pendente', 'Confirmada' => 'confirmada', 'Concluída' => 'concluida', 'Cancelada' => 'cancelada', 'Ativo' => 'confirmada', 'Inativo' => 'cancelada'];

function pill(string $status): string { return '<span class="pill pill-' . CLASSE_STATUS[$status] . '">' . e($status) . '</span>'; }

function pill_ativo(bool $ativo): string { return pill($ativo ? 'Ativo' : 'Inativo'); }

function link_icone(string $href, string $icone, string $titulo): string {
  return '<a class="btn-icone" href="' . e($href) . '" title="' . e($titulo) . '" aria-label="' . e($titulo) . '">' . icone($icone) . '</a>';
}

// Botão de ação que envia POST (inativar, reativar, cancelar) com confirmação em diálogo.
function botao_acao(int $id, string $acao, string $icone, string $titulo, string $confirmar, bool $perigo = false): string {
  return '<form method="post"><input type="hidden" name="acao" value="' . $acao . '"><input type="hidden" name="id" value="' . $id . '"><button class="btn-icone' . ($perigo ? ' perigo' : '') . '" type="submit" title="' . e($titulo) . '" aria-label="' . e($titulo) . '" data-confirmar="' . e($confirmar) . '">' . icone($icone) . '</button></form>';
}

// Inativar/reativar (RF-08, RF-14, RF-19, RF-23).
function botao_status(int $id, bool $ativo, string $nome): string {
  return $ativo
    ? botao_acao($id, 'inativar', 'inativar', "Inativar $nome", "Inativar $nome? O cadastro e o histórico são mantidos.", true)
    : botao_acao($id, 'reativar', 'reativar', "Reativar $nome", "Reativar $nome?");
}

function data_br(string $iso): string { return date('d/m/Y', strtotime($iso)); }

function data_extenso(): string {
  $dias = ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'];
  $meses = [1 => 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
  return $dias[(int) date('w')] . ', ' . date('j') . ' de ' . $meses[(int) date('n')] . ' de ' . date('Y');
}

function iniciais(string $nome): string {
  $p = array_values(array_filter(preg_split('/\s+/', trim($nome)), fn($x) => !in_array(rtrim($x, '.'), ['Dr', 'Dra'], true)));
  return mb_strtoupper(mb_substr($p[0] ?? '', 0, 1) . (count($p) > 1 ? mb_substr($p[count($p) - 1], 0, 1) : ''));
}

function peso(float $kg): string { return number_format($kg, 2, ',', '.') . ' kg'; }
