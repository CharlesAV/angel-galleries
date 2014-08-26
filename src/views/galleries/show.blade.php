@extends('core::template')

@section('title', $gallery->name)

@section('css')
<style>
.tiles {
	margin:0px;
	padding:0px;
	list-style:none;
}
.tiles li {
	margin:10px;
	padding:0px;
	list-style:none;
}
</style>
@stop

@section('content')
<div class="row">
	<h1 class="gallery-name">{{ $gallery->name }}</h1>
	<div class="gallery-html">
		{{ $gallery->html }}
	</div>
	<div class="gallery-items">
	@if($gallery->layout == "tiles")
		<ul class="tiles">
		@foreach($gallery->items as $item)
			<li>
			@if($gallery->display == "modal")
				<a href="{{ $item->file }}" title="{{ $item->name }}" class="fancybox" rel="gallery-{{ $gallery->id }}">
			@elseif($gallery->display == "slide-down")
				<a href="javascript:void(0);" onclick="gallery_show({{ $item->id }})">
			@else
				<a href="{{ $item->link() }}" title="{{ $item->name }}">
			@endif
					<img src="{{ $item->file }}" width="300" />
				</a>
			</li>
		@endforeach
		</ul>
	@endif
	</div>
</div>
@stop

@section('js')
	@if($gallery->layout == "tiles")
		{{ HTML::script('packages/angel/galleries/js/isotope.pkgd.min.js') }}
		{{ HTML::script('packages/angel/galleries/js/imagesloaded.pkgd.min.js') }}
		<script type="text/javascript">
		var $container;
		$(document).ready(function() {
			$container = $('.tiles').isotope({
				itemSelector: 'li',
				layoutMode: 'masonry'
			});
			$container.imagesLoaded( function() {
				$container.isotope('layout');
				paginate(1,1);
			});
		});
		</script>
	@endif
@stop