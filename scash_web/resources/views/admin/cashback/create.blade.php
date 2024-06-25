@extends('layout/main')

@section('title', 'Cash Back Create')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Cash Back</h1>
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
                <h3>Add</h3>
                <form id="cashback-form" method="POST" action="{{route('admin.cashback.store')}}" enctype="multipart/form-data">
					@csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group input-group-outline mt-3" id="business_category_id">
                                <label for="image" class="form-label">Business Category</label>
                                {!! Form::select('business_category_id', $businessCategory->pluck('name', 'id'), null,['class' => 'form-control']) !!}
                                <strong class="text-danger is-invalid" id="business_category_id"></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group input-group-outline mt-3" id="imageInput">
                                <label for="image" class="form-label">Percentage</label>
                                
                                {!! Form::input('number', 'percentage', null, ['class' => 'form-control', 'step' => '0.01']) !!}
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
<script src="{{ asset('assets') }}/js/admin/cashback.js"></script>
<script>
	var cashback_datatable_url = "{{ route('admin.cashback.table') }}";
	var cashback_status_change_url = "{{ route('admin.cashback.status.change') }}";

</script>

@endpush