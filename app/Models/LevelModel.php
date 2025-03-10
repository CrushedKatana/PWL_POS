<?php
 
 namespace App\Models;
 
 use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
 class LevelModel extends Model
 {
     use HasFactory;
 
     protected $table = 'm_level';
     protected $primaryKey = 'level_id';
 
     public function user(): BelongsTo
     {
         // JS4: Praktikum 2.7 / One to One
         return $this->belongsTo(UserModel::class);
     }
 }