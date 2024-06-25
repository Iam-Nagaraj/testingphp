@extends('layout/main')

@section('title', 'Update Email')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Update Email</h1>
    </div>
    <!-- Content Row -->
    <div class="form_card merchant_form card shadow mb-4 p-5">
        <div class="row">
            <div class="col-md-12">
                <!-- <h3>Profile</h3> -->
            
                <form method="POST" id="update-email"  action="{{route('merchant.checkEmail')}}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col">
                            <label>New Email</label>
                            <input type="email" value="" id="email_address" name="email" class="form-control view-only">
                            <strong class="text-danger is-invalid" id="email"></strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit">Send OTP</button>
                        </div>
                    </div>
                   
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered custom_moddal">
    <div class="modal-content">
        <div class="modal-header">
        <h2 class="modal-title fs-5" id="depositModelLabel">Enter OTP to verify</h2>
      </div>
      <form id="email-verify" method="POST" action="{{route('merchant.verifyEmail')}}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="email_phone_number" id="new_email" value="">
        <div class="modal-body">
          <div class="form-group">
            <label class="control-label my-2">Enter OTP</label>
            <input type="number" name="code" required="required" value="" class="form-control" placeholder="Enter OTP">
            <strong class="text-danger is-invalid" id="code"></strong>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Submit</button>
          <button type="button" class="btn btn-secondary" onclick="$('#otpModal').modal('hide')">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('style')
@endpush

@push('js')
<script src="{{ asset('assets') }}/js/merchant/email-update.js"></script>
@endpush