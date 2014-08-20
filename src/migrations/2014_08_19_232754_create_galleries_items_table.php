<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGalleriesItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('galleries_items', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('gallery_id');
			$table->string('name');
			$table->text('description');
			$table->string('file');
			$table->integer('order');
			$table->timestamps();

			if (Config::get('core::languages')) {
				$table->integer('language_id')->unsigned()->default(1);
				$table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
			}
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('galleries_items');
	}

}
