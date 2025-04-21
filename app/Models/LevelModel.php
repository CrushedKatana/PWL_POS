<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LevelModel extends Model

{

    protected $table = 'm_level';
    protected $primaryKey = 'level_id'; // Sesuaikan dengan primary key yang benar
    
    // Tambahkan kolom-kolom yang diizinkan untuk mass assignment
    protected $fillable = ['level_kode', 'level_nama'];
}

