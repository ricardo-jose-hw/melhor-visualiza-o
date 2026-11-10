<?php // Nova senha a partir do link enviado por e-mail (RF-03). BACKEND: validar o token. ?>
<img class="auth-logo" src="assets/img/logo-marrom.png" alt="Arizona Pet Care" width="150" height="54">
<h1>Nova senha</h1>
<p class="sub">Escolha uma senha com pelo menos 8 caracteres.</p>
<form class="form" method="post">
  <input type="hidden" name="token" value="<?= e($_GET['token'] ?? '') ?>">
  <?= campo('senha', 'Nova senha', [], $erros, ['type' => 'password', 'required' => true, 'minlength' => 8, 'autocomplete' => 'new-password']) ?>
  <?= campo('senha_confirmacao', 'Confirmar nova senha', [], $erros, ['type' => 'password', 'required' => true, 'minlength' => 8, 'autocomplete' => 'new-password']) ?>
  <button class="btn btn-primario btn-bloco" type="submit">Salvar senha</button>
  <a class="link" href="<?= url('login') ?>">Voltar ao login</a>
</form>
