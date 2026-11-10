<?php
// Lista de pets, filtrável por cliente (RF-24). BACKEND: aplicar na query.
$cliente_id = $_GET['cliente_id'] ?? '';
$q = trim($_GET['q'] ?? '');
$lista = array_filter($db['pets'], fn($p) => ($cliente_id === '' || (string) $p['tutor_id'] === $cliente_id)
  && ($q === '' || stripos($p['nome'] . ' ' . $p['especie'] . ' ' . $p['raca'], $q) !== false));
$clientes = array_column($db['clientes'], 'nome', 'id');
?>
<div class="page-head">
  <div>
    <h1>Pets</h1>
    <p class="sub">Pets cadastrados por cliente, com acesso ao histórico médico.</p>
  </div>
  <a class="btn btn-primario" href="<?= url('pets-form', $cliente_id !== '' ? ['cliente_id' => $cliente_id] : []) ?>"><?= icone('mais') ?>Novo pet</a>
</div>

<form class="card filtros" method="get">
  <input type="hidden" name="p" value="pets">
  <?= campo_select('cliente_id', 'Cliente', $clientes, $cliente_id, 'Todos os clientes') ?>
  <?= campo('q', 'Buscar', ['q' => $q], [], ['type' => 'search', 'placeholder' => 'Nome, espécie ou raça']) ?>
  <div class="filtros-acoes">
    <button class="btn btn-primario" type="submit">Filtrar</button>
    <a class="btn btn-secundario" href="<?= url('pets') ?>">Limpar</a>
  </div>
</form>

<div class="card tabela-card">
  <?php if (!$lista): ?><p class="vazio">Nenhum pet encontrado.</p><?php else: ?>
  <div class="tabela-wrap">
    <table>
      <thead><tr><th>Pet</th><th>Espécie e raça</th><th>Peso</th><th>Tutor</th><th>Status</th><th><span class="sr-only">Ações</span></th></tr></thead>
      <tbody>
        <?php foreach ($lista as $id => $p): ?>
        <tr>
          <td><strong><?= e($p['nome']) ?></strong></td>
          <td><?= e($p['especie']) ?><small><?= e($p['raca']) ?></small></td>
          <td><?= peso($p['peso']) ?></td>
          <td><?= e($clientes[$p['tutor_id']]) ?></td>
          <td><?= pill_ativo($p['ativo']) ?></td>
          <td class="acoes"><?= link_icone(url('pets-historico', ['id' => $id]), 'historico', 'Histórico médico de ' . $p['nome']) ?><?= link_icone(url('pets-form', ['id' => $id]), 'editar', 'Editar ' . $p['nome']) ?><?= botao_status($id, $p['ativo'], $p['nome']) ?><?= botao_acao($id, 'excluir', 'lixo', 'Excluir ' . $p['nome'], 'Excluir ' . $p['nome'] . ' definitivamente? Isso apaga todo o histórico do pet (consultas e exames). Não dá para desfazer.', true) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
