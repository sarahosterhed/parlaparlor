document.addEventListener('DOMContentLoaded', () => {
    const parentSelect = document.getElementById('collection-parent');
    const childCategoriesWrapper = document.getElementById('child-categories-wrapper');

    if (!parentSelect) return;

    parentSelect.addEventListener('change', async () => {
        const parentId = parentSelect.value;
        console.log("parent", parentId);

        childCategoriesWrapper.innerHTML = '';

        if (!parentId) {
            childCategoriesWrapper.innerHTML = '';
            return;
        }

        try {
            const response = await fetch(`/wp-admin/admin-ajax.php?action=mp_get_child_categories&parent_id=${parentId}`);
            const childCategories = await response.json();
            console.log("child", childCategories);

            if (childCategories.length > 0) {
                const subCategoriesTitle = document.createElement('p');
                subCategoriesTitle.innerText = 'Lägg till underkategori:';
                childCategoriesWrapper.appendChild(subCategoriesTitle);

                childCategories.forEach((category) => {
                    const subCategoryWrapper = document.createElement('div');
                    subCategoryWrapper.className = 'sub-category-wrapper';
                    subCategoryWrapper.innerHTML = `
                        <label>
                            <input type="radio" name="collection-sub-categories" value="${category.id}">
                            ${category.name}
                        </label>
                    `;
                    childCategoriesWrapper.appendChild(subCategoryWrapper);
                })
            } else {
                childCategoriesWrapper.innerHTML = '<p>Inga underkategorier hittades.</p>';
            }

        } catch (error) {
            console.log("error", error);
        }
    })

})
