@extends('core::admin.template')

@section('title', ucfirst($action).' Gallery')

@section('css')
	{{ HTML::style('packages/angel/core/js/jquery/jquery.datetimepicker.css') }}
@stop

@section('js')
	{{ HTML::script('packages/angel/core/js/ckeditor/ckeditor.js') }}
	{{ HTML::script('packages/angel/core/js/jquery/jquery.datetimepicker.js') }}
	<script type='text/javascript'>
	var slug_entered = 0;
	$(document).ready(function() {
		$('#name').keyup(function() {
			if(!slug_entered) {
				var slug = $(this).val();
				slug = slug.toLowerCase().replace(/[^a-z0-9\-_]/ig,'-');
				$('#slug').val(slug);
			}
		});
		$('#slug').keyup(function() {
			slug_entered = 1;	
		});
		var slug = $('#slug').val();
		if(slug.length) slug_entered = 1;
	});
	</script>
@stop

@section('content')
	<h1>{{ ucfirst($action) }} Gallery</h1>
	@if ($action == 'edit')
		{{ Form::open(array('role'=>'form',
							'url'=>admin_uri('galleries/delete/'.$gallery->id),
							'class'=>'deleteForm',
							'data-confirm'=>'Delete this gallery forever?  This action cannot be undone!')) }}
			<input type="submit" class="btn btn-sm btn-danger" value="Delete" />
		{{ Form::close() }}
	@endif

	@if ($action == 'edit')
		{{ Form::model($gallery, array('role'=>'form')) }}
	@elseif ($action == 'add')
		{{ Form::open(array('role'=>'form')) }}
	@endif

	@if (isset($menu_id))
		{{ Form::hidden('menu_id', $menu_id) }}
	@endif

	<div class="row">
		<div class="col-md-9">
			<table class="table table-striped">
				<tbody>
					@if (Config::get('core::languages'))
						<tr>
							<td>
								{{ Form::label('language_id', 'Language') }}
							</td>
							<td>
								<div style="width:300px">
									{{ Form::select('language_id', $language_drop, $active_language->id, array('class' => 'form-control')) }}
								</div>
							</td>
						</tr>
					@endif
					<tr>
						<td>
							<span class="required">*</span>
							{{ Form::label('name', 'Name') }}
						</td>
						<td>
							<div style="width:300px">
								{{ Form::text('name', null, array('class'=>'form-control', 'required')) }}
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<span class="required">*</span>
							{{ Form::label('slug', 'Slug') }}
						</td>
						<td>
							<div style="width:300px">
								{{ Form::text('slug', null, array('class'=>'form-control', 'required')) }}
							</div>
						</td>
					</tr>
					<tr>
						<td>
							{{ Form::label('html', 'Description') }}
						</td>
						<td>
							{{ Form::textarea('html', null, array('class'=>'ckeditor')) }}
						</td>
					</tr>
					<?php
					$options = array(
						'' => '',
						'tiles' => 'Tiles'
					);
					?>
					<tr>
						<td>
							{{ Form::label('layout', 'Layout') }}
						</td>
						<td>
							<div style="width:300px">
								{{ Form::select('layout', $options, null, array('class' => 'form-control')) }}
							</div>
						</td>
					</tr>
					<?php
					$options = array(
						'' => '',
						'page' => 'Page',
						'modal' => 'Modal',
						'expand' => 'Expand'
					);
					?>
					<tr>
						<td>
							{{ Form::label('display', 'Item Display') }}
						</td>
						<td>
							<div style="width:300px">
								{{ Form::select('display', $options, null, array('class' => 'form-control')) }}
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>{{-- Left Column --}}
		<div class="col-md-3">
			@if ($action == 'edit')
				<div class="expandBelow">
					<span class="glyphicon glyphicon-chevron-down"></span> Change Log
				</div>
				<div class="expander changesExpander">
					@include('core::admin.changes.log')
				</div>{{-- Changes Expander --}}
			@endif
		</div>{{-- Right Column --}}
	</div>{{-- Row --}}
	<div class="text-right pad">
		<input type="submit" class="btn btn-primary" value="Save" />
	</div>
	{{ Form::close() }}
@stop