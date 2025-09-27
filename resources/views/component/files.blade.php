<div class="row">
    <div class="@if(isset($tour)) col-md-6 @else col-md-12 @endif">

        <div class="panel panel-info table_photos">
            <div class="panel-heading">{!!trans('main.Photos')!!}</div>
            <div class="panel-body image">
                @foreach($files['image'] as $image)
                <div class="panel panel-default del-container" style="display: inline-block; height: 325px; width: 350px;">
                    <div class="panel-heading">
                        {{-- <form action="{{route('file_delete', ['id' => $image->id])}}" method="post"> --}}
                            {{-- {{csrf_field()}} --}}
                            <button class="btn btn-danger btn-xs del-attach" data-attach-id="{{$image->id}}" data-attach-url="{{route('file_delete', ['id' => $image->id])}}">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true" title="Delete"></span>
                            </button>
                            {{-- </form> --}}
                    </div>
                    <div class="panel-body">
                        <a href="{{'/public'.$image->attach->url()}}"><img src="{{'/public'.$image->attach->url()}}" style="height: 250px; max-width: 325px"></a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @if(isset($tour))
    <div class="col-md-6">
        <div class="panel panel-info table_photos">
            <div class="panel-heading">{!!trans('main.imageforlanding')!!}</div>
            <div class="panel-body image">
                <div class="form-group">
                    <div class="thumbnail text-center">
                        <img class="pic" style="max-height: 350px;" src="@if($tour->attachments()->first() != null) {{ $tour->attachments()->first()->url }} @endif" alt="Image for landing page" style="width:100%">
                        <div class="caption">

                            <div class="upload-btn-wrapper">
                                <label for="check" class="btn btn-primary">Change</label>
                                <input id="check" name="fileToUpload[]" data-model="Tour" data-id="{{ $tour->id }}"class="fileToUpload"type="file"  style="display:none"/>
                            </div>
                        </div>
                    </div>
                    {{--        
                    <div class="thumbnail text-center">
                        <img class="pic" style="max-height: 350px;" src="@if($tour->attachments()->first() != null) {{ $tour->attachments()->first()->url }} @endif" alt="Image for landing page" style="width:100%">
                    </div>   
                    --}}        
                </div>
            </div>
        </div>
    </div>
    @endif
</div>


            <div class="panel panel-default">
                <div class="panel-heading">{!!trans('main.Files')!!}</div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>{!!trans('main.Name')!!}</th>
                            <th>{!!trans('main.Uploaded')!!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files['attach'] as $attach)
                        <tr class='del-container'>
                            <td>
                                <div class="link_attach_file">
                                    <a href="{{url('public/'.$attach->attach->url())}}" target="_blank" class="link_file">
                                        <span class="glyphicon glyphicon-paperclip"></span>
                                        <span class="name_link_file">{{$attach->attach_file_name}}</span>
                                    </a>
                                </div>
                                <div style="display: inline-block" class="pull-right">
                                    {{-- <form action="{{route('file_delete', ['id' => $attach->id])}}" method="post"> --}}
                                        {{-- {{csrf_field()}} --}}
                                        <button class="btn btn-danger btn-xs del-attach" data-attach-url="{{route('file_delete', ['id' => $attach->id])}}">
                                            <span class="glyphicon glyphicon-remove" aria-hidden="true" title="Delete"></span>
                                        </button>
                                        {{-- </form> --}}
                                </div>
                            </td>
                            <td><span>{{$attach->created_at}}</span></td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>

            <style>
                .link_attach_file {
                    display: inline-block;
                }

                .link_attach_file > a {
                    color: #333;
                    text-decoration: none;
                }

                .link_attach_file > a > span {
                    line-height: 0;
                    vertical-align: middle;
                }

                .name_link_file{
                    margin-left: 10px;
                }

                .link_attach_file > a:hover .name_link_file{
                    text-decoration: underline;
                }
            </style>

            @push('scripts')
            <script>
                $('.link_file').click(function (e) {
                    window.open($(this).attr('href'));
                });
                $('.image').magnificPopup({
                    delegate: 'a',
                    type: 'image',
                    gallery: {enabled: true}
                });
               $('.del-attach').on('click', function (e) {
					e.preventDefault();
					let elem = $(this).context;
					let deleteUrl = $(elem).attr('data-attach-url');

					// Ask for confirmation before proceeding
					let confirmDelete = confirm("Are you sure you want to delete this attachment?");

					if (confirmDelete) {
						$.ajax({
							url: deleteUrl,
							method: 'POST',
							data: {
								"_token": "{{csrf_token()}}"
							},
							success: (res) => {
								$(this).closest('.del-container').hide();
							},
							error: (res) => {
								console.log(res);
							}
						});
					} else {
						// Do nothing if user cancels
						console.log("Deletion cancelled.");
					}
				});

            </script>
            @endpush