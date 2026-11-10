<?php
// Cadastrar (sem id) e editar (com ?id=) cliente — RF-16 a RF-18.
$id = (int) ($_GET['id'] ?? 0);
$reg = $db['clientes'][$id] ?? [];
$editar = $reg !== [];
?>
<div class="page-head">
  <div>
    <h1><?= $editar ? 'Editar cliente' : 'Novo cliente' ?></h1>
    <p class="sub">Dados pessoais e de contato do tutor.</p>
  </div>
  <a class="btn btn-secundario" href="<?= url('clientes') ?>">Voltar</a>
</div>

<form class="card form" method="post">
  <?php if ($editar): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>
  <div class="form-grid">
    <?= campo('nome', 'Nome completo', $reg, $erros, ['required' => true, 'maxlength' => 50, 'autocomplete' => 'off']) ?>
    <?= campo('cpf', 'CPF', $reg, $erros, ['required' => true, 'inputmode' => 'numeric', 'data-mascara' => 'cpf', 'placeholder' => '000.000.000-00']) ?>
    <?= campo('email', 'E-mail', $reg, $erros, ['type' => 'email', 'required' => true]) ?>
    <?= campo('telefone', 'Telefone', $reg, $erros, ['type' => 'tel', 'required' => true, 'data-mascara' => 'telefone', 'placeholder' => '(00) 00000-0000']) ?>
    <?= campo('endereco', 'Endereço', $reg, $erros, ['maxlength' => 50, 'classe' => 'campo-full']) ?>
    <?= $editar ? '' : forma_acesso() ?>
  </div>
  <div class="form-acoes">
    <a class="btn btn-secundario" href="<?= url('clientes') ?>">Cancelar</a>
    <button class="btn btn-primario" type="submit">Salvar cliente</button>
  </div>
</form>
