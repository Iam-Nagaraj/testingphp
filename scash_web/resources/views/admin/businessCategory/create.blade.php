@extends('layout/main')

@section('title', 'Business Category Create')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Business Category Create</h1>
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
                <h3>Business Category</h3>
                <form id="businessCategory-form" method="POST" action="{{route('admin.businessCategory.store')}}" enctype="multipart/form-data">
					@csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label>Name</label>
                            <input class="form-control" name="name" type="text" value="">
                            <strong class="text-danger is-invalid" id="name"></strong>
                        </div>

                        <div class="col-md-6">
                            <label>Dwolla Key</label>
                            <input class="form-control" name="dwolla_key" type="text" value="">
                            <strong class="text-danger is-invalid" id="dwolla_key"></strong>
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
<script src="{{ asset('assets') }}/js/admin/business-category.js"></script>
<script>
	var businessCategory_datatable_url = "{{ route('admin.businessCategory.table') }}";
	var businessCategory_status_change_url = "{{ route('admin.businessCategory.status.change') }}";

</script>

@endpush