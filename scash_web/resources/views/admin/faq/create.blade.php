@extends('layout/main')

@section('title', 'Faq Create')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Faq Create</h1>
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
                <h3>Faq</h3>
                <form id="faq-form" method="POST" action="{{route('admin.faq.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group input-group-outline mt-3" id="imageInput">
                                <label for="name" class="form-label">Title</label>
                                <input class="form-control" name="title" type="text" value="">
                                <strong class="text-danger is-invalid" id="title"></strong>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group input-group-outline mt-3" id="imageInput">
                                <label for="latitude" class="form-label">Description</label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" style="height:100px;" name="description" rows="5"></textarea>
                                <strong class="text-danger is-invalid" id="description"></strong>
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
<script src="{{ asset('assets') }}/js/admin/faq.js"></script>
<script>
    var faq_datatable_url = "{{ route('admin.faq.table') }}";
    var faq_status_change_url = "{{ route('admin.faq.status.change') }}";
</script>

@endpush