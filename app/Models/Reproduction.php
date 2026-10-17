<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reproduction extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'productrice',
        'belier',
        'date',
        'nbr_anneau',
        'diagnostic',
        'date_prevue'
    ];
    public function belier() {
        return $this->belongsTo(Animal::class,"belier");
    }
     public function productrice() {
        return $this->belongsTo(Animal::class,"productrice");
    }
}
