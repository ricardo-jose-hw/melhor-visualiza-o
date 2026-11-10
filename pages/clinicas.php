<?php
// Lista de clínicas (RF-09). BACKEND: trocar por query.
$equipe = fn(int $id) => count(array_filter($db['funcionarios'], fn($f) => in_array($id, $f['clinicas'], true)));
?>
<div class="page-head">
  <div>
    <h1>Clínicas</h1>
    <p class="sub">Unidades veterinárias cadastradas no sistema.</p>
  </div>
  <a class="btn btn-primario" href="<?= url('clinicas-form') ?>"><?= icone('mais') ?>Nova clínica</a>
</div>

<div class="card tabela-card">
  <div class="tabela-wrap">
    <table>
      <thead><tr><th>Clínica</th><th>CNPJ</th><th>Contato</th><th>Cidade</th><th>Equipe</th><th>Status</th><th><span class="sr-only">Ações</span></th></tr></thead>
      <tbody>
        <?php foreach ($db['clinicas'] as $id => $c): ?>
        <tr>
          <td><strong><?= e($c['nome']) ?></strong><small><?= e($c['endereco']) ?></small></td>
          <td><?= e($c['cnpj'] ?: '—') ?></td>
          <td><?= e($c['telefone']) ?><small><?= e($c['email'] ?: '—') ?></small></td>
          <td><?= e($c['cidade']) ?>/<?= e($c['uf']) ?></td>
          <td><a class="link" href="<?= url('funcionarios', ['clinica_id' => $id]) ?>"><?= $equipe($id) ?></a></td>
          <td><?= pill_ativo($c['ativo']) ?></td>
          <td class="acoes"><?= link_icone(url('clinicas-form', ['id' => $id]), 'editar', 'Editar ' . $c['nome']) ?><?= botao_status($id, $c['ativo'], $c['nome']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
