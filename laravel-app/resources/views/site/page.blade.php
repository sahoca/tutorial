@extends('site.layout')
@section('meta_title', ($page->meta_title ?: $page->title) . ' — ' . ($siteSettings['site_name'] ?? ''))
@section('meta_description', $page->meta_description)

@section('content')
    <article class="max-w-3xl mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold mb-8">{{ $page->title }}</h1>
        <div class="prose max-w-none">{!! $page->content !!}</div>
    </article>
@endsection
