<!DOCTYPE html>
 <html>
 <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>Registrasi Pengguna</title>
     <!-- Google Font -->
     <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro">
     <!-- Font Awesome -->
     <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
     <!-- icheck bootstrap -->
     <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
     <!-- SweetAlert2 -->
     <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
     <!-- AdminLTE -->
     <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
 </head>
 <body class="hold-transition login-page">
     <div class="login-box">
         <div class="card card-outline card-success">
             <div class="card-header text-center">
                 <a href="{{ url('/') }}" class="h3"><b>Registrasi Pengguna</b></a>
             </div>
             <div class="card-body">
                 <p class="login-box-msg">Silakan daftar untuk membuat akun</p>
                 <form action="{{ url('register') }}" method="POST" id="form-register">
                     @csrf
                     <div class="input-group mb-3">
                         <input id="username" type="text" name="username" class="form-control" placeholder="Username">
                         <div class="input-group-append">
                             <div class="input-group-text d-flex align-items-center justify-content-center" style="width: 42px;">
                                 <i class="fas fa-user"></i>
                             </div>
                         </div>
                     </div>
 
                     <div class="input-group mb-3">
                         <input id="nama" type="text" name="nama" class="form-control" placeholder="Nama">
                         <div class="input-group-append">
                             <div class="input-group-text d-flex align-items-center justify-content-center" style="width: 42px;">
                                 <i class="fas fa-id-card"></i>
                             </div>
                         </div>
                     </div>
 
                     <div class="input-group mb-3">
                         <input id="password" type="password" name="password" class="form-control" placeholder="Password">
                         <div class="input-group-append">
                             <div class="input-group-text d-flex align-items-center justify-content-center" style="width: 42px;">
                                 <i class="fas fa-lock"></i>
                             </div>
                         </div>
                     </div>
 
                     <div class="row">
                         <div class="col-12 mb-2">
                             <button type="submit" class="btn btn-success btn-block">Daftar</button>
                         </div>
                         <div class="col-12 text-center">
                             <a href="{{ url('login') }}">Sudah punya akun? <strong>Login!</strong></a>
                         </div>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 
     <!-- Scripts -->
     <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
     <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
     <script src="{{ asset('adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
     <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
     <script>
         $.ajaxSetup({
             headers: {
                 'X-CSRF-TOKEN': $('input[name="_token"]').val()
             }
         });
 
         $(document).ready(function () {
             $("#form-register").validate({
                 rules: {
                     username: { required: true, minlength: 4, maxlength: 20 },
                     nama: { required: true, minlength: 3, maxlength: 100 },
                     password: { required: true, minlength: 6, maxlength: 20 },
                 },
                 submitHandler: function (form) {
                     $.ajax({
                         url: form.action,
                         method: form.method,
                         data: $(form).serialize(),
                         success: function (response) {
                             $('.error-text').text('');
                             if (response.status) {
                                 Swal.fire({
                                     icon: 'success',
                                     title: 'Berhasil Registrasi',
                                     text: response.message
                                 }).then(() => {
                                     window.location.href = "{{ url('login') }}";
                                 });
                             } else {
                                 if (response.msgField) {
                                     $.each(response.msgField, function (prefix, val) {
                                         $('#error-' + prefix).text(val[0]);
                                     });
                                 }
                                 Swal.fire({
                                     icon: 'error',
                                     title: 'Gagal Registrasi',
                                     text: response.message || 'Silakan periksa kembali data yang diisi.'
                                 });
                             }
                         },
                         error: function (xhr) {
                             Swal.fire({
                                 icon: 'error',
                                 title: 'Error',
                                 text: 'Terjadi kesalahan di server.'
                             });
                         }
                     });
                     return false;
                 },
                 errorElement: 'span',
                 errorPlacement: function (error, element) {
                     error.addClass('invalid-feedback');
                     element.closest('.input-group').append(error);
                 },
                 highlight: function (element) {
                     $(element).addClass('is-invalid');
                 },
                 unhighlight: function (element) {
                     $(element).removeClass('is-invalid');
                 }
             });
         });
     </script>
 </body>
 </html>