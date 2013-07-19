<?php
/*
 * Plugin Name: Brands for WooCommerce
 * Plugin URI: http://wordpress.lowtone.nl/plugins/woocommerce-brands/
 * Description: Assign products to brands.
 * Version: 1.0
 * Author: Lowtone <info@lowtone.nl>
 * Author URI: http://lowtone.nl
 * License: http://wordpress.lowtone.nl/license
 */
/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\plugins\lowtone\woocommerce\brands
 */

namespace lowtone\woocommerce\brands {

	use lowtone\content\packages\Package;

	// Includes
	
	if (!include_once WP_PLUGIN_DIR . "/lowtone-content/lowtone-content.php") 
		return trigger_error("Lowtone Content plugin is required", E_USER_ERROR) && false;

	// Init

	Package::init(array(
			Package::INIT_MERGED_PATH => __NAMESPACE__,
			Package::INIT_SUCCESS => function() {

				add_action("init", function() {

					register_taxonomy(
						"product_brand", 
						"product",
						array(
							"label" => __("Brands", "lowtone_woocommerce_brands"),
							"labels" => array(
								"name" => __("Brands", "lowtone_woocommerce_brands"),
								"singular_name" => __("Brand", "lowtone_woocommerce_brands"),
								"menu_name" => __("Brands", "lowtone_woocommerce_brands" ),
								"search_items" => __("Search Brands", "lowtone_woocommerce_brands"),
								"all_items" => __("All Brands", "lowtone_woocommerce_brands"),
								"parent_item" => __("Parent Brand", "lowtone_woocommerce_brands"),
								"parent_item_colon" => __("Parent Brand:", "lowtone_woocommerce_brands"),
								"edit_item" => __("Edit Brand", "lowtone_woocommerce_brands"),
								"update_item" => __("Update Brand", "lowtone_woocommerce_brands"),
								"add_new_item" => __("Add New Brand", "lowtone_woocommerce_brands"),
								"new_item_name" => __("New Brand Name", "lowtone_woocommerce_brands")
							),
							"rewrite" => array("slug" => slug()),
							"hierarchical" => false,
							"public" => true,
							"show_ui" => true,
							"show_in_nav_menus" => true,
							"show_admin_column" => false,
						)
					);

					add_filter("body_class", function($class) {
						global $wp_query;

						if ("product_brand" != $wp_query->get("taxonomy"))
							return $class;

						$class[] = "woocommerce";
						$class[] = "woocommerce-page";

						return $class;
					});

				});

				add_action("woocommerce_product_options_general_product_data", function() {

					echo '<div class="options_group show_if_simple show_if_external show_if_variable">';

					$terms = array();

					$args = array(
							"hide_empty" => false,
						);

					$terms = array("" => "");

					foreach (get_terms("product_brand", $args) as $term) 
						$terms[$term->term_id] = $term->name;

					global $post;

					$brand = reset(wp_get_post_terms($post->ID, "product_brand"));

					woocommerce_wp_select(array(
							"id" => "lowtone_woocommerce_brand", 
							"label" => __("Brand", "lowtone_woocommerce_brands"), 
							"options" => $terms, 
							"value" => $brand ? $brand->term_id : NULL
						));

					echo '</div>';

				});

				add_action("save_post", function($id, $post) {
					if ("product" != $post->post_type)
						return;

					if (!isset($_POST["lowtone_woocommerce_brand"]))
						return;

					wp_set_object_terms($id, (int) $_POST["lowtone_woocommerce_brand"], "product_brand", false);
				}, 10, 2);

				add_action("woocommerce_archive_description", function() {
					if (!(is_tax(array("product_brand")) && 0 == get_query_var("paged"))) 
						return;

					$description = apply_filters("the_content", term_description());
						
					if (!$description) 
						return;

					echo '<div class="term-description">' . $description . '</div>';
				});

				add_action("admin_init", function() {

					add_settings_field(
						"lowtone_woocommerce_brands_slug",
						__("Product brand base", "lowtone_woocommerce_brands"),
						function() {
							echo sprintf('<input name="lowtone_woocommerce_brands_slug" type="text" class="regular-text code" value="%s" />', esc_attr(slug()));
						},
						"permalink",
						"optional"
					);

				}, 20);

				add_action("before_woocommerce_init", function() {
					if (!is_admin())
						return;

					if (!isset($_POST["lowtone_woocommerce_brands_slug"]))
						return;

					$slug = untrailingslashit($_POST["lowtone_woocommerce_brands_slug"]);

					update_option("lowtone_woocommerce_brands_slug", $slug);
				});

			}
		));

	// Functions
	
	function slug() {
		return trim(get_option("lowtone_woocommerce_brands_slug")) ?: "brand";
	}

}