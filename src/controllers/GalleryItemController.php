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
		// Item
		$gallery = $this->GalleryItem->find($id);
		if (!$gallery || !$gallery->is_published()) App::abort(404);
		$this->data['gallery'] = $gallery;
		
		// Return
		return View::make('galleries::galleries.items.show', $this->data);
	}
}