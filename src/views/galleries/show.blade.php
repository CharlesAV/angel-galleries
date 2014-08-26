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
			@if($gallery->display == "expand")
				<div class="gallery-item-full" id="gallery-item-full-{{ $item->id }}" style="display:none;">
					<div class="gallery-item-full-image"><img src="{{ $item->thumb('l') }}" /></div>
				@if($item->name)
					<div class="gallery-item-full-name">{{ $item->name }}</div>
				@endif
				@if($item->description)
					<div class="gallery-item-full-description">{{ $item->description }}</div>
				@endif
				</div>
			@endif
				<div class="gallery-item-preview" id="gallery-item-preview-{{ $item->id }}">
			@if($gallery->display == "modal")
					<a href="{{ $item->file }}" title="{{ $item->name }}" class="fancybox" rel="gallery-{{ $gallery->id }}">
			@elseif($gallery->display == "expand")
					<a href="javascript:void(0);" onclick="gallery_item_expand({{ $item->id }})">
			@else
					<a href="{{ $item->link() }}" title="{{ $item->name }}">
			@endif
						<img src="{{ $item->thumb('m') }}" />
					</a>
				</div>
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
				layoutMode: 'masonry',
				masonry: {
					columnWidth: $('.tiles li').first().css({float:'left'}).outerWidth(true) // It'll calculat this, but want to 'define' it as we may expand the first item and, if undefined, it would recalculate the columnWidth
				}
			});
			$container.imagesLoaded( function() {
				$container.isotope('layout');
			});
		});
		</script>
	@endif
	
	@if($gallery->display == "expand")
		<script type="text/javascript">
		function gallery_item_expand(id) {
			$('.gallery-item-full-open').hide().removeClass('gallery-item-full-open');
			$('.gallery-item-preview-closed').show().removeClass('gallery-item-preview-closed');
			
			var $display = $('#gallery-item-full-'+id);
			if(!$display.is(':visible')) {
				$display.show().addClass('gallery-item-full-open');
				$('#gallery-item-preview-'+id).hide().addClass('gallery-item-preview-closed');
			}
			
			$container.isotope('layout');
		}
		</script>
	@endif
@stop