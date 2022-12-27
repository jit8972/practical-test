@extends('layouts.app')

@section('content')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="styleshet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="styleshet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <style>
        .error{
            color: #dc3545;
        }
    </style>
@endpush
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Category list <a href="javascript:;" class="btn btn-primary add_category" style="float: right;"><i class="fa fa-plus"></i>Add</a></div>
                <div class="card-body">
                    <table class="table table-striped" id="category_tbl">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Category name</th>
                                <th>Parent Category name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <h5>Category List (Tree view)</h5>
            <ul id="tree"></ul>
        </div>
    </div>
    
    <div class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close closemodal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="category_form" method="post" action="{{route('storecategory')}}">
                    <div class="form-group">
                        <input type="hidden" name="id" id="hidden_id">
                        <label>Name<span style="color:red">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter name">
                    </div>
                    <div class="form-group">
                        <label>Parent category</label>
                        <select name="parent_id" id="parent_id" class="form-control">
                            <option value="">Select</option>
                            @if(!empty($categories))
                                @foreach($categories as $row)
                                <option value="{{$row->id}}">{{$row->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary pull-right"><span class="spinner"></span>Save</button>
                        <button type="button" class="btn btn-secondary pull-right closemodal" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    
</div>



@push('script')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
$(document).ready(function () {
    //Load default tree
    loadtree();
    var table = $("#category_tbl").DataTable({
        "responsive": true,
        "autoWidth": false,
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: {
            'url': "{{ route('categorylist') }}",
            'type': 'POST',
            'data': function (d) {
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            {
                data: 'DT_RowIndex',
                "orderable": false
            },
            {
                data: 'name'
            },
            {
                data: 'parent_name'
            },
            {
                data: 'action',
                orderable: false
            }
        ]
    });
    
    $(document).on('click','.add_category',function(){
        $('.modal-title').text('Add Category');
        $('#category_form')[0].reset();
        $('.modal').modal('show');
    });
    
    $('#category_form').validate({
        ignore: [],
        rules: {
            name: {
                required: true,
                maxlength: 50
            }
          },
          messages: {
            name: {
                required: "Please enter name",
                maxlength: "The name must be less than 50 characters"
            }
        },
        errorPlacement: function(error, element) {
            var errordiv = $(element).closest(".form-group");
            error.appendTo(errordiv);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass("is-invalid").removeClass("is-valid");
            $(element).css({ "background-image": "none" });
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).addClass("is-valid").removeClass("is-invalid");
            $(element).css({ "background-image": "none" });
        },
        submitHandler: function () {
            var formData = new FormData($("#category_form")[0]);
            $.ajax({
                url: $('#category_form').attr('action'),
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    $('.spinner').html('<i class="fa fa-spinner fa-spin"></i>');
                },
                success: function (res) {
                    if (res.status == 400) {
                        $('.spinner').html('');
                        toastr.error(res.msg, 'Oh No!');
                    }
                    if (res.status == 200) {
                        $('.spinner').html('');
                        $('.modal').modal('hide');
                        $('#parent_id').append('<option value="'+res.result.data.id+'">'+res.result.data.name+'</option>');
                        $('#category_tbl').DataTable().ajax.reload();
                        loadtree();
                        toastr.success(res.msg, 'Success');
                    }
                }
            });
        }
    });
    
    $(document).on('click','.update_record',function(){
        var id = $(this).attr('data-id');
        $('.modal-title').text('Edit Category');
        
        $.ajax({
            url: "{{ route('categorydetail') }}",
            type: 'POST',
            data: {
                id:id
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function () {

            },
            success: function (res) {
                if (res.status == '200') {
                    $('#name').val(res.result.data.name);
                    $('#parent_id').val(res.result.data.parent_id);
                    $('#hidden_id').val(id);
                    $('.modal').modal('show');
                }else{
                    toastr.error(res.msg, 'Oh No!');
                }
            }
        });
        
    });
    
    $(document).on('click','.delete_record',function(){
        
        if(confirm("Are you sure you want to delete this?")){
            var id = $(this).attr('data-id');
            $.ajax({
                url: "{{ route('categorydelete') }}",
                type: 'POST',
                data: {
                    id:id
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                beforeSend: function () {

                },
                success: function (res) {
                    if (res.status == '200') {
                        $('#category_tbl').DataTable().ajax.reload();
                        loadtree();
                        toastr.success(res.msg, 'Success!');
                    }else{
                        toastr.error(res.msg, 'Oh No!');
                    }
                }
            });
        }
        else{
            return false;
        }
    });
    
    $('.closemodal').click(function(){
        $('.modal').modal('hide');
    });
    
});

function loadtree(){
    $.ajax({
        url: "{{ route('loadtree') }}",
        type: 'POST',
        data: {},
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        beforeSend: function () {

        },
        success: function (res) {
            if (res.status == '200') {
                $('#tree').html(res.result.html);
            }else{
                $('#tree').html('');
            }
        }
    });
}

</script>
@endpush
@endsection
