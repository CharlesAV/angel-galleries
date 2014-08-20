<?php namespace Angel\Galleries;

use Illuminate\Support\ServiceProvider;

class GalleriesServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('angel/galleries');
		
		include __DIR__ . '../../../routes.php';

		$bindings = array(
			// Models
			'Gallery'        => '\Angel\Galleries\Gallery',
			'GalleryItem'    => '\Angel\Galleries\GalleryItem',
	
			// Controllers
			'GalleryController'             => '\Angel\Galleries\GalleryController',
			'AdminGalleryController'        => '\Angel\Galleries\AdminGalleryController',
			'GalleryItemController'         => '\Angel\Galleries\GalleryItemController',
			'AdminGalleryItemController'    => '\Angel\Galleries\AdminGalleryItemController'
		);
		
		foreach ($bindings as $name=>$class) {
			$this->app->singleton($name, function() use ($class) {
				return new $class;
			});
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
