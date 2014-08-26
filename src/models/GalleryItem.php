<?php namespace Angel\Galleries;

use Angel\Core\LinkableModel;
use App, Config;

class GalleryItem extends LinkableModel {
	public $table = "galleries_items";
	
	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function changes()
	{
		$Change = App::make('Change');

		return $Change::where('fmodel', 'GalleryItem')
				   	       ->where('fid', $this->id)
				   	       ->with('user')
				   	       ->orderBy('created_at', 'DESC')
				   	       ->get();
	}
	public function gallery()
	{
		return $this->belongsTo(App::make('Gallery'));
	}

	// Handling relationships in controller CRUD methods
	public function pre_delete()
	{
		parent::pre_delete();
		$Change = App::make('Change');
		$Change::where('fmodel', 'GalleryItem')
			        ->where('fid', $this->id)
			        ->delete();
	}

	///////////////////////////////////////////////
	//               Menu Linkable               //
	///////////////////////////////////////////////
	// Menu link related methods - all menu-linkable models must have these
	// NOTE: Always pull models with their languages initially if you plan on using these!
	// Otherwise, you're going to be performing repeated queries.  Naughty.
	public function link()
	{
		$language_segment = (Config::get('core::languages')) ? $this->language->uri . '/' : '';

		return url($language_segment . 'galleries/' . $this->gallery->slug . '/' . $this->id);
	}
	public function link_edit()
	{
		return admin_url('galleries/' . $this->gallery_id . '/items/edit/' . $this->id);
	}
}