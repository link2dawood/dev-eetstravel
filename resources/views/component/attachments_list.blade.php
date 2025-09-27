@foreach($files as $attach)
    <ul class="del-container">
        <li>
            <div style="display: inline-block;margin-right: 20px">
                <a href="{{$attach->attach->url()}}" target="_blank"><span class="glyphicon glyphicon-paperclip"></span>{{$attach->attach_file_name}}</a>
            </div>
        </li>

        {{-- {{csrf_field()}} --}}


    </ul>
@endforeach