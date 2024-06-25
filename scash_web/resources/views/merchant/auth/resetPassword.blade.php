@extends('layout/blank')

@section('title', 'Merchant changePassword')

@section('content')
<div class="position-relative">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">

      <!-- changePassword -->
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
          <p class="mb-4">Please enter your password to change</p>

          <form id="changePassword-form" class="mb-3" action="{{route('merchant.auth.updateResetPassword')}}" method="POST">
          @csrf
          <input type="hidden" name="password_token" value="{{$email}}">
            <div class="form-floating form-floating-outline mb-3">
              <label for="email">Password</label>
              <input type="text" class="form-control" name="password" placeholder="Enter your password" autofocus>
              <strong class="text-danger is-invalid" id="password"></strong>
            </div>
            <div class="form-floating form-floating-outline mb-3">
              <label for="email">Confirm Password</label>
              <input type="text" class="form-control" name="password_confirmation" placeholder="Enter your password" autofocus>
              <strong class="text-danger is-invalid" id="password_confirmation"></strong>
            </div>
            <div class="mb-3">
              <button class="btn btn-primary nextBtn d-grid w-100" type="submit">Confirm</button>
            </div>
          </form>

          <p class="text-center">
            <span>New on our platform?</span>
            <a href="{{route('merchant.auth.register')}}">
              <span>Create an account</span>
            </a>
          </p>
        </div>
      </div>
      <!-- /changePassword -->
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

<script src="{{ asset('assets') }}/js/auth/reserPassword.js"></script>

@endpush
