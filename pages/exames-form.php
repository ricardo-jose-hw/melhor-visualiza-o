<?php
// Adicionar exames a uma consulta concluída (RF-33) com anexo PDF/imagem (RF-34).
// Sem ?consulta_id=, mostra as consultas concluídas para escolher.
$consulta_id = (int) ($_GET['consulta_id'] ?? 0);
$c = $db['consultas'][$consulta_id] ?? null;
$concluidas = array_filter($db['consultas'], fn($x) => $x['status'] === 'Concluída');
uasort($concluidas, fn($a, $b) => strcmp($b['data'], $a['data']));
$linha = '<div class="exame-linha"><label class="campo"><span class="rotulo">Exame</span><input name="nome_exame[]" list="exames-sugeridos" maxlength="100" required></label><label class="campo"><span class="rotulo">Observações</span><input name="obs_exame[]" maxlength="255"></label><label class="campo"><span class="rotulo">Arquivo (PDF ou imagem)</span><input name="arquivo_exame[]" type="file" accept=".pdf,image/*" required></label><button class="btn-icone perigo" type="button" data-remover-exame title="Remover exame" aria-label="Remover exame">' . icone('lixo') . '</button></div>';
?>
<div class="page-head">
  <div>
    <h1>Adicionar exames</h1>
    <p class="sub">Só consultas concluídas podem receber exames.</p>
  </div>
  <a class="btn btn-secundario" href="<?= url($c ? 'consultas' : 'exames') ?>">Voltar</a>
</div>

<?php if (!$c): ?>
<div class="card tabela-card">
  <?php if (!$concluidas): ?><p class="vazio">Nenhuma consulta concluída ainda.</p><?php else: ?>
  <ul class="lista-itens lista-links">
    <?php foreach ($concluidas as $id => $x): $pet = $db['pets'][$x['pet_id']]; ?>
    <li>
      <time class="item-hora" datetime="<?= e($x['data']) ?>"><?= data_br($x['data']) ?></time>
      <p class="item-info"><strong><?= e($pet['nome']) ?></strong><small><?= e($x['motivo']) ?> · <?= e($db['clientes'][$pet['tutor_id']]['nome']) ?></small></p>
      <a class="btn btn-secundario btn-sm" href="<?= url('exames-form', ['consulta_id' => $id]) ?>">Adicionar exames</a>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</div>
<?php elseif ($c['status'] !== 'Concluída'): ?>
<div class="alerta alerta-erro" role="alert">Esta consulta está como “<?= e($c['status']) ?>”. Conclua a consulta antes de adicionar exames.</div>
<?php else: $pet = $db['pets'][$c['pet_id']]; $existentes = array_filter($db['exames'], fn($x) => $x['consulta_id'] === $consulta_id); ?>
<section class="card resumo" aria-label="Consulta">
  <p><strong><?= e($pet['nome']) ?></strong><small><?= e($db['clientes'][$pet['tutor_id']]['nome']) ?></small></p>
  <p><strong><?= data_br($c['data']) ?> às <?= e($c['hora']) ?></strong><small><?= e($c['motivo']) ?></small></p>
  <p><strong><?= e($db['funcionarios'][$c['veterinario_id']]['nome']) ?></strong><small><?= e($db['clinicas'][$c['clinica_id']]['nome']) ?></small></p>
</section>

<?php if ($existentes): ?>
<section class="card" aria-labelledby="ja-anexados">
  <h2 id="ja-anexados">Já anexados</h2>
  <ul class="chips">
    <?php foreach ($existentes as $x): ?>
    <li><a href="uploads/<?= e($x['arquivo']) ?>" download="<?= e($x['arquivo_nome']) ?>"><?= icone('arquivo') ?><?= e($x['nome']) ?></a></li>
    <?php endforeach; ?>
  </ul>
</section>
<?php endif; ?>

<form class="card form" method="post" enctype="multipart/form-data">
  <input type="hidden" name="consulta_id" value="<?= $consulta_id ?>">
  <h2>Novos exames</h2>
  <div id="exames-lista"><?= $linha ?></div>
  <template id="exame-modelo"><?= $linha ?></template>
  <button class="btn btn-secundario btn-sm" type="button" data-add-exame><?= icone('mais') ?>Adicionar outro exame</button>
  <?= datalist('exames-sugeridos', EXAMES_SUGERIDOS) ?>
  <p class="dica">Formatos aceitos: PDF e imagens. Tamanho máximo definido pelo servidor.</p>
  <div class="form-acoes">
    <a class="btn btn-secundario" href="<?= url('consultas') ?>">Cancelar</a>
    <button class="btn btn-primario" type="submit">Salvar exames</button>
  </div>
</form>
<?php endif; ?>
