<?php
 
 namespace App\Http\Controllers;
 
 use Illuminate\Http\Request;
 use App\Models\SupplierModel;
 use Yajra\DataTables\Facades\DataTables;
 use Illuminate\Support\Facades\Validator;
 use PhpOffice\PhpSpreadsheet\IOFactory;
 use Barryvdh\DomPDF\Facade\Pdf;
 
 class SupplierController extends Controller
 {
     public function index()
     {
         $breadcrumb = (object) [
             'title' => 'Daftar Supplier',
             'list' => ['Home', 'Supplier']
         ];
 
         $page = (object) [
             'title' => 'Daftar supplier yang terdaftar dalam sistem'
         ];
 
         $activeMenu = 'supplier';
         $supplier = SupplierModel::all(); // Ambil semua data supplier untuk dropdown
 
         return view('supplier.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'supplier' => $supplier, 'activeMenu' => $activeMenu]);
     }
 
     public function list(Request $request)
     {
         $suppliers = SupplierModel::select('supplier_id', 'supplier_kode', 'supplier_nama', 'supplier_alamat');
 
         // Filter berdasarkan supplier_kode
         if ($request->supplier_kode) {
             $suppliers->where('supplier_kode', $request->supplier_kode);
         }
 
         return DataTables::of($suppliers)
             ->addIndexColumn()
             ->addColumn('aksi', function ($supplier) {
                //  $btn = '<a href="' . url('/supplier/' . $supplier->supplier_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                //  $btn .= '<a href="' . url('/supplier/' . $supplier->supplier_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                //  $btn .= '<form class="d-inline-block" method="POST" action="' . url('/supplier/' . $supplier->supplier_id) . '">' .
                //      csrf_field() . method_field('DELETE') .
                //      '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                $btn = '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                 return $btn;
             })
             ->rawColumns(['aksi'])
             ->make(true);
     }
 
     public function create()
     {
         $breadcrumb = (object) [
             'title' => 'Tambah Supplier',
             'list' => ['Home', 'Supplier', 'Tambah']
         ];
 
         $page = (object) [
             'title' => 'Tambah supplier baru'
         ];
 
         $activeMenu = 'supplier';
 
         return view('supplier.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
     }
 
     public function store(Request $request)
     {
         $request->validate([
             'supplier_kode' => 'required|string|max:10|unique:m_supplier,supplier_kode',
             'supplier_nama' => 'required|string|max:100',
             'supplier_alamat' => 'required|string|max:255',
         ]);
 
         SupplierModel::create([
             'supplier_kode' => $request->supplier_kode,
             'supplier_nama' => $request->supplier_nama,
             'supplier_alamat' => $request->supplier_alamat,
         ]);
 
         return redirect('/supplier')->with('success', 'Data supplier berhasil disimpan');
     }
 
     public function show(string $id)
     {
         $supplier = SupplierModel::find($id);
 
         $breadcrumb = (object) [
             'title' => 'Detail Supplier',
             'list' => ['Home', 'Supplier', 'Detail']
         ];
 
         $page = (object) [
             'title' => 'Detail supplier'
         ];
 
         $activeMenu = 'supplier';
 
         return view('supplier.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'supplier' => $supplier, 'activeMenu' => $activeMenu]);
     }
 
     public function edit(string $id)
     {
         $supplier = SupplierModel::find($id);
 
         $breadcrumb = (object) [
             'title' => 'Edit Supplier',
             'list' => ['Home', 'Supplier', 'Edit']
         ];
 
         $page = (object) [
             'title' => 'Edit supplier'
         ];
 
         $activeMenu = 'supplier';
 
         return view('supplier.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'supplier' => $supplier, 'activeMenu' => $activeMenu]);
     }
 
     public function update(Request $request, string $id)
     {
         $request->validate([
             'supplier_kode' => 'required|string|max:10|unique:m_supplier,supplier_kode,' . $id . ',supplier_id',
             'supplier_nama' => 'required|string|max:100',
             'supplier_alamat' => 'required|string|max:255',
         ]);
 
         SupplierModel::find($id)->update([
             'supplier_kode' => $request->supplier_kode,
             'supplier_nama' => $request->supplier_nama,
             'supplier_alamat' => $request->supplier_alamat,
         ]);
 
         return redirect('/supplier')->with('success', 'Data supplier berhasil dirubah');
     }
 
     public function destroy(string $id)
     {
         $check = SupplierModel::find($id);
         if (!$check) {
             return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
         }
 
         try {
             SupplierModel::destroy($id);
             return redirect('/supplier')->with('success', 'Data supplier berhasil dihapus');
         } catch (\Illuminate\Database\QueryException $e) {
             return redirect('/supplier')->with('error', 'Data supplier gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
         }
     }

     // tugas jb 6
     public function create_ajax()
     {
         return view('supplier.create_ajax');
     }
 
     public function store_ajax(Request $request)
     {
         // Mengecek apakah request berupa ajax
         if ($request->ajax() || $request->wantsJson()) {
             $rules = [
                 'supplier_kode' => 'required|string|min:4|max:10|unique:m_supplier,supplier_kode',
                 'supplier_nama' => 'required|string|max:100',
                 'supplier_alamat' => 'required|string',
             ];
 
             // use Illuminate\Support\Facades\Validator;
             $validator = Validator::make($request->all(), $rules);
 
             if ($validator->fails()) {
                 return response()->json([
                     'status' => false, // response status, false: error/gagal, true: berhasil
                     'message' => 'Validasi Gagal',
                     'msgField' => $validator->errors() // Validasi pesan error 
                 ]);
             }
 
             SupplierModel::create($request->all());
 
             return response()->json([
                 'status' => true,
                 'message' => 'Data supplier berhasil disimpan'
             ]);
         }
         return redirect('/');
     }
 
     public function show_ajax(string $id)
     {
         $supplier = SupplierModel::find($id);
 
         return view('supplier.show_ajax', ['supplier' => $supplier]);
     }
 
     public function edit_ajax(string $id)
     {
         $supplier = SupplierModel::find($id);
 
         return view('supplier.edit_ajax', ['supplier' => $supplier]);
     }
 
     public function update_ajax(Request $request, $id)
     {
         // Mengecek apakah request berasal dari ajax
         if ($request->ajax() || $request->wantsJson()) {
             $rules = [
                 'supplier_kode' => 'required|string|min:4|max:10|unique:m_supplier,supplier_kode,' . $id . ',supplier_id',
                 'supplier_nama' => 'required|string|max:100',
                 'supplier_alamat' => 'required|string',
            ];
 
             // use Illuminate\Support\Facades\Validator;
             $validator = Validator::make($request->all(), $rules);
 
             if ($validator->fails()) {
                 return response()->json([
                     'status' => false, 
                     'message' => 'Validasi gagal. Periksa kembali data yang diinput.',
                     'msgField' => $validator->errors() // Menampilkan error pada field yang salah
                 ]);
             }
 
             try {
                 $supplier = SupplierModel::findOrFail($id); // Memastikan data ditemukan
                 $supplier->update($request->all());
 
                 return response()->json([
                     'status' => true,
                     'message' => 'Data berhasil diperbarui'
                 ]);
             } catch (\Illuminate\Database\QueryException $e) {
                 return response()->json([
                     'status' => false,
                     'message' => 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.',
                     'error' => $e->getMessage() // Tampilkan pesan error 
                 ]);
             }
         }
 
         return redirect('/');
     }
 
 
     public function confirm_ajax(string $id)
     {
         $supplier = SupplierModel::find($id);
 
         return view('supplier.confirm_ajax', ['supplier' => $supplier]);
     }
 
     public function delete_ajax(Request $request, $id)
     {
         // cek apakah request dari ajax
         if ($request->ajax() || $request->wantsJson()) {
             $supplier = SupplierModel::find($id);
             if ($supplier) {
                 try {
                     $supplier->delete();
                     return response()->json([
                         'status' => true,
                         'message' => 'Data berhasil dihapus'
                     ]);
                 } catch (\Illuminate\Database\QueryException $e) {
                     return response()->json([
                         'status' => false,
                         'message' => 'Data tidak bisa dihapus'
                     ]);
                 }
             } else {
                 return response()->json([
                     'status' => false,
                     'message' => 'Data tidak ditemukan'
                 ]);
             }
         }
         return redirect('/');
     }

      // jb 8 tugas 1
      public function import()
      {
          return view('supplier.import');
      }
  
      public function import_ajax(Request $request)
      {
          if ($request->ajax() || $request->wantsJson()) {
              $rules = [
                  // validasi file harus xls atau xlsx, max 1MB
                  'file_supplier' => ['required', 'mimes:xlsx', 'max:1024']
              ];
              $validator = Validator::make($request->all(), $rules);
              if ($validator->fails()) {
                  return response()->json([
                      'status' => false,
                      'message' => 'Validasi Gagal',
                      'msgField' => $validator->errors()
                  ]);
              }
              $file = $request->file('file_supplier'); // ambil file dari request
              $reader = IOFactory::createReader('Xlsx'); // load reader file excel
              $reader->setReadDataOnly(true); // hanya membaca data
              $spreadsheet = $reader->load($file->getRealPath()); // load file excel
              $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif
              $data = $sheet->toArray(null, false, true, true); // ambil data excel
              $insert = [];
              if (count($data) > 1) { // jika data lebih dari 1 baris
                  foreach ($data as $baris => $value) {
                      if ($baris > 1) { // baris ke 1 adalah header, maka lewati
                          $insert[] = [
                              'supplier_kode' => $value['A'],
                              'supplier_nama' => $value['B'],
                              'supplier_alamat' => $value['C'],
                              'created_at' => now(),
                          ];
                      }
                  }
                  if (count($insert) > 0) {
                      // insert data ke database, jika data sudah ada, maka diabaikan
                      SupplierModel::insertOrIgnore($insert);
                  }
                  return response()->json([
                      'status' => true,
                      'message' => 'Data berhasil diimport'
                  ]);
              } else {
                  return response()->json([
                      'status' => false,
                      'message' => 'Tidak ada data yang diimport'
                  ]);
              }
          }
          return redirect('/');
      }

      // jb 8 tugas prak 2 no 2
      public function export_excel()
     {
         // ambil data supplier yang akan di export
         $supplier = SupplierModel::select('supplier_id','supplier_kode', 'supplier_nama', 'supplier_alamat')
             ->orderBy('supplier_id')
             ->get();
 
         // load library excel
         $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
         $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif
 
         $sheet->setCellValue('A1', 'No');
         $sheet->setCellValue('B1', 'Id Supplier');
         $sheet->setCellValue('C1', 'Kode Supplier');
         $sheet->setCellValue('D1', 'Nama Supplier');
         $sheet->setCellValue('E1', 'Alamat Supplier');
 
         $sheet->getStyle('A1:E1')->getFont()->setBold(true); // bold header
 
         $no = 1; // nomor data dimulai dari 1
         $baris = 2; // baris data dimulai dari baris ke 2
         foreach ($supplier as $key => $value) {
             $sheet->setCellValue('A' . $baris, $no);
             $sheet->setCellValue('B' . $baris, $value->supplier_id);
             $sheet->setCellValue('C' . $baris, $value->supplier_kode);
             $sheet->setCellValue('D' . $baris, $value->supplier_nama);
             $sheet->setCellValue('E' . $baris, $value->supplier_alamat);
             $baris++;
             $no++;
         }
 
         foreach (range('A', 'E') as $columnID) {
             $sheet->getColumnDimension($columnID)->setAutoSize(true); // set auto size untuk kolom
         }
 
         $sheet->setTitle('Data Supplier'); // set title sheet
 
         $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
         $filename = 'Data Supplier_' . date('Y-m-d H:i:s') . '.xlsx';
 
         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
         header('Content-Disposition: attachment;filename="' . $filename . '"');
         header('Cache-Control: max-age=0');
         header('Cache-Control: max-age=1');
         header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
         header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
         header('Cache-Control: cache, must-revalidate');
         header('Pragma: public');
 
         $writer->save('php://output');
         exit;
     }

     public function export_pdf()
     {
         $supplier = SupplierModel::select('supplier_id', 'supplier_kode', 'supplier_nama', 'supplier_alamat')
             ->orderBy('supplier_id')
             ->get();
         $pdf = Pdf::loadView('supplier.export_pdf', ['supplier' => $supplier]);
         $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
         $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
         $pdf->render(); // Render the PDF as HTML - uncomment if you want to see the HTML outputw
 
         return $pdf->stream('Data Supplier' . date('Y-m-d H:i:s') . '.pdf');
     }
 }