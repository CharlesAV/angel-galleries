@extends('core::template')

@section('title', $gallery->title)

@section('meta')
	{{ $gallery->meta_html() }}
@stop

@section('content')
	<div class="row">
		<h1 class="galleries-name">{{ $gallery->name }}</h1>
		<div class="galleries-date">{{ date('F j, Y',strtotime($gallery->date)) }}</div>
		<div class="galleries-html">
			{{ $gallery->html }}
		</div>
	</div>
@stop