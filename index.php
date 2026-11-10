<?php
// Ponto de entrada: sessão, login, permissão por perfil, POST e escolha da tela (?p=consultas).
session_start();
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/store.php';   // BACKEND: trocar por acesso ao banco
require __DIR__ . '/includes/acoes.php';   // BACKEND: trocar por controllers

$db = carregar_db();
$rota = $_GET['p'] ?? 'painel';

if ($rota === 'sair') {
  session_destroy();
  session_start();
  ir('login');
}
if (!isset(ROTAS[$rota])) {
  http_response_code(404);
  $rota = '404';
}

// Usuário logado: funcionário ativo cuja sessão está aberta.
$usuario = null;
$logado = $db['funcionarios'][$_SESSION['uid'] ?? 0] ?? null;
if ($logado && $logado['ativo']) $usuario = ['id' => $logado['id'], 'nome' => $logado['nome'], 'perfil' => $logado['perfil']];
if (!$usuario && !in_array($rota, ROTAS_PUBLICAS, true)) ir('login');

// Permissão por perfil, conforme o MENU (BACKEND: repetir esta checagem no servidor real).
foreach (MENU as [$prefixo, , , , $perfis]) {
  if ($usuario && str_starts_with($rota, $prefixo) && !in_array($usuario['perfil'], $perfis, true)) {
    http_response_code(403);
    $rota = '403';
  }
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$erros = $_SERVER['REQUEST_METHOD'] === 'POST' ? tratar_post($rota, $db) : [];

require __DIR__ . '/includes/layout-' . ROTAS[$rota][1] . '.php';
