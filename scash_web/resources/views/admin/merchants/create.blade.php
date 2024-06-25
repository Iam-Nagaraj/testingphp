@extends('layout/main')

@section('title', 'Merchant Details')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Merchant Detail</h1>
        <!-- <a
                href="#"
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"
                ><i class="fas fa-download fa-sm text-white-50"></i> Generate
                Report</a
              > -->
    </div>

    <!-- Content Row -->
    <div class="form_card merchant_form card shadow mb-4 p-5 merchant_detail_form">
        <div class="row">
            <div class="col-md-12">
                <h3>Merchant Detail</h3>
                <form id="merchant-form" method="POST" action="{{route('admin.merchant.store')}}"  enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <label>Name</label>
                            <input type="text" placeholder="Name" name="name" class="form-control view-only @error('name') is-invalid @enderror">
                            <strong class="text-danger is-invalid" id="name"></strong>
                        </div>
                        <div class="col">
                            <label>Logo</label>
                            <input class="form-control @error('logo') is-invalid @enderror" name="logo" type="file" value="">
                            <strong class="text-danger is-invalid" id="logo"></strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Email</label>
                            <input name="email" type="email" class="form-control @error('email') is-invalid @enderror view-only" placeholder="Enter Email">
                            <strong class="text-danger is-invalid" id="email"></strong>
                        </div>
                        <div class="col">
                            <label>Password</label>
                            <input name="password" type="password" class="form-control @error('password') is-invalid @enderror view-only" placeholder="Enter password">
                            <strong class="text-danger is-invalid" id="password"></strong>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Address</label>
                            <input name="address" id="myAddress" type="text" class="form-control view-only">
                            <strong class="text-danger is-invalid" id="address"></strong>
                        </div>
                        <div class="col">
                            <label>Phone Number </label>
                            <input type="text" id="mobile_code" placeholder="Phone Number" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" onkeypress="return isNumberKey(event)">
                            <input type="hidden" name="country_code" id="dial_code">
                            <strong class="text-danger is-invalid" id="phone_number"></strong>
                           
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Line 1</label>
                            <input name="line_1" id="line_1" type="text" class="form-control view-only">
                            <strong class="text-danger is-invalid"></strong>
                        </div>
                        <div class="col">
                            <label>Line 2</label>
                            <input name="line_2" id="line_2" type="text" class="form-control view-only">
                            <strong class="text-danger is-invalid" ></strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>State</label>
                            <input name="state" id="state" readonly type="state" class="form-control view-only">
                        </div>
                        <div class="col">
                            <label>City</label>
                            <input name="city" id="city" readonly type="city" class="form-control view-only">
                        </div>
                    </div>
                    <input  name="country" id="country" type="hidden">
                    <input  name="latitude" id="latitude" type="hidden">
                    <input  name="longitude" id="longitude" type="hidden">

                    <div class="row">

                        <div class="col">
                            <label>Business Document</label>
                            <input class="form-control @error('business_proff') is-invalid @enderror" name="business_proff" type="file" value="">
                            <strong class="text-danger is-invalid" id="business_proff"></strong>
                           
                        </div>
                        <div class="col">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                <option value="">Select Option</option>
                                <option value="0">Pending</option>
                                <option value="1">Active</option>
                                <option value="2">Rejected</option>
                                <option value="3">Kyc Verification</option>
                            </select>
                            <strong class="text-danger is-invalid" id="status"></strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit">Save Changes</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Choose addess by pin your location</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="map" style="height: 400px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css" />
<style>

/* #mobile_code {
    padding-left: 55px !important;
  } */
</style>
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput-jquery.min.js"></script>
<script src="{{ asset('assets') }}/js/admin/merchant.js"></script>
<script>

  $("#mobile_code").intlTelInput({
    initialCountry: "in",
    separateDialCode: true,
    // utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.4/js/utils.js"
  });
  var selectedCountryCode = 91;
  $("#mobile_code").on("blur", function () {
    var countryData = $("#mobile_code").intlTelInput("getSelectedCountryData");
    var selectedCountryCode = countryData.dialCode;
    $("#dial_code").val('+'+selectedCountryCode);
  });
</script>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key={{env('GOOGLE_MAPS_API_KEY')}}"></script>
<script src="{{ asset('assets') }}/js/auth/address-google-location.js"></script>

@endpush