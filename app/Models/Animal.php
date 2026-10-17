<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;
     protected $fillable = [
        'id',
        'identifiant',
        'categorie',
        'race',
        'sexe',
        'date_naiss',
        'age',
        'poids',
        'type',
        'observation',
        'photo'
    ];
}
