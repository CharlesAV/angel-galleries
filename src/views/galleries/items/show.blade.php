@extends('core::template')

@section('title', ($item->name ? $item->name." | " : "").$gallery->name)

@section('css')
@stop

@section('content')
<div class="row">
	<h1 class="gallery-item-name">{{ $item->name }}</h1>
	<div class="gallery-item-description">
		{{ $gallery->description }}
	</div>
	<div class="gallery-item-image">
		<img src="{{ $item->file }}" />
	</div>
</div>
@stop

@section('js')
@stop