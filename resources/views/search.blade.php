@extends('layouts.app')

@section('content')
        <div class="container">
            <div class="input-group mb-3">
                <input type="text" id="search" class="form-control" placeholder="Enter URI" aria-label="Enter URI to search" aria-describedby="basic-addon2">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" id="button-search" type="button">Search</button>
                </div>
            </div>
        </div>
@endsection

@section('script')
<script>
    $(document).ready(function(){
        console.log('ready')
        $('#button-search').on('click', function(){
            console.log('mexico')
            if($('#search').val().length > 0){
                document.location = '/' + $('#search').val()
            }
        });
    });
</script>
@endsection