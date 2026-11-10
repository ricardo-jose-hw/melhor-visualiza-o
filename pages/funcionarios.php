<?php
// Funcionários por clínica (RF-15). BACKEND: aplicar filtros na query.
$clinica_id = $_GET['clinica_id'] ?? '';
$q = trim($_GET['q'] ?? '');
$lista = array_filter($db['funcionarios'], fn($f) => ($clinica_id === '' || in_array((int) $clinica_id, $f['clinicas'], true))
  && ($q === '' || stripos($f['nome'] . ' ' . $f['cpf'] . ' ' . $f['email'], $q) !== false));
$clinicas = array_column($db['clinicas'], 'nome', 'id');
?>
<div class="page-head">
  <div>
    <h1>Funcionários</h1>
    <p class="sub">Equipe da clínica, perfis de acesso e vínculos.</p>
  </div>
  <a class="btn btn-primario" href="<?= url('funcionarios-form') ?>"><?= icone('mais') ?>Novo funcionário</a>
</div>

<form class="card filtros" method="get">
  <input type="hidden" name="p" value="funcionarios">
  <?= campo_select('clinica_id', 'Clínica', $clinicas, $clinica_id, 'Todas as clínicas') ?>
  <?= campo('q', 'Buscar', ['q' => $q], [], ['type' => 'search', 'placeholder' => 'Nome, CPF ou e-mail']) ?>
  <div class="filtros-acoes">
    <button class="btn btn-primario" type="submit">Filtrar</button>
    <a class="btn btn-secundario" href="<?= url('funcionarios') ?>">Limpar</a>
  </div>
</form>

<div class="card tabela-card">
  <?php if (!$lista): ?><p class="vazio">Nenhum funcionário encontrado.</p><?php else: ?>
  <div class="tabela-wrap">
    <table>
      <thead><tr><th>Funcionário</th><th>CPF</th><th>Telefone</th><th>Especialidade</th><th>Perfil</th><th>Clínicas</th><th>Status</th><th><span class="sr-only">Ações</span></th></tr></thead>
      <tbody>
        <?php foreach ($lista as $id => $f): ?>
        <tr>
          <td><strong><?= e($f['nome']) ?></strong><small><?= e($f['email']) ?></small></td>
          <td><?= e($f['cpf'] ?: '—') ?></td>
          <td><?= e($f['telefone'] ?: '—') ?></td>
          <td><?= e($f['especialidade'] ?: '—') ?></td>
          <td><?= e(PERFIS[$f['perfil']]) ?></td>
          <td><?= e(implode(', ', array_map(fn($i) => $clinicas[$i], $f['clinicas']))) ?></td>
          <td><?= pill_ativo($f['ativo']) ?></td>
          <td class="acoes"><?= link_icone(url('funcionarios-form', ['id' => $id]), 'editar', 'Editar ' . $f['nome']) ?><?= botao_status($id, $f['ativo'], $f['nome']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
