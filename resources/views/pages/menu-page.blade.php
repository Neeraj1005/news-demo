@extends('layouts.app')

@section('title', meta_title($menu_page->title))
@section('url', meta_url() )
@section('description', meta_description($menu_page->body) )

@section('content')
<div class="container" id="app">
    <div class="row featured-post-heading">
        <div class="col-md-12 mt-5 mb-3">
            <h2 class="mb-4">{{ $menu_page->title }}</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9 pl-0 pr-5">
            <div class="col-md-12">
                @if($menu_page->image)
                    <img src="{{ $menu_page->imageUrl() }}" class="img-fluid rounded mb-3"
                        alt="{{ $menu_page->slug }}">
                @endif
                <p>
                    {!! $menu_page->body !!}
                </p>
            </div>
        </div>
        <livewire:landing-page.right-sidebar />
    </div>
</div>

@endsection
