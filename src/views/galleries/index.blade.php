@extends('core::template')

@section('title','Galleries')

@section('content')
	<div class="row">
		@foreach($galleries as $gallery) 
		<div class="galleries-item">
			<h1 class="galleries-name"><a href="{{ $gallery->link() }}">{{ $gallery->name }}</a></h1>
			<div class="galleries-date">{{ date('F j, Y',strtotime($gallery->date)) }}</div>
			<div class="galleries-html">
				{{ $gallery->html }}
			</div>
			<hr />
		</div>
		@endforeach
		
		<div class="row text-center">
			{{ $links }}
		</div>
	</div>
@stop