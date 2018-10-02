@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-12 group-tabs">
                <ul class="nav nav-tabs">
                    <li class="nav-item nav-action-button">
                        <div class="nav-link" id="new-box">
                            <i class="fas fa-box-open"></i>
                            <i class="fas fa-plus"></i>
                        </div>
                    </li>
                    @if(env('APP_GROUPING'))
                        <li class="nav-item">
                            <div class="nav-link active" id="show-all">All</div>
                        </li>
                        @foreach($groupsNames as $groupId => $groupName)
                            <li class="nav-item">
                                <div class="nav-link" group-number="{{$groupId}}" href="#">
                                    {{$groupName}}
                                </div>
                            </li>
                        @endforeach
                        <li class="nav-item nav-action-button">
                            <div class="nav-link" data-toggle="modal" data-target="#addGroupModal">
                                <i class="fas fa-boxes"></i>
                                <i class="fas fa-plus"></i>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="row">
            @php
            $contentTypeIcons = [
                'code' => '<i class="fas fa-font"></i>',
                'text' => '<i class="fas fa-code"></i>',
                'url' => '<i class="fas fa-link"></i>',
                'image' => '<i class="far fa-image"></i>',
            ]
            @endphp
            @foreach($boxes as $box)
            <div class="col-md-6 col-lg-4 mb-4 {{ array_key_exists($box->id, $boxGroupsList) ? 'box-group-' . $boxGroupsList[$box->id] : null }} box-card-container">
                <div class="card box-card">
                    {{--<img class="card-img-top" src="..." alt="Card image cap">--}}
                    <div class="card-body " style="overflow-x: auto">
                        <small class="card-title">
                            <a href="{!! url('/') !!}/{{ $box->uri }}">
                                {!! url('/') !!}/{{ $box->uri }}
                            </a>

                        </small>
                        @if(array_key_exists($box->content_type, $contentTypeIcons))
                            <div class="content-type-icon">
                                <span class="badge badge-dark">{!! $contentTypeIcons[$box->content_type] !!}</span>
                            </div>
                        @else
                            ({{ empty($box->content_type) ? 'text' : $box->content_type}})
                        @endif
                        @if(env('APP_GROUPING') && array_key_exists($box->id, $boxGroupsList))
                            <div class="content-type-icon mr-1">
                            <span class="badge badge-primary ">
                                {{$groupsNames[$boxGroupsList[$box->id]]}}&nbsp;
                            </span>
                            </div>
                        @endif
                        <p class="card-text">
                            {{ $box->content }}
                        </p>
                    </div>
                    <div class="card-footer">
                        @if(env('APP_EXPIRE_DATE'))
                            <small class="float-left expires-at">
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
                        <div class="edit-btn btn btn-light float-right" data-id="{{$box->id}}">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="delete-btn btn btn-light float-right" id="{{ $box->id }}">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <div id="group-empty-box" class="col-md-6 col-lg-4 mb-4 box-card-container d-none">
                <div class="card box-card">
                    <div class="card-body" style="overflow-x: auto">
                        <small class="card-title">
                            Group Empty
                        </small>
                        <p class="card-text">
                            This group is empty. Edit any of your boxes
                            and select a group from the list.
                        </p>
                    </div>
                    <div class="card-footer">
                        <div class="btn btn-light float-right" id="delete-group-btn">
                            Delete Group
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="modal fade" id="addGroupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGroupModalLabel">Add Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add-group-form" method="post" action="{!! route('addGroup') !!}">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="text" name="groupName" class="form-control" placeholder="Enter Group Name" aria-label="Group Name" aria-describedby="basic-addon1">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="add-group" class="btn btn-primary">Add group</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $( document ).ready(function(){

            $('.edit-btn').on('click', function(){
                var id = $(this).attr('data-id')
                    document.location = '{{ url('/box/edit') }}/' +  id
            })

            $('.delete-btn').on('click', function(){
                var id = $(this).attr('id')
                if(confirm('Are you sure you want to delete the selected box?')){
                    document.location = '{{ url('/box/delete') }}/' +  id
                }
            })

            $('#add-group').on('click', function(){
                $('#add-group-form').submit();
            })

            $('.group-tabs .nav-item:not(.nav-action-button)').on('click', function(){
                var item = this;
                $('.group-tabs .nav-link').removeClass('active');
                $(item).find('.nav-link').addClass('active');
                if($(item).text() == "All"){
                    $('.box-card-container').show();
                    $('#group-empty-box').hide();
                }else{
                    var groupNumber = $(item).find('.nav-link').attr('group-number');
                    $('.box-card-container').hide();
                    $('.box-group-'+groupNumber).show();

                    if($('.box-group-'+groupNumber).length == 0){
                        console.log('must be showing empty group message')
                        $('#group-empty-box').removeClass('d-none');
                        $('#delete-group-btn').attr('group-number', groupNumber);
                        $('#group-empty-box').show();
                    };
                }
            })

            $('#new-box').on('click', function(){
                document.location="{!! route('newBox') !!}";
            })

            $('#delete-group-btn').on('click', function(){
                document.location="/box/group/delete/"+$('#delete-group-btn').attr('group-number');
            })

        })
    </script>
@endsection