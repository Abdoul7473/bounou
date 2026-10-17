<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Embauche extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'animal_id',
        'prix_init',
        'prix_vente',
        'poids_entre',
        'poids_sortie',
        'date_entre',
        'date_prevue',
        'statut'
    ];
    public function animal() {
        return $this->belongsTo(Animal::class);
    }
}
