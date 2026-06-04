document.addEventListener('DOMContentLoaded', () => {
    // Create Collection Search
    const searchInput = document.getElementById('product-search');
    const resultsContainer = document.getElementById('product-results');
    const selectedContainer = document.getElementById('selected-products');
    const form = document.querySelector('form');

    const selectedProducts = new Set();
    const savedProductData = {};

    if (searchInput) {
        const searchType = 'product';

        searchInput.addEventListener('input', async (e) => {
            const query = e.target.value.trim();
            if (query.length < 2) {
                resultsContainer.innerHTML = '';
                return;
            }
            try {
                const response = await fetch(
                    `/wp-admin/admin-ajax.php?action=mp_search_items&query=${encodeURIComponent(query)}&post_type=${searchType}`
                );
                const result = await response.json();

                resultsContainer.innerHTML = '';
                result.forEach((item) => {
                    const productContainer = document.createElement('div');
                    productContainer.className = "search-wrapper";
                    productContainer.dataset.id = item.id;

                    savedProductData[item.id] = item;

                    productContainer.innerHTML = `
                        <img src="${item.image}" class="search-img">
                        <h4>${item.title} <small>(${item.type})</small></h4>
                    `;

                    productContainer.addEventListener('click', () => {
                        if (selectedProducts.has(item.id)) {
                            selectedProducts.delete(item.id);
                        } else {
                            selectedProducts.add(item.id);
                        }
                        updateSelectedList();

                        // Hide results and clear input
                        resultsContainer.innerHTML = '';
                        searchInput.value = '';
                    });

                    resultsContainer.appendChild(productContainer);
                });
            } catch (error) {
                console.log('AJAX error', error);
            }
        });

        const updateSelectedList = () => {
            selectedContainer.innerHTML = '';
            selectedProducts.forEach((id) => {
                const product = savedProductData[id];
                if (product) {
                    const item = document.createElement('article');
                    item.className = 'selected-product';
                    item.innerHTML = `
                        <img src="${product.image}" class="search-img">
                        ${product.title}
                        <button type="button" class="remove-btn" data-id="${id}">X</button>
                    `;

                    const removeBtn = item.querySelector('.remove-btn');
                    removeBtn.addEventListener('click', (e) => {
                        const id = parseInt(e.target.dataset.id, 10);

                        selectedProducts.delete(id);
                        updateSelectedList();

                        const productToRemove = resultsContainer.querySelector(`.search-wrapper[data-id="${id}"]`);
                        if (productToRemove) {
                            productToRemove.classList.remove('selected');
                        }
                    });

                    selectedContainer.appendChild(item);
                }
            });
        };

        if (form) {
            form.addEventListener('submit', () => {
                form.querySelectorAll('input[name="collection_products[]"]').forEach((element) => element.remove());

                selectedProducts.forEach((id) => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'collection_products[]';
                    hiddenInput.value = id;
                    form.appendChild(hiddenInput);
                });
            });
        }

        // Close results when clicking outside it
        document.addEventListener('click', (e) => {
            const isClickInside = searchInput.contains(e.target) || resultsContainer.contains(e.target);
            if (!isClickInside) {
                resultsContainer.innerHTML = '';
            }
        });
    }

    // Global Search 
    const globalSearchInput = document.getElementById('global-search');
    const globalResults = document.getElementById('global-search-results');

    if (globalSearchInput) {
        globalSearchInput.addEventListener('input', async (e) => {
            const query = e.target.value.trim();
            if (query.length < 2) {
                globalResults.innerHTML = '';
                return;
            }

            try {
                const response = await fetch(
                    `/wp-admin/admin-ajax.php?action=mp_search_items&query=${encodeURIComponent(query)}&post_type=product,collection`
                );
                const results = await response.json();

                globalResults.innerHTML = '';
                results.forEach((item) => {
                    const el = document.createElement('div');
                    el.className = 'search-wrapper';
                    el.innerHTML = `
                        <a href="${item.link}">
                            <img src="${item.image}" class="search-img">
                            <p>${item.title}</p>
                            <span class="search-type">${item.type}</span>
                        </a>
                    `;
                    globalResults.appendChild(el);
                });
            } catch (error) {
                console.log('AJAX error', error);
            }
        });

        // Close results when clicking outside it
        document.addEventListener('click', (e) => {
            const isClickInside = globalSearchInput.contains(e.target) || globalResults.contains(e.target);
            if (!isClickInside) {
                globalResults.innerHTML = '';
            }
        });
    }
});
