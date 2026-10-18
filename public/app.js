document.querySelectorAll('[data-dismiss]').forEach(button => button.addEventListener('click', () => button.closest('.notice').remove()));
document.querySelectorAll('[data-submit-form]').forEach(form => form.addEventListener('submit', () => {
  const button = form.querySelector('button[type="submit"]');
  button.disabled = true;
  button.textContent = 'Сохраняем…';
}));
const photo = document.querySelector('#photo');
let previewUrl;
photo?.addEventListener('change', () => {
  const file = photo.files[0];
  const error = document.querySelector('#photo-error');
  error.textContent = '';
  photo.setCustomValidity('');
  if (!file) return;
  if (file.size > 4 * 1024 * 1024 || !['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
    error.textContent = 'Выберите JPG, PNG или WebP размером до 4 МБ.';
    photo.value = '';
    photo.setCustomValidity(error.textContent);
    return;
  }
  if (previewUrl) URL.revokeObjectURL(previewUrl);
  previewUrl = URL.createObjectURL(file);
  const preview = document.querySelector('#photo-preview');
  preview.src = previewUrl;
  preview.hidden = false;
});
