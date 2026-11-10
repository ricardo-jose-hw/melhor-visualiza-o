<?php
// Armazenamento provisório em arquivo JSON (data/db.json), para o front-end guardar o que for cadastrado.
// BACKEND: substituir estas funções por acesso ao banco de dados.

const ARQUIVO_DB = __DIR__ . '/../data/db.json';
const UPLOADS = __DIR__ . '/../uploads';

function carregar_db(): array {
  if (!file_exists(ARQUIVO_DB)) {
    if (!is_writable(dirname(ARQUIVO_DB))) exit('A pasta data/ precisa de permissão de escrita.');
    salvar_db(require __DIR__ . '/../data/seed.php');
  }
  return json_decode(file_get_contents(ARQUIVO_DB), true);
}

function salvar_db(array $db): void { file_put_contents(ARQUIVO_DB, json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX); }

function novo_id(array $lista): int { return $lista ? max(array_keys($lista)) + 1 : 1; }
