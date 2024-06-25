@extends('layout/main')

@section('title', 'Merchant List')

@section('content')

<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">Merchant List</h1>

  </div>

  <!-- Content Row -->
  <div class="card merchant_card shadow mb-4">
    <div class="card-header user_list py-3">
      <h4 class="my-4 text-gray-600">Merchant List</h4>
      <div class="row border-bottom mb-4">
        <div class="col-md-9">
          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active merchant-list-tab" id="home-tab" id="pills-merchant-list-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true" data-type="1">
                Active
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link merchant-list-tab" id="pending-tab" id="pills-merchant-list-tab" data-toggle="tab" data-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false" data-type="0">
                Pending
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link merchant-list-tab" id="kYC-tab" id="pills-merchant-list-tab" data-toggle="tab" data-target="#kYC" type="button" role="tab" aria-controls="kYC" aria-selected="false" data-type="3">
                kYC Verification
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link merchant-list-tab" id="profile-tab" id="pills-merchant-list-tab" data-toggle="tab" data-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false" data-type="2">
                Inactive
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <select class="form-control" name="business_type" id="business_type" required="required" aria-label="Default select example">
                <option value="">Business Type</option>
                @foreach($BusinessType as $singletype)
                <option value="{{$singletype->id}}">{{$singletype->name}}</option>
                @endforeach
              </select>
            </li>
            <li class="nav-item" role="presentation">
              <select class="form-control" name="business_category" id="business_category" required="required" aria-label="Default select example">
                <option value="">Business Category</option>
                @foreach($BusinessCategory as $singlecategory)
                <option value="{{$singlecategory->id}}">{{$singlecategory->name}}</option>
                @endforeach
              </select>
            </li>
          </ul>
        </div>
        <div class="col-md-3 text-right">
          <!-- <a href="" class="add_btn">+ Add New</a> -->
        </div>
      </div>

      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="pills-merchant-list" role="tabpanel" aria-labelledby="home-tab">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-bordered dataTable-table" id="merchants-table" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <!-- <th>Image</th> -->
                    <th>Id</th>
                    <th>Business Name</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>City</th>
                    <th>Status</th>
                    <th>Action</th>
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

<script src="{{ asset('assets') }}/js/admin/merchant.js"></script>
<script>
  var merchant_datatable_url = "{{ route('admin.merchant.table') }}";
  var merchant_status_change_url = "{{ route('admin.merchant.status.change') }}";
</script>
@endpush