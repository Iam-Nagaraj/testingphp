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
    <div class="row">
        <div class="col-md-2">
            <div class="form_card merchant_form card shadow mb-4 p-5">
                <div class="row">
                    <div class="col-md-12">
                        <center>
                            <h6>Logo</h6>
                        </center>    
                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="{{$detail->image_url}}">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="form_card merchant_form card shadow mb-4 p-5">
                <div class="row">
                    <div class="col-md-12">
                        <center>
                            <h6>Business Document</h6>
                        </center>    
                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="{{$detail->business_document}}">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form_card merchant_form card shadow mb-4 p-5">
            <div class="row">
                <div class="col-md-12">
                    <h4>Personal Detail</h4>

                    <input value="{{$detail->id}}" name="id" type="hidden" >
                    <div class="row">
                        <div class="col">
                            <label>First Name</label>
                            <input type="text" placeholder="Name" value="{{$detail->first_name}}" name="name" class="form-control view-only" readonly>
                        </div>
                        <div class="col">
                            <label>Last Name</label>
                            <input value="{{$detail->last_name}}" name="email" type="text" class="form-control view-only" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Email</label>
                            <input value="{{$detail->email}}" name="email" type="email" class="form-control view-only" readonly>
                        </div>
                        <div class="col">
                            <label>Phone Number </label>
                            <input type="text" value="{{$detail->country_code}}{{$detail->phone_number}}" class="form-control" placeholder="+918607672206" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Address</label>
                            <input value="{{$detail->address->address??'N/A'}}" name="address" id="address" type="address" class="form-control view-only" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Line 1</label>
                            <input value="{{$detail->address->line_1??'N/A'}}" name="line_1" type="text" class="form-control view-only" readonly>
                        </div>
                        <div class="col">
                            <label>Line 2</label>
                            <input value="{{$detail->address->line_2??'N/A'}}" name="line_2" type="text" class="form-control view-only" readonly>
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
                    <input class="form-control " name="latitude" id="latitude" type="hidden" 
                        value="{{$detail->address->latitude??'N/A'}}" readonly>
                    <input class="form-control " name="longitude" id="longitude" type="hidden" 
                        value="{{$detail->address->longitude??'N/A'}}" readonly>

                    <div class="row">
                        <div class="col">
                            <label>Zip Code</label>
                            <input value="{{$detail->address->postal_code??'N/A'}}" name="postal_code" id="postal_code" type="text" class="form-control view-only" readonly>
                        </div>
                        <div class="col">
                            <label>Status</label>
                            <select class="form-control" name="status" readonly>
                                <option value="">Select Option</option>
                                <option value="0" {{($detail->status == 0)?'selected':''}} >Pending</option>
                                <option value="1" {{($detail->status == 1)?'selected':''}} >Active</option>
                                <option value="2" {{($detail->status == 2)?'selected':''}} >Rejected</option>
                                <option value="3" {{($detail->status == 3)?'selected':''}} >Kyc Verification</option>
                            </select>
                        </div>
                    </div>
                
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form_card merchant_form card shadow mb-4 p-5">
                <div class="row">
                    <div class="col-md-12">
                        <h4>Business Details</h4>
                        @if($detail->status == 3)
                        <form action="{{route('admin.merchant.certifyMerchant')}}" id="merchant-certified-form" method="post">
                            @csrf
                            <input type="hidden" name="customer" value="{{$detail->uuid}}">
                            <button type="submit" class="btn btn-success">Certified</button>
                        </form>
                        @endif
                        
                        <div class="row">
                            <div class="col">
                                <label>Business name</label>
                                <input value="{{$businessDetails->business_name}}" name="name" type="name" class="form-control" readonly>
                            </div>
                            <div class="col">
                                <label>Business legal name</label>
                                <input value="{{$businessDetails->leagal_name}}" name="name" type="name" class="form-control" readonly>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col">
                                <label>Registration type</label>
                                <input type="text" value="{{$businessDetails->BusinessType ? $businessDetails->BusinessType->name : ''}}"  class="form-control" readonly>
                            </div>
                            <div class="col">
                                <label>Tax Type</label>
                                <input type="text" value="{{$businessDetails->tax_type_name}}" class="form-control" readonly>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col">
                                <label>Business Category</label>
                                <input value="{{$businessDetails->BusinessCategory ? $businessDetails->BusinessCategory->name : ''}}" name="name" type="name" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label>About Business</label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" readonly>{{$businessDetails->about_business}}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label>Address Line 1</label>
                                <input value="{{$businessDetails->business_street_address ? $businessDetails->business_street_address : ''}}" name="name" type="name" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label>State</label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" readonly>{{$businessDetails->business_city}}</textarea>
                            </div>
                            <div class="col">
                                <label>City</label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" readonly>{{$businessDetails->business_state}}</textarea>
                            </div>
                        </div>
                    
                    </div>
                </div>
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
<x-loader-ajax-component></x-loader-ajax-component>

@endsection

@push('style')
@endpush

@push('js')

<script>
	var marker_lat = parseFloat($('#latitude').val());
	var marker_lang = parseFloat($('#longitude').val());
	if(isNaN(marker_lat)){
		marker_lat = 0;
	}
	if(isNaN(marker_lang)){
		marker_lang = 0;
	}
</script>

<script src="{{ asset('assets') }}/js/admin/merchant-google.js"></script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places&callback=initMap"></script>

@endpush