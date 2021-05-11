<?php defined('SYSPATH') or die('No direct access allowed.');

$app_version = '1.51';

return [

	'default' => [

		'jquery' => [
			'core' => [
				'file' => 'assets/vendor/jquery/jquery-3.5.1.min.js',
			],
			'autocomplete' => [
				'file' => 'assets/vendor/jquery/jquery.autocomplete.min.js',
				'requires' => ['jquery.core'],
			],
		],

		'base' => [
			'files' => [
//				'https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap&subset=cyrillic,cyrillic-ext',
				'assets/css/main.min.css?v='.$app_version,
				'assets/js/main.js?v='.$app_version,
			],
			'requires' => ['jquery.core'],
		],

		'filters' => [
			'files' => [
				'assets/js/filters.js?v='.$app_version,
			],
			'requires' => ['base', 'jquery'],
		],

		'fotorama' => [
			'files' => [
				'assets/vendor/fotorama/fotorama.css',
				'assets/vendor/fotorama/fotorama.js',
			],
			'requires' => ['jquery.core'],
		],

		'yandex_map' => [
			'files' => [
				'https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey='.Settings::get('integrations.yandex_api_key', ''),
			],
		],

		'carpicker' => [
			'files' => [
				'assets/js/helpers/carpicker.js?v='.$app_version,
			],
			'requires' => ['base', 'jquery'],
		],

		'sizepicker' => [
			'files' => [
				'assets/js/helpers/sizepicker.js?v='.$app_version,
			],
			'requires' => ['base', 'jquery'],
		],

		'splide' => [
			'files' => [
				'assets/vendor/splide/splide.min.js?v='.$app_version,
				'assets/vendor/splide/css/splide.min.css?v='.$app_version,
			],
			'requires' => ['base', 'jquery'],
		],

		'nouislider' => [
			'files' => [
				'assets/vendor/nouislider/nouislider.min.js?v='.$app_version,
				'assets/vendor/nouislider/nouislider.min.css?v='.$app_version,
			],
			'requires' => ['base', 'jquery'],
		],

		'auth' => [
			'sign_in' => [
				'files' => [
					'assets/js/auth/sign_in.js?v='.$app_version,
				],
				'requires' => ['base', 'jquery'],
			],
			'sign_up' => [
				'files' => [
					'assets/admin/css/admin.css?v='.$app_version,
					'assets/admin/css/vof.css?v='.$app_version,
				],
			],
			'verify_email' => [
				'files' => [
					'assets/admin/css/admin.css?v='.$app_version,
					'assets/admin/css/vof.css?v='.$app_version,
				],
			],
			'complete_sign_up' => [
				'files' => [
					'assets/admin/css/admin.css?v='.$app_version,
					'assets/admin/css/vof.css?v='.$app_version,
				],
			],
		],
		'catalog' => [
			'tire' => [
				'file' => 'assets/js/catalog/tire.js?v='.$app_version,
				'requires' => ['base', 'filters'],
			],
			'rim' => [
				'file' => 'assets/js/catalog/rim.js?v='.$app_version,
				'requires' => ['base', 'filters'],
			],
			'ttire' => [
				'file' => 'assets/js/catalog/ttire.js?v='.$app_version,
				'requires' => ['base', 'filters'],
			],
			'mtire' => [
				'file' => 'assets/js/catalog/mtire.js?v='.$app_version,
				'requires' => ['base', 'filters'],
			],
			'akb' => [
				'file' => 'assets/js/catalog/akb.js?v='.$app_version,
				'requires' => ['base', 'filters'],
			],
			'na_karte' => [
				'file' => 'assets/js/catalog/na_karte.js?v='.$app_version,
				'requires' => ['base', 'yandex_map'],
			],
		],
		'product' => [
			'sizepickerline' => [
				'file' => 'assets/js/product/sizepickerline.js?v='.$app_version,
				'requires' => ['base', 'filters'],
			],
			'get' => [
				'file' => 'assets/js/product/get.js?v='.$app_version,
				'requires' => ['base', 'fotorama', 'product.sizepickerline', 'splide'],
			],
			'otzyvy' => [
				// Используем тот же файл, чтобы добавлять отзывы
				'file' => 'assets/js/product/get.js?v='.$app_version,
				'requires' => ['base'],
			],
			'obzory' => [
				// Используем тот же файл, чтобы добавлять обзоры
				'file' => 'assets/js/product/get.js?v='.$app_version,
				'requires' => ['base'],
			],
			'kupit' => [
				'files' => [
					'assets/js/product/get.js?v='.$app_version,
					'assets/js/product/na_karte.js?v='.$app_version
				],
				'requires' => ['base', 'filters', 'product.sizepickerline', 'yandex_map'],
			],
		],
		'page' => [
			'index' => [
				'file' => 'assets/js/page/index.js?v='.$app_version,
				'requires' => ['base', 'carpicker', 'splide', 'sizepicker', 'nouislider'],
			],
			'blog' => [
				'file' => 'assets/js/page/blog.js?v='.$app_version,
				'requires' => ['base', 'filters'],
			],
			'shincalc' => [
				'file' => 'assets/js/page/shincalc.js?v='.$app_version,
				'requires' => ['base', 'filters'],
			],
		],
	],
	'admin' => [
		'jquery' => [
			'core' => [
				'file' => 'assets/admin/vendor/jquery/jquery-3.5.1.min.js',
			],
			'autocomplete' => [
				'file' => 'assets/admin/vendor/jquery/jquery.autocomplete.min.js',
				'requires' => ['jquery.core'],
			],
			'scrollTo' => [
				'file' => 'assets/admin/vendor/jquery/jquery.scrollTo.min.js',
				'requires' => ['jquery.core'],
			],
			'cookie' => [
				'file' => 'assets/admin/vendor/jquery/jquery.cookie.js',
				'requires' => ['jquery.core'],
			],
		],

		'jquery.ui' => [
			'files' => [
				'assets/admin/vendor/jquery-ui/jquery-ui.min.css',
				/*'assets/vendor/jquery-ui/jquery-ui.theme.min.css',*/
				'assets/admin/vendor/jquery-ui/jquery-ui.min.js',
			],
			'required' => ['jquery.core'],
		],

		'base' => [
			'files' => [
				'https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap&subset=cyrillic,cyrillic-ext',
				'assets/admin/css/admin.css?v='.$app_version,
				'assets/admin/css/vof.css?v='.$app_version,
				'assets/admin/js/base/main.js?v='.$app_version,
				'assets/admin/js/base/vof.js?v='.$app_version,
			],
			// TODO Почистить от мусора
			'requires' => ['jquery.core', 'jquery.autocomplete', 'font-awesome', 'select2', 'colpick', 'color2color', 'dragula'],
		],

		'helpers' => [
			'datepicker' => [
				'file' => 'assets/admin/js/helpers/datepicker.js?v='.$app_version,
			],
			'requires' => ['base'],
		],

		'select2' => [
			'files' => [
				// 'assets/vendor/select2/css/select2.min.css',
				'assets/admin/vendor/select2/js/select2.full.min.js',
			],
			'requires' => ['jquery.core'],
		],

		'colpick' => [
			'files' => [
				'assets/admin/vendor/colpick/colpick.css',
				'assets/admin/vendor/colpick/colpick.js',
			],
			'requires' => ['jquery.core'],
		],

		'color2color' => [
			'file' => '/assets/admin/vendor/color2color/colorcolor.min.js',
			'requires' => ['jquery.core'],
		],

		// Classes like fa-* (used exceptionally when needed to be specified from admin panel]
		'font-awesome' => [
			'files' => [
				'assets/admin/css/fontawesome.css',
				'assets/admin/css/solid.css',
				'assets/admin/css/regular.css',
			],
		],

		'tinymce' => [
			'files' => [
				'assets/admin/vendor/tinymce/tinymce.min.js',
			],
			'required' => ['jquery.core'],
		],

		'inputmask' => [
			'files' => [
				'assets/admin/vendor/inputmask/jquery.inputmask.min.js',
			],
			'required' => ['jquery.core'],
		],

		'fancytree' => [
			'files' => [
				'assets/admin/vendor/fancytree/skin-win8/ui.fancytree.min.css',
				'assets/admin/vendor/fancytree/jquery.fancytree-all-deps.min.js',
			],
			'required' => ['jquery.core'],
		],

		'dragula' => [
			'files' => [
				'assets/admin/vendor/dragula/dragula.min.css',
				'assets/admin/vendor/dragula/dragula.min.js',
			],
		],

		'api_maps_yandex' => [
			'files' => [
				'https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey='.Settings::get('integrations.yandex_api_key', ''),
				'assets/admin/js/helpers/yandex_map.js',
			],
		],
/*		'auth' => [
			'sign_in' => [
				'file' => 'assets/admin/js/auth/sign_in.js?v='.$app_version,
				'requires' => ['base'],
			],
		],*/

		'content' => [
			'file' => 'assets/admin/js/content.js?v='.$app_version,
			'requires' => ['base', 'fancytree', 'jquery.ui', 'tinymce', 'dragula'],
		],

		'user' => [
			'list' => [
				'file' => 'assets/admin/js/user/list.js?v='.$app_version,
				'requires' => ['base'],
			],
			'profile' => [
				'file' => 'assets/admin/js/user/profile.js?v='.$app_version,
				'requires' => ['base', 'helpers.datepicker'],
			],
			'update' => [
				'file' => 'assets/admin/js/user/update.js?v='.$app_version,
				'requires' => ['base'],
			],
			'create' => [
				'file' => 'assets/admin/js/user/create.js?v='.$app_version,
				'requires' => ['base'],
			],
			'access' => [
				'file' => 'assets/admin/js/user/access.js?v='.$app_version,
				'requires' => ['base', 'fancytree'],
			],
		],
		'shop' => [
			'list' => [
				'file' => 'assets/admin/js/shop/list.js?v='.$app_version,
				'requires' => ['base'],
			],
			'get' => [
				'file' => 'assets/admin/js/shop/get.js?v='.$app_version,
				'requires' => ['base', 'api_maps_yandex'],
			],
			'geopoints' => [
				'file' => 'assets/admin/js/shop/geopoints.js?v='.$app_version,
				'requires' => ['base', 'api_maps_yandex'],
			],
			'users' => [
				'file' => 'assets/admin/js/shop/users.js?v='.$app_version,
				'requires' => ['base'],
			],
			'analytics' => [
				// Переименовано в analyt1cs, чтобы не блокироваться AD Blocker-ами
				'file' => 'assets/admin/js/shop/analyt1cs.js?v='.$app_version,
				'requires' => ['base', 'helpers.datepicker'],
			],
			'bids' => [
				'file' => 'assets/admin/js/shop/bids.js?v='.$app_version,
				'requires' => ['base'],
			],
			'balance' => [
				'file' => 'assets/admin/js/shop/balance.js?v='.$app_version,
				'requires' => ['base'],
			],
			'invoices' => [
				'files' => ['assets/admin/js/shop/invoices.js?v='.$app_version],
				'requires' => ['base'],
			],
			'acts' => [
				'files' => ['assets/admin/js/shop/acts.js?v='.$app_version],
				'requires' => ['base'],
			],
		],
		'shop_pricelist' => [
			'list' => [
				'file' => 'assets/admin/js/shop_pricelist/list.js?v='.$app_version,
				'requires' => ['base'],
			],
			'create' => [
				'file' => 'assets/admin/js/shop_pricelist/create.js?v='.$app_version,
				'requires' => ['base'],
			],
			'update' => [
				'file' => 'assets/admin/js/shop_pricelist/update.js?v='.$app_version,
				'requires' => ['base'],
			],
			'product_types' => [
				'file' => 'assets/admin/js/shop_pricelist/product_types.js?v='.$app_version,
				'requires' => ['base'],
			],
			'city_options' => [
				'file' => 'assets/admin/js/shop_pricelist/city_options.js?v='.$app_version,
				'requires' => ['base'],
			],
		],
		'reports' => [
			'products' => [
				'file' => 'assets/admin/js/reports/products.js?v='.$app_version,
				'requires' => ['base', 'helpers.datepicker'],
			],
			'shop_offers' => [
				'file' => 'assets/admin/js/reports/shop_offers.js?v='.$app_version,
				'requires' => ['base'],
			],
		],
		'settings' => [
			'settings' => [
				'file' => 'assets/admin/js/settings/content.js?v='.$app_version,
				'requires' => ['base', 'tinymce'],
			],
			'tiretests_profiles' => [
				'file' => 'assets/admin/js/settings/tiretests_profiles.js?v='.$app_version,
				'requires' => ['base'],
			],
			'cities' => [
				'file' => 'assets/admin/js/settings/cities.js?v='.$app_version,
				'requires' => ['base'],
			],
			'sidelinks' => [
				'file' => 'assets/admin/js/settings/sidelinks.js?v='.$app_version,
				'requires' => ['base'],
			],
			'tags' => [
				'file' => 'assets/admin/js/settings/tags.js?v='.$app_version,
				'requires' => ['base', 'tinymce'],
			],
		],
		'reference' => [
			'recognize_products' => [
				'file' => 'assets/admin/js/reference/recognize_products.js?v='.$app_version,
				'requires' => ['base'],
			],
			'ws_import_rules' => [
				'file' => 'assets/admin/js/reference/ws_import_rules.js?v='.$app_version,
				'requires' => ['base'],
			],
		],
	],
];
