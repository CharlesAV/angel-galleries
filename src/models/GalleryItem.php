<?php namespace Angel\Galleries;

use Angel\Core\LinkableModel;
use App, Config, Image;

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
			        
		// Thumbs
		/*if($this->file) {
			# Should really check if these are used anywhere else, but no easy way to do that in other modules yet so just leaving them for now
			
			// Name
			$name = basename(public_path().$this->file);
			
			// Thumbs
			$thumbs = Config::get('galleries::thumbs');
			foreach($thumbs as $k => $v) {
				if(file_exists(public_path().$v['path'].$name)) unlink(public_path().$v['path'].$name);
			}
		}*/
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
	
	function thumbs() {
		if(!$this->file) return;
		
		// Name
		$name = basename(public_path().$this->file);
		
		// Thumbs
		$thumbs = Config::get('galleries::thumbs');
		foreach($thumbs as $k => $v) {
			// Image
			$image = Image::make(public_path().$this->file);
			
			// Resize
			if(!$v['crop'] and !$v['enlarge']) $image->resize($v['width'], $v['height'],function($constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			});
			else if(!$v['crop']) $image->resize($v['width'], $v['height'],function($constraint) {
				$constraint->aspectRatio();
			});
			else if(!$v['enlarge']) $image->resize($v['width'], $v['height'],function($constraint) {
				$constraint->upsize();
			});
			else $image->resize($v['width'], $v['height']);
			/*$image->resize($v['width'], $v['height'],function($constraint) {
				if(!$v['crop']) $constraint->aspectRatio();
				if(!$v['enlarge']) $constraint->upsize();
			});*/
			
			// Save
			$image->save(public_path().$v['path'].$name);
		}
	}
	
	function thumb($k) {
		if(!$this->file) return;
		
		// Name
		$name = basename(public_path().$this->file);
		
		// Thumb
		$v = Config::get('galleries::thumbs.'.$k);
		
		// URL
		$url = $v['path'].$name;
		
		// Exists?
		if(!file_exists(public_path().$url)) return;
		
		// Return
		return $url;
	}
}