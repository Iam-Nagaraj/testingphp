@extends('layout/blank')

@section('title', 'Admin Login')

@section('content')
<div class="position-relative">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">

      <!-- Login -->
      <div class="card p-2">
        <!-- Logo -->
        <div class="app-brand justify-content-center mt-5">
          <a href="{{url('/')}}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">@include('_partials.macros',["height"=>20,"withbg"=>'fill: #fff;'])</span>
          </a>
        </div>
        <!-- /Logo -->

        <div class="card-body mt-2">
          <h4 class="mb-2">Welcome to {{config('variables.templateName')}}!</h4>
          <p class="mb-4">Please sign in to your account and start your journey with Scash</p>

          <form id="login-form" class="mb-3" action="{{route('admin.auth.login.store')}}" method="POST">
          @csrf
            <div class="form-floating form-floating-outline mb-3">
              <label for="email">Email</label>
              <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" autofocus>
              <strong class="text-danger is-invalid" id="name"></strong>
            </div>
            <div class="mb-3">
              <div class="form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                  <label for="password">Password</label>
                    <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                    <i class="fa fa-eye" aria-hidden="true" onclick="initPasswordToggle()"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-3 d-flex justify-content-between">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember-me">
                <label class="form-check-label" for="remember-me">
                  Remember Me
                </label>
              </div>
              <!-- <a href="{{url('auth/forgot-password-basic')}}" class="float-end mb-1">
                <span>Forgot Password?</span>
              </a> -->
            </div>
            <div class="mb-3">
              <button class="btn btn-primary nextBtn d-grid w-100" type="submit">Sign in</button>
            </div>
          </form>

        </div>
      </div>
      <!-- /Login -->

    </div>
  </div>
</div>
@endsection

@push('style')
<style>
  #wrapper #content-wrapper #content {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
@endpush

@push('js')
<script src="{{ asset('assets') }}/js/auth/login.js"></script>



@endpush
