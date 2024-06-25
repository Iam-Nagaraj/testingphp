@extends('layout/main')

@section('title', 'Notification')

@section('content')

<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">Notification</h1>
  </div>

  <!-- Content Row -->
  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered dataTable-table" id="notification-table" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Notification</th>
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

@endsection

@push('style')
<link href="{{ asset('/asset/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@push('js')
<script>
	var notification_datatable_url = "{{ route('merchant.notification.table') }}";
</script>
<script src="{{ asset('/asset/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/asset/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets') }}/js/merchant/notification.js"></script>
  

@endpush
