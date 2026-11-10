<?php
// Indicadores do dia. BACKEND: trocar por consultas agregadas no banco.
$hoje = date('Y-m-d');
$meus = array_filter($db['consultas'], fn($c) => $usuario['perfil'] !== 'veterinario' || $c['veterinario_id'] === $usuario['id']); // veterinário vê só as suas
$agenda = array_filter($meus, fn($c) => $c['data'] === $hoje);
usort($agenda, fn($a, $b) => strcmp($a['hora'], $b['hora']));
$total = fn(array $lista, callable $f) => count(array_filter($lista, $f));
$indicadores = [
  ['calendario', 'Consultas hoje', count($agenda)],
  ['relogio', 'Aguardando confirmação', $total($meus, fn($c) => $c['status'] === 'Pendente')],
  ['pet', 'Pets ativos', $total($db['pets'], fn($p) => $p['ativo'])],
  ['clientes', 'Clientes ativos', $total($db['clientes'], fn($c) => $c['ativo'])],
];
$exames = array_slice(array_reverse($db['exames']), 0, 5);
?>
<div class="page-head">
  <div>
    <h1>Painel</h1>
    <p class="sub"><?= $usuario['perfil'] === 'veterinario' ? 'Resumo da sua agenda hoje.' : 'Resumo da operação da clínica hoje.' ?></p>
  </div>
  <a class="btn btn-primario" href="<?= url('consultas-form') ?>"><?= icone('mais') ?>Agendar consulta</a>
</div>

<section class="welcome-care" aria-labelledby="welcome-title">
  <div class="welcome-copy">
    <p class="eyebrow">NA COMPANHIA DO CUIDADO</p>
    <h2 id="welcome-title">Um bom dia começa com carinho.</h2>
    <p>A agenda em dia. O cuidado sempre por perto.</p>
  </div>
  <div class="welcome-pets" aria-hidden="true">
    <span class="welcome-orbit"></span>
    <img class="welcome-dog" src="assets/img/pets/dog.svg" alt="" width="260" height="260">
    <img class="welcome-cat" src="assets/img/pets/cat.svg" alt="" width="200" height="150">
    <span class="welcome-star">✧</span>
  </div>
</section>

<section class="kpis" aria-label="Indicadores">
  <?php foreach ($indicadores as [$ic, $rotulo, $valor]): ?>
  <article class="kpi">
    <span class="kpi-icone"><?= icone($ic) ?></span>
    <p><strong><?= $valor ?></strong><span><?= e($rotulo) ?></span></p>
  </article>
  <?php endforeach; ?>
</section>

<div class="grid-2">
  <section class="card" aria-labelledby="agenda-titulo">
    <div class="card-head">
      <h2 id="agenda-titulo">Agenda de hoje</h2>
      <a class="link" href="<?= url('consultas', ['data' => $hoje]) ?>">Ver todas</a>
    </div>
    <?php if (!$agenda): ?><p class="vazio">Nenhuma consulta agendada para hoje.</p><?php else: ?>
    <ul class="lista-itens">
      <?php foreach ($agenda as $c): $pet = $db['pets'][$c['pet_id']]; ?>
      <li>
        <time class="item-hora" datetime="<?= e($c['data'] . 'T' . $c['hora']) ?>"><?= e($c['hora']) ?></time>
        <p class="item-info"><strong><?= e($pet['nome']) ?></strong><small><?= e($db['clientes'][$pet['tutor_id']]['nome']) ?> · <?= e($db['funcionarios'][$c['veterinario_id']]['nome']) ?></small></p>
        <?= pill($c['status']) ?>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </section>

  <section class="card" aria-labelledby="exames-titulo">
    <div class="card-head">
      <h2 id="exames-titulo">Exames recentes</h2>
      <a class="link" href="<?= url('exames') ?>">Ver todos</a>
    </div>
    <?php if (!$exames): ?><p class="vazio">Nenhum exame anexado ainda.</p><?php else: ?>
    <ul class="lista-itens">
      <?php foreach ($exames as $x): ?>
      <li>
        <span class="item-icone"><?= icone('arquivo') ?></span>
        <p class="item-info"><strong><?= e($x['nome']) ?></strong><small><?= e($db['pets'][$x['pet_id']]['nome']) ?> · <?= e($x['arquivo_nome']) ?></small></p>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </section>
</div>
