@extends('layout/main')

@section('title', 'Change Password')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Change Password</h1>
    </div>

    <!-- Content Row -->
    <div class="form_card merchant_form card shadow mb-4 p-5">
        <div class="row">
            <div class="col-md-12">
                <h3>Change Password</h3>
                <form id="changePassword-form" method="POST" action="{{route('merchant.auth.update-password')}}" enctype="multipart/form-data">
					@csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label>Password</label>
                            <input class="form-control" name="password" type="text" value="">
                            <strong class="text-danger is-invalid" id="password"></strong>
                        </div>
                        <div class="col-md-6">
                            <label>Confirm Password</label>
                            <input type="text" name="password_confirmation" class="form-control" >
                            <strong class="text-danger is-invalid" id="password_confirmation"></strong>
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

@endsection

@push('style')

@endpush

@push('js')
<script src="{{ asset('assets') }}/js/admin/change-password.js"></script>

@endpush