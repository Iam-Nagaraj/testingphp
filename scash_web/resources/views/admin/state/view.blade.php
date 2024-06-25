@extends('layout/main')

@section('title', 'State')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">State</h1>
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
                <h3>State</h3>
                <form id="state-form" method="POST" action="{{route('admin.state.store')}}" enctype="multipart/form-data">
					@csrf
                    <input value="{{$detail->id}}" name="id" type="hidden" >
                    <div class="row">
						<div class="col-md-6">
							<div class="form-group input-group-outline mt-3" [ngClass]="{ 'is-filled': name }">
								<label class="form-label">Name</label>
								<input value="{{$detail->name}}" name="name" type="name" class="form-control view-only" >
								<strong class="text-danger is-invalid" id="name"></strong>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group input-group-outline mt-3" [ngClass]="{ 'is-filled': email }">
								<label class="form-label">Code</label>
								<input value="{{$detail->code}}" name="code" type="text" class="form-control view-only" >
								<strong class="text-danger is-invalid" id="code"></strong>
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
<x-loader-ajax-component></x-loader-ajax-component>

@endsection

@push('style')
@endpush

@push('js')
<script src="{{ asset('assets') }}/js/admin/state.js"></script>
<script>
	var state_datatable_url = "{{ route('admin.state.table') }}";
	var state_status_change_url = "{{ route('admin.state.status.change') }}";

</script>

@endpush