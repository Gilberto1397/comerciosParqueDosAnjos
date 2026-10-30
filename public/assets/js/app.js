(function () {
  'use strict';

  // Validação Bootstrap em todos os formulários .needs-validation
  document.querySelectorAll('.needs-validation').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      var ok = form.checkValidity();

      // Regras extras do formulário de comércio
      if (form.id === 'form-comercio') {
        ok = validarDias() && validarHorario() && validarImagem() && ok;
      }

      if (!ok) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  });

  var form = document.getElementById('form-comercio');
  if (!form) return;

  var MAX_BYTES = 2 * 1024 * 1024;

  // Contador de caracteres
  var descricao = document.getElementById('descricao');
  var contador = document.getElementById('contador');
  var atualizaContador = function () { contador.textContent = descricao.value.length; };
  descricao.addEventListener('input', atualizaContador);
  atualizaContador();

  // Marcar/desmarcar todos os dias
  var checks = form.querySelectorAll('.dia-check');
  document.getElementById('btn-todos').addEventListener('click', function () {
    var todos = Array.prototype.every.call(checks, function (c) { return c.checked; });
    checks.forEach(function (c) { c.checked = !todos; });
    this.textContent = todos ? 'Marcar todos' : 'Desmarcar todos';
    validarDias();
  });
  checks.forEach(function (c) { c.addEventListener('change', validarDias); });

  function validarDias() {
    var ok = Array.prototype.some.call(checks, function (c) { return c.checked; });
    document.getElementById('erro-dias').classList.toggle('d-none', ok);
    return ok;
  }

  var abertura = document.getElementById('horario_abertura');
  var fechamento = document.getElementById('horario_fechamento');
  function validarHorario() {
    var ok = abertura.value && fechamento.value && abertura.value < fechamento.value;
    document.getElementById('erro-horario').classList.toggle('d-none', !!ok || !(abertura.value && fechamento.value));
    abertura.setCustomValidity(ok || !(abertura.value && fechamento.value) ? '' : 'invalid');
    return !!ok;
  }
  abertura.addEventListener('change', validarHorario);
  fechamento.addEventListener('change', validarHorario);

  // Pré-visualização e validação da imagem
  var input = document.getElementById('imagem_capa');
  var preview = document.getElementById('preview');
  function validarImagem() {
    var file = input.files[0];
    var ok = !!file && /^image\/(jpeg|png|webp)$/.test(file.type) && file.size <= MAX_BYTES;
    input.setCustomValidity(ok ? '' : 'invalid');
    return ok;
  }
  input.addEventListener('change', function () {
    validarImagem();
    var file = input.files[0];
    if (file && input.validity.valid) {
      preview.src = URL.createObjectURL(file);
      preview.classList.remove('d-none');
    } else {
      preview.classList.add('d-none');
    }
  });
})();
