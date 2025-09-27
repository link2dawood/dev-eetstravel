@extends('email.layout')
@section('title','Index')
@section('main-content')
    @if(!$imapConnected)
    @else
    <!-- /.box-header -->
    <div class="box-body no-padding">


        <div class="mailbox-controls">
            <div class="pull-right">
                @if ($mails)
                {{($page-1) * $per_page + 1}}-{{($page-1) * $per_page + count($mails)}} / {{$mailsCount}}

                <div class="btn-group">

                    @if($page != 1)
                        @if (Route::getCurrentRoute()->getName() == 'email.index')
                            <a href="{{route('email.index', ['page' => $page-1] )}}">
                        @endif
                                @if (Route::getCurrentRoute()->getName() == 'email.search_result' || Route::getCurrentRoute()->getName() == 'email.search')
                                    <a href="{{route('email.search', ['page' => $page-1,"searched" => $search ] )}}">
                                        @endif

                        @if (Route::getCurrentRoute()->getName() == 'email.folder')
                            <a href="{{route('email.folder', ['page' => $page-1, 'currentFolder'=> $currentFolder] )}}">
                        @endif
                    @endif
                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button></a>



                        @if (Route::getCurrentRoute()->getName() == 'email.index')
                            <a href="{{route('email.index', ['page' => $page+1] )}}">
                        @endif

                                @if (Route::getCurrentRoute()->getName() == 'email.search_result' || Route::getCurrentRoute()->getName() == 'email.search')
                                    <a href="{{route('email.search', ['page' => $page+1,"searched" => $search] )}}">
                                        @endif

                        @if (Route::getCurrentRoute()->getName() == 'email.folder')
                            <a href="{{route('email.folder', ['page' => $page+1, 'currentFolder'=> $currentFolder] )}}">
                        @endif
                                @if (Route::getCurrentRoute()->getName() == 'email.search_result' || Route::getCurrentRoute()->getName() == 'email.search')
                                    <a href="{{route('email.search', ['page' => $page+1, "searched" => $search] )}}">
                                        @endif
                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button></a>

                </div>

            </div>
        @endif
                <!-- /.btn-group -->
            </div>
            <!-- /.pull-right -->
        </div>
        <div class="table-responsive mailbox-messages">

            <table class="table table-hover table-striped finder-disable">
                <tbody>
                @if (!$mails)
                    <tr class="row">
                        <td class="col-md-12">
                            @if(!isset($search))
                            <h2 >The folder is empty</h2>
                            @else
                            <h2 >No search result</h2>
                            @endif
                        </td>
                    </tr>

                @endif
                @foreach ($mails as $mail)
                    <tr style="cursor: pointer" data-link="{{route('email.mail', ['id'   => $mail->message_id, 'folder' => $currentFolder])}}">
                        <!--<td><div class="icheckbox_flat-blue" aria-checked="false" aria-disabled="false" style="position: relative;"><input type="checkbox" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div></td>-->
                        <td class="mailbox-star onclick_redirect">

                            {{--<a href="#"><i class="fa fa-star text-yellow"></i></a>--}}
                        </td>

                        <td class="mailbox-name onclick_redirect"><a href="{{route('email.mail', ['id'   => $mail->getNumber(), 'folder' => $currentFolder])}}">
                                @if(\App\Helper\AdminHelper::emailCheck($mail))
                                @if($currentFolder == 'INBOX.Sent')
                                    @php
                                    $addresses = [];
                                    $toArray = $mail->getTo();
                                    array_walk($toArray, function($to) use (&$addresses){
                                         $addresses[] = $to->getAddress();
                                    })

                                    @endphp
                                    {{implode(', ', $addresses)}}
                                @else
                                    {{$mail->getFrom()}}
                                @endif
                                    @endif
                            </a></td>
                        <td class="mailbox-subject onclick_redirect">{{$mail->getSubject()}}
                        </td>
                        <td class="mailbox-attachment onclick_redirect"></td>
                        <td class="mailbox-date onclick_redirect">
                            @if ($mail->getDate())
                                {{\Carbon\Carbon::createFromTimestamp($mail->getDate()->getTimestamp())->diffForHumans()}}</td>
                            @endif
                        <td style="width: 150px;">
                            <div class="btn-group pull-right">
                                <a href="#" class="btn btn-sm btn-default moveToFolder"
                                   data-message-id="{{$mail->getNumber()}}"
                                   data-message-folder="{{$currentFolder}}"
                                   data-link="{{route('email.getMoveToForm', ['id' => $mail->getNumber(), 'folder' => $currentFolder], false)}}">
                                    <i class="fa fa-folder-open" aria-hidden="true"></i></a>
                                <a class="btn  btn-default btn-sm reply"
                                   data-reply-message="{{$mail->getNumber()}}"
                                   data-reply-folder="{{$currentFolder}}"
                                   data-to="
                                        @if($currentFolder == 'INBOX.Sent')
                                   {{implode(',', $addresses)}}

                                        @else
                                            {{$mail->getFrom()}}
                                        @endif
                                           "
                                   data-link="{{route('email.getComposeForm', ['id' => $mail->getNumber(), 'folder' => $currentFolder], false)}}"><i class="fa fa-reply" aria-hidden="true"></i></a>
                                <a data-toggle="modal" data-target="#myModal" class="btn btn-danger btn-sm delete" data-link="{{route('email.deleteMsg', ['id' => $mail->getNumber(), 'folder' => $currentFolder], false)}}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                            </div>
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
            <!-- /.table -->
        </div>
        <!-- /.mail-box-messages -->
    <div class="box-footer no-padding">
        <div class="mailbox-controls">

            <!-- /.pull-right -->
        </div>
    </div>
    </div>
    @endif
@stop