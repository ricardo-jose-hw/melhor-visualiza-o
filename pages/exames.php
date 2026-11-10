<?php
// Exames por pet (RF-35). BACKEND: trocar o filtro por query.
$pet_id = $_GET['pet_id'] ?? '';
$lista = array_filter($db['exames'], fn($x) => $pet_id === '' || (string) $x['pet_id'] === $pet_id);
$pets = array_column($db['pets'], 'nome', 'id');
?>
<div class="page-head">
  <div>
    <h1>Exames</h1>
    <p class="sub">Exames anexados às consultas concluídas.</p>
  </div>
  <a class="btn btn-primario" href="<?= url('exames-form') ?>"><?= icone('mais') ?>Adicionar exames</a>
</div>

<form class="card filtros" method="get">
  <input type="hidden" name="p" value="exames">
  <?= campo_select('pet_id', 'Pet', $pets, $pet_id, 'Todos os pets') ?>
  <div class="filtros-acoes">
    <button class="btn btn-primario" type="submit">Filtrar</button>
    <a class="btn btn-secundario" href="<?= url('exames') ?>">Limpar</a>
  </div>
</form>

<div class="card tabela-card">
  <?php if (!$lista): ?><p class="vazio">Nenhum exame encontrado.</p><?php else: ?>
  <div class="tabela-wrap">
    <table>
      <thead><tr><th>Exame</th><th>Pet</th><th>Consulta</th><th>Arquivo</th><th>Observações</th></tr></thead>
      <tbody>
        <?php foreach ($lista as $x): $c = $db['consultas'][$x['consulta_id']]; ?>
        <tr>
          <td><strong><?= e($x['nome']) ?></strong></td>
          <td><a class="link" href="<?= url('pets-historico', ['id' => $x['pet_id']]) ?>"><?= e($pets[$x['pet_id']]) ?></a></td>
          <td><?= data_br($c['data']) ?><small><?= e($db['clinicas'][$c['clinica_id']]['nome']) ?></small></td>
          <td><a class="link link-arquivo" href="uploads/<?= e($x['arquivo']) ?>" download="<?= e($x['arquivo_nome']) ?>"><?= icone('arquivo') ?><?= e($x['arquivo_nome']) ?></a></td>
          <td><?= e($x['obs'] ?: '—') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
