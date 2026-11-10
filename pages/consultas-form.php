<?php
// Agendar (sem id) e reagendar (com ?id=) — RF-26 e RF-27. O pet pode vir pré-selecionado por ?pet_id=.
$id = (int) ($_GET['id'] ?? 0);
$reg = $db['consultas'][$id] ?? [];
$reagendar = $reg !== [];
$pet_sel = (int) ($reg['pet_id'] ?? $_POST['pet_id'] ?? $_GET['pet_id'] ?? 0);
$cliente_sel = $db['pets'][$pet_sel]['tutor_id'] ?? '';
$travado = $reagendar ? ['disabled' => true] : [];
$clinicas = array_column(array_filter($db['clinicas'], fn($c) => $c['ativo']), 'nome', 'id');
$vets = array_column(array_filter($db['funcionarios'], fn($f) => $f['ativo'] && $f['perfil'] === 'veterinario'), 'nome', 'id');
$clientes = array_column(array_filter($db['clientes'], fn($c) => $c['ativo']), 'nome', 'id');
?>
<div class="page-head">
  <div>
    <h1><?= $reagendar ? 'Reagendar consulta' : 'Agendar consulta' ?></h1>
    <p class="sub"><?= $reagendar ? 'Altere data, horário ou veterinário. Cliente e pet não mudam.' : 'Escolha o cliente, o pet e o horário do atendimento.' ?></p>
  </div>
  <a class="btn btn-secundario" href="<?= url('consultas') ?>">Voltar</a>
</div>

<form class="card form" method="post">
  <?php if ($reagendar): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>
  <div class="form-grid">
    <?= campo_select('clinica_id', 'Clínica', $clinicas, $reg['clinica_id'] ?? '', 'Selecione', $erros, ['required' => true]) ?>
    <?= campo_select('veterinario_id', 'Veterinário', $vets, $reg['veterinario_id'] ?? '', 'Selecione', $erros, ['required' => true]) ?>
    <?= campo_select('cliente_id', 'Cliente', $clientes, $cliente_sel, 'Selecione', $erros, ['required' => true, 'data-filtra-pets' => true] + $travado) ?>
    <div class="campo">
      <label for="pet_id">Pet</label>
      <select id="pet_id" name="pet_id" required<?= $reagendar ? ' disabled' : '' ?>>
        <option value="">Selecione</option>
        <?php foreach ($db['pets'] as $p): if (!$p['ativo']) continue; ?>
        <option value="<?= $p['id'] ?>" data-tutor="<?= $p['tutor_id'] ?>"<?= $p['id'] === $pet_sel ? ' selected' : '' ?>><?= e($p['nome']) ?> (<?= e($p['especie']) ?>)</option>
        <?php endforeach; ?>
      </select>
      <?= erro($erros, 'pet_id') ?>
    </div>
    <?= campo('data', 'Data', $reg, $erros, ['type' => 'date', 'required' => true, 'min' => date('Y-m-d')]) ?>
    <?= campo('hora', 'Horário', $reg, $erros, ['type' => 'time', 'required' => true, 'step' => 900]) ?>
    <div class="campo campo-full">
      <label for="motivo">Motivo / observações</label>
      <textarea id="motivo" name="motivo" rows="3"><?= e($_POST['motivo'] ?? $reg['motivo'] ?? '') ?></textarea>
    </div>
    <p class="nota campo-full"><?= icone('sino') ?>O cliente é notificado automaticamente por e-mail sobre o <?= $reagendar ? 'reagendamento' : 'agendamento' ?>.</p>
  </div>
  <div class="form-acoes">
    <a class="btn btn-secundario" href="<?= url('consultas') ?>">Cancelar</a>
    <button class="btn btn-primario" type="submit"><?= $reagendar ? 'Reagendar' : 'Agendar' ?></button>
  </div>
</form>
