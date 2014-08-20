<?php namespace Angel\Galleries;

use Angel\Core\AngelController;
use App, Input, View, Config;

class AdminGalleryItemController extends AngelController {

	protected $Model	= 'GalleryItem';
	protected $uri		= 'galleries/items'; // Won't work, never used, but required
	protected $plural	= 'items';
	protected $singular	= 'item';
	protected $package	= 'galleries';

	protected $log_changes = true;
	protected $searchable = array(
		'name'
	);
	public $reorderable = true;
	
	// Columns to update on edit/add
	protected static function columns()
	{
		$columns = array(
			'name',
			'description',
			'file'
		);
		if (Config::get('core::languages')) $columns[] = 'language_id';
		return $columns;
	}

	public function index($gallery)
	{
		$Gallery   = App::make('Gallery');
		$this->data['gallery'] = $Gallery->find($gallery);
		
		$GalleryItem   = App::make($this->Model);
		$items = $GalleryItem->withTrashed()->where('gallery_id','=',$gallery);

		if (Config::get('core::languages') && in_array(Config::get('language_models'), $this->Model)) {
			$items = $items->where('language_id', $this->data['active_language']->id);
		}

		if (isset($this->searchable) && count($this->searchable)) {
			$search = Input::get('search') ? urldecode(Input::get('search')) : null;
			$this->data['search'] = $search;

			if ($search) {
				$terms = explode(' ', $search);
				$items = $items->where(function($query) use ($terms) {
					foreach ($terms as $term) {
						$term = '%'.$term.'%';
						foreach ($this->searchable as $column) {
							$query->orWhere($column, 'like', $term);
						}
					}
				});
			}
		}

		$items->orderBy('order','asc');
		$paginator = $items->paginate();
		$this->data[$this->plural] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();

		return View::make('galleries::admin.galleries.items.index', $this->data);
	}
	public function add($gallery) {
		$Gallery   = App::make('Gallery');
		$this->data['gallery'] = $Gallery->find($gallery);
		
		$this->data['action'] = 'add';

		return View::make('galleries::admin.galleries.items.add-or-edit', $this->data);
	}
	public function edit($gallery,$id)
	{
		$Gallery   = App::make('Gallery');
		$this->data['gallery'] = $Gallery->find($gallery);
		
		$GalleryItem = App::make($this->Model);

		$item = $GalleryItem::withTrashed()->find($id);
		$this->data['item'] = $item;
		$this->data['changes'] = $item->changes();
		$this->data['action'] = 'edit';

		return View::make('galleries::admin.galleries.items.add-or-edit', $this->data);
	}

	/**
	 * @param int $id - The ID of the model when editing, null when adding.
	 * @return array - Rules for the validator.
	 */
	public function validate_rules($id = null)
	{
		return array(
			'name' => 'required',
			'file' => 'required'
		);
	}
}