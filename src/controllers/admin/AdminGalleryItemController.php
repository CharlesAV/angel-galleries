<?php namespace Angel\Galleries;

use Angel\Core\AngelController;
use App, Input, View, Config, Validator, Redirect, Auth;

class AdminGalleryItemController extends AngelController {
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
		
		$GalleryItem   = App::make('GalleryItem');
		$items = $GalleryItem->withTrashed()->where('gallery_id','=',$gallery);

		if (Config::get('core::languages') && in_array(Config::get('language_models'), 'GalleryItem')) {
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
		$this->data['items'] = $paginator->getCollection();
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
	

	public function attempt_add($gallery)
	{
		$GalleryItem = App::make('GalleryItem');

		$errors = $this->validate();
		if (count($errors)) {
			return Redirect::to(admin_url('galleries/'.$gallery.'/items/add'))->withInput()->withErrors($errors);
		}

		$object = new $GalleryItem;
		foreach(static::columns() as $column) {
			$object->{$column} = Input::get($column);
		}
		if (isset($this->reorderable) && $this->reorderable) {
			$object->order = $GalleryItem::count();
		}
		$object->gallery_id = $gallery;
		$object->save();

		if (method_exists($this, 'after_save')) $this->after_save($object);

		return Redirect::to(admin_url('galleries/'.$gallery.'/items'))->with('success', '<p>Gallery item successfully created.</p>');
	}
	
	public function edit($gallery,$id)
	{
		$Gallery   = App::make('Gallery');
		$this->data['gallery'] = $Gallery->find($gallery);
		
		$GalleryItem = App::make('GalleryItem');

		$item = $GalleryItem::withTrashed()->find($id);
		$this->data['item'] = $item;
		$this->data['changes'] = $item->changes();
		$this->data['action'] = 'edit';

		return View::make('galleries::admin.galleries.items.add-or-edit', $this->data);
	}

	public function attempt_edit($gallery,$id)
	{
		$GalleryItem  = App::make('GalleryItem');
		$Change = App::make('Change');

		$errors = $this->validate($id);
		if (count($errors)) {
			return Redirect::to(admin_url('galleries/'.$gallery.'/items/edit/'.$id))->withInput()->withErrors($errors);
		}

		$object  = $GalleryItem::withTrashed()->findOrFail($id);
		$changes = array();

		foreach (static::columns() as $column) {
			$new_value = Input::get($column);

			if (isset($this->log_changes) && $this->log_changes && $object->{$column} != $new_value) {
				$changes[$column] = array(
					'old' => $object->{$column},
					'new' => $new_value
				);
			}

			$object->{$column} = $new_value;
		}
		$object->save();

		if (method_exists($this, 'after_save')) $this->after_save($object, $changes);

		if (count($changes)) {
			$change = new $Change;
			$change->user_id = Auth::user()->id;
			$change->fmodel  = 'GalleryItem';
			$change->fid     = $object->id;
			$change->changes = json_encode($changes);
			$change->save();
		}

		return Redirect::to(admin_url('galleries/'.$gallery.'/items/edit/'.$object->id))->with('success', '
			<p>Gallery item successfully updated.</p>
			<p><a href="' . admin_url('galleries/'.$gallery.'/items') . '">Return to index</a></p>
		');
	}

	/**
	 * Validate all input when adding or editing.
	 *
	 * @param int $id - (Optional) ID of member beind edited
	 * @return array - An array of error messages to show why validation failed
	 */
	public function validate($id = null)
	{
		$validator = Validator::make(Input::all(), $this->validate_rules($id));
		$errors = ($validator->fails()) ? $validator->messages()->toArray() : array();
		return $errors;
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
	
	

	/**
	 * AJAX for reordering menu items
	 */
	public function order()
	{
		$GalleryItem   = App::make('GalleryItem');
		$orders  = Input::get('orders');
		$objects = $GalleryItem::whereIn('id', array_keys($orders))->get();

		foreach ($objects as $object) {
			$object->order = $orders[$object->id];
			$object->save();
		}

		return 1;
	}

	/**
	 * Called after delete/restore/etc. to ensure that the 'gap' in orders is filled in.
	 */
	public function reorder()
	{
		if (!isset($this->reorderable) || !$this->reorderable) return;
		$GalleryItem = App::make('GalleryItem');

		$objects = $GalleryItem::orderBy('order')->get();

		$order = 0;
		foreach ($objects as $object) {
			$object->order = $order++;
			$object->save();
		}
	}

	public function delete($gallery, $id, $ajax = false)
	{
		$GalleryItem = App::make('GalleryItem');

		$object = $GalleryItem::find($id);
		if (method_exists($object, 'pre_delete')) {
			$object->pre_delete();
		}
		$object->delete();

		$this->reorder();

		if ($ajax) return 1;

		return Redirect::to(admin_url('galleries/'.$gallery.'/items'))->with('success', '
			<p>Gallery item successfully deleted forever.</p>
		');
	}
}