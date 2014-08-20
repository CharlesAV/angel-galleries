<?php namespace Angel\Galleries;

use Angel\Core\AdminCrudController;
use App, Input, View, Config;

class AdminGalleryController extends AdminCrudController {

	protected $Model	= 'Gallery';
	protected $uri		= 'galleries';
	protected $plural	= 'galleries';
	protected $singular	= 'gallery';
	protected $package	= 'galleries';

	protected $log_changes = true;
	protected $searchable = array(
		'name',
		'slug'
	);

	// Columns to update on edit/add
	protected static function columns()
	{
		$columns = array(
			'name',
			'slug',
			'html',
			'layout',
			'display'
		);
		if (Config::get('core::languages')) $columns[] = 'language_id';
		return $columns;
	}

	public function index()
	{
		$Gallery   = App::make($this->Model);
		$galleries = $Gallery->withTrashed();

		if (Config::get('core::languages') && in_array(Config::get('language_models'), $this->Model)) {
			$galleries = $galleries->where('language_id', $this->data['active_language']->id);
		}

		if (isset($this->searchable) && count($this->searchable)) {
			$search = Input::get('search') ? urldecode(Input::get('search')) : null;
			$this->data['search'] = $search;

			if ($search) {
				$terms = explode(' ', $search);
				$galleries = $galleries->where(function($query) use ($terms) {
					foreach ($terms as $term) {
						$term = '%'.$term.'%';
						foreach ($this->searchable as $column) {
							$query->orWhere($column, 'like', $term);
						}
					}
				});
			}
		}

		$galleries->orderBy('id','asc');
		$paginator = $galleries->paginate();
		$this->data[$this->plural] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();

		return View::make($this->view('index'), $this->data);
	}

	public function edit($id)
	{
		$Gallery = App::make($this->Model);

		$gallery = $Gallery::withTrashed()->find($id);
		$this->data['gallery'] = $gallery;
		$this->data['changes'] = $gallery->changes();
		$this->data['action'] = 'edit';

		return View::make($this->view('add-or-edit'), $this->data);
	}

	/**
	 * @param int $id - The ID of the model when editing, null when adding.
	 * @return array - Rules for the validator.
	 */
	public function validate_rules($id = null)
	{
		return array(
			'name' => 'required',
			'slug' => 'required|alpha_dash|unique:galleries,slug,' . $id
		);
	}
}