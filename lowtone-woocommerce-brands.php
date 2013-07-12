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
							"rewrite" => array("slug" => "product-brand"),
							"hierarchical" => false
						)
					);

				});

				add_action("woocommerce_product_options_general_product_data", function() {

					echo '<div class="options_group show_if_simple show_if_external show_if_variable">';

					$terms = array();

					$args = array(
							"hide_empty" => false,
						);

					foreach (get_terms("product_brand", $args) as $term) 
						$terms[$term->term_id] = $term->name;

					woocommerce_wp_select(array(
							"id" => "tax_input[product_brand][]", 
							"label" => __("Brand", "lowtone_woocommerce_brands"), 
							"options" => $terms
						));

					echo '</div>';

				});

			}
		));

}