<?php
// Cadastrar (sem id) e editar (com ?id=) pet — RF-21 e RF-22. O cliente pode vir por ?cliente_id=.
$id = (int) ($_GET['id'] ?? 0);
$reg = $db['pets'][$id] ?? [];
$editar = $reg !== [];
$reg['cliente_id'] = $reg['tutor_id'] ?? $_GET['cliente_id'] ?? '';
$clientes = array_column(array_filter($db['clientes'], fn($c) => $c['ativo']), 'nome', 'id');
?>
<div class="page-head">
  <div>
    <h1><?= $editar ? 'Editar pet' : 'Novo pet' ?></h1>
    <p class="sub">Todo pet pertence a um cliente.</p>
  </div>
  <a class="btn btn-secundario" href="<?= url('pets') ?>">Voltar</a>
</div>

<form class="card form" method="post">
  <?php if ($editar): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>
  <div class="form-grid">
    <?= campo_select('cliente_id', 'Cliente (tutor)', $clientes, $reg['cliente_id'], 'Selecione', $erros, ['required' => true, 'classe' => 'campo-full']) ?>
    <?= campo('nome', 'Nome do pet', $reg, $erros, ['required' => true, 'maxlength' => 50]) ?>
    <?= campo('especie', 'Espécie', $reg, $erros, ['required' => true, 'maxlength' => 50, 'list' => 'especies']) ?>
    <?= campo('raca', 'Raça', $reg, $erros, ['maxlength' => 50]) ?>
    <?= campo('peso', 'Peso (kg)', $reg, $erros, ['type' => 'number', 'step' => '0.01', 'min' => '0', 'max' => '999.99']) ?>
  </div>
  <?= datalist('especies', ESPECIES) ?>
  <div class="form-acoes">
    <a class="btn btn-secundario" href="<?= url('pets') ?>">Cancelar</a>
    <button class="btn btn-primario" type="submit">Salvar pet</button>
  </div>
</form>
