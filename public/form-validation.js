function messageFor(input) {
  const label = input.labels?.[0]?.textContent.trim() || input.placeholder || 'este campo';
  if (input.validity.valueMissing) {
    if (input.type === 'checkbox') return 'Confirme esta opção para continuar.';
    if (input.tagName === 'SELECT') return 'Selecione uma opção.';
    if (input.name === 'name') return 'Informe seu nome completo.';
    if (input.type === 'email') return 'Informe seu e-mail.';
    return `Preencha ${label.toLocaleLowerCase('pt-BR')}.`;
  }
  if (input.validity.typeMismatch && input.type === 'email') return 'Confira o e-mail. Exemplo: nome@email.com.';
  if (input.validity.patternMismatch && /cep|postal/i.test(input.name)) return 'Informe os 8 números do CEP.';
  if (input.validity.rangeUnderflow || input.validity.rangeOverflow) return 'Confira o valor informado.';
  return 'Confira o preenchimento deste campo.';
}

function clearError(input) {
  if (!input.dataset.errorId) return;
  document.getElementById(input.dataset.errorId)?.remove();
  const descriptions = (input.getAttribute('aria-describedby') || '').split(' ').filter(id => id && id !== input.dataset.errorId);
  if (descriptions.length) input.setAttribute('aria-describedby', descriptions.join(' '));
  else input.removeAttribute('aria-describedby');
  input.removeAttribute('aria-invalid');
  delete input.dataset.errorId;
}

export function prepareFormValidation(root) {
  root.querySelectorAll('form').forEach(form => {
    form.noValidate = true;
    if (form.dataset.customValidation) return;
    form.dataset.customValidation = 'true';
    form.addEventListener('submit', event => {
      const fields = [...form.elements].filter(el => el.willValidate);
      fields.forEach(clearError);
      const invalid = fields.filter(input => !input.validity.valid);
      if (!invalid.length) return;
      event.preventDefault();
      event.stopImmediatePropagation();
      invalid.forEach((input, index) => {
        const error = document.createElement('small');
        error.id = `form-error-${Date.now()}-${index}`;
        error.className = 'field-error';
        error.textContent = messageFor(input);
        input.dataset.errorId = error.id;
        input.setAttribute('aria-invalid', 'true');
        input.setAttribute('aria-describedby', `${input.getAttribute('aria-describedby') || ''} ${error.id}`.trim());
        const anchor = input.type === 'checkbox' ? input.closest('label') || input : input;
        anchor.insertAdjacentElement('afterend', error);
      });
      invalid[0].focus();
    }, true);
    form.addEventListener('input', event => {
      if (event.target.validity?.valid) clearError(event.target);
    });
    form.addEventListener('change', event => {
      if (event.target.validity?.valid) clearError(event.target);
    });
  });
  root.querySelectorAll('input[autocomplete="postal-code"], input[name="postal_code"], input[name="cep"]').forEach(input => {
    input.inputMode = 'numeric';
    input.maxLength = 9;
    input.pattern = '[0-9]{5}-?[0-9]{3}';
    input.addEventListener('input', () => {
      const digits = input.value.replace(/\D/g, '').slice(0, 8);
      input.value = digits.length > 5 ? `${digits.slice(0, 5)}-${digits.slice(5)}` : digits;
    });
  });
}
