@extends('layouts.template') 
  
 @section('content') 
   <div class="card"> 
       <div class="card-header"> 
         <h3 class="card-title">Daftar User</h3> 
         <div class="card-tools"> 
          <button onclick="modalAction('{{ url('/user/import') }}')" class="btn btn-info">Import User</button>
          <a href="{{ url('/user/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export User(Excel)</a> 
          <a href="{{ url('/user/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export User(PDF)</a>
          <button onclick="modalAction('{{ url('user/create_ajax') }}')" class="btn btn-success">Tambah Data Ajax</button>
         </div> 
       </div> 
       <div class="card-body">
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
      <div class="row mb-3">
        <div class="col-md-6">
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Filter:</label>
          <div class="col-sm-9">
          <select class="form-control" id="level_id" name="level_id">
            <option value="">- Semua -</option>
            @foreach($level as $item)
          <option value="{{ $item->level_id }}">{{ $item->level_nama }}</option>
        @endforeach
          </select>
          <small class="form-text text-muted">Level Pengguna</small>
          </div>
        </div>
        </div>
      </div>
        <table class="table table-bordered table-striped table-hover table-sm" id="table_user">
           <thead> 
             <tr>
              <th style="text-align: center;">No</th>
              <th style="text-align: center;">ID Pengguna</th>
              <th style="text-align: center;">Username</th>
              <th style="text-align: center;">Nama</th>
              <th style="text-align: center;">Level Pengguna</th>
              <th style="text-align: center;">Aksi</th>
            </tr> 
           </thead> 
       </table> 
     </div> 
   </div> 
   <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
   <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
   @endsection
  

 @push('js') 
   <script> 
   function modalAction(url = '') {
         $('#myModal').load(url, function () {
             $('#myModal').modal('show');
         });
     }
  
    var dataUser;
     $(document).ready(function() { 
       dataUser = $('#table_user').DataTable({ 
          processing: true,
           serverSide: true,      
           ajax: { 
               url: "{{ url('user/list') }}", 
               dataType: "json", 
               type: "POST",
                data: function (d) {
                d.level_id = $('#level_id').val();
               }
           }, 
           columns: [ 
             { 
   // nomor urut dari laravel datatable addIndexColumn() 
            data: "DT_RowIndex",             
               className: "text-center", 
               width: "4%",
               orderable: false, 
               searchable: false     
             },{ 
                data: "user_id",
                className: "text-center",
                width: "10%",
                orderable: true,
                searchable: false
                 },
                {
               data: "username",                
               className: "", 
               width:"20%",
               // orderable: true, jika ingin kolom ini bisa diurutkan  
               orderable: true,     
               // searchable: true, jika ingin kolom ini bisa dicari 
               searchable: true     
             },{ 
               data: "nama",                
               className: "", 
               width: "30%",
               orderable: true,     
               searchable: true     
             },{ 
               // mengambil data level hasil dari ORM berelasi 
               data: "level.level_nama",                
               className: "", 
               width: "20%",
               orderable: true,     
               searchable: true     
             },{ 
               data: "aksi",                
               className: "text-center", 
               orderable: false,     
               searchable: false     
             } 
           ] 
       });

       $('#level_id').on('change', function () {
       dataUser.ajax.reload();
     });

     }); 
   </script> 
 @endpush  