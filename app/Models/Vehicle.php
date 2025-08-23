<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'marca',
        'modelo',
        'ano',
        'preco',
        'cor',
        'combustivel',
        'url_imagem',
    ];
}
