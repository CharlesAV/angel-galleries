@extends('core::admin.template')

@section('title', 'Gallery Items')

@section('js')
	{{ HTML::script('packages/angel/core/js/jquery/jquery-ui.min.js') }}
	<script>
		$(function() {
			$("tbody").sortable(sortObj);
		});
	</script>
@stop

@section('content')
	<div class="row pad">
		<div class="col-sm-8 pad">
			<h1>Gallery Items</h1>
			<a class="btn btn-sm btn-primary" href="{{ admin_url('galleries/'.$gallery->id.'/items/add') }}">
				<span class="glyphicon glyphicon-plus"></span>
				Add
			</a>
		</div>
		<div class="col-sm-4 well">
			{{ Form::open(array('role'=>'form', 'method'=>'get')) }}
				<div class="form-group">
					<label>Search</label>
					<input type="text" name="search" class="form-control" value="{{ $search }}" />
				</div>
				<div class="text-right">
					<input type="submit" class="btn btn-primary" value="Search" />
				</div>
			{{ Form::close() }}
		</div>
	</div>
	@if (Config::get('core::languages') && !$single_language)
		{{ Form::open(array('url'=>admin_uri('galleries/'.$gallery->id.'/items/copy'), 'role'=>'form', 'class'=>'noSubmitOnEnter')) }}
	@endif

	<div class="row">
		<div class="col-sm-9">
			<table class="table table-striped">
				<thead>
					<tr>
						<th style="width:100px;"></th>
						@if (Config::get('core::languages') && !$single_language)
						<th style="width:60px;">Copy</th>
						@endif
						<th style="width:{{ Config::get('galleries::thumbs.t.width') }}px;">Image</th>
						<th>Name</th>
					</tr>
				</thead>
				<tbody data-url="galleries/{{ $gallery->id }}/items/order">
				@if(count($items))
					@foreach ($items as $item)
						<tr data-id="{{ $item->id }}">
							<td>
								<input type="hidden" class="orderInput" value="{{ $item->order }}" />
								<a href="{{ $item->link_edit() }}" class="btn btn-xs btn-default">
									<span class="glyphicon glyphicon-edit"></span>
								</a>
								<button type="button" class="btn btn-xs btn-default handle">
									<span class="glyphicon glyphicon-resize-vertical"></span>
								</button>

							</td>
						@if (Config::get('core::languages') && !$single_language)
							<td>{{ Form::checkbox('ids[]', $item->id, false, array('class'=>'idCheckbox')) }}</td>
						@endif
							<td><img src="{{ $item->thumb('t') }}" /></td>
							<td>{{ $item->name }}</td>
						</tr>
					@endforeach
				@else 
					<tr>
						<td colspan="5" align="center" style="padding:30px;">
							No Gallery Items Found.
						</td>
					</tr>
				@endif
				</tbody>
			</table>
		</div>
	</div>
	@if (Config::get('core::languages') && !$single_language)
		<div class="row pad">
			{{ Form::hidden('all', 0, array('id'=>'all')) }}
			<button type="button" id="copyChecked" class="btn btn-sm btn-primary">Copy checked...</button>
			<button type="button" id="copyAll" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#copyModal">Copy all...</button>
		</div>
		<div class="modal fade" id="copyModal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Copy to...</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<?php
								$language_drop_minus_active = $language_drop;
								unset($language_drop_minus_active[$active_language->id]);
							?>
							{{ Form::label('language_id', 'Language') }}
							{{ Form::select('language_id', $language_drop_minus_active, $active_language->id, array('class' => 'form-control')) }}
						</div>
						<p class="text-right">
							{{ Form::submit('Done', array('class'=>'btn btn-primary')) }}
						</p>
					</div>{{-- Modal --}}
				</div>{{-- Modal --}}
			</div>{{-- Modal --}}
		</div>{{-- Modal --}}
	{{ Form::close() }}
	@endif
@stop