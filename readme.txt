=== Plugin Name ===
Contributors: nathanrice, studiopress, studiograsshopper
Tags: genesis, genesiswp, studiopress, woocommerce
Requires at least: 3.3
Tested up to: 4.7
Stable tag: 0.9.9

This plugin allows you to seamlessly integrate WooCommerce with the Genesis Framework and Genesis child themes.

== Description ==

This plugin replaces WooCommerce's built-in shop templates with its own Genesis-ready versions, specifically the `single-product.php`, `archive-product.php` and `taxonomy.php` templates needed to display the single product page, the main shop page, and Product Category and Product Tag archive pages.

To allow easy customization of these templates, and ensure that you do not lose your customizations when the plugin is updated, you can place your own copies of these templates in your child theme's 'woocommerce' folder and customize these copies as much as you like. You can also create your own `taxonomy-{taxonomy}.php` and `taxonomy-{taxonomy}-{term}.php` templates in the same location and this plugin will find them and use them to display your shop's Product Category and Product Tag archives. See the [Template Hierarchy](http://codex.wordpress.org/Template_Hierarchy#Custom_Taxonomies_display) to learn more about naming requirements for taxonomy templates.

Additionally, the plugin makes [Genesis Simple Sidebars](http://wordpress.org/extend/plugins/genesis-simple-sidebars/) and [Genesis Simple Menus](http://wordpress.org/extend/plugins/genesis-simple-menus/) compatible with WooCommerce.

**This version is compatible with WooCommerce 2.1+**

== Installation ==

1. Upload the entire `genesis-connect-woocommerce` folder to the `/wp-content/plugins/` directory
2. DO NOT change the name of the `genesis-connect-woocommerce` folder
3. Activate the plugin through the 'Plugins' menu in WordPress
5. That's it. Navigate to your shop pages and you should see the new templates in action.

Note: You must have a Genesis child theme activated before installing and activating this plugin.

== Frequently Asked Questions ==

= Can I customize the Genesis Connect for Woocommerce templates? =

It's not recommended to customize the plugin's templates because, if you do, you will lose any customizations the next time the plugin is updated. Instead, take copies of the plugin's `single-product.php`, `archive-product.php` and `taxonomy.php` files, and place these copies in a folder called `woocommerce` in the root of your child theme's main folder, like this: `wp-content/themes/my-child-theme/woocommerce/`

Make sure you keep the same file names!

**Important**
While the templates provided with this plugin will be kept up to date with any future changes to WooCommerce, please bear in mind that, if you create your own custom Genesis Connect for WooCommerce templates, it is your responsibility to enure that any code you add to your custom templates is compatible with WooCommerce.

The plugin's templates provide a great starting point for your own customizations and can be found in the plugin's `templates` folder.

= I want to use WooCommerce's breadcrumbs, not Genesis breadcrumbs =

There's no need! Genesis Connect for WooCommerce modifies the default Genesis breadcrumbs to give the same crumb structure as WooCommerce's built-in breadcrumbs. The modified Genesis breadcrumbs will reflect all your existing Genesis breadcrumb customizations too.

= What if I want the main Shop page to be the site's front page? =

1. Go to the *Dashboard > Settings > Reading* page select A Static Page and select "Shop" as the front page.
2. It is recommended to turn off Genesis breadcrumbs for the Home page in *Dashboard > Genesis > Theme Settings > Breadcrumb options*.

= Does it work with Genesis Simple Sidebars? =

Yes.

= Does it work with Genesis Simple Sidebars? =

Yes.

= How does the plugin handle WooCommerce's CSS? =

Genesis Connect for WooCommerce does not modify WooCommerce's way of working with CSS. By default, WooCommerce provides its own `woocommerce.css` file containing basic styles for the shop pages which is located here: `wp-content/plugins/woocommerce/assets/css/woocommerce.css`.

To use this stylesheet, check the "*Enable WooCommerce CSS styles*" checkbox in the *WooCommerce Settings page > General tab*. Alternatively, you can add this code to your child theme's `functions.php` file: `define( 'WOOCOMMERCE_USE_CSS', true );`

Note that this code takes precedence over the checkbox in the *WooCommerce Settings page > General tab*; in other words, when you use this code, the checkbox is ignored.

If you decide to use the WooCommerce CSS and wish to customize its styles, do *not* edit the `woocommerce.css` file. Instead, make a copy of this file, rename it `style.css` and place it in your child theme's `woocommerce` folder, and make all your edits in this file. This ensures that you do not lose your CSS customizations when WooCommerce is updated.

Alternatively, you can add your WooCommerce styles to your child theme's main style.css stylesheet. In this case, you should disable the WooCommerce built-in stylesheet: either uncheck the "*Enable WooCommerce CSS styles*" checkbox in the *WooCommerce Settings page > General tab*, or a better option, add this code to your child theme's `functions.php` file: `define( 'WOOCOMMERCE_USE_CSS', false );`

If you are using a Genesis child theme specially designed for WooCommerce, refer to the theme's documentation to find out if all of the above has been been taken care of for you already.

= Where is the plugin's settings page? =

There isn't one! This plugin does not need one as all of its work is behind the scenes, integrating the display of WooCommerce within Genesis themes.


== Other Notes ==

= Technical Info =

For more technically minded users, this is what the plugin does:

* Unhooks the WooCommerce template loader function
* Adds its own template loader function to control the templates used by the single product, archive product and Product Category and Product Tag (taxonomy) archive pages.
* Adds Genesis Layouts and SEO support to the WooCommerce `Product` custom post type
* Provides three Genesis-ready templates to display the shop pages, located in the plugin's `templates` folder:
	* single-product.php
	* archive-product.php
	* taxonomy.php
* These templates use WooCommerce core functions to display the shop loops which:
	* unhook WooCommerce's built-in breadcrumbs
	* unhook the Genesis Loop and replace it with the relevant WooCommerce shop loop
	* remove WooCommerce's #container and #content divs, which are not required or wanted by Genesis
* The shop loop function in each template is heavily based on its WooCommerce counterpart, but has been modified to accommodate certain Genesis features such as the Taxonomy term headings and descriptions feature.
* The templates contain the `genesis();` function and therefore are fully customisable using Genesis hooks and filters.
* The template loader allows users to use their own templates in the child theme's 'woocommerce' folder. These user templates, if they exist in the child theme's `woocommerce' folder, will be loaded in place of the supplied Genesis Connect for WooCommerce templates
* Using appropriate filters, modifies the Genesis breadcrumbs output to mimic the breadcrumb structure provided by WooCommerce's built-in breadcrumbs.

= More about breadcrumbs =

By default, the Genesis breadcrumbs do not provide the same breadcrumb structure as those built-in to WooCommerce. Genesis Connect for WooCommerce modifies the normal Genesis Breadcrumbs output on shop pages to mimic the structure of those built-in to WooCommerce.

Note that the templates provided in this plugin automatically unhook WooCommerce's built-in breadcrumbs via this code in each template:
`remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );`

= Filters =

This plugin provides some filters which may be useful for developers.

`genesiswooc_custom_query`
Located in `gencwooc_single_product_loop()` in `templates/single-product.php`.
The filter callback function should return a query object or false.

`gencwooc_product_archive_crumb`
Located in `gencwooc_get_archive_crumb_filter()` in `lib/breadcrumb.php`.
Allows further modification of the single product page breadcrumbs.

`gencwooc_single_product_crumb`
Located in `gencwooc_get_single_crumb()` in `lib/breadcrumb.php`.
Allows further modification of the product archive (shop page) breadcrumbs.

= More info about WooCommerce CSS handling =

For the benefit of theme developers and customizers, here is a summary of possible scenarios for dealing with WooCommerce CSS:

* Case 1: If the *WooCommerce > General settings > Enable WooCommerce CSS* option is checked, the default stylesheet supplied with WooCommerce will be loaded (see `wp-content/plugins/woocommerce/assets/css/woocommerce.css`).
* Case 2: If *WooCommerce > General settings > Enable WooCommerce CSS* option is unchecked, no stylesheet is loaded.
* Case 3: If the user (or theme developer) sets `define( 'WOOCOMMERCE_USE_CSS', true );` in the child theme functions.php the options setting is ignored and the default WooCommerce stylesheet is loaded, ie has same effect as checking the settings box.
* Case 4: If the user (or theme developer) sets `define( 'WOOCOMMERCE_USE_CSS', false );` in the child theme functions.php the options setting is ignored and NO stylesheet is loaded, ie has same effect as unchecking the settings box. Note: the value of WOOCOMMERCE_USE_CSS always takes precedence over the WooCommerce Settings page option!
* If either Case 1 or Case 3 applies, if themes/my-child-theme/woocommerce/styles.css exists it will be loaded in place of the default woocommerce stylesheet (plugins/woocommerce/assets/css/woocommerce.css).
* If either Case 2 or 4 applies, as no built-in stylesheet is loaded, all WooCommerce CSS styles need to be added to the theme's main style.css stylesheet
* Note for Genesis child theme developers: For new themes, theme developers can use `define( 'WOOCOMMERCE_USE_CSS', false );` and place all WooCommerce styles in the theme's main stylesheet, or do nothing and let the user handle this via Case 1 or 3.
* The above information is based on WooCommerce 1.4.4

== Changelog ==

= 0.9.9 =
* Released 12 January 2017
* Adds the Genesis Connect Addons tab to the WooCommerce settings page.
* Adds an option to control the products to show per page on the Shop page template (can be overriden by theme).
* Removes the add_theme_support( 'genesis-connect-woocommerce' ); requirement.
* Update activation check function to only verify that Genesis is active.

= 0.9.8 =
* Released 9 July 2014
* Updates genesiswooc_content_product() to reflect WooC 2.1+ templates and correct handling of WooC's page title filter function

= 0.9.7 =
* Released 22 December 2013
* Removed link from Shop breadcrumb when viewing Shop page.

= 0.9.6 =
* Released 18 December 2013
* Fixed bug re missing argument in the_title filter (in template-loader.php). Props Gary Jones.

= 0.9.5 =
* Released 14 March 2013
* add_theme_support( 'woocommerce' ) added to ensure compatibility with WooCommerce 2.0+

= 0.9.4 =
* Released 19 July 2012
* Tweaked archive-product.php and taxonomy.php loop functions to provide compatibility with WooCommerce 1.6.0

= 0.9.3 =
* Released 14 May 2012
* taxonomy.php and archive-product.php now use woocommerce_get_template_part() instead of gencwooc_get_template_part()
* gencwooc_get_template_part() updated to reflect latest version of woocommerce_get_template_part(). Note: gencwooc_get_template_part() will be deprecated in a future version and is only retained for backwards compatibility.

= 0.9.2 =
* Released 15 March 2012
* single-product.php - Single product title template file now hooked in as per WooC 1.5.2

= 0.9.1 =
* Released 6 March 2012
* Fixes call to undefined function error in sp-plugins-integration/genesis-simple-sidebars.php

= 0.9.0 =
* Initial Release
