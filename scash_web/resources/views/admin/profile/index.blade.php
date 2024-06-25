@extends('layout/main')

@section('title', 'Profile')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Profile</h1>
    </div>

    <!-- Content Row -->
    <div class="form_card merchant_form card shadow mb-4 p-5">
        <div class="row">
            <div class="col-md-12">
                <h3>Profile</h3>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col">
                            <label>Image</label>
                            <img src="{{$detail->image_url}}">
                        </div>

                    </div>
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