<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class StokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangs = DB::table('m_barang')->get();

        foreach ($barangs as $barang) {
            DB::table('t_stok')->insert([
                'barang_id' => $barang->barang_id,
                'user_id' => rand(1, 3),
                'stok_tanggal' => now(),
                'stok_jumlah' => rand(0, 256),
            ]);
        }
    }
}