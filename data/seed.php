<?php
// Dados iniciais: só a clínica e o administrador (necessário para o primeiro login).
// Para voltar a este estado, apague data/db.json (e a pasta uploads/).
// Acesso inicial: admin@exemplo.com.br / admin123
return [
  'clinicas' => [
    1 => ['id' => 1, 'nome' => 'Arizona Pet Care', 'cnpj' => '', 'email' => '', 'telefone' => '(11) 98217-9717', 'endereco' => 'Av. Dr. Altino Arantes, 292 — Vila Clementino', 'cidade' => 'São Paulo', 'uf' => 'SP', 'ativo' => true],
  ],
  'funcionarios' => [
    1 => ['id' => 1, 'nome' => 'Administrador', 'email' => 'admin@exemplo.com.br', 'cpf' => '', 'telefone' => '', 'especialidade' => '', 'perfil' => 'administrador', 'clinicas' => [1], 'ativo' => true, 'acesso' => 'email', 'senha_hash' => password_hash('admin123', PASSWORD_DEFAULT)],
  ],
  'clientes' => [],
  'pets' => [],
  'consultas' => [],
  'exames' => [],
  'resets' => [],
];
