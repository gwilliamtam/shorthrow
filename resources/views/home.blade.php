@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                        <div class="row">
                            <div class="col-sm-6">

                                <div class="card mb-4">
                                    <img class="card-img-top">
                                    <div class="card-body">
                                        <h5 class="card-title">New</h5>
                                        <p class="card-text">Create a new box to share content</p>
                                        <a href="{!! route('newBox') !!}" class="btn btn-primary">Go new box</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="card mb-4">
                                    <img class="card-img-top">
                                    <div class="card-body">
                                        <h5 class="card-title">List</h5>
                                        <p class="card-text">List all the boxes you have created</p>
                                        <a href="{!! route('listBox') !!}" class="btn btn-primary">Go list boxes</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
