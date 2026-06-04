document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('collection-image');
    const collectionImagePreview = document.getElementById('collection-image-preview');

    if (!fileInput || !collectionImagePreview) return;

    fileInput.addEventListener('change', () => {
        collectionImagePreview.innerHTML = '';

        const file = fileInput.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'preview-image';
            collectionImagePreview.appendChild(img);
        };
        reader.readAsDataURL(file);
    })
});