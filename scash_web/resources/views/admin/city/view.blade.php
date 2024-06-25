@extends('layout/main')

@section('title', 'City')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">City</h1>
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
                <h3>City</h3>
                <form id="city-form" method="POST" action="{{route('admin.city.store')}}" enctype="multipart/form-data">
					@csrf
                    <input value="{{$detail->id}}" name="id" type="hidden" >
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group input-group-outline mt-3" id="imageInput">
                                <label for="name" class="form-label">Name</label>
                                <input class="form-control @error('name') is-invalid @enderror" name="name" type="text" value="{{$detail->name}}" >
                                <strong class="text-danger is-invalid" id="name"></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group input-group-outline mt-3" id="imageInput">
                                <label for="state_id" class="form-label">Code</label>
                                {!! Form::select('state_id', $stateModel, $detail->state_id, ['class' => 'form-control']) !!}
                                <strong class="text-danger is-invalid" id="state_id"></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group input-group-outline mt-3" id="imageInput">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input class="form-control @error('latitude') is-invalid @enderror" name="latitude" type="text" value="{{$detail->latitude}}">
                                <strong class="text-danger is-invalid" id="latitude"></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group input-group-outline mt-3" id="imageInput">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input class="form-control @error('longitude') is-invalid @enderror" name="longitude" type="text" value="{{$detail->longitude}}" >
                                <strong class="text-danger is-invalid" id="longitude"></strong>
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
<script src="{{ asset('assets') }}/js/admin/city.js"></script>
<script>
	var city_datatable_url = "{{ route('admin.city.table') }}";
	var city_status_change_url = "{{ route('admin.city.status.change') }}";

</script>

@endpush