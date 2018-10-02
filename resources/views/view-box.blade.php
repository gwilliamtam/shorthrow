@extends('layouts.app')

@php
    $contentTypeIcons = [
        'code' => '<i class="fas fa-font"></i>',
        'text' => '<i class="fas fa-code"></i>',
        'url' => '<i class="fas fa-link"></i>',
        'image' => '<i class="far fa-image"></i>',
    ]
@endphp

@section('content')
    @if(empty($content))
        <div class="title m-b-md">
            Shorthrow
        </div>
    @else
        <div class="container">
            <div class="card">
                <div class="card-header">
                    {{ $viewUri }}
                    @if(array_key_exists($contentType, $contentTypeIcons))
                        <div class="content-type-icon">
                            <span class="badge badge-pill badge-secondary">{!! $contentTypeIcons[$contentType] !!}</span>
                        </div>
                    @else
                        <small>({{ empty($contentType) ? 'text' : $contentType}})</small>
                    @endif
                </div>
                <div class="card-body">
                    @if($contentType == 'text')
                        {{ $content }}
                    @endif
                    @if($contentType == 'code')
                    <textarea style="width: 100%; height: 400px; border: none">{{ $content }}</textarea>
                    @endif
                    @if($contentType == 'url')
                    <a href="{!! $content !!}">{!! $content !!}</a>
                    @endif
                    @if($contentType == 'image')
                    <img src="{{ $content }}">
                    @endif
                </div>
            </div>
            @if(env('APP_EXPIRE_DATE'))
                <small class="float-left expires-at">
                    This Box 
                    @if(empty($box->expires_at))
                        @if(empty($box->expires_other))
                            Never expires
                        @else
                            {{ ucwords($box->getExpireOptions()[$box->expires_other]) }}
                        @endif

                    @else
                        Expires in {{$box->remainingTime("auto")}}
                    @endif
                </small>
            @endif
        </div>
    @endif
@endsection