<?php
// Busca por nome, CPF ou e-mail. BACKEND: aplicar na query.
$q = trim($_GET['q'] ?? '');
$lista = array_filter($db['clientes'], fn($c) => $q === '' || stripos($c['nome'] . ' ' . $c['cpf'] . ' ' . $c['email'], $q) !== false);
$qtd_pets = fn(int $id) => count(array_filter($db['pets'], fn($p) => $p['tutor_id'] === $id));
?>
<div class="page-head">
  <div>
    <h1>Clientes</h1>
    <p class="sub">Tutores cadastrados e seus pets.</p>
  </div>
  <a class="btn btn-primario" href="<?= url('clientes-form') ?>"><?= icone('mais') ?>Novo cliente</a>
</div>

<form class="card filtros" method="get">
  <input type="hidden" name="p" value="clientes">
  <?= campo('q', 'Buscar', ['q' => $q], [], ['type' => 'search', 'placeholder' => 'Nome, CPF ou e-mail', 'classe' => 'filtro-largo']) ?>
  <div class="filtros-acoes">
    <button class="btn btn-primario" type="submit">Buscar</button>
    <a class="btn btn-secundario" href="<?= url('clientes') ?>">Limpar</a>
  </div>
</form>

<div class="card tabela-card">
  <?php if (!$lista): ?><p class="vazio">Nenhum cliente encontrado.</p><?php else: ?>
  <div class="tabela-wrap">
    <table>
      <thead><tr><th>Cliente</th><th>CPF</th><th>Telefone</th><th>Pets</th><th>Status</th><th><span class="sr-only">Ações</span></th></tr></thead>
      <tbody>
        <?php foreach ($lista as $id => $c): ?>
        <tr>
          <td><strong><?= e($c['nome']) ?></strong><small><?= e($c['email']) ?></small></td>
          <td><?= e($c['cpf']) ?></td>
          <td><?= e($c['telefone']) ?></td>
          <td><a class="link" href="<?= url('pets', ['cliente_id' => $id]) ?>"><?= $qtd_pets($id) ?></a></td>
          <td><?= pill_ativo($c['ativo']) ?></td>
          <td class="acoes"><?= link_icone(url('pets', ['cliente_id' => $id]), 'pet', 'Ver pets de ' . $c['nome']) ?><?= link_icone(url('clientes-form', ['id' => $id]), 'editar', 'Editar ' . $c['nome']) ?><?= botao_status($id, $c['ativo'], $c['nome']) ?><?= botao_acao($id, 'excluir', 'lixo', 'Excluir ' . $c['nome'], 'Excluir ' . $c['nome'] . ' definitivamente? Isso apaga também os pets, as consultas e os exames deste cliente. Não dá para desfazer.', true) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
