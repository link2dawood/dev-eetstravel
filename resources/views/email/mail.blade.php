@extends('email.layout')
@section('title','Index')
@section('main-content')
    <div id="mail">

        <a href="{{ route('email.ajaxMail', ['id'   => $mail->message_id, 'currentFolder' => $currentFolder]) }}" id="url" style="display: block"></a>
    </div>

    <script>
        $(document).ready(function () {
            var url = $('#url').attr('href');

            $.ajax({
                method: 'GET',
                url: url,
                data: {}
            }).done((res) => {
                $('#mail').html(res);
            });
        });
    </script>
@endsection
