<?php namespace Angel\Galleries;

use Angel\Core\LinkableModel;
use App, Config;

class Gallery extends LinkableModel {
	
	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function changes()
	{
		$Change = App::make('Change');

		return $Change::where('fmodel', 'Gallery')
				   	       ->where('fid', $this->id)
				   	       ->with('user')
				   	       ->orderBy('created_at', 'DESC')
				   	       ->get();
	}
	public function items()
	{
		return $this->hasMany(App::make('GalleryItem'))->orderBy('order','asc');
	}

	// Handling relationships in controller CRUD methods
	public function pre_delete()
	{
		parent::pre_delete();
		$Change = App::make('Change');
		$Change::where('fmodel', 'Gallery')
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

		return url($language_segment . 'galleries/' . $this->slug);
	}
	public function link_edit()
	{
		return admin_url('galleries/edit/' . $this->id);
	}
	public function link_edit_items()
	{
		return admin_url('galleries/' . $this->id . '/items');
	}
}