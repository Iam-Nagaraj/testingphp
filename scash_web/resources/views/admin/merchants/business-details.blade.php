@extends('layout/main')

@section('title', 'Merchant Business Details')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Business Details</h1>
        <!-- <a
                href="#"
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"
                ><i class="fas fa-download fa-sm text-white-50"></i> Generate
                Report</a
              > -->
    </div>

    <!-- Content Row -->
    <div class="form_card merchant_form card shadow mb-4 p-5">
        <div class="row">
            <div class="col-md-12">
                <h3>Business Details</h3>
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Logo</label>
                            <img src="{{$detail->image_url}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Business name</label>
                            <input value="{{$detail->business_name}}" name="name" type="name" class="form-control" readonly>
                        </div>
                        <div class="col">
                            <label>Business legal name</label>
                            <input value="{{$detail->leagal_name}}" name="name" type="name" class="form-control" readonly>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Registration type</label>
                            <input type="text" value="{{$detail->BusinessType ? $detail->BusinessType->name : ''}}"  class="form-control" readonly>
                        </div>
                        <div class="col">
                            <label>Tax Type</label>
                            <input type="text" value="{{$detail->tax_type_name}}" class="form-control" readonly>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Business Category</label>
                            <input value="{{$detail->BusinessCategory ? $detail->BusinessCategory->name : ''}}" name="name" type="name" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>About Business</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" readonly>{{$detail->about_business}}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Address Line 1</label>
                            <input value="{{$detail->business_street_address ? $detail->business_street_address : ''}}" name="name" type="name" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>State</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" readonly>{{$detail->business_city}}</textarea>
                        </div>
                        <div class="col">
                            <label>City</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" readonly>{{$detail->business_state}}</textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('style')



@endpush

@push('js')



@endpush