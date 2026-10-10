<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">{{ trans('main.Comments') }}</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <br>
        <table class="table table-striped table-bordered table-hover" style='background:#fff'>
            <thead>
            <th>ID</th>
            <th>Content</th>
            <th>Files</th>
            <th>Date</th>
            <th>Sender</th>
            <th style="width: 140px">{{ trans('main.Actions') }}</th>
            </thead>
            <tbody>
            @foreach($commentaries as $comment)
                <tr>
                    <td>{!!$comment->id!!}</td>
                    <td>{!!$comment->content!!}</td>
                    <td>
                        @foreach($comment->files as $attach)
                            <ul class="del-container">
                                <li>
                                    <div style="display: inline-block;margin-right: 20px">
                                        <a href="{{ $attach->url }}" target="_blank"><span class="glyphicon glyphicon-paperclip"></span>{{$attach->attach_file_name}}</a>
                                    </div>
                                </li>

                                {{-- {{csrf_field()}} --}}
                            </ul>
                        @endforeach

                    </td>
                    <td>{!!$comment->created_at!!}</td>
                    <td>
                        {{\App\User::find($comment->author_id)->first()->name}}
                    </td>
                    <td>
                        <!-- INFO BUTTON-->
                        <a href='/comment/{!!$comment->id!!}' class='btn btn-warning btn-sm dash-act dash-act-view' title='View'><i class="ti ti-eye" aria-hidden="true"></i></a>
                        <!-- EDIT BUTTON-->
                        <a href='/comment/{!!$comment->id!!}/edit' class='btn btn-primary btn-sm dash-act dash-act-edit' title='Edit'
                           data-link='/comment/{!!$comment->id!!}/edit'><i class="ti ti-pencil" aria-hidden="true"></i></a>
                        <!-- DELETE BUTTON-->
                        <a data-toggle="modal" data-target="#myModal" class='btn btn-danger btn-sm delete dash-act dash-act-delete' title='Delete'
                           data-link="/comment/{!!$comment->id!!}/delete_msg"><i class="ti ti-trash" aria-hidden="true"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="box-footer clearfix">
            <a href="{{route('comment.index')}}">
                <button href="javascript:void(0)" class="btn btn-default btn-flat pull-right">{{ trans('main.ViewAllComments') }}
                </button>
            </a>
        </div>
    </div>
</div>
{{--    END Activities Table--}}
<!--  END Commentaries  -->