<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'files';
    protected $fillable = [
        'username',
        'file_name',
        'encrypted_file',
        'decrypted_file',
        'password',
        'keterangan',
        'file_size',
        'status',
        'tanggal',
        'tanggal_enkripsi',
    ];
    protected $dates = [
        'tanggal',
        'tanggal_enkripsi',
    ];
}
