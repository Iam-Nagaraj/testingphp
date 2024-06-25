@extends('layout/main')

@section('title', 'User Details')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">User Details</h1>
        <!-- <a
                href="#"
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"
                ><i class="fas fa-download fa-sm text-white-50"></i> Generate
                Report</a
              > -->
    </div>

    <!-- Content Row -->
    <div class="form_card card shadow mb-4 p-5">
        <div class="row">
            <div class="col-md-12">
                <h3>User Details</h3>
                <form>
                    <div class="row">
                        <div class="col">
                            <label>Image</label>
                            <img src="{{$detail->image_url}}">
                        </div>

                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Name</label>
                            <input type="text" class="form-control" value="{{$detail->name}}" name="name" >
                        </div>
                        <div class="col">
                            <label>Email</label>
                            <input type="email" class="form-control" value="{{$detail->email}}" name="email">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Phone Number </label>
                            <input type="text" value="{{$detail->country_code}}{{$detail->phone_number}}" class="form-control" >
                        </div>
                        <div class="col">
                            <label>Address Line 1</label>
                            <input type="text" value="{{$detail->address->address??'N/A'}}" name="address"  class="form-control" placeholder="N/A">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Address Line 2</label>
                            <input type="text" class="form-control" value="{{$detail->address->address_2??'N/A'}}" name="address_2" placeholder="N/A">
                        </div>
                        <div class="col">
                            <label>State</label>
                            <input type="text" class="form-control" value="{{$detail->address->state??'N/A'}}" name="state"  placeholder="State">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>City</label>
                            <input type="text" class="form-control" value="{{$detail->address->city??'N/A'}}" name="city" placeholder="N/A">
                        </div>
                        <div class="col">
                            <label>Postal Code</label>
                            <input type="text" class="form-control" value="{{$detail->address->postal_code??'N/A'}}" name="postal_code" placeholder="N/A">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label>Status</label>
                            <label class="switch">
                                <input type="checkbox" class="status-action" onchange="changeStatus({{$detail->id}})"
                                {{$detail->status === 1?'checked':''}} data-id="{{$detail->id}}" data-status="{{$detail->status}}">
                                <span class="slider round"></span>
                            </label>
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
<script>
    function changeStatus(id)
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{route('admin.user.changeStatus')}}",
            data: { user_id: id },
            dataType: "json",
            success: function(response) {

            },
            error: function(response) {                  

            }
        });
    }

</script>
@endpush