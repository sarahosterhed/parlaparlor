<?php
/*
* Plugin Name: Collections plugin
* Description: A plugin to enable collections of different products
* Text Domain: collections-plugin
*/

function mp_create_collections() {

    //Post type: Collection
    register_post_type('collection', [
        'label' => 'Kollektioner',
        'public' => true,
        'has_archive' => true,
        'show_in_nav_menus' => true,
        'menu_icon' => plugin_dir_url(__FILE__) . 'img/collections-icon.svg',
        'supports' => [
            'title',
            'editor',
            'thumbnail',
            'author',
            'excerpt' 
        ],
        'show_in_rest' => true
    ]);
}

add_action('init', 'mp_create_collections');

function mp_register_collection_taxonomy() {
    register_taxonomy('collection_category', 'collection', [
        'labels' => [

            'name' => __('Collection Categories', 'collections-plugin'),
            'singular_name' => __('Collection Category', 'collections-plugin'),
            'search_items' => __('Search Categories', 'collections-plugin'),
            'all_items' => __('All Categories', 'collections-plugin'),
            'parent_item' => __('Parent Category', 'collections-plugin'),
            'parent_item_colon' => __('Parent Category:', 'collections-plugin'),
            'edit_item' => __('Edit Category', 'collections-plugin'),
            'update_item' => __('Update Category', 'collections-plugin'),
            'add_new_item' => __('Add New Category', 'collections-plugin'),
            'new_item_name' => __('New Category Name', 'collections-plugin'),
            'menu_name' => __('Collection Categories', 'collections-plugin'),
        ],
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
    ]);

    $parent_terms = ['Boho', 'Fest', 'Vintage', 'Sommar', 'Minimalism', 'Chic'];
    $child_terms = ['Armband', 'Halsband', 'Örhängen', 'Fotlänkar', 'Övrigt'];

    foreach ($parent_terms as $parent_name) {
        $existing_parent = term_exists($parent_name, 'collection_category');
        if(!$existing_parent) {
            $existing_parent = wp_insert_term($parent_name, 'collection_category');
        }

        $parent_id = is_array($existing_parent) ? $existing_parent['term_id'] : $existing_parent;

        if(!is_wp_error($parent_id)) {
            foreach ($child_terms as $child) {
                $slug = sanitize_title($parent_name . '-' . $child);

                $existing_child = term_exists($slug, 'collection_category');

                if (!$existing_child) {
                    wp_insert_term($child, 'collection_category', [
                        'parent' => $parent_id,
                        'slug'   => $slug
                    ]);
                }
            }
        }
     }
}


add_action('init', 'mp_register_collection_taxonomy');

function mp_enqueue_collection_scripts() {
    wp_enqueue_script(
        'mp-collection-js',
        plugin_dir_url(__FILE__) . 'js/search.js',
        [],
        null,
        true
    );

    wp_enqueue_script(
        'mp-child-categories-js',
        plugin_dir_url(__FILE__) . 'js/get-categories.js',
        [],
        null,
        true
    );

    wp_enqueue_script(
        'mp-image-preview-js',
        plugin_dir_url(__FILE__) . 'js/image-preview.js',
        [],
        null,
        true
    );
}

add_action('wp_enqueue_scripts', 'mp_enqueue_collection_scripts');

function mp_create_collection_form_shortcode() {
    ob_start();
    
    $parent_categories = get_terms([
        'taxonomy'          => 'collection_category',
        'hide_empty'        => false,
        'parent'            => 0,
    ]);

    $products = get_posts([
        'post_type'         => 'product',
        'posts_per_page'    => -1,
    ]);

    
    ?>
        <form method="POST" enctype="multipart/form-data" class="create-collection main-grid">
            <?php wp_nonce_field('mp_collection_action', 'mp_collection_nonce'); ?>

            <div class="create-collection-title column-flex">
                <label for="collection-title">Kollektionens namn</label>
                <input type="text" id="collection-title" name="collection-title" required>
            </div>

            <div class="create-collection-description column-flex">
                <label for="collection-description">Beskrivning</label>
                <textarea type="text" id="collection-description" name="collection-description" required></textarea>
            </div>

            <div class="create-collection-image column-flex">
                <label for="collection-image"><?php _e('Lägg till en bild för kollektionen', 'collections-plugin'); ?></label>
                <input type="file" id="collection-image" name="collection-image" accept="image/*">
                <div id="collection-image-preview"></div>
            </div>

            <div class="create-collection-categories column-flex">
                <label for="collection-parent">Kategori</label>
                <select name="collection-parent" id="collection-parent">
                    <option value=""><?php _e('Välj kategori', 'collections-plugin'); ?></option>
                    <?php foreach($parent_categories as $parent_category) : ?>
                            <option value="<?php echo esc_attr($parent_category->term_id); ?>">
                                <?php echo esc_html($parent_category->name); ?>
                            </option>
                    <?php endforeach; ?>
                </select>
                
                <div id="child-categories-wrapper"></div>
            </div>
            
            <div class="create-collection-products">
                <div class="column-flex">
                    <label for="product-search">Lägg till produkter</label>
                    <input type="text" id="product-search" placeholder="Sök produkter">
                    <div id="product-results" class="product-search-results"></div>
                </div>

                <div class="selected-products column-flex">
                    <h4>Produkter i kollektion:</h4>
                    <div id="selected-products"></div>
                </div>
            </div>

            <input type="submit" name="mp_collection_submit" value="Ladda upp kollektion" class="button">
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('.create-collection');
                if (form) {
                    form.addEventListener('submit', function() {
                        window.dataLayer = window.dataLayer || [];
                        window.dataLayer.push({
                            'event': 'collectionFormSubmitted'
                        });
                    });
                }
            });
        </script>

    <?php

     return ob_get_clean();
}

add_shortcode('collection_form', 'mp_create_collection_form_shortcode');

function mp_handle_collection_form_submission() {
    if(!isset($_POST['mp_collection_submit'])) {
        return;
    }

    if (!isset($_POST['mp_collection_nonce']) || !wp_verify_nonce($_POST['mp_collection_nonce'], 'mp_collection_action')) {
        return; 
    }

    $title = sanitize_text_field($_POST['collection-title']);
    $description = sanitize_textarea_field($_POST['collection-description']);
    
    
    $post_id = wp_insert_post([
        'post_title'        => $title,
        'post_content'      => $description,
        'post_type'    => 'collection',
        'post_status'  => 'publish',
        'post_author'  => get_current_user_id(),
    ]);
    
    if (is_wp_error($post_id)) {
        echo '<p>' . __('Something went wrong when creating the collection. Please try again.', 'collections-plugin') . '</p>';
        return;
    }

    // Handle Category
    if (!empty($_POST['collection-parent'])) {
        $parent_term_id = intval($_POST['collection-parent']);
        wp_set_post_terms($post_id, [$parent_term_id], 'collection_category');
    }

    if (!empty($_POST['collection-sub-categories'])) {
        $child_term_id = intval($_POST['collection-sub-categories']);
        wp_set_post_terms($post_id, [$child_term_id], 'collection_category', true);
    }

    // Handle image upload
    if (!empty($_FILES['collection-image']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $image_id = media_handle_upload('collection-image', $post_id);

        if (!is_wp_error($image_id)) {
            set_post_thumbnail($post_id, $image_id);
        }
    }

    // Handle selected products
    if (!empty($_POST['collection_products']) && count($_POST['collection_products']) >= 2) {
        $product_ids = array_map('intval', $_POST['collection_products']);
        update_post_meta($post_id, 'mp_collection_products', $product_ids);
    } else {
        echo '<p>' . __('You must select at least 2 products for your collection.', 'collections-plugin') . '</p>';
        return;
    }

    // Redirect to collection page
    wp_redirect(get_permalink($post_id));
    exit;
}

add_action('template_redirect', 'mp_handle_collection_form_submission');


function mp_get_collection_total_price( $collection_id ) {
    $product_ids = get_post_meta( $collection_id, 'mp_collection_products', true );
    $total = 0;

    if ( !empty($product_ids) && is_array($product_ids) ) {
        foreach ( $product_ids as $product_id ) {
            $product = wc_get_product($product_id);
            if ( $product ) {
                $total += (float) $product->get_price();
            }
        }
    }

    return $total;
}



// Add collection products to cart
function mp_add_collection_to_cart() {
    if ( empty($_GET['add-collection-to-cart']) ) {
        return;
    }

    $collection_id = intval($_GET['add-collection-to-cart']);
    $product_ids = get_post_meta($collection_id, 'mp_collection_products', true);

    if (!empty($product_ids) && is_array($product_ids)) {
        foreach ($product_ids as $product_id) {
            WC()->cart->add_to_cart($product_id);
        }
    }

    wp_safe_redirect(wc_get_cart_url());
    exit;
}

add_action('wp_loaded', 'mp_add_collection_to_cart');


function filter_collections_by_category($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('collection')) {
        if (!empty($_GET['collection_category'])) {
            $raw = (array) $_GET['collection_category'];

            $slugs = [];
            foreach ($raw as $value) {
                // split comma-separated values into individual slugs
                foreach (explode(',', $value) as $slug) {
                    $slugs[] = trim($slug);
                }
            }
            $slugs = array_filter($slugs);

            if (!empty($slugs)) {
                $query->set('tax_query', [
                    [
                        'taxonomy' => 'collection_category',
                        'field'    => 'slug',
                        'terms'    => $slugs,
                    ],
                ]);
            }
        }
    }
}

add_action('pre_get_posts', 'filter_collections_by_category');

function mp_render_collection_filters() {

    $raw_selected = (array) ($_GET['collection_category'] ?? []);
    $selected_slugs = [];
    foreach ($raw_selected as $value) {
        foreach (explode(',', $value) as $slug) {
            $selected_slugs[] = trim($slug);
        }
    }

    //Parent terms
    $parent_terms = get_terms([
        'taxonomy'   => 'collection_category',
        'hide_empty' => true,
        'parent'     => 0,
    ]);
    ?>

    <form method="GET">
        <?php if (!empty($parent_terms) && !is_wp_error($parent_terms)) : ?>
            <div class="filter-alternatives-wrapper">
                <h4>Kategori</h4>
                <?php foreach ($parent_terms as $term) : ?>
                    <label>
                        <input type="checkbox"
                            name="collection_category[]"
                            value="<?php echo esc_attr($term->slug); ?>"
                            <?php checked(in_array($term->slug, $selected_slugs, true)); ?>>
                        <?php echo esc_html($term->name); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php

        // Children grouped as jewellery type
        $all_children = [];
        foreach ($parent_terms as $parent) {
            $children = get_terms([
                'taxonomy'   => 'collection_category',
                'hide_empty' => true,
                'parent'     => $parent->term_id,
            ]);
            foreach ($children as $child) {
                $all_children[$child->name][] = $child->slug;
            }
        }
        if (!empty($all_children)) : ?>
            <div class="filter-alternatives-wrapper">
                <h4>Smyckestyp</h4>
                <?php foreach ($all_children as $name => $slugs) :
                    $value = implode(',', $slugs);
                    $is_checked = array_intersect($slugs, $selected_slugs);
                    ?>
                    <label>
                        <input type="checkbox"
                            name="collection_category[]"
                            value="<?php echo esc_attr($value); ?>"
                            <?php checked(!empty($is_checked)); ?>>
                        <?php echo esc_html($name); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="filter-buttons-wrapper">
            <button type="submit" class="button">Filtrera</button>
            <?php if ( !empty($_GET['collection_category']) ) : ?>
                <a href="<?php echo esc_url(get_post_type_archive_link('collection')); ?>" class="button">
                    Rensa filter
                </a>
            <?php endif; ?>
        </div>
    </form>
    <?php
}

add_action('collections_render_filters', 'mp_render_collection_filters');


function mp_sort_collections( $query ) {
    if ( !is_admin() && $query->is_main_query() && is_post_type_archive('collection') ) {
        $sort = $_GET['sort'] ?? '';

        // Alphabetical sorting
        if ( $sort === 'alpha_asc' ) {
            $query->set( 'orderby', 'title' );
            $query->set( 'order', 'ASC' );
        } elseif ( $sort === 'alpha_desc' ) {
            $query->set( 'orderby', 'title' );
            $query->set( 'order', 'DESC' );
        }

        // Price sorting 
        if ( in_array( $sort, ['price_asc','price_desc'], true ) ) {
            add_filter( 'the_posts', function( $posts ) use ( $sort ) {
                usort( $posts, function( $a, $b ) use ( $sort ) {
                    $price_a = mp_get_collection_total_price( $a->ID );
                    $price_b = mp_get_collection_total_price( $b->ID );

                    return $sort === 'price_asc'
                        ? $price_a <=> $price_b
                        : $price_b <=> $price_a;
                });
                return $posts;
            });
        }
    }
}
add_action( 'pre_get_posts', 'mp_sort_collections' );


function mp_render_collection_sorting() {
    $current = $_GET['sort'] ?? '';
    ?>
    <form method="GET">
        <div class="sort-alternatives-wrapper">
            <label>
                <input type="radio" name="sort" value="alpha_asc" <?php checked($current, 'alpha_asc'); ?>>
                Alfabetiskt, A–Ö
            </label>
            <label>
                <input type="radio" name="sort" value="alpha_desc" <?php checked($current, 'alpha_desc'); ?>>
                Alfabetiskt, Ö–A
            </label>
            <label>
                <input type="radio" name="sort" value="price_asc" <?php checked($current, 'price_asc'); ?>>
                Pris, lågt till högt
            </label>
            <label>
                <input type="radio" name="sort" value="price_desc" <?php checked($current, 'price_desc'); ?>>
                Pris, högt till lågt
            </label>
        </div>
        <div class="sort-buttons-wrapper">
            <button type="submit" class="button">Sortera</button>
        </div>
    
    </form>
    <?php
}
add_action('collections_render_sorting', 'mp_render_collection_sorting');


// AJAX Handlers

//AJAX Handeler: Search items
function mp_search_items() {
    $query = sanitize_text_field($_GET['query'] ?? '');

    // Default to both
    $post_types = ['product', 'collection'];

    // If a specific type is requested
    if (!empty($_GET['post_type'])) {
        $requested = sanitize_text_field($_GET['post_type']);
        if (in_array($requested, ['product', 'collection'], true)) {
            $post_types = [$requested];
        }
    }

    $args = [
        'post_type'      => $post_types,
        'posts_per_page' => 10,
        's'              => $query,
    ];

    $items = get_posts($args);

    $results = [];
    foreach ($items as $item) {
        $results[] = [
            'id'    => $item->ID,
            'title' => $item->post_title,
            'image' => get_the_post_thumbnail_url($item->ID, 'thumbnail') ?: wc_placeholder_img_src(),
            'type'  => $item->post_type,
            'link'  => get_permalink($item->ID)
        ];
    }

    wp_send_json($results);
}

add_action('wp_ajax_mp_search_items', 'mp_search_items');
add_action('wp_ajax_nopriv_mp_search_items', 'mp_search_items');


//AJAX Handeler: Get child categories
function mp_get_child_categories() {
    $parent_id = intval($_GET['parent_id'] ?? 0);

    if(!$parent_id) {
        wp_send_json([]);
    }

    $child_categories = get_terms([
        'taxonomy'          => 'collection_category',
        'hide_empty'        => false,
        'parent'            => $parent_id,
    ]);

    $results = [];
    foreach ($child_categories as $child_category) {
        $results[] = [
            'id'   => $child_category->term_id,
            'name' => $child_category->name,
        ];
    }

    wp_send_json($results);
}


add_action('wp_ajax_mp_get_child_categories', 'mp_get_child_categories');
add_action('wp_ajax_nopriv_mp_get_child_categories', 'mp_get_child_categories');