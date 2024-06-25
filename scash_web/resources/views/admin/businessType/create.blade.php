@extends('layout/main')

@section('title', 'Business Type Create')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Business Type Create</h1>
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
                <h3>Business Type</h3>
                <form id="businessType-form" method="POST" action="{{route('admin.businessType.store')}}" enctype="multipart/form-data">
					@csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label>Name</label>
                            <input class="form-control" name="name" type="text" value="">
                            <strong class="text-danger is-invalid" id="name"></strong>
                        </div>
                        <div class="col-md-6">
                            <label>Tax ID</label>
                            <select name="type" class="form-control">
                                <option value="">Select</option>
                                <option value="1">SSN</option>
                                <option value="2">EIN</option>
                            </select>
                            <strong class="text-danger is-invalid" id="type"></strong>
                        </div>

                        <div class="col-md-6">
                            <label>Dwolla Key</label>
                            <select name="dwolla_key" class="form-control">
                                <option value="">Select</option>
                                <option value="corporation">Corporation</option>
                                <option value="llc">Llc</option>
                                <option value="partnership">Partnership</option>
                                <option value="soleProprietorship">Sole Proprietorship</option>
                            </select>
                            <strong class="text-danger is-invalid" id="type"></strong>
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
<script src="{{ asset('assets') }}/js/admin/business-type.js"></script>
<script>
	var businessType_datatable_url = "{{ route('admin.businessType.table') }}";
	var businessType_status_change_url = "{{ route('admin.businessType.status.change') }}";

</script>

@endpush