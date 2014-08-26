<?php namespace Angel\Galleries;

use App, View;

class GalleryItemController extends \Angel\Core\AngelController {
	
	public function __construct()
	{
		$this->GalleryItem = $this->data['GalleryItem'] = App::make('GalleryItem');

		parent::__construct();
	}

	public function show($slug,$id)
	{
		// Gallery
		$Gallery = App::make('Gallery');
		$gallery = $Gallery
			->where('slug', $slug)
			->first();
		
		// Item
		$item = $this->GalleryItem->find($id);
		
		if (!$item || !$gallery) App::abort(404);
		$this->data['gallery'] = $gallery;
		$this->data['item'] = $item;
		
		// Return
		return View::make('galleries::galleries.items.show', $this->data);
	}
}