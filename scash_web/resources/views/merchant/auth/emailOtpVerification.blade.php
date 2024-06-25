@extends('layout/blank')

@section('title', 'Otp Verification')

@section('content')
<div class="position-relative">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-2">

      <!-- otp -->
      <div class="card p-2">
        <!-- Logo -->
        <div class="app-brand justify-content-center mt-5">
          <a href="{{url('/')}}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">@include('_partials.macros',["height"=>20,"withbg"=>'fill: #fff;'])</span>
          </a>
        </div>
        <!-- /Logo -->

        <div class="card-body mt-1">
          <p class="mb-4">Please Verify Email Using OTP &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </p>

          <form id="otp-verification-form" class="mb-3" action="{{route('merchant.auth.verifyOtp')}}" method="POST">
          @csrf
            <div class="form-floating form-floating-outline mb-3">
              <label for="email">Email</label>
              <input type="email" class="form-control"  name="email_phone_number" placeholder="Enter your Email" autofocus>
              <strong class="text-danger is-invalid" id="email_phone_number"></strong>
            </div>
            <div class="form-floating form-floating-outline mb-3">
              <label for="email">OTP</label>
              <input type="number" class="form-control"  name="code" >
              <strong class="text-danger is-invalid" id="code"></strong>
            </div>
            <div class="mb-3">
              <button class="btn btn-primary d-grid w-100" type="submit">Verify Otp</button>
            </div>
          </form>

          <p class="text-center">
            <span>Existing Account?</span>
            <a href="{{url('auth/login')}}">
              <span>Sign In</span>
            </a>
          </p>
        </div>
      </div>
      <!-- /otp -->
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
<script src="{{ asset('assets') }}/js/auth/register.js"></script>
<script src="{{ asset('assets') }}/js/auth/verify-otp.js"></script>

@endpush
