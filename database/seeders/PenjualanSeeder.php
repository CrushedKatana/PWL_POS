<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('m_user')->get();

        for ($i = 0; $i < 10; $i++) {
            $user = $users[rand(0, $users->count() - 1)];
            DB::table('t_penjualan')->insert([
                'user_id' => $user->user_id,
                'pembeli' => substr($user->nama, 0, 50),
                'penjualan_kode' => 'penjualan' . $i,
                'penjualan_tanggal' => now(),
            ]);
        }
    }
}