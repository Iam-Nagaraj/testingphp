@extends('layout/main')

@section('title', 'Business Subcategory')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Business Subcategory</h1>
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
                <h3>Business Subcategory</h3>
                <form id="businessSubCategory-form" method="POST" action="{{route('admin.businessSubCategory.store')}}" enctype="multipart/form-data">
					@csrf
                    <input value="{{$detail->id}}" name="id" type="hidden" >
                    <div class="row">
                        <div class="col">
                            <label>Name</label>
                            <input value="{{$detail->name}}" name="name" type="name" class="form-control view-only" >
								<strong class="text-danger is-invalid" id="name"></strong>
                        </div>

                        <div class="col-md-6">
                            <label>Dwolla Key</label>
                            <input class="form-control" name="dwolla_key" type="text" value="{{$detail->dwolla_key}}">
                            <strong class="text-danger is-invalid" id="dwolla_key"></strong>
                        </div>

                        <div class="col-md-6">
                            <label>Business Category</label>
                            <select name="business_category" class="form-control" id="">
                                <option value="">Select Business Category</option>
                                @foreach($businessCategory as $category)
                                <option value="{{$category->id}}" {{($category->id == $detail->parent_id) ? 'selected' : ''}} >{{$category->name}}</option>
                                @endforeach
                            </select>
                            <strong class="text-danger is-invalid" id="business_category"></strong>
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
<script src="{{ asset('assets') }}/js/admin/business-subcategory.js"></script>
<script>
	var businessSubCategory_datatable_url = "{{ route('admin.businessSubCategory.table') }}";
	var businessSubCategory_status_change_url = "{{ route('admin.businessSubCategory.status.change') }}";

</script>

@endpush