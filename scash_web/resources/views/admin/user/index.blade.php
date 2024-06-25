@extends('layout/main')

@section('title', 'User List')

@section('content')

<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">User List</h1>
  </div>

  <!-- Content Row -->
  <div class="card shadow mb-4">
    <div class="card-header user_list py-3">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active user-list-tab" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true" data-type="1">
            Active
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link user-list-tab" id="profile-tab" data-toggle="tab" data-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false" data-type="0">
            Inactive
          </button>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-bordered dataTable-table" id="users-table" width="100%" cellspacing="0">
                <thead>
                  <tr>
                  <!-- <th class="text-uppercase text-sm font-weight-bolder">
                    IMAGE
                  </th> -->
                  <th class="text-uppercase text-sm font-weight-bolder ps-2">
                    ID</th>
                  <th class="text-uppercase text-sm font-weight-bolder ps-2">
                    NAME</th>
                  <th class="text-uppercase text-sm font-weight-bolder ps-2">
                    PHONE NUMBER</th>
                  <th class="text-uppercase text-sm font-weight-bolder ps-2">
                    EMAIL</th>
                  <th class="text-uppercase text-sm font-weight-bolder ps-2">
                    City</th>
                  <th class="text-uppercase text-sm font-weight-bolder ps-2">
                    STATUS</th>
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
<!-- Modal -->
<div class="modal fade" id="successModel" tabindex="-1" aria-labelledby="successModelLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered custom_moddal">
    <div class="modal-content">
        <div class="modal-body">
          <div class="inner_modal text-center">
            <img src="{{asset('/asset/img/payment-success.png')}}">
            <h3>Payment Successfull!</h3>
            <button type="button" class="btn btn-secondary" onclick="window.location.reload();">Done</button>
          </div>
        </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="pauUserModel" tabindex="-1" aria-labelledby="pauUserModelLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered custom_moddal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title fs-5" id="UserNameLabel">Pay to User</h2>
      </div>
      <form id="pauUser-wallet-form" method="POST" action="{{route('admin.bank.payToUser')}}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <input type="number" name="amount" class="form-control amount" placeholder="Enter Amount">
            <strong class="text-danger is-invalid" id="amount"></strong>

            <input type="number" name="pin" class="form-control pin mt-3" placeholder="Enter Pin" maxlength="4"
            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
            <strong class="text-danger is-invalid" id="pin"></strong>
            <input type="hidden" name="user_id" id="destination_user_id">

          </div>
          <strong class="text-danger is-invalid" id="destination_id"></strong>
          <strong class="text-danger is-invalid" id="all_errors"></strong>
          
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Pay</button>
          <button type="button" class="btn btn-secondary" onclick="$('#pauUserModel').modal('hide')">Close</button>
        </div>
      </form>
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

<script src="{{ asset('assets') }}/js/admin/user.js"></script>
<script>
	var user_datatable_url = "{{ route('admin.user.table') }}";
	var user_status_change_url = "{{ route('admin.user.status.change') }}";
</script>

@endpush