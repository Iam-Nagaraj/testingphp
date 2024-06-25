@extends('layout/main')

@section('title', 'Profile')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Profile</h1>
    </div>
    <div class="profile_top_heading card shadow mb-4 p-4">
        <div class="row">
            <div class="col">
                <div class="profile_user">
                    <div class="img">
                        <img src="{{$detail->image_url}}">
                    </div>
                    <div class="text">
                        <h5><span>{{$detail->first_name}} {{$detail->last_name}}</span></h5>
                    </div>
                </div>
            </div>
            <div class="col text-right">                            
                <div class="btn_flex">
                    <div id="qrcode"></div>
                    @if(!empty($detail->business_document))
                    <a class="btn btn-primary" target="_blank" href="{{$detail->business_document}}">Document</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Content Row -->
    <div class="form_card merchant_form card shadow mb-4 p-5">
        <div class="row">
            <div class="col-md-12">
                <!-- <h3>Profile</h3> -->
            
                <form method="POST" id="profile-form"  action="{{route('merchant.profile-update')}}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col">
                            <label>First Name</label>
                            <input type="text" placeholder="Name" value="{{$detail->first_name}}" name="first_name" class="form-control view-only">
                            <strong class="text-danger is-invalid" id="first_name"></strong>
                        </div>
                        <div class="col">
                            <label>Last Name</label>
                            <input value="{{$detail->last_name}}" name="last_name" type="text" class="form-control view-only">
                            <strong class="text-danger is-invalid" id="last_name"></strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Email 
                                <a href="{{route('merchant.updateEmail')}}"><i class="fa fa-pencil" aria-hidden="true"></i></a> 
                            </label>
                            <input value="{{$detail->email}}" name="email" type="email" class="form-control view-only" readonly>
                        </div>
                        <div class="col">
                            <label>Phone Number 
                                <a href="{{route('merchant.updatePhone')}}"><i class="fa fa-pencil" aria-hidden="true"></i></a> 
                            </label>
                            <input type="text" value="{{$detail->country_code}}{{$detail->phone_number}}" class="form-control" placeholder="+918607672206" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Tax (%)</label>
                            <input value="{{$detail->tax_percentage}}" name="tax_percentage" type="text" class="form-control view-only">
                            <strong class="text-danger is-invalid" id="tax_percentage"></strong>
                        </div>
                        <div class="col">
                            <label>Address</label>
                            <input value="{{$detail->address->address??'N/A'}}" name="address" id="myAddress" type="text" class="form-control view-only" readonly>
                            <strong class="text-danger is-invalid" id="address"></strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Line 1</label>
                            <input value="{{$detail->address->address??'N/A'}}" name="line_1" id="line_1" type="text" class="form-control view-only" readonly>
                        </div>
                        <div class="col">
                            <label>Line 2</label>
                            <input value="{{$detail->address->line_2??'N/A'}}" name="line_2" id="line_2" type="text" class="form-control view-only" readonly>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col">
                            <label>State</label>
                            <input value="{{$detail->address->state??'N/A'}}" name="state" id="state" type="text" class="form-control view-only" readonly>
                        </div>
                        <div class="col">
                            <label>City</label>
                            <input value="{{$detail->address->city??'N/A'}}" name="city" id="city" type="text" class="form-control view-only" readonly>
                        </div>
                    </div>

                    <input name="latitude" id="latitude" type="hidden" value="{{$detail->address->latitude??'N/A'}}" readonly>
                    <input name="longitude" id="longitude" type="hidden" value="{{$detail->address->longitude??'N/A'}}" readonly>

                    <div class="row">
                        <div class="col">
                            <label>Zip Code</label>
                            <input value="{{$detail->address->postal_code??'N/A'}}" name="postal_code" id="postal_code" type="text" class="form-control view-only" readonly>
                        </div>
                        <div class="col">
                            <label>Date Of Birth</label>
                            <input value="{{$detail->date_of_birth}}" name="date_of_birth" type="date" class="form-control view-only" readonly>
                            <strong class="text-danger is-invalid" id="date_of_birth"></strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Business Display name</label>
                            <input value="{{$detail->BusinessDetail->business_name??'N/A'}}" name="business_name" type="text" class="form-control view-only">
                            <strong class="text-danger is-invalid" id="business_name"></strong>
                        </div>
                        <div class="col">
                            <label>Legal Name</label>
                            <input value="{{$detail->BusinessDetail->leagal_name}}" name="leagal_name" type="text" class="form-control view-only">
                            <strong class="text-danger is-invalid" id="leagal_name"></strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Business category</label>
                            {!! Form::select('business_category', $BusinessCategory, $detail->BusinessDetail->business_category??0, ['class' => 'form-control']) !!}
                            <strong class="text-danger is-invalid" id="business_category"></strong>
                        </div>
                        <div class="col">
                            <label>About Your Business</label>
                            <input value="{{$detail->BusinessDetail->about_business??'N/A'}}" name="about_business" type="text" class="form-control view-only">
                            <strong class="text-danger is-invalid" id="about_business"></strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Business Address Line 1</label>
                            <input value="{{$detail->BusinessDetail->business_street_address??'N/A'}}" readonly name="about_business" type="text" class="form-control view-only">
                            <strong class="text-danger is-invalid" id="about_business"></strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>State</label>
                            <input value="{{$detail->BusinessDetail->business_state??'N/A'}}" readonly name="about_business" type="text" class="form-control view-only">
                            <strong class="text-danger is-invalid" id="about_business"></strong>
                        </div>
                        <div class="col">
                            <label>City</label>
                            <input value="{{$detail->BusinessDetail->business_city??'N/A'}}" readonly name="about_business" type="text" class="form-control view-only">
                            <strong class="text-danger is-invalid" id="about_business"></strong>
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
<div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered custom_moddal">
      <div class="modal-content p-5">

        <div>
            <h2 class="modal-title fs-5 mb-3" id="withdrawModelLabel">Scan to pay merchant</h2>

            <img src="" id="download_qr" alt="" style="border:1px solid #ccc; padding:2px;">
            
            <div class="mt-5">
                <button type="button" class="btn btn-success" onclick="downloadImageDataUrl()">download</button>
                <button type="button" class="btn btn-secondary" onclick="$('#otpModal').modal('hide')">Close</button>
            </div>

        </div>

    </div>
  </div>
</div>

@endsection

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endpush

@push('js')
<script src= "https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script> var qrcode = new QRCode("qrcode", "{{Auth::user()->uuid}}"); </script>
<script src="{{ asset('assets') }}/js/merchant/profile.js"></script>


@endpush