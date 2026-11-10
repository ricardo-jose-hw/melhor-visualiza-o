<?php
// Histórico médico completo do pet (RF-25): consultas e exames, da mais recente para a mais antiga.
$id = (int) ($_GET['id'] ?? 0);
$pet = $db['pets'][$id] ?? null;
if (!$pet): ?>
<div class="page-head"><div><h1>Pet não encontrado</h1></div><a class="btn btn-secundario" href="<?= url('pets') ?>">Voltar</a></div>
<?php return; endif;
$tutor = $db['clientes'][$pet['tutor_id']];
$consultas = array_filter($db['consultas'], fn($c) => $c['pet_id'] === $id);
uasort($consultas, fn($a, $b) => strcmp($b['data'] . $b['hora'], $a['data'] . $a['hora']));
$exames_de = fn(int $consulta_id) => array_filter($db['exames'], fn($x) => $x['consulta_id'] === $consulta_id);
?>
<div class="page-head">
  <div>
    <h1>Histórico de <?= e($pet['nome']) ?></h1>
    <p class="sub">Consultas e exames registrados para este pet.</p>
  </div>
  <div class="page-head-acoes">
    <a class="btn btn-secundario" href="<?= url('pets', ['cliente_id' => $pet['tutor_id']]) ?>">Voltar</a>
    <a class="btn btn-primario" href="<?= url('consultas-form', ['pet_id' => $id]) ?>"><?= icone('mais') ?>Agendar consulta</a>
  </div>
</div>

<section class="card pet-resumo" aria-label="Dados do pet">
  <span class="avatar avatar-grande" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($pet['nome'], 0, 1))) ?></span>
  <dl>
    <div><dt>Pet</dt><dd><?= e($pet['nome']) ?> <?= pill_ativo($pet['ativo']) ?></dd></div>
    <div><dt>Espécie e raça</dt><dd><?= e($pet['especie']) ?> · <?= e($pet['raca']) ?></dd></div>
    <div><dt>Peso</dt><dd><?= peso($pet['peso']) ?></dd></div>
    <div><dt>Tutor</dt><dd><?= e($tutor['nome']) ?> · <?= e($tutor['telefone']) ?></dd></div>
  </dl>
</section>

<section class="card" aria-labelledby="linha-titulo">
  <h2 id="linha-titulo">Linha do tempo</h2>
  <?php if (!$consultas): ?><p class="vazio">Este pet ainda não tem consultas.</p><?php else: ?>
  <ol class="linha-tempo">
    <?php foreach ($consultas as $cid => $c): ?>
    <li>
      <p class="lt-topo"><strong><?= data_br($c['data']) ?> às <?= e($c['hora']) ?></strong><?= pill($c['status']) ?></p>
      <p><?= e($c['motivo']) ?></p>
      <small><?= e($db['funcionarios'][$c['veterinario_id']]['nome']) ?> · <?= e($db['clinicas'][$c['clinica_id']]['nome']) ?></small>
      <?php if ($exames = $exames_de($cid)): ?>
      <ul class="chips">
        <?php foreach ($exames as $x): ?>
        <li><a href="uploads/<?= e($x['arquivo']) ?>" download="<?= e($x['arquivo_nome']) ?>" title="<?= e($x['obs'] ?: $x['arquivo_nome']) ?>"><?= icone('arquivo') ?><?= e($x['nome']) ?></a></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </li>
    <?php endforeach; ?>
  </ol>
  <?php endif; ?>
</section>
