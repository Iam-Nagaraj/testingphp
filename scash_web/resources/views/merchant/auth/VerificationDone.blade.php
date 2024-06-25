@extends('layout/blank')

@section('title', 'Verification Done')

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
          <h4 class="mb-4">Account Verified Successfully</h4>
          <p class="mb-4">Please Wait For Admin Verification</p>
          <p class="text-center">
            <a href="{{url('auth/login')}}" class="btn btn-primary nextBtn d-grid w-100">
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
<script src="{{ asset('assets') }}/js/auth/verify-otp.js"></script>

@endpush
