@extends('layout/blank')

@section('title', 'Merchant Register')

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('content')
<div class="position-relative">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-2">

      <!-- register -->
      <div class="card p-2">
        <!-- Logo -->
        <div class="app-brand justify-content-center mt-5">
          <a href="{{url('/')}}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">@include('_partials.macros',["height"=>20,"withbg"=>'fill: #fff;'])</span>
          </a>
        </div>
        <!-- /Logo -->

        <div class="card-body mt-1">
          <h4 class="mb-2">Welcome to {{config('variables.templateName')}}!</h4>
          <p class="mb-4">Please sign in to your account and start your journey with Scash</p>

          <div class="">

            <div class="stepwizard col-md-offset-3">
              <div class="stepwizard-row setup-panel">
                <div class="stepwizard-step">
                  <a href="#step-1" type="button" class="btn btn-primary btn-circle" style="margin-right: 140px;">1</a>
                </div>
                <div class="stepwizard-step">
                  <a href="#step-2" type="button" class="btn btn-default btn-circle" style="margin-right: 140px;" disabled="disabled">2</a>
                </div>
                <div class="stepwizard-step">
                  <a href="#step-3" type="button" class="btn btn-default btn-circle" style="margin-right: 0px;" disabled="disabled">3</a>
                </div>
              </div>
            </div>

            <form role="form" id="stepForm"  action="{{route('merchant.auth.web-register')}}" class="mt-3 mb-3" method="post" enctype="multipart/form-data">
              @csrf
              <!-- ######## 1 ######## -->
              <div class="row setup-content" id="step-1">
                <div class="col-md-12">
                  <div class="col-md-12">
                    <h3> Step 1</h3>
                    <label class="control-label mb-2">Business Type</label>
                    <div class="form-group">
                      @foreach($BusinessType as $k => $singletype)
                      <div class="form-check">
                        <input class="form-check-input" type="radio" value="{{$singletype->id}}" name="registration_type" id="registration_type{{$k}}" checked data-value="{{$singletype->type}}">
                        <label class="form-check-label" for="registration_type{{$k}}">
                          {{$singletype->name}}
                        </label>
                      </div>
                      @endforeach

                      <input class="tax_radio SSN_RADIO" disabled type="radio" value="{{getConfigConstant('BUSINESS_TYPE_SSN')}}" name="tax_type" style="display:none;">
                      <input class="tax_radio EIN_RADIO" disabled type="radio" value="{{getConfigConstant('BUSINESS_TYPE_EIN')}}" name="tax_type" style="display:none;">

                    </div>
                    <button class="btn btn-primary nextBtn btn-lg pull-right mt-3" id="CHECK_TYPE_BUTTON" type="button">Next</button>
                  </div>
                </div>
              </div>
              <!-- ######## 2 ######## -->
              <div class="row setup-content" id="step-2">
                <div class="col-md-12">
                  <div class="col-md-12">
                    <h3> Step 2</h3>
                    <div class="form-group">
                      <label class="control-label my-2">First Name</label>
                      <input maxlength="200" type="text" name="first_name" required="required" class="form-control" placeholder="Enter Your First Name">
                      <strong class="text-danger is-invalid" id="first_name"></strong>
                    </div>
                    <div class="form-group">
                      <label class="control-label my-2">Last Name</label>
                      <input maxlength="200" type="text" name="last_name" required="required" class="form-control" placeholder="Enter Your Last Name">
                      <strong class="text-danger is-invalid" id="last_name"></strong>
                    </div>
                    <div class="form-group mb-3 mobile_number">
                      <label class="control-label my-2">Phone</label>
                      <img src="{{asset('assets/img/united-states.png')}}">
                      <span>+1</span>
                      <input type="number" id="phone_number" required="required" class="form-control" name="phone_number" placeholder="Enter Mobile Number" />
                      <input type="hidden" id="country_code" name="country_code" value="+1">
                      <strong class="text-danger is-invalid" id="phone_number_error"></strong>
                    </div>
                    <div class="form-group mb-3 form-email">
                      <label class="control-label my-2">Email</label>
                      <input type="email" class="form-control" required="required" id="email" name="email" placeholder="Enter your email" autofocus>
                      <strong class="text-danger is-invalid email"></strong>
                    </div>
                    <div class="form-password-toggle form-group form-password">
                      <div class="input-group input-group-merge form-group">
                        <div class="form-floating form-floating-outline">
                        <label for="password">Password</label>
                          <input type="password" id="password" required="required" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                          <i class="fa fa-eye" aria-hidden="true" onclick="initPasswordToggle()"></i>
                        </div>
                      </div>
                    </div>
                    <div class="form-password-toggle form-group form-password">
                      <div class="input-group input-group-merge form-group">
                        <div class="form-floating form-floating-outline">
                        <label for="password">Confirm Password</label>
                          <input type="password" id="confirm_password" required="required" class="form-control" name="confirm_password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                          <i class="fa fa-eye" aria-hidden="true" onclick="initPasswordToggle2()"></i>
                        </div>
                        <strong class="text-danger is-invalid confirm_password"></strong>
                      </div>
                    </div>

                    <div class="form-group form-state">
                      <label class="control-label my-2">Address Line 1</label>
                      <input type="text" class="form-control" id="myAddress" required id="" name="address" placeholder="Enter Address Line 1" autofocus>
                      <strong class="text-danger is-invalid confirm_state"></strong>
                    </div>
                    <div class="form-group">
                      <label class="control-label my-2">Address Line 2</label>
                      <input type="hidden" id="line_1" name="line_1" class="form-control" >
                      <input type="text" id="line_2" name="line_2" class="form-control" placeholder="Enter Address Line 2">
                    </div>
                    <div class="form-group">
                      <label class="control-label my-2">State</label>
                      <input type="text" name="state_long_name" id="state_long_name" class="form-control" readonly >
                    </div>
                    <div class="form-group">
                      <label class="control-label my-2">City</label>
                      <input name="city" id="city" readonly type="text" class="form-control" >
                    </div>

                    <input name="state" id="state" type="hidden" >
                    <input name="country" id="country" readonly type="hidden" >
                    <input name="latitude" id="latitude" readonly type="hidden" >
                    <input name="longitude" id="longitude" readonly type="hidden" >

                    <div class="form-group mb-3">
                      <label class="control-label my-2">Zip code</label>
                      <input type="number" maxlength="5" id="business_zip_code2" name="zip_code" required="required" class="form-control" placeholder="Enter Zip code"
                      oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                    </div>
                    <div class="form-group">
                      <label class="control-label my-2">Date of birth</label>
                      <input maxlength="200" type="date" id="birthdate" onclick="setDate()" name="dob" required="required" class="form-control" placeholder="Enter Company Name">
                    </div>
                    
                    <button class="btn btn-primary prevBtn btn-lg pull-left" type="button">Previous</button>
                    <button class="btn btn-primary stepTwoBtn btn-lg pull-right" id="SSN_NEXT_BUTTON" type="button">Next</button>
                  </div>
                </div>
              </div>
              <!-- ######## 3 ######## -->
              <div class="row setup-content" id="step-3">
                <div class="col-md-12">
                  <div class="col-md-12">
                    <h3> Step 3</h3>
                    <div class="form-group">
                      <label class="control-label my-2">Business display name</label>
                      <input maxlength="200" type="text" name="business_name"  required="required" class="form-control" placeholder="Enter Company Name">
                    </div>
                    <div class="form-group upload_doc mt-3" id="imageInput">
                      <label for="image" class="form-label">Logo</label>
                      <input class="form-control" name="logo" required="required" type="file" value="" id="logo">
                      <div class="upload_img form-control"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M288 109.3V352c0 17.7-14.3 32-32 32s-32-14.3-32-32V109.3l-73.4 73.4c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l128-128c12.5-12.5 32.8-12.5 45.3 0l128 128c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L288 109.3zM64 352H192c0 35.3 28.7 64 64 64s64-28.7 64-64H448c35.3 0 64 28.7 64 64v32c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V416c0-35.3 28.7-64 64-64zM432 456a24 24 0 1 0 0-48 24 24 0 1 0 0 48z"/></svg><span>Upload Logo</span></div>
                    </div>
                    <div class="form-group upload_doc mt-3" id="imageInput">
                      <label for="image" class="form-label">Business Document</label>
                      <input class="form-control" name="business_proff" required="required" type="file" value="" id="business_proff">
                      <div class="upload_img form-control"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M288 109.3V352c0 17.7-14.3 32-32 32s-32-14.3-32-32V109.3l-73.4 73.4c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l128-128c12.5-12.5 32.8-12.5 45.3 0l128 128c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L288 109.3zM64 352H192c0 35.3 28.7 64 64 64s64-28.7 64-64H448c35.3 0 64 28.7 64 64v32c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V416c0-35.3 28.7-64 64-64zM432 456a24 24 0 1 0 0-48 24 24 0 1 0 0 48z"/></svg><span>Upload Business Document</span></div>
                    </div>
                    <div class="form-group">
                      <label class="control-label my-2">Business legal name</label>
                      <input maxlength="200" type="text" name="leagal_name" required="required" class="form-control" placeholder="Enter Business's legal name">
                    </div>

                    <div class="form-group form-state">
                      <label class="control-label my-2">Business Address Line 1</label>
                      <input type="text" class="form-control" id="myAddress2" required id="" name="business_address" placeholder="Enter Address Line 1" autofocus>
                      <strong class="text-danger is-invalid confirm_state"></strong>
                    </div>
                    <div class="form-group">
                      <label class="control-label my-2">Business Address Line 2</label>
                      <input type="hidden" id="business_line_1" name="business_line_1" class="form-control" >
                      <input type="text" id="business_line_2" name="business_line_2" class="form-control" placeholder="Enter Address Line 2">
                    </div>
                    <div class="form-group">
                      <label class="control-label my-2">State</label>
                      <input type="text" name="business_state_long_name" id="business_state_long_name" class="form-control" readonly type="hidden" >
                    </div>
                    <div class="form-group">
                      <label class="control-label my-2">City</label>
                      <input name="business_city" id="business_city" readonly type="text" class="form-control" >
                    </div>

                    <input name="business_state" id="business_state" type="hidden" >
                    <input name="business_country" id="business_country" readonly type="hidden" >
                    <input name="business_latitude" id="business_latitude" readonly type="hidden" >
                    <input name="business_longitude" id="business_longitude" readonly type="hidden" >

                    <div class="form-group">
                      <label class="control-label my-2">Zipcode</label>
                      <input name="business_zip_code" type="number" id="business_zip_code" required type="text" class="form-control" maxlength="5"
                      oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                    </div>

                    <div class="form-group">
                      <label class="control-label my-2">Talk about your business</label>
                      <textarea required="required" name="about_business" class="form-control" placeholder="Enter about your business"></textarea>
                    </div>
                    <div class="form-group  mb-3">
                      <label class="control-label my-2">Business Category</label>
                      <select class="form-control" name="business_category" id="business_category_id" onChange="getBusinessSubCategory()" required="required" aria-label="Default select example">
                        <option value="">Select Business Category</option>
                        @foreach($BusinessCategory as $singlecategory)
                        <option value="{{$singlecategory->id}}">{{$singlecategory->name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group mb-3" id="getBusinessSubCategory">

                    </div>
                    <div id="SSN_EIN_FORM" class="form-group form-ssn">
                    </div>
                    <div class="form-group upload_doc mt-3 upload_document_verification" id="imageInput" style="display: none;">
                      <label for="image" class="form-label">Verification Document</label>
                      <input class="form-control" name="verification_document" type="file" value="" id="verification_document">
                      <div class="upload_img form-control"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M288 109.3V352c0 17.7-14.3 32-32 32s-32-14.3-32-32V109.3l-73.4 73.4c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l128-128c12.5-12.5 32.8-12.5 45.3 0l128 128c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L288 109.3zM64 352H192c0 35.3 28.7 64 64 64s64-28.7 64-64H448c35.3 0 64 28.7 64 64v32c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V416c0-35.3 28.7-64 64-64zM432 456a24 24 0 1 0 0-48 24 24 0 1 0 0 48z"/></svg><span>Upload Verification Document</span></div>
                      <small class="form-text text-muted">Front and back photo of the document to be uploaded.</small>
                    </div>
                    <div class="form-group  mb-3 upload_document_verification" style="display: none;">
                      <label class="control-label my-2">Document Type</label>
                      <select class="form-control" name="document_type" id="document_type">
                        <option value="">Select Document Type</option>
                        <option value="passport">Passport</option>
                        <option value="license">License</option>
                        <option value="idCard">IdCard</option>
                        <option value="other">Other</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <a href="#" onclick="$('#ssnModal').modal('show')"  data-bs-toggle="modal" data-bs-target="#ssnModal" >Why do we need SSN/EIN</a> 
                    </div>
                    <div class="form-check form-privacy">
                        <input class="form-check-input" type="radio" name="privacy_policy" name="privacy_policy" id="privacy_policy" >
                        <label class="form-check-label" for="privacy_policy">
                          I agree to the Scash <a href="{{route('guest.auth.termsAndCondition.wevView')}}" target="_blank">Terms of Services</a> & 
                          <a href="{{route('guest.auth.privacyPolicy.webView')}}" target="_blank">Privacy Policy</a> as well as our partner Dwolla's 
                          <a href="https://www.dwolla.com/legal/dwolla-account-terms-of-service" target="_blank"> Terms of Services </a> & 
                          <a href="https://www.dwolla.com/legal/privacy" target="_blank"> Privacy Policy </a>
                        </label>
		                    <strong class="text-danger is-invalid privacy_policy_error"></strong>

                        <strong class="text-danger is-invalid" id="all_errors"></strong>

                      </div>
                    
                    <button class="btn btn-primary prevBtn btn-lg pull-left" type="button">Previous</button>
                    <button class="btn submitBtn btn-lg pull-right" style="float: right;" type="submit">Submit</button>
                  </div>
                </div>
              </div>
             
            </form>
          </div>

          <p class="text-center">
            <span>Existing Account?</span>
            <a href="{{url('auth/login')}}">
              <span>Sign In</span>
            </a>
          </p>
        </div>
      </div>

      <!-- /register -->
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="ssnModal" tabindex="-1" aria-labelledby="ssnModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered custom_moddal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title fs-5" id="ssnModalLabel">Why we need SSN/EIN number</h2>
      </div>
      <div class="modal-body">
      <label class="control-label my-2">
        We use this to verify your identity -
          we won't check your credit. if you receive more than your $20,000 and
          more than 200 transactions annually, we'll use it for your tax info.
        </label>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="$('#ssnModal').modal('hide')">Close</button>
      </div>
    </div>
  </div>
</div>
<x-loader-ajax-component></x-loader-ajax-component>

@endsection

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css" />
<link rel="stylesheet" href="{{asset('asset/css/register.css')}}" />
<style>
  .has-error .form-control {
    border-color: #a94442;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
}
</style>
@endpush

@push('js')

<script>
  var phone_ajax_url = "{{route('merchant.auth.checkValidPhoneNumber')}}";
  var business_subcategory_url = "{{route('merchant.auth.business-subcategory')}}";
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput-jquery.min.js"></script>


<!-- <script src="{{ asset('assets') }}/js/admin/merchant-google.js"></script> -->

<script src="{{ asset('assets') }}/js/auth/step-form.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=AIzaSyC63pR8Sg_IqtWOjBHE-G-ZXs-xTCdihjc"></script>
<script src="{{ asset('assets') }}/js/auth/register-address-google-location.js"></script>

<script>
  $("input[type=file]").change(function (e) {
  $(this).parents(".upload_doc").find(".upload_img").text(e.target.files[0].name);
});
</script>
@endpush