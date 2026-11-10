<?php
// Filtros do RF-30. BACKEND: aplicar os mesmos filtros na query (aqui filtramos o array de exemplo).
$f = [
  'data' => $_GET['data'] ?? '', 'cliente' => trim($_GET['cliente'] ?? ''), 'pet' => trim($_GET['pet'] ?? ''),
  'clinica_id' => $_GET['clinica_id'] ?? '', 'status' => $_GET['status'] ?? '',
];
$lista = array_filter($db['consultas'], function ($c) use ($f, $db, $usuario) {
  $pet = $db['pets'][$c['pet_id']];
  return ($usuario['perfil'] !== 'veterinario' || $c['veterinario_id'] === $usuario['id'])
    && ($f['data'] === '' || $c['data'] === $f['data'])
    && ($f['cliente'] === '' || stripos($db['clientes'][$pet['tutor_id']]['nome'], $f['cliente']) !== false)
    && ($f['pet'] === '' || stripos($pet['nome'], $f['pet']) !== false)
    && ($f['clinica_id'] === '' || (string) $c['clinica_id'] === $f['clinica_id'])
    && ($f['status'] === '' || $c['status'] === $f['status']);
});
uasort($lista, fn($a, $b) => strcmp($b['data'] . $b['hora'], $a['data'] . $a['hora']));
$clinicas = array_column($db['clinicas'], 'nome', 'id');
?>
<div class="page-head">
  <div>
    <h1>Consultas</h1>
    <p class="sub">Agende, reagende e acompanhe o status de cada atendimento.</p>
  </div>
  <a class="btn btn-primario" href="<?= url('consultas-form') ?>"><?= icone('mais') ?>Agendar consulta</a>
</div>

<form class="card filtros" method="get">
  <input type="hidden" name="p" value="consultas">
  <?= campo('data', 'Data', $f, [], ['type' => 'date']) ?>
  <?= campo('cliente', 'Cliente', $f, [], ['type' => 'search', 'placeholder' => 'Nome do cliente']) ?>
  <?= campo('pet', 'Pet', $f, [], ['type' => 'search', 'placeholder' => 'Nome do pet']) ?>
  <?= campo_select('clinica_id', 'Clínica', $clinicas, $f['clinica_id'], 'Todas') ?>
  <?= campo_select('status', 'Status', STATUS_CONSULTA, $f['status'], 'Todos') ?>
  <div class="filtros-acoes">
    <button class="btn btn-primario" type="submit">Filtrar</button>
    <a class="btn btn-secundario" href="<?= url('consultas') ?>">Limpar</a>
  </div>
</form>

<div class="card tabela-card">
  <?php if (!$lista): ?><p class="vazio">Nenhuma consulta encontrada com esses filtros.</p><?php else: ?>
  <div class="tabela-wrap">
    <table>
      <thead><tr><th>Data e hora</th><th>Pet</th><th>Cliente</th><th>Veterinário</th><th>Clínica</th><th>Status</th><th><span class="sr-only">Ações</span></th></tr></thead>
      <tbody>
        <?php foreach ($lista as $id => $c): $pet = $db['pets'][$c['pet_id']]; $aberta = in_array($c['status'], ['Pendente', 'Confirmada'], true); ?>
        <tr>
          <td><strong><?= data_br($c['data']) ?></strong><small><?= e($c['hora']) ?></small></td>
          <td><?= e($pet['nome']) ?></td>
          <td><?= e($db['clientes'][$pet['tutor_id']]['nome']) ?></td>
          <td><?= e($db['funcionarios'][$c['veterinario_id']]['nome']) ?></td>
          <td><?= e($clinicas[$c['clinica_id']]) ?></td>
          <td><form method="post"><input type="hidden" name="acao" value="status"><input type="hidden" name="id" value="<?= $id ?>"><select class="pill-select pill-<?= CLASSE_STATUS[$c['status']] ?>" name="status" aria-label="Status da consulta de <?= e($pet['nome']) ?>" data-autosubmit><?= opcoes(STATUS_CONSULTA, $c['status']) ?></select></form></td>
          <td class="acoes">
            <?php if ($aberta): ?>
            <?= link_icone(url('consultas-form', ['id' => $id]), 'reagendar', 'Reagendar consulta de ' . $pet['nome']) ?>
            <?= botao_acao($id, 'cancelar', 'cancelar', 'Cancelar consulta de ' . $pet['nome'], 'Cancelar a consulta de ' . $pet['nome'] . '? O cliente será notificado.', true) ?>
            <?php elseif ($c['status'] === 'Concluída'): ?>
            <?= link_icone(url('exames-form', ['consulta_id' => $id]), 'exames', 'Adicionar exames à consulta de ' . $pet['nome']) ?>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
