<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penjualans = DB::table('t_penjualan')->get();
        $barangs = DB::table('m_barang')->get();

        foreach ($penjualans as $penjualan) {
            $barangId = rand(0, $barangs->count() - 1);
            for ($i = 0; $i < 3; $i++) {
                $barang = $barangs[$barangId];
                DB::table('t_penjualan_detail')->insert([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $barang->barang_id,
                    'harga' => $barang->harga_jual,
                    'jumlah' => rand(1, 15),
                ]);
            }
        }
    }
}
