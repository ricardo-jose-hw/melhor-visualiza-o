<?php
// Configuração do painel: rotas, menu por perfil e listas fixas.

const PERFIS = [
  'administrador' => 'Administrador da clínica',
  'veterinario'   => 'Veterinário',
  'funcionario'   => 'Funcionário',
]; // clientes têm sempre o perfil "cliente" e não acessam este painel (RF-04)

const STATUS_CONSULTA = ['Pendente', 'Confirmada', 'Concluída', 'Cancelada'];

const ESPECIALIDADES = [
  'Cardiologia', 'Dermatologia', 'Ortopedia', 'Oftalmologia', 'Oncologia', 'Anestesia', 'Endocrinologia', 'Neurologia',
  'Infectologia', 'Nefrologia', 'Odontologia', 'Gastroenterologia', 'Medicina Integrativa', 'Medicina Felina', 'Nutrologia', 'Silvestres e Exóticos',
];

const ESPECIES = ['Cão', 'Gato', 'Coelho', 'Ave', 'Roedor', 'Réptil'];

const EXAMES_SUGERIDOS = ['Hemograma', 'Bioquímico', 'Citologia', 'Ecocardiograma', 'Eletrocardiograma', 'Ultrassonografia', 'Raio-X'];

// rota => [título, layout]. A rota é também o nome do arquivo em pages/.
const ROTAS = [
  'painel'            => ['Painel', 'app'],
  'consultas'         => ['Consultas', 'app'],
  'consultas-form'    => ['Agendar consulta', 'app'],
  'clientes'          => ['Clientes', 'app'],
  'clientes-form'     => ['Cliente', 'app'],
  'pets'              => ['Pets', 'app'],
  'pets-form'         => ['Pet', 'app'],
  'pets-historico'    => ['Histórico do pet', 'app'],
  'exames'            => ['Exames', 'app'],
  'exames-form'       => ['Adicionar exames', 'app'],
  'funcionarios'      => ['Funcionários', 'app'],
  'funcionarios-form' => ['Funcionário', 'app'],
  'clinicas'          => ['Clínicas', 'app'],
  'clinicas-form'     => ['Clínica', 'app'],
  'login'             => ['Entrar', 'auth'],
  'recuperar-senha'   => ['Recuperar senha', 'auth'],
  'redefinir-senha'   => ['Redefinir senha', 'auth'],
  '403'               => ['Acesso negado', 'app'],
  '404'               => ['Página não encontrada', 'app'],
];

const ROTAS_PUBLICAS = ['login', 'recuperar-senha', 'redefinir-senha'];

// Menu lateral: [rota, rótulo, ícone, grupo, perfis que enxergam o item]
const MENU = [
  ['painel',       'Painel',       'painel',       'Operação',      ['administrador', 'veterinario', 'funcionario']],
  ['consultas',    'Consultas',    'calendario',   'Operação',      ['administrador', 'veterinario', 'funcionario']],
  ['clientes',     'Clientes',     'clientes',     'Operação',      ['administrador', 'veterinario', 'funcionario']],
  ['pets',         'Pets',         'pet',          'Operação',      ['administrador', 'veterinario', 'funcionario']],
  ['exames',       'Exames',       'exames',       'Operação',      ['administrador', 'veterinario', 'funcionario']],
  ['funcionarios', 'Funcionários', 'funcionarios', 'Administração', ['administrador']],
  ['clinicas',     'Clínicas',     'clinicas',     'Administração', ['administrador']],
];
