<?php
// Cadastrar (sem id) e editar (com ?id=) funcionário — RF-10 a RF-13. Regra: CPF e e-mail únicos entre funcionários.
// O e-mail é o login; a senha só é pedida no acesso por e-mail e senha.
$id = (int) ($_GET['id'] ?? 0);
$reg = $db['funcionarios'][$id] ?? [];
$editar = $reg !== [];
$marcadas = array_map('intval', (array) ($_POST['clinicas'] ?? $reg['clinicas'] ?? []));
$com_senha = !$editar || $reg['acesso'] === 'email'; // funcionário cadastrado via Google não usa senha
?>
<div class="page-head">
  <div>
    <h1><?= $editar ? 'Editar funcionário' : 'Novo funcionário' ?></h1>
    <p class="sub">CPF e e-mail devem ser únicos. O e-mail é o login do funcionário.</p>
  </div>
  <a class="btn btn-secundario" href="<?= url('funcionarios') ?>">Voltar</a>
</div>

<form class="card form" method="post">
  <?php if ($editar): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>
  <div class="form-grid">
    <?= campo('nome', 'Nome completo', $reg, $erros, ['required' => true, 'maxlength' => 100, 'autocomplete' => 'off']) ?>
    <?= campo('email', 'E-mail', $reg, $erros, ['type' => 'email', 'required' => true]) ?>
    <?= campo('cpf', 'CPF', $reg, $erros, ['required' => true, 'inputmode' => 'numeric', 'data-mascara' => 'cpf', 'placeholder' => '000.000.000-00']) ?>
    <?= campo('telefone', 'Telefone', $reg, $erros, ['type' => 'tel', 'required' => true, 'data-mascara' => 'telefone', 'placeholder' => '(00) 00000-0000']) ?>
    <?= campo_select('especialidade', 'Especialidade', ESPECIALIDADES, $reg['especialidade'] ?? '', 'Não se aplica', $erros) ?>
    <?= campo_select('perfil', 'Perfil de acesso', PERFIS, $reg['perfil'] ?? '', 'Selecione', $erros, ['required' => true]) ?>
    <fieldset class="campo campo-full">
      <legend>Clínicas vinculadas</legend>
      <div class="opcoes">
        <?php foreach ($db['clinicas'] as $cid => $cl): if (!$cl['ativo']) continue; ?>
        <label><input type="checkbox" name="clinicas[]" value="<?= $cid ?>"<?= in_array($cid, $marcadas, true) ? ' checked' : '' ?>> <?= e($cl['nome']) ?></label>
        <?php endforeach; ?>
      </div>
      <small class="dica">Selecione uma ou mais clínicas.</small>
      <?= erro($erros, 'clinicas') ?>
    </fieldset>
    <?= $editar ? '' : forma_acesso($_POST['acesso'] ?? 'email') ?>
    <?php if ($com_senha): ?>
    <div class="form-grid campo-full" data-senha>
      <?= campo('senha', $editar ? 'Nova senha (deixe em branco para manter)' : 'Senha de acesso', [], $erros, ['type' => 'password', 'required' => !$editar, 'minlength' => 8, 'autocomplete' => 'new-password']) ?>
      <?= campo('senha_confirmacao', 'Confirmar senha', [], $erros, ['type' => 'password', 'required' => !$editar, 'minlength' => 8, 'autocomplete' => 'new-password']) ?>
      <p class="nota campo-full"><?= icone('funcionarios') ?>O funcionário entra no sistema com este e-mail e a senha definida aqui. O que ele enxerga depende do perfil de acesso.</p>
    </div>
    <?php endif; ?>
  </div>
  <div class="form-acoes">
    <a class="btn btn-secundario" href="<?= url('funcionarios') ?>">Cancelar</a>
    <button class="btn btn-primario" type="submit">Salvar funcionário</button>
  </div>
</form>
