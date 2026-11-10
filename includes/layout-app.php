<?php $classe_body = 'app'; require __DIR__ . '/head.php'; ?>
<a class="skip-link" href="#conteudo">Pular para o conteúdo</a>
<div class="fundo-menu" data-menu-fechar></div>
<aside class="sidebar" id="menu" aria-label="Menu principal">
  <a class="sidebar-marca" href="<?= url('painel') ?>" aria-label="Arizona Pet Care — painel"><img src="assets/img/logo-branco.png" alt="Arizona Pet Care" width="150" height="54"></a>
  <nav class="sidebar-nav">
    <?php $grupo = ''; foreach (MENU as [$r, $rotulo, $ic, $g, $perfis]): if (!in_array($usuario['perfil'], $perfis, true)) continue; ?>
    <?php if ($g !== $grupo): $grupo = $g; ?><p class="sidebar-grupo"><?= e($g) ?></p><?php endif; ?>
    <a href="<?= url($r) ?>"<?= str_starts_with($rota, $r) ? ' class="ativo" aria-current="page"' : '' ?>><?= icone($ic) ?><span><?= e($rotulo) ?></span></a>
    <?php endforeach; ?>
  </nav>
  <div class="sidebar-companion" aria-hidden="true">
    <img src="assets/img/pets/cat.svg" alt="" width="200" height="150">
    <p>Uma boa companhia<br>para o seu dia.</p>
  </div>
  <a class="sidebar-sair" href="<?= url('sair') ?>"><?= icone('sair') ?><span>Sair</span></a>
</aside>
<div class="principal">
  <header class="topbar">
    <button class="menu-toggle" type="button" data-menu aria-controls="menu" aria-label="Abrir menu"><?= icone('menu') ?></button>
    <p class="topbar-data"><?= e(data_extenso()) ?></p>
    <div class="usuario">
      <span class="avatar" aria-hidden="true"><?= e(iniciais($usuario['nome'])) ?></span>
      <p><strong><?= e($usuario['nome']) ?></strong><small><?= e(PERFIS[$usuario['perfil']]) ?></small></p>
    </div>
  </header>
  <main id="conteudo">
    <?php if ($flash): ?><div class="alerta alerta-<?= e($flash['tipo']) ?>" role="status"><?= e($flash['msg']) ?></div><?php endif; ?>
    <?php require __DIR__ . '/../pages/' . $rota . '.php'; ?>
    <footer class="care-footer"><img src="assets/img/pets/paw.svg" alt="" aria-hidden="true" width="20" height="20"> Pequenos pacientes. Grandes cuidados.</footer>
  </main>
</div>
<dialog id="confirmar" aria-labelledby="confirmar-msg">
  <form method="dialog">
    <p id="confirmar-msg" data-msg></p>
    <div class="dialog-acoes">
      <button class="btn btn-secundario" value="cancelar">Voltar</button>
      <button class="btn btn-primario" value="ok">Confirmar</button>
    </div>
  </form>
</dialog>
<script src="assets/js/app.js" defer></script>
</body>
</html>
