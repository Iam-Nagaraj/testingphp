@extends('layout/main')

@section('title', 'Cash Back')

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
                <h3>Cash Back</h3>
                <form id="cashback-form" method="POST" action="{{route('merchant.cashback.store')}}" enctype="multipart/form-data">
					@csrf
                    <input value="{{$detail->id}}" name="id" type="hidden" >
                    <div class="row">
                        <div class="col-md-6">
							<div class="form-group input-group-outline mt-3" id="imageInput">
								<label for="image" class="form-label">Cash Back</label>
								<input class="form-control" name="cashback" type="number" value="{{$detail->cashback}}">
								<strong class="text-danger is-invalid" id="cashback"></strong>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group input-group-outline mt-3" id="imageInput">
								<label for="image" class="form-label">Percentage</label>
								<input class="form-control" name="percentage" type="text" value="{{$detail->percentage}}" >
								<strong class="text-danger is-invalid" id="percentage"></strong>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group input-group-outline mt-3" id="imageInput">
								<label for="image" class="form-label">Min Amount</label>
								<input class="form-control" name="min_amount" type="number" value="{{$detail->min_amount}}">
								<strong class="text-danger is-invalid" id="min_amount"></strong>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group input-group-outline mt-3" id="imageInput">
								<label for="image" class="form-label">Max Amount</label>
								<input class="form-control" name="max_amount" type="text" value="{{$detail->max_amount}}" >
								<strong class="text-danger is-invalid" id="max_amount"></strong>
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
	var cashback_datatable_url = "{{ route('merchant.cashback.table') }}";
	var cashback_status_change_url = "{{ route('merchant.cashback.status.change') }}";

</script>

@endpush