@extends('layout/main')

@section('title', 'Business Type')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Business Type</h1>
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
                    <input value="{{$detail->id}}" name="id" type="hidden" >
                    <div class="row">
                        <div class="col-md-6">
                            <label>Name</label>
                            <input value="{{$detail->name}}" name="name" type="name" class="form-control view-only" >
								<strong class="text-danger is-invalid" id="name"></strong>
                        </div>
                        <div class="col-md-6">
                            <label>Tax ID</label>
                            <select name="type" class="form-control">
                                <option value="">Select</option>
                                <option value="1" {{$detail->type == '1' ? 'selected' : ''}} > SSN</option>
                                <option value="2" {{$detail->type == '2' ? 'selected' : ''}} > EIN</option>
                            </select>
                            <strong class="text-danger is-invalid" id="type"></strong>
                        </div>
                        <div class="col-md-6">
                            <label>Dwolla Key</label>
                            <select name="dwolla_key" class="form-control">
                                <option value="">Select</option>
                                <option value="corporation" {{$detail->dwolla_key == 'corporation' ? 'selected' : ''}} >Corporation</option>
                                <option value="llc" {{$detail->dwolla_key == 'llc' ? 'selected' : ''}} >Llc</option>
                                <option value="partnership" {{$detail->dwolla_key == 'partnership' ? 'selected' : ''}} >Partnership</option>
                                <option value="soleProprietorship" {{$detail->dwolla_key == 'soleProprietorship' ? 'selected' : ''}} >Sole Proprietorship</option>
                            </select>
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
<script src="{{ asset('assets') }}/js/admin/business-type.js"></script>
<script>
	var businessType_datatable_url = "{{ route('admin.businessType.table') }}";
	var businessType_status_change_url = "{{ route('admin.businessType.status.change') }}";

</script>

@endpush