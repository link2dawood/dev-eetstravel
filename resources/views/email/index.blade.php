@php
use App\Email;
use App\User;

$user = Auth::user();

$userName  = "Unknown User";

@endphp
@extends('scaffold-interface.layouts.modernapp')

@section('content')
@include('layouts.title',
['title' => 'Email', 'sub_title' => 'Inbox',
'breadcrumbs' => [
['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
['title' => 'Emails', 'icon' => 'map-signs', 'route' => null]]])

@if(Auth::user()->can('dashboard.inbox'))

<section class="content">
    <div class="main">
        <div class="main-content">
            <section class="mail d-none" style="display:none">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-xl-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center gap-3 custom-card-header">
                                                <a href="#" class="btn btn-gray"><i class="fas fa-chevron-left"></i></a>
                                                <h1 class="title">Mailbox</h1>
                                            </div>
                                            <a href="#compose" data-bs-toggle="modal" class="btn btn-primary w-100 mt-4"><i class="fas fa-pen me-2"></i>Compos</a>

                                            <ul class="nav nav-tabs nav-tabs-vertical mt-4" id="myTab" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="inbox-tab" data-bs-toggle="tab" data-bs-target="#inbox-tab-pane" type="button" role="tab" aria-controls="inbox-tab-pane" aria-selected="true"><i class="fas fa-inbox me-2"></i>Inbox</button>
                                                </li>


                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="card bg-primaryLight">
                                        <div class="card-body">
                                            <div class="custom-card-header">
                                                <input type="text" class="form-control" placeholder="Search">
                                            </div>
                                            <ul class="email-user-list mt-4">
                                                <li class="user-item active">
                                                    <a href="#" class="text-decoration-none">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <img src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80" alt="" class="user-img">
                                                                        <div>
                                                                            <h3 class="card-title mb-0">John doe</h3>
                                                                            <p class="card-text mail-text">Lorem, ipsum
                                                                                dolor sit amet consectetur adipisicing
                                                                                elit. Mollitia, animi?</p>
                                                                        </div>

                                                                    </div>
                                                                    <div class="d-flex flex-column align-items-end">
                                                                        <p class="card-text">11:20Pm</p>
                                                                        <div class="mail-status">100</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li class="user-item">
                                                    <a href="#" class="text-decoration-none">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <img src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80" alt="" class="user-img">
                                                                        <div>
                                                                            <h3 class="card-title mb-0">John doe</h3>
                                                                            <p class="card-text mail-text">Lorem, ipsum
                                                                                dolor sit amet consectetur adipisicing
                                                                                elit. Mollitia, animi?</p>
                                                                        </div>

                                                                    </div>
                                                                    <div class="d-flex flex-column align-items-end">
                                                                        <p class="card-text">11:20Pm</p>
                                                                        <div class="mail-status">100</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li class="user-item">
                                                    <a href="#" class="text-decoration-none">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <img src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80" alt="" class="user-img">
                                                                        <div>
                                                                            <h3 class="card-title mb-0">John doe</h3>
                                                                            <p class="card-text mail-text">Lorem, ipsum
                                                                                dolor sit amet consectetur adipisicing
                                                                                elit. Mollitia, animi?</p>
                                                                        </div>

                                                                    </div>
                                                                    <div class="d-flex flex-column align-items-end">
                                                                        <p class="card-text">11:20Pm</p>
                                                                        <div class="mail-status">100</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-5">
                                    <div class="card mb-0">
                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <div class="d-flex align-items-center justify-content-between custom-card-header">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="https://t4.ftcdn.net/jpg/00/97/00/09/240_F_97000908_wwH2goIihwrMoeV9QF3BW6HtpsVFaNVM.jpg" alt="" class="user-img">
                                                    <div>
                                                        <h1 class="card-title fs-5 mb-1">johndoe</h1>
                                                        <p class="card-text">1 day ago</p>
                                                    </div>
                                                </div>
                                                <div class="btn-group">
                                                    <button type="button" href="" class="btn px-2 shadow-none card-title" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><button class="dropdown-item" type="button">Action</button>
                                                        </li>
                                                        <li><button class="dropdown-item" type="button">Another
                                                                action</button></li>
                                                        <li><button class="dropdown-item" type="button">Something else
                                                                here</button></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="email-wrapper">
                                                <div class="emails-list accordion" id="accordionExample">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingOne">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                                    <div class="d-flex align-items-center">
                                                                        <span>{{Auth::user()->name}}</span>
                                                                        <p class="card-text truncate ms-2">Lorem, ipsum
                                                                            dolor sit amet consectetur adipisicing elit.
                                                                            Cupiditate, architecto?</p>
                                                                    </div>
                                                                </button>
                                                                <a href="#" class="card-title d-block mb-0 p-1"><i class="fas fa-trash-alt"></i></a>
                                                            </div>
                                                        </h2>

                                                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <p class="card-text">Lorem ipsum dolor sit amet
                                                                    consectetur adipisicing elit. Repudiandae quisquam
                                                                    ipsum enim officiis, nostrum soluta at atque
                                                                    voluptate eveniet a aspernatur tempore nihil
                                                                    recusandae esse, totam dolorum placeat quis
                                                                    consectetur.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <form action="">
                                                    <textarea name="" id="div_editor1" cols="30" rows="10"></textarea>
                                                    <button class="btn btn-primary w-100">Send</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</section>
            <section class="mail dashboard">
                <div class="containerr">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="card bg-primaryLight">
                                        <div class="card-body">
                                            <div class="custom-card-header">
                                                <input type="text" class="form-control" placeholder="Search">
                                            </div>
                                            <a href="#compose" data-bs-toggle="modal" class="btn btn-primary w-100 mt-4"><i class="fas fa-pen me-2"></i>Compose</a>
                                            

                                           
                                            <ul class="nav nav-tabs nav-tabs-vertical mt-4" id="myTab" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="inbox-tab" data-bs-toggle="tab" data-bs-target="#inbox-tab-pane" type="button" role="tab" aria-controls="inbox-tab-pane" aria-selected="true"><i class="fas fa-inbox me-2"></i>Inbox</button>
                                                </li>
												 <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="sent-tab" data-bs-toggle="tab" data-bs-target="#sent-tab-pane" type="button" role="tab" aria-controls="sent-tab-pane" aria-selected="false"><i class="fas fa-paper-plane me-2"></i>Sent
                                                        Emails</button>
                                                </li>
                                                
                                            </ul>
                                           	<div class="tab-content" id="myTabContent">
                                                <div class="tab-pane fade show active" id="inbox-tab-pane"
                                                    role="tabpanel" aria-labelledby="inbox-tab" tabindex="0">
                                                    inbox
                                                    <ul class="email-user-list mt-4">
                                                       
                                                        @if(!empty($emails))
                                                        @foreach($emails as $email)
                                                        @php
                                                        $id= $email->id;
                                                        @endphp
                                                        <li class="user-item active">
                                                            <a href="#" class="text-decoration-none" onclick="myfunction('{{$id}}','inbox')">
        
                                                                <div class="card">
        
                                                                    <div class="card-body">
        
                                                                        <div class="d-flex align-items-center justify-content-between">
        
                                                                            @php
                                                                            $users = User::where('email', $email->from)->get();
                                                                            foreach($users as $user){
                                                                            $userName = $user->name;
                                                                            }
                                                                            @endphp
                                                                            <div class="d-flex align-items-center gap-2">
                                                                                <img src="https://t4.ftcdn.net/jpg/00/97/00/09/240_F_97000908_wwH2goIihwrMoeV9QF3BW6HtpsVFaNVM.jpg" alt="" class="user-img">
                                                                                <div>
                                                                                    <h3 class="card-title mb-0">{{$userName}}
                                                                                        <p class="card-text mail-text">
                                                                                            {{$email->from}}
                                                                                        </p>
                                                                                </div>
        
                                                                            </div>
        
                                                                            <div class="d-flex flex-column align-items-end">
                                                                                <p class="card-text"> {{$email->date}}</p>
                                                                                <div class="mail-status"></div>
                                                                            </div>
        
                                                                        </div>
        
                                                                    </div>
        
                                                                </div>
        
                                                            </a>
                                                        </li>
                                                        @endforeach
                                                        @endif
        
                                                    </ul> 
                                                </div>
                                                <div class="tab-pane fade" id="sent-tab-pane" role="tabpanel"
                                                    aria-labelledby="sent-tab" tabindex="0">
                                                    sent
                                                    
                                                    <ul class="email-user-list mt-4">
														
                                                        @if(!empty($sentemails))
                                                        @foreach($sentemails as $sentemail)
                                                        @php
                                                        $id= $sentemail->id;
                                                        @endphp
                                                        <li class="user-item active">
                                                            <a href="#" class="text-decoration-none" onclick="myfunction('{{$id}}','sent')">
        
                                                                <div class="card">
        
                                                                    <div class="card-body">
        
                                                                        <div class="d-flex align-items-center justify-content-between">
        
                                                                            @php
                                                                            $users = User::where('email', $sentemail->to)->get();
																			$userName = "Outside User";
                                                                            foreach($users as $user){
                                                                            $userName = $user->name;
                                                                            }
                                                                            @endphp
                                                                            <div class="d-flex align-items-center gap-2">
                                                                                <img src="https://t4.ftcdn.net/jpg/00/97/00/09/240_F_97000908_wwH2goIihwrMoeV9QF3BW6HtpsVFaNVM.jpg" alt="" class="user-img">
                                                                                <div>
                                                                                    <h3 class="card-title mb-0">To: {{$userName}}
                                                                                        <p class="card-text mail-text">
                                                                                            {{$sentemail->to}}
                                                                                        </p>
                                                                                </div>
        
                                                                            </div>
        
                                                                            <div class="d-flex flex-column align-items-end">
                                                                                <p class="card-text"> {{$sentemail->date}}</p>
                                                                                <div class="mail-status"></div>
                                                                            </div>
        
                                                                        </div>
        
                                                                    </div>
        
                                                                </div>
        
                                                            </a>
                                                        </li>
                                                        @endforeach
                                                        @endif
        
                                                    </ul>
                                                
                                                </div>
                                                <div class="tab-pane fade" id="favorite-tab-pane"
                                                    role="tabpanel" aria-labelledby="favorite-tab" tabindex="0">
                                                    Favorite</div>
                                                <div class="tab-pane fade" id="drafts-tab-pane" role="tabpanel"
                                                    aria-labelledby="drafts-tab" tabindex="0">
                                                    Drafts</div>
                                                <div class="tab-pane fade" id="trash-tab-pane" role="tabpanel"
                                                    aria-labelledby="trash-tab" tabindex="0">
                                                    Trash</div>
                                                <div class="tab-pane fade" id="spam-tab-pane" role="tabpanel"
                                                    aria-labelledby="spam-tab" tabindex="0">
                                                    Spam</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								@if(count($emails)!="0")
                                <div class="col-lg-8">

                                    <div class="card mb-0">
                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <div class="d-flex align-items-center justify-content-between custom-card-header">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="https://t4.ftcdn.net/jpg/00/97/00/09/240_F_97000908_wwH2goIihwrMoeV9QF3BW6HtpsVFaNVM.jpg" alt="" class="user-img">
                                                    <div>
                                                        <h1 class="card-title fs-5 mb-1 username">{{$userName}}</h1>
                                                        <p class="card-text">1 day ago</p>
                                                    </div>
                                                </div>
                                                <div class="btn-group">
                                                    <button type="button" href="" class="btn px-2 shadow-none card-title" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><button class="dropdown-item" type="button">Action</button>
                                                        </li>
                                                        <li><button class="dropdown-item" type="button">Another
                                                                action</button></li>
                                                        <li><button class="dropdown-item" type="button">Something else
                                                                here</button></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="email-wrapper">

                                                <div class="emails-list accordion" id="accordionExample">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingOne">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="subject">{{$email->subject}}</span>
                                                                        <p class="card-text truncate ms-2 body">
                                                                            {{$email->body_text}}
                                                                        </p>
                                                                    </div>
                                                                </button>
                                                                <a id = "delete_id" href="users/{{$email->id}}/emails/delete" class="card-title d-block mb-0 p-1"><i class="fas fa-trash-alt"></i></a>
                                                            </div>
                                                        </h2>

                                                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <p class="card-text body">{{$email->body_text}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                               <form id="replyForm" >
                                                    <input type="hidden" name="email_sent" value="{{$email->to}}" id = "email_sent">
                                                    <input type="hidden" name="subject" value="{{$email->subject}}" id = "email_subject" >
                                                    <textarea name="body" id="div_editor3" cols="30" rows="10"></textarea>
                                                    <button class="btn btn-primary w-100">Reply</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </div>
								@else
                                <div class="col-lg-8">Your Inbox is empty right now</div>
								 @endif
                            </div>
                        </div>
                    </div>

                </div>
            </section>

        </div>
    </div>

    <div class="modal fade" id="compose" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">New Message</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form">
                    <div class="input-wrapper">
                            <input type="text" id = "to" class="form-control" placeholder="To:" v-model="newEmail.to" >
                        </div>
                        <div class="input-wrapper">
                            <input type="text" id = "subject" class="form-control" placeholder="Subject" v-model="newEmail.subject" >
                        </div>
                        <div class="input-wrapper">
                            <textarea name="" id="div_editor2" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" @click="sendEmail2" onclick="sendEmail2('{{$user->id}}')">Send</button>
                </div>
            </div>
        </div>
    </div>


    {{--@include('email.modals.message')--}}
</section>

<script>
    function myfunction(email,mailtype) {
        $.ajax({
            type: "GET",
            url: `/getemailbyId/${email}/${mailtype}`,


            success: function(result) {
                console.log(result.users);
                $(".username").html(result.users);
                $(".body").html(result.email.body_text);
                $(".subject").html(result.email.subject);
				$("#email_subject").val(result.email.subject);
				$("#delete_id").attr("href",`/users/${result.email.id}/emails/delete`);
				if(mailtype === "inbox"){
				$("#email_sent").val(result.email.from);
				}
				else{
				$("#email_sent").val(result.email.to);	
				}
            },
            error: function(result) {
                console.log(result);
            }
        });
    }
    function sendEmail2(userId) {
        var form_data = new FormData();
        var to = $('#to').val();
        
        var subject = $('#subject').val();
       
        
  
      
        var message = editor2.getHTMLCode();
        console.log(message);
         
        form_data.append("to",to);
        form_data.append("subject",subject);
        form_data.append("message",message);
		 $.ajaxSetup({
			   headers: {
				  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			   }
			});
                    $.ajax({
            type: "POST",
            url: `users/${userId}/emails/sending`,
            data:form_data,
          contentType:false,
          cache:false,
          processData:false,
    


            success: function(result) {
             console.log("SUCCESS");
				location.reload();
            },
            error: function(result) {
                console.log(result);
            }
        });
                   
        }
	
	
        $('#replyForm').submit(function(e){
        e.preventDefault();
        var form_data = new FormData(this);
		$.ajaxSetup({
			   headers: {
				  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			   }
			});
        $.ajax({
            type: "POST",
            url: 'users/{{$user->id}}/emails/reply',
            data:form_data,
            contentType:false,
            cache:false,
            processData:false,
            success: function(result) {
             console.log("SUCCESS");
            },
            error: function(result) {
                console.log(result);
            }
        });
        // alert("work")
    })
</script>
@endif

@endsection