document.querySelector('.form').addEventListener('submit', function (event) {
  event.preventDefault();

  const name = document.getElementById('form-name').value.trim();
  const email = document.getElementById('form-email').value.trim();
  const message = document.getElementById('form-text').value.trim();

  let isValid = true;

  const MAX_NAME_LENGTH = 2;
  const MAX_MESSAGE_LENGTH = 50;

  const emailRegex = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

  document.querySelectorAll('.error-message').forEach(el => el.remove());

  const contentLanguageMeta = document.querySelector('meta[http-equiv="Content-Language"]');

  const lang = contentLanguageMeta ? contentLanguageMeta.getAttribute('content') : 'en';

  if (name === '' || name.length < MAX_NAME_LENGTH) {
    isValid = false;
    showError('form-name',
        lang === 'bg' ? 'Името неможе да е празно или по кратко от '
            + MAX_NAME_LENGTH + ' символа'
            : 'Name cannot be empty or shorter than ' + MAX_NAME_LENGTH
            + ' characters');
  }

  if (!emailRegex.test(email)) {
    isValid = false;
    showError('form-email',
        lang === 'bg' ? 'Невалиден email адрес' : 'Invalid email address');
  }

  if (message === '' || message.length < MAX_MESSAGE_LENGTH) {
    isValid = false;
    showError('form-text', lang === 'bg'
        ? 'Съобщението не може да е празно или по кратко от '
        + MAX_MESSAGE_LENGTH + ' символа'
        : 'Message cannot be empty or shorter than ' + MAX_MESSAGE_LENGTH
        + ' characters');
  }

  if (isValid) {
    document.getElementById('contact-form-loader').style.display = 'grid';

    event.target.submit();
  }
});

function showError(inputId, message) {
  const inputElement = document.getElementById(inputId);
  const errorElement = document.createElement('div');

  errorElement.className = 'error-message';
  errorElement.textContent = message;
  inputElement.parentNode.appendChild(errorElement);
}
