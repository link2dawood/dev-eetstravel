<!DOCTYPE html>
<html lang="en">
  @include('TMSClient.layout.supplier_head')
  <body style="background-image: url('https://images.unsplash.com/photo-1577366643984-84350d3ebf2e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1471&q=80'); background-position: center;">
    <div class="main">
      <div class="main-content">
        <section class="register">
          <div class="container">
            <div class="wrapper">
              <div class="card">
                <div class="card-body p-5">
                  <img src="https://eetstravel.com/wp-content/uploads/2022/05/final-1-300x263.jpg" class="img-fluid w-50 mx-auto d-block mb-4">
                  <h1 class="title text-center fs-2">Login to your account</h1>
                  @if(session('error'))
                      <div class="alert alert-danger">{{ session('error') }}</div>
                  @endif
                  <form action="{{ route('supplier.login') }}" method="post">
                   {{csrf_field()}}
                    <div class="d-flex flex-column align-items-center w-100">
                      <div class="input-wrapper">
                        <input type="text" class="form-control" placeholder="Email Address" name = "contact_email">
                      </div>
                      <div class="input-wrapper mb-4">
                        <input type="password" class="form-control" placeholder="Password" name = "password">
                      </div>
                      <button class="btn btn-primary px-lg-5 w-100" type="submit">Login</button>
                    </div>
                   
                  </form>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <div class="cursor"></div>
    </div>

    @include('TMSClient.layout.footer')
  </body>
</html>
