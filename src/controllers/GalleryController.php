<?php namespace Angel\Galleries;

use App, View;

class GalleryController extends \Angel\Core\AngelController {
	
	public function __construct()
	{
		$this->Gallery = $this->data['Gallery'] = App::make('Gallery');

		parent::__construct();
	}
	
	function index()
	{
		// Query
		$objects = $this->Gallery
			->orderBy('date','desc');
			
		// Pagination
		$paginator = $objects->paginate(5);
		$this->data['galleries'] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();
			
		// Return
		return View::make('galleries::galleries.index',$this->data);
	}

	public function show($slug)
	{
		// Item
		$gallery = $this->Gallery
			->where('slug', $slug)
			->first();
		if (!$gallery) App::abort(404);
		$this->data['gallery'] = $gallery;
		
		// Return
		return View::make('galleries::galleries.show', $this->data);
	}

	public function show_language($language_uri = 'en', $slug)
	{
		// Language
		$language = $this->languages->filter(function ($language) use ($language_uri) {
			return ($language->uri == $language_uri);
		})->first();
		if (!$language) App::abort(404);

		//  Item
		$gallery = $this->Gallery
			->where('language_id', $language->id)
			->where('slug', $slug)
			->first();
		if (!$gallery) App::abort(404);
		$this->data['active_language'] = $language;
		$this->data['gallery'] = $gallery;

		// Return
		return View::make('galleries::galleries.show', $this->data);
	}
}