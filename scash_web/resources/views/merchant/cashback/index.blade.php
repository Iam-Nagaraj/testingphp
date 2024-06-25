@extends('layout/main')

@section('title', 'Cash Back List')

@section('content')

<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">Cash Back List</h1>
  </div>

  <!-- Content Row -->
  <div class="card shadow mb-4">
    <div class="card-header user_list py-3">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
        <div class="row border-bottom mb-4 align-items-center">
            <div class="col-md-9">
              <h4 class="my-4 text-gray-600">Cash Back List</h4>
            </div>
            <div class="col-md-3 text-right">
              <a href="{{route('merchant.cashback.create')}}" class="add_btn">+ Add New</a>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-bordered dataTable-table" id="cashback-table" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-sm font-weight-bolder ps-2">
                      Cash Back</th>
                      <th class="text-uppercase text-sm font-weight-bolder ps-2">
                      Percentage</th>
                      <th class="text-uppercase text-sm font-weight-bolder ps-2">
                      Min Amount</th>
                      <th class="text-uppercase text-sm font-weight-bolder ps-2">
                      Max Amount</th>
                      <th class="text-uppercase text-sm font-weight-bolder ps-2">
                      Created By</th>
                    <th class="text-sm">ACTION</th>
                  </tr>
                </thead>

                <tbody>
                  
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('style')

<link href="{{ asset('/asset/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


@endpush

@push('js')

<script src="{{ asset('/asset/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/asset/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Page level custom scripts -->
<script src="{{ asset('/asset/js/demo/datatables-demo.js') }}"></script>

<script src="{{ asset('assets') }}/js/admin/cashback.js"></script>
<script>
	var cashback_datatable_url = "{{ route('merchant.cashback.table') }}";
	var cashback_status_change_url = "{{ route('merchant.cashback.status.change') }}";

</script>
@endpush
