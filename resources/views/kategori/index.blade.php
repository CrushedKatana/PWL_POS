@extends('layouts.template')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Kategori</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/kategori/import') }}')" class="btn btn-info">Import Kategori</button>
                <a href="{{ url('/kategori/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Kategori(Excel)</a>
                <a href="{{ url('/kategori/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Kategori (PDF)</a>
                <button onclick="modalAction('{{ url('/kategori/create_ajax') }}')" class="btn btn-success">Tambah Data (Ajax)</button>  
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="row mx-3 mt-2">
            <div class="col-md-12">
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Filter:</label>
                    <div class="col-3">
                        <select class="form-control" id="kategori_kode" name="kategori_kode">
                            <option value="">- Semua -</option>
                            @foreach ($kategori as $item)
                                <option value="{{ $item->kategori_kode }}">{{ $item->kategori_kode }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Kode Kategori</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-sm table-striped table-hover" id="table_kategori">                <thead>
                <thead>    
                <tr>
                    <th style="text-align: center;">No</th>
                    <th style="text-align: center;">Id Kategori</th>
                    <th style="text-align: center;">Kode Kategori</th>
                    <th style="text-align: center;">Nama Kategori</th>
                    <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="75%"></div>
@endsection

@push('js')

<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    var dataKategori;
    $(document).ready(function () {
        dataKategori = $('#table_kategori').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('kategori/list') }}",
                dataType: "json",
                type: "POST",
            },
            columns: [
                {
                    data: "DT_RowIndex",
                    className: "text-center",
                    width: "4%",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "kategori_id",
                    className: "text-center",
                    width: "10%",
                    orderable: true,
                    searchable: false
                },
                {
                    data: "kategori_kode",
                    className: "text-center",
                    width: "15%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "kategori_nama",
                    className: "",
                    width: "40%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "aksi",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                }]
            });

            $('#kategori_kode').on('change', function () {
                dataKategori.ajax.reload();
            });
        });
    </script>
@endpush