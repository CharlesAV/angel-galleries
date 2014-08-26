Angel Galleries
==============
This is a galleries module for the [Angel CMS](https://github.com/JVMartin/angel).

This module allows you to create multiple 'galleries', each with their own collection of images.

Installation
------------
Add the following requirements to your `composer.json` file:
```javascript
"require": {
	"angel/galleries": "dev-master"
},
```

Issue a `composer update` to install the package.

Add the following service provider to your `providers` array in `app/config/app.php`:
```php
'Angel\Galleries\GalleriesServiceProvider',
'Intervention\Image\ImageServiceProvider'
```

Add the following alias to your `alias` array in `app/config/app.php`:
```php,
'Image' 		  => 'Intervention\Image\Facades\Image'
```

Issue the following commands:
```bash
php artisan migrate --package="angel/galleries"  # Run the migrations
php artisan asset:publish                        # Publish the assets
php artisan config:publish angel/galleries       # Publish the config
```

Open up your `app/config/packages/angel/core/config.php` and add the galleries routes to the `menu` array:
```php
'menu' => array(
	'Pages'     => 'pages',
	'Menus'     => 'menus',
	'Galleries' => 'galleries', // <--- Add this line
	'Users'     => 'users',
	'Settings'  => 'settings'
),
```

...and the menu-linkable models to the `linkable_models` array:
```php
'linkable_models' => array(
	'Page'             => 'pages',
	'Gallery'          => 'galleries', // <--- Add this line
)
```

Open up your `app/config/packages/angel/galleries/config.php` and define your desired thumbnail settings. The default is:
```php
array(
	'thumbs' => array(
		't' => array(
			'width' => 75,
			'height' => 75,
			'crop' => 1,
			'enlarge' => 1,
			'path' => '/uploads/images/t/'
		),
		's' => array(
			'width' => 150,
			'height' => 150,
			'crop' => 1,
			'enlarge' => 1,
			'path' => '/uploads/images/s/'
		),
		'm' => array(
			'width' => 300,
			'height' => 300,
			'crop' => 0,
			'enlarge' => 1,
			'path' => '/uploads/images/m/'
		),
		'l' => array(
			'width' => 600,
			'height' => 600,
			'crop' => 0,
			'enlarge' => 0,
			'path' => '/uploads/images/l/'
		)
	)
)
```

Make sure you actually create the directories where the thumbnail images will be saved. These paths are relative to the /public/ folder (ex: /public/uploads/images/t/).
