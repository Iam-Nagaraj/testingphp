@extends('layout/main')

@section('title', 'Cash Back Create')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Generate Pin</h1>
    </div>

    <!-- Content Row -->
    <div class="form_card merchant_form card shadow mb-4 p-5">
        <div class="row">
            <div class="col-md-12">
                <form id="pin-form" method="POST" action="{{route('merchant.auth.store-pin')}}" enctype="multipart/form-data">
					@csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group input-group-outline mt-3" id="imageInput">
                                <label for="image" class="form-label">Pin</label>
                                <input class="form-control" name="pin" type="number" maxlength="4"
                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                <strong class="text-danger is-invalid" id="pin"></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group input-group-outline mt-3" id="imageInput">
                                <label for="image" class="form-label">Confirm Pin</label>
                                <input class="form-control" name="confirm_pin" type="number" value="" maxlength="4"
                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                <strong class="text-danger is-invalid" id="percentage"></strong>
                            </div>
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
<script src="{{ asset('assets') }}/js/admin/generate-pin.js"></script>

@endpush