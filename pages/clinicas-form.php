<?php
// Cadastrar (sem id) e editar (com ?id=) clínica — RF-05 a RF-07.
$id = (int) ($_GET['id'] ?? 0);
$reg = $db['clinicas'][$id] ?? [];
$editar = $reg !== [];
?>
<div class="page-head">
  <div>
    <h1><?= $editar ? 'Editar clínica' : 'Nova clínica' ?></h1>
    <p class="sub">Dados da unidade veterinária.</p>
  </div>
  <a class="btn btn-secundario" href="<?= url('clinicas') ?>">Voltar</a>
</div>

<form class="card form" method="post">
  <?php if ($editar): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>
  <div class="form-grid">
    <?= campo('nome', 'Nome da clínica', $reg, $erros, ['required' => true, 'maxlength' => 100, 'classe' => 'campo-full']) ?>
    <?= campo('cnpj', 'CNPJ', $reg, $erros, ['required' => true, 'inputmode' => 'numeric', 'data-mascara' => 'cnpj', 'placeholder' => '00.000.000/0000-00']) ?>
    <?= campo('telefone', 'Telefone', $reg, $erros, ['type' => 'tel', 'required' => true, 'data-mascara' => 'telefone', 'placeholder' => '(00) 00000-0000']) ?>
    <?= campo('email', 'E-mail', $reg, $erros, ['type' => 'email', 'required' => true, 'classe' => 'campo-full']) ?>
    <?= campo('endereco', 'Endereço', $reg, $erros, ['required' => true, 'maxlength' => 150, 'classe' => 'campo-full']) ?>
    <?= campo('cidade', 'Cidade', $reg, $erros, ['required' => true, 'maxlength' => 80]) ?>
    <?= campo('uf', 'UF', $reg, $erros, ['required' => true, 'maxlength' => 2, 'pattern' => '[A-Za-z]{2}', 'style' => 'text-transform:uppercase']) ?>
    <?= $editar ? '' : forma_acesso() ?>
  </div>
  <div class="form-acoes">
    <a class="btn btn-secundario" href="<?= url('clinicas') ?>">Cancelar</a>
    <button class="btn btn-primario" type="submit">Salvar clínica</button>
  </div>
</form>
