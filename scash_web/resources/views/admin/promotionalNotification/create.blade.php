@extends('layout/main')

@section('title', 'Promotional Notification Create')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Promotional Notification Create</h1>
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
                <h3>Promotional Notification</h3>
                <form id="promotionalNotification-form" method="POST" action="{{route('admin.promotionalNotification.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group input-group-outline mt-3" id="imageInput">
                                <label for="subject" class="form-label">Subject</label>
                                <input class="form-control @error('subject') is-invalid @enderror" name="subject" type="text" value="">
                                <strong class="text-danger is-invalid" id="subject"></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group input-group-outline mt-3" id="imageInput">
                                <label for="text" class="form-label">Text</label>
                                <input class="form-control @error('text') is-invalid @enderror" name="text" type="text" value="">
                                <strong class="text-danger is-invalid" id="text"></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group input-group-outline mt-3" id="imageInput">
                                <label for="date" class="form-label">Date</label>
                                <input class="form-control @error('date') is-invalid @enderror" name="date" type="date" value="">
                                <strong class="text-danger is-invalid" id="date"></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group input-group-outline mt-3" id="imageInput">
                                <label for="date" class="form-label">Time</label>
                                <input class="form-control @error('time') is-invalid @enderror" name="time" type="time" value="">
                                <strong class="text-danger is-invalid" id="time"></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group input-group-outline mt-3" id="imageInput">
                                <label for="state_id" class="form-label">Send To</label>
                                <select name="send_to" class="form-control" id="">
                                    <option value="">Choose Users</option>
                                    <option value="1">Merchants</option>
                                    <option value="2">Users</option>
                                    <option value="3">Merchant With Users</option>
                                    <option value="4">All</option>
                                </select>
                                <strong class="text-danger is-invalid" id="send_to"></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group input-group-outline mt-3" id="imageInput">
                                <label for="zip_code" class="form-label">Zip Code</label>
                                <select class="form-control select2" name="zip_code[]" id="business_zip_code" multiple="multiple"></select>
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@endpush

@push('js')
<script src="{{ asset('assets') }}/js/admin/promotionalNotification.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    var promotionalNotification_datatable_url = "{{ route('admin.promotionalNotification.table') }}";
    var promotionalNotification_status_change_url = "{{ route('admin.promotionalNotification.status.change') }}";

    $(".select2").select2({
        tags: true,
        tokenSeparators: [',', ' ']
    });

</script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key={{env('GOOGLE_MAPS_API_KEY')}}"></script>
<script src="{{ asset('assets') }}/js/auth/address-google-location.js"></script>

@endpush