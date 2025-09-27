@php
use App\Hotel;

$supplier_id =  session('SUPPLIER_ID') ;
$hotel= Hotel::find($supplier_id);
@endphp
<header class="header fixed-top w-100">
      <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container">
          <a class="navbar-brand me-lg-5" href="#">LOGO</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

              
            </ul>

            <div class="dropdown">
				
              <a href="#" class="" type="button" data-bs-toggle="dropdown" aria-expanded="false">
				  <h3 class="card-title">{{ $hotel->name }}</h3>
              {{--  <img class="profile-img"
                  src="https://images.unsplash.com/photo-1639149888905-fb39731f2e6c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=464&q=80"
                  alt="">--}}
              </a>
              <ul class="dropdown-menu">
				  
                <li><a class="dropdown-item" href="{{route('supplier.logout')}}" >Logout</a></li>
              </ul>
            </div>
          </div>
        </div>
      </nav>
    </header>