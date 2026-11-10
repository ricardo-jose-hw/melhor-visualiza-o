<?php // Pedido de redefinição de senha (RF-03). BACKEND: enviar e-mail com link para ?p=redefinir-senha&token=... ?>
<img class="auth-logo" src="assets/img/logo-marrom.png" alt="Arizona Pet Care" width="150" height="54">
<h1>Recuperar senha</h1>
<p class="sub">Informe o e-mail da sua conta. Enviaremos um link para criar uma nova senha.</p>
<form class="form" method="post">
  <?= campo('email', 'E-mail', [], $erros, ['type' => 'email', 'required' => true, 'autocomplete' => 'username']) ?>
  <button class="btn btn-primario btn-bloco" type="submit">Enviar link</button>
  <a class="link" href="<?= url('login') ?>">Voltar ao login</a>
</form>
