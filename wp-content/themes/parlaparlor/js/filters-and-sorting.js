document.addEventListener('DOMContentLoaded', () => {
    const layout = document.querySelector('.main-content-layout');
    if (!layout) return;

    const filterBtn = document.querySelector('.filter-button');
    const sortBtn = document.querySelector('.sort-button');

    if (filterBtn) {
        filterBtn.addEventListener('click', () => {
            layout.classList.toggle('show-filters');
            layout.classList.remove('show-sort');
        });
    }

    if (sortBtn) {
        sortBtn.addEventListener('click', () => {
            layout.classList.toggle('show-sort');
            layout.classList.remove('show-filters');
        });
    }
});