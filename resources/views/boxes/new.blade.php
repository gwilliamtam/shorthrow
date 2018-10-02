@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12 col-md-8">
                <div class="card">
                    <div class="card-header">
                        @if(!empty($id) && $groups->count()>0)
                        <div class="float-right group-selector">
                            <div class="form-group">
                                <select class="form-control" id="groups-avail">
                                    <option value="no-group">No Group</option>
                                        @foreach($groups as $group)
                                            <option value="{{$group->id}}" {{ $groupId == $group->id ? 'selected' : null }}>{{$group->name}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="group-done" class="float-right mt-2 mr-2 text-success d-none">
                            <i class="fas fa-check"></i>
                        </div>
                        @endif
                        <i class="fas fa-box-open"></i>
                        {{ empty($id) ? 'New Box' : 'Edit Box' }}
                    </div>

                    <div class="card-body">

                        <form id="new-box-form" class="container was-validated" novalidate="" method="post" action="{!! route('saveBox') !!}">
                            @csrf
                            <div class="form-group">
                                <small for="url">{{ url('/') }}/<span id="urlWord">{{empty($uri) ? null : $uri}}</span></small>
                                    <input type="hidden" id="id" name="id" value="{{ empty($id) ? '' : $id }}">
                                    <input type="hidden" id="uri" name="uri" value="{{ empty($uri) ? '' : $uri }}">
                                    <input type="text" name="url" class="form-control" id="url" placeholder="Enter a word to complete your URL" aria-describedby="url" required="true" value="{{ empty($uri) ? '' : $uri }}">

                                {{--<small id="urlHelp" class="form-text text-muted">Your word is subject to availability</small>--}}

                            </div>

                            {{--<div class="btn-group btn-group-toggle mb-4 mt-4" data-toggle="buttons">--}}
                                {{--<label class="btn btn-outline-primary active">--}}
                                    {{--<input type="radio" name="my_url" id="option1" autocomplete="off" checked> My URL--}}
                                {{--</label>--}}
                                {{--<label class="btn btn-outline-primary">--}}
                                    {{--<input type="radio" name="short_url" id="option2" autocomplete="off"> Short URL--}}
                                {{--</label>--}}
                                {{--<label class="btn btn-outline-primary">--}}
                                    {{--<input type="radio" name="long_url" id="option3" autocomplete="off"> Long URL--}}
                                {{--</label>--}}
                            {{--</div>--}}

                            <div class="form-group">
                                {{--<label for="content">Content to share</label>--}}
                                <textarea class="form-control" id="content" name="content" rows="5" required placeholder="Paste here the content you want to share">{{empty($content) ? '' : $content}}</textarea>
                                <img src="" id="pasted-image" class="d-none">
                            </div>

                            <div class="form-group">
                                <div class="form-control btn-group btn-group-toggle" data-toggle="buttons" id="content-type-buttons">
                                    @php($cnt=0)
                                    @foreach($contentTypes as $conType => $conTypeIcon)
                                        @if(empty($contentType))
                                            <label class="btn btn-light content-type-radio {{ $cnt == 0  ? 'active' : null }}" data-attr="{{$conType}}">
                                        @else
                                            <label class="btn btn-light content-type-radio {{ $conType == $contentType  ? 'active' : null }}" data-attr="{{$conType}}">
                                        @endif
                                        <input type="radio" value="never" autocomplete="off">
                                                {!! $conTypeIcon !!}<br>{{ ucwords($conType) }}
                                    </label>
                                    @php($cnt++)
                                    @endforeach
                                </div>
                            </div>
                            <input type="hidden" id="content-type" name="content_type" value="{{ empty($contentType) ? key($contentTypes) : $contentType }}">

                            <input type="hidden" id="expires-at" name="expires_at" value="{{ empty($expiresAt) ? 'never' : $expiresAt }}">
                            @if(env('APP_EXPIRE_DATE'))
                            <div class="date">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="expiring-date">This box expires</label>
                                    </div>
                                    <select class="custom-select" id="expiring-date">
                                        @if(!empty($expiresAt))
                                            <option value="{{$expiresAt}}" selected>{{$expiresAt}}</option>
                                        @endif
                                        @foreach($boxExpireOptions as $expIndex => $expValue)
                                        <option value="{{$expIndex}}">{{$expValue}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif

                            <div id="submit-button" class="btn btn-light float-right disabled">
                                <i class="fas fa-check"></i>
                            </div>
                            <div id="cancel-button" class="btn btn-light float-right">
                                <i class="fas fa-times"></i>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $( document ).ready(function(){
            var timerId = null;

            $('#url').on('input', function(){
                var word = $('#url').val()
                $('#uri').val( '' )
                $('#urlWord').html( '' )
                for (var i = 0; i < word.length; i++) {
                    var letter = word.charAt(i)
                    if(/[A-Za-z0-9\-_]/.test(letter)){

                    }else{
                        letter = '_'
                    }
                    $('#urlWord').append( letter )
                    $('#uri').val( $('#uri').val() + letter )
                }
                clearTimeout(timerId);
                timerId = setTimeout(() => checkAvailable(word), 300);
            })

            function checkAvailable(word){
                if(word.length > 0){
                    var url = "{!! route('checkWord') !!}"
                    var data = {
                        "_token": "{{ csrf_token() }}",
                        "word": word
                    }
                    $.post(url, data)
                        .done(function(){
                            $('#urlWord').css('color','green');
                        }).fail(function(){
                            $('#urlWord').css('color','red');
                    });
                }
            }

            $(".content-type-radio").on('click', function(){
                $('#content-type').val( $(this).attr('data-attr') )
            })

            // $("#content").bind('paste', function(e) {
            //     var self = this;
            //     setTimeout(function(e) {
            //         alert($(self).val());
            //     }, 0);
            // });

            document.getElementById("content").addEventListener("paste", pasteHandler);

            function pasteHandler(e) {
                var items = e.clipboardData.items;
                for (var i = 0 ; i < items.length ; i++) {
                    var item = items[i];
                    if (item.type.indexOf("image") >=0) {
                        console.log("Ahhh... you pasted an image!")
                        console.log(item);

                        var blob = items[i].getAsFile();
                        // and use a URL or webkitURL (whichever is available to the browser)
                        // to create a temporary URL to the object
                        var URLObj = window.URL || window.webkitURL;
                        var source = URLObj.createObjectURL(blob);

                        // The URL can then be used as the source of an image
                        // console.log(createImage(source)) ;

                        $("#pasted-image").attr("src", source);
                        $("#content").val(source);
                        $("#content").addClass("d-none");
                        $("#pasted-image").removeClass("d-none");
                        $("#content-type-buttons .content-type-radio").removeClass("active");
                        $("#content-type-buttons .content-type-radio").addClass("disabled");
                        $("#content-type-buttons label[data-attr='image']").removeClass("disabled");
                        $("#content-type-buttons label[data-attr='image']").addClass("active");
                    } else {
                        console.log("Pasting non-image.");
                    }
                }
            }

            function createImage(source) {
                var pastedImage = new Image();
                pastedImage.onload = function() {
                    // You now have the image!
                }
                pastedImage.src = source;
            }

            $('#groups-avail').on('change', function(){
                var selected = $(this).find('option:selected').attr('value');
                var url = "{!! route('addBoxToGroup') !!}";
                var data = {
                    "_token": "{{ csrf_token() }}",
                    "box": "{{empty($id) ? null : $id}}",
                    "group": selected
                }
                $.post(url, data)
                    .done(function(){
                        $('#group-done').removeClass('d-none');
                        $('#group-done').show().fadeOut(3000);
                    });
            });

            $("#url").on("input", function(){
                validateSubmit();
            });

            $("#content").on("input", function(){
                validateSubmit();
            });

            function validateSubmit(){
                if($("#url").val().length>0 &&
                    $("#content").val().length>0)
                {
                    $('#submit-button').removeClass("disabled");
                }else{
                    $('#submit-button').addClass("disabled");
                }
            }

            validateSubmit();

            $('#submit-button').on('click', function(){

                if(!$('#submit-button').hasClass("disabled")){
                    $('#new-box-form').submit();
                }
            })
            $('#cancel-button').on('click', function(){
                document.location = "{!! route('listBox') !!}"
            })

            $('#expiring-date').change(function(){
                $('#expires-at').val($("#expiring-date option:selected").val());
            });
        })
    </script>
@endsection