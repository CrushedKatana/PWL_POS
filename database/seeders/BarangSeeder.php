<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $kategori = [
            ['Elektronik', 'eletronik'],
            ['Pakaian', 'pakaian'],
            ['Aksesoris', 'aksesoris'],
            ['Buku', 'buku'],
            ['Alat Tulis', 'alat_tulis'],
        ];

        for ($i = 0; $i < 10; $i++) {
            $barangId = rand(0, 4);
            $barang = $kategori[$barangId];
            $hargaBeli = rand(10000, 100000);
            DB::table('m_barang')->insert([
                'kategori_id' => $barangId + 1,
                'barang_nama' => $barang[0] . ' Versi-' . ($i + 1),
                'barang_kode' => $barang[1],
                'harga_beli' => $hargaBeli,
                'harga_jual' => $hargaBeli * 1.5,
            ]);
        }
    }
}
