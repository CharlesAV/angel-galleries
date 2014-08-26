@extends('core::admin.template')

@section('title', ucfirst($action).' Gallery Item')

@section('css')
@stop

@section('js')
@stop

@section('content')
	<h1>{{ ucfirst($action) }} Gallery Item</h1>
	@if ($action == 'edit')
		{{ Form::open(array('role'=>'form',
							'url'=>admin_uri('galleries/'.$item->gallery_id.'/items/delete/'.$item->id),
							'class'=>'deleteForm',
							'data-confirm'=>'Delete this item forever?  This action cannot be undone!')) }}
			<input type="submit" class="btn btn-sm btn-danger" value="Delete" />
		{{ Form::close() }}
	@endif

	@if ($action == 'edit')
		{{ Form::model($item, array('role'=>'form')) }}
	@elseif ($action == 'add')
		{{ Form::open(array('role'=>'form')) }}
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
							{{ Form::label('description', 'Description') }}
						</td>
						<td>
							<div style="width:300px">
								{{ Form::textarea('description') }}
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<span class="required">*</span>	
							{{ Form::label('file', 'Image') }}
						</td>
						<td>
							{{ Form::text('file', NULL, array('class'=>'form-control','style' => "float:left;width:300px;")) }}
							<button type="button" class="btn btn-default imageBrowse" style="float:left">Browse...</button>
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