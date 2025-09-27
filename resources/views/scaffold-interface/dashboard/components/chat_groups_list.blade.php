<div class="col-md-6" id="chat_groups_list_container">
</div>

<script id="custom_chat_users_list" type="text/template">
    <div class="box box-primary">
        @if(Auth::user()->can('dashboard.chat_groups'))
        <div class="box-header with-border">
            <h4>Chat groups</h4>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body" style="height: 332px;overflow-y: scroll;">
            <ul class="nav nav-stacked custom-chat-users-list">
                @foreach( $chatUsers as $id => $user)
                    <li>
                        <a href="#" class="dashboard-custom-chat-user-select" data-user-id="{{$user->id}}">
                            <img width="25" height="25" src="{{$user->avatar_file_name == null ? asset('img/avatar.png') : $user->avatar->url('logo')}}" alt="">
                            <span>{{$user->name}}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        @else
            <div class="box-header">
                <h4>{{ trans('main.ChatGroups') }}</h4>
            </div>
            <div class="box-body">
                {{ trans('main.Youdonthavepermissions') }}
            </div>
        @endif
    </div>
</script>


<script type="text/javascript">
    jQuery(document).ready(function() {
        showUsers();
        bindFunctions();
        $('.fa-bell-o').each(function(i,elem) {
                playChatSound();
        });
    });

    function playChatSound() {
        var chat_sound = document.getElementById('chat_message');
        console.log('PlaY')
        chat_sound.play();
    }

    function showUsers()
    {
        jQuery('#chat_groups_list_container').html(jQuery('#custom_chat_users_list').html());
    }

    function bindFunctions(){
        jQuery('.dashboard-custom-chat-user-select').on('click', function(){
            var userId = jQuery(this).attr('data-user-id');
            $.ajax({
                type: "GET",
                url: "/chat/" + userId + "/renderDirectChat",
                data: {'dashboard' : true},
                success: function (result) {
                    jQuery('#chat_groups_list_container').html(result);
                    $('.direct-chat-messages').scrollTo('max', {'duration': 500});
                    $('#return-contact').on('click', function(){
                        showUsers();
                        bindFunctions();
                    })
                },
                error: function (result) {
                    console.log(result);
                }

            });
            return false;
        });
    }

</script>