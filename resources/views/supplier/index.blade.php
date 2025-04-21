@extends('layouts.template')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Supplier</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/supplier/import') }}')" class="btn btn-info">Import Supplier</button>
                <a href="{{ url('/supplier/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Supplier(Excel)</a>
                <a href="{{ url('/supplier/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Supplier (PDF)</a>
                <button onclick="modalAction('{{ url('supplier/create_ajax') }}')" class="btn btn-success">Tambah Data Ajax</button>
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
                        <select class="form-control" id="supplier_kode" name="supplier_kode">
                            <option value="">- Semua -</option>
                            @foreach ($supplier as $item)
                                <option value="{{ $item->supplier_kode }}">{{ $item->supplier_kode }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Kode Supplier</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover table-sm" id="table_supplier">
                <thead>
                    <tr>
                        <th style="text-align: center;">No</th>
                          <th style="text-align: center;">ID Supplier</th>
                          <th style="text-align: center;">Kode Supplier</th>
                          <th style="text-align: center;">Nama Supplier</th>
                          <th style="text-align: center;">Alamat Supplier</th>
                          <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
         data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    var tableSupplier;
    $(document).ready(function () {
        tableSupplier = $('#table_supplier').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('supplier/list') }}",
                type: "POST",
                dataType: "json",
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
                    data: "supplier_id",
                    className: "text-center",
                    width: "10%",
                    orderable: true,
                    searchable: false
                },
                {
                    data: "supplier_kode",
                    className: "text",
                    width: "10%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "supplier_nama",
                    className: "",
                    width: "20%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "supplier_alamat",
                    className: "",
                    width: "30%",
                    orderable: true,
                    searchable: true
                }, {
                    data: "aksi",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                }]
            });

            $('#supplier_kode').on('change', function () {
                dataSupplier.ajax.reload();
            });
        });
    </script>
@endpush