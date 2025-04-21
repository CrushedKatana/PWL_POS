<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class KategoriCotroller extends Controller
{
    public function index()
    {
        // $data = [
        //     'kategori_kode' => 'SNK',
        //     'kategori_nama' => 'Snack/Makanan Ringan',
        //     'created_at' => now()
        // ];
        // DB::table('m_kategori')->insert($data);
        // return 'Insert data baru berhasil';

        // $row = DB::table('m_kategori')->where('kategori_kode', 'SNK')->update(['kategori_nama' => 'Camilan']);
        // return 'Update data berhasil. Jumlah data yang diupdate: ' . $row . ' baris';

        // $row = DB::table('m_kategori')->where('kategori_kode', 'SNK')->delete();
        // return 'Delete data berhasil. Jumlah data yang dihapus: ' . $row . ' baris';
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list' => ['Home', 'Kategori']
        ];

        $page = (object) [
            'title' => 'Daftar kategori yang terdaftar dalam sistem'
        ];

        // return 'Update data berhasil. Jumlah data yang diupdate: ' . $row . ' baris';
        $activeMenu = 'kategori';
        $kategori = KategoriModel::all(); // Ambil semua data kategori untuk dropdown

        return view('kategori.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
     }
 
     public function list(Request $request)
     {
         $kategoris = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama');
 
         // Filter berdasarkan kategori_kode
         if ($request->kategori_kode) {
             $kategoris->where('kategori_kode', $request->kategori_kode);
         }
 
         return DataTables::of($kategoris)
             ->addIndexColumn()
             ->addColumn('aksi', function ($kategori) {
                //  $btn = '<a href="' . url('/kategori/' . $kategori->kategori_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                //  $btn .= '<a href="' . url('/kategori/' . $kategori->kategori_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                //  $btn .= '<form class="d-inline-block" method="POST" action="' . url('/kategori/' . $kategori->kategori_id) . '">' .
                //      csrf_field() . method_field('DELETE') .
                //      '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                // jb 6 tugas
                $btn = '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                 return $btn;
             })
             ->rawColumns(['aksi'])
             ->make(true);
     }
 
        public function create()
        {
            $breadcrumb = (object) [
                'title' => 'Tambah Kategori',
                'list' => ['Home', 'Kategori', 'Tambah']
            ];
    
            $page = (object) [
                'title' => 'Tambah kategori baru'
            ];
    
            $activeMenu = 'kategori';
    
            return view('kategori.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
        }
    
        public function store(Request $request)
        {
            $request->validate([
                'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode',
                'kategori_nama' => 'required|string|max:100',
            ]);
    
            KategoriModel::create([
                'kategori_kode' => $request->kategori_kode,
                'kategori_nama' => $request->kategori_nama,
            ]);
    
            return redirect('/kategori')->with('success', 'Data kategori berhasil disimpan');
        }
    
        public function show(string $id)
        {
            $kategori = KategoriModel::find($id);

        
        $breadcrumb = (object) [
                'title' => 'Detail Kategori',
                'list' => ['Home', 'Kategori', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail kategori'
        ];

        $activeMenu = 'kategori';

        return view('kategori.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    public function edit(string $id)
    {
        $kategori = KategoriModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Edit Kategori',
            'list' => ['Home', 'Kategori', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit kategori'
        ];

        $activeMenu = 'kategori';

        return view('kategori.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    
    }
        public function update(Request $request, string $id)
        {
            $request->validate([
                'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode,' . $id . ',kategori_id',
                'kategori_nama' => 'required|string|max:100',
            ]);
    
            KategoriModel::find($id)->update([
                'kategori_kode' => $request->kategori_kode,
                'kategori_nama' => $request->kategori_nama,
            ]);
    
            return redirect('/kategori')->with('success', 'Data kategori berhasil dirubah');
        }
    
        public function destroy(string $id)
        {
            $check = KategoriModel::find($id);
            if (!$check) {
                return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
            }
    
            try {
                KategoriModel::destroy($id);
                return redirect('/kategori')->with('success', 'Data kategori berhasil dihapus');
            } catch (\Illuminate\Database\QueryException $e) {
                return redirect('/kategori')->with('error', 'Data kategori gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
            }
        }

        // tugas jb 6
        public function create_ajax()
     {
         return view('kategori.create_ajax');
     }
 
     public function store_ajax(Request $request)
     {
         // Mengecek apakah request berupa ajax
         if ($request->ajax() || $request->wantsJson()) {
             $rules = [
                 'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode',
                 'kategori_nama' => 'required|string|max:100'
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
 
             KategoriModel::create($request->all());
 
             return response()->json([
                 'status' => true,
                 'message' => 'Data kategori berhasil disimpan'
             ]);
         }
         return redirect('/');
     }
 
     public function show_ajax(string $id)
     {
         $kategori = KategoriModel::find($id);
 
         return view('kategori.show_ajax', ['kategori' => $kategori]);
     }
 
     public function edit_ajax(string $id)
     {
         $kategori = KategoriModel::find($id);
 
         return view('kategori.edit_ajax', ['kategori' => $kategori]);
     }
 
     public function update_ajax(Request $request, $id)
     {
         // Mengecek apakah request dari ajax
         if ($request->ajax() || $request->wantsJson()) {
             $rules = [
                 'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode,' . $id . ',kategori_id',
                 'kategori_nama' => 'required|string|max:100'
             ];
 
             // use Illuminate\Support\Facades\Validator;
             $validator = Validator::make($request->all(), $rules);
 
             if ($validator->fails()) {
                 return response()->json([
                     'status' => false, // respon json, true: berhasil, false: gagal
                     'message' => 'Validasi gagal.',
                     'msgField' => $validator->errors() // Menunjukkan field mana yang error
                 ]);
             }
 
             try {
                 KategoriModel::find($id)->update($request->all());
                 return response()->json([
                     'status' => true,
                     'message' => 'Data berhasil diupdate'
                 ]);
             } catch (\Illuminate\Database\QueryException $e) {
                 return response()->json([
                     'status' => false, // respon json, true: berhasil, false: gagal
                     'message' => 'Validasi gagal.',
                     'msgField' => $validator->errors() // Menunjukkan field mana yang error
                 ]);
             }
         }
         return redirect('/');
     }
 
     public function confirm_ajax(string $id)
     {
         $kategori = KategoriModel::find($id);
 
         return view('kategori.confirm_ajax', ['kategori' => $kategori]);
     }

     public function delete_ajax(Request $request, $id)
     {
         // Mengecek apakah request dari ajax
         if ($request->ajax() || $request->wantsJson()) {
             $level = KategoriModel::find($id);
             if ($level) {
                 try {
                     $level->delete();
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
          return view('kategori.import');
      }
  
      public function import_ajax(Request $request)
      {
          if ($request->ajax() || $request->wantsJson()) {
              $rules = [
                  // validasi file harus xls atau xlsx, max 1MB
                  'file_kategori' => ['required', 'mimes:xlsx', 'max:1024']
              ];
              $validator = Validator::make($request->all(), $rules);
              if ($validator->fails()) {
                  return response()->json([
                      'status' => false,
                      'message' => 'Validasi Gagal',
                      'msgField' => $validator->errors()
                  ]);
              }
              $file = $request->file('file_kategori'); // ambil file dari request
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
                              'kategori_kode' => $value['A'],
                              'kategori_nama' => $value['B'],
                              'created_at' => now(),
                          ];
                      }
                  }
                  if (count($insert) > 0) {
                      // insert data ke database, jika data sudah ada, maka diabaikan
                      KategoriModel::insertOrIgnore($insert);
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

      //jb 8 tugas 1 prak 2
      public function export_excel()
      {
          // ambil data kategori yang akan di export
          $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama')
             ->orderBy('kategori_id')
             ->get();
 
         // load library excel
         $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
         $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif
 
         $sheet->setCellValue('A1', 'No');
         $sheet->setCellValue('B1', 'Id Kategori');
         $sheet->setCellValue('C1', 'Kode Kategori');
         $sheet->setCellValue('D1', 'Nama Kategori');
 
         $sheet->getStyle('A1:D1')->getFont()->setBold(true); // bold header
 
         $no = 1; // nomor data dimulai dari 1
         $baris = 2; // baris data dimulai dari baris ke 2
         foreach ($kategori as $key => $value) {
             $sheet->setCellValue('A' . $baris, $no);
             $sheet->setCellValue('B' . $baris, $value->kategori_id);
             $sheet->setCellValue('C' . $baris, $value->kategori_kode);
             $sheet->setCellValue('D' . $baris, $value->kategori_nama);
             $baris++;
             $no++;
         }
 
         foreach (range('A', 'D') as $columnID) {
             $sheet->getColumnDimension($columnID)->setAutoSize(true); // set auto size untuk kolom
         }
 
         $sheet->setTitle('Data Kategori'); // set title sheet
 
         $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
         $filename = 'Data Kategori_' . date('Y-m-d H:i:s') . '.xlsx';
 
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
         $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama')
         ->orderBy('kategori_id')
         ->get();
         $pdf = Pdf::loadView('kategori.export_pdf', ['kategori' => $kategori]);
         $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
         $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
         $pdf->render(); // Render the PDF as HTML - uncomment if you want to see the HTML output
 
         return $pdf->stream('Data Kategori_' . date('Y-m-d H:i:s') . '.pdf');
     }
      
    }