(function () {
  "use strict";
  var corpo = document.body;

  // Menu lateral no celular
  document.addEventListener("click", function (e) {
    if (!(e.target instanceof Element)) return;
    if (e.target.closest("[data-menu]")) corpo.classList.toggle("menu-aberto");
    else if (e.target.closest("[data-menu-fechar]")) corpo.classList.remove("menu-aberto");
  });

  // Confirmação em diálogo para botões com data-confirmar (inativar, cancelar...)
  var dialogo = document.getElementById("confirmar");
  var formPendente = null;
  if (dialogo) {
    document.addEventListener("click", function (e) {
      var botao = e.target instanceof Element ? e.target.closest("[data-confirmar]") : null;
      if (!botao || !botao.form) return;
      e.preventDefault();
      formPendente = botao.form;
      dialogo.querySelector("[data-msg]").textContent = botao.getAttribute("data-confirmar");
      dialogo.showModal();
    });
    dialogo.addEventListener("close", function () {
      if (dialogo.returnValue === "ok" && formPendente) formPendente.submit();
      formPendente = null;
      dialogo.returnValue = "";
    });
  }

  // Selects com data-autosubmit enviam o formulário ao mudar (status da consulta)
  document.addEventListener("change", function (e) {
    if (e.target instanceof HTMLSelectElement && e.target.hasAttribute("data-autosubmit")) e.target.form.submit();
  });

  // Máscaras de CPF, CNPJ e telefone
  var mascaras = {
    cpf: function (v) {
      return v.replace(/\D/g, "").slice(0, 11).replace(/(\d{3})(\d)/, "$1.$2").replace(/(\d{3})(\d)/, "$1.$2").replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    },
    cnpj: function (v) {
      return v.replace(/\D/g, "").slice(0, 14).replace(/^(\d{2})(\d)/, "$1.$2").replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3").replace(/\.(\d{3})(\d)/, ".$1/$2").replace(/(\d{4})(\d)/, "$1-$2");
    },
    telefone: function (v) {
      var d = v.replace(/\D/g, "").slice(0, 11);
      if (d.length < 3) return d;
      var resto = d.slice(2), corte = d.length > 10 ? 5 : 4;
      return "(" + d.slice(0, 2) + ") " + (resto.length > corte ? resto.slice(0, corte) + "-" + resto.slice(corte) : resto);
    }
  };
  document.addEventListener("input", function (e) {
    var campo = e.target;
    if (campo instanceof HTMLInputElement && mascaras[campo.getAttribute("data-mascara")]) campo.value = mascaras[campo.getAttribute("data-mascara")](campo.value);
  });

  // Agendamento: o select de pets só mostra os pets do cliente escolhido
  var cliente = document.querySelector("[data-filtra-pets]");
  var pet = document.getElementById("pet_id");
  if (cliente && pet) {
    var filtrar = function () {
      Array.prototype.forEach.call(pet.options, function (op) {
        var visivel = !op.value || op.getAttribute("data-tutor") === cliente.value;
        op.hidden = op.disabled = !visivel;
      });
      if (pet.selectedOptions[0] && pet.selectedOptions[0].disabled) pet.value = "";
    };
    cliente.addEventListener("change", filtrar);
    filtrar();
  }

  // Cadastro de funcionário: a senha só é necessária no acesso por e-mail e senha
  var opcoesAcesso = document.querySelectorAll('input[name="acesso"]');
  var blocoSenha = document.querySelector("[data-senha]");
  if (opcoesAcesso.length && blocoSenha) {
    var ajustarSenha = function () {
      var google = document.querySelector('input[name="acesso"]:checked').value === "google";
      blocoSenha.hidden = google;
      Array.prototype.forEach.call(blocoSenha.querySelectorAll("input"), function (i) { i.disabled = google; });
    };
    Array.prototype.forEach.call(opcoesAcesso, function (r) { r.addEventListener("change", ajustarSenha); });
    ajustarSenha();
  }

  // Exames: adicionar e remover linhas
  var listaExames = document.getElementById("exames-lista");
  var modelo = document.getElementById("exame-modelo");
  if (listaExames && modelo) {
    document.addEventListener("click", function (e) {
      if (!(e.target instanceof Element)) return;
      if (e.target.closest("[data-add-exame]")) listaExames.appendChild(modelo.content.cloneNode(true));
      var remover = e.target.closest("[data-remover-exame]");
      if (remover && listaExames.children.length > 1) remover.closest(".exame-linha").remove();
    });
  }
})();
