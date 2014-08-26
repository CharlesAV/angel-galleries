<?php

Route::group(array('prefix' => 'galleries'), function() {

	$controller = 'GalleryController';

	Route::get('/', $controller . '@index');
	Route::get('{slug}', $controller . '@show');
	
	// Items
	$controller = 'GalleryItemController';
	
	Route::get('{slug}/{id}', $controller . '@show');
});

Route::group(array('prefix' => admin_uri('galleries'), 'before' => 'admin'), function() {

	$controller = 'AdminGalleryController';

	Route::get('/', $controller . '@index');
	Route::get('add', $controller . '@add');
	Route::post('add', array(
		'before' => 'csrf',
		'uses' => $controller . '@attempt_add'
	));
	Route::get('edit/{id}', $controller . '@edit');
	Route::post('edit/{id}', array(
		'before' => 'csrf',
		'uses' => $controller . '@attempt_edit'
	));
	Route::post('delete/{id}', array(
		'before' => 'csrf',
		'uses' => $controller . '@delete'
	));
	
	// Items
	$controller = 'AdminGalleryItemController';

	Route::get('{gallery}/items', $controller . '@index');
	Route::get('{gallery}/items/add', $controller . '@add');
	Route::post('{gallery}/items/add', array(
		'before' => 'csrf',
		'uses' => $controller . '@attempt_add'
	));
	Route::get('{gallery}/items/edit/{id}', $controller . '@edit');
	Route::post('{gallery}/items/edit/{id}', array(
		'before' => 'csrf',
		'uses' => $controller . '@attempt_edit'
	));
	Route::post('{gallery}/items/delete/{id}', array(
		'before' => 'csrf',
		'uses' => $controller . '@delete'
	));
	Route::post('{gallery}/items/order', $controller . '@order');
});