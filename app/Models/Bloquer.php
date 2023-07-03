<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Bloquer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'dateDebut',
        'dateFin',
        'raison',
        'terrain_Bloquer',
        'bloquer_Par',
    ];

    public function terrain()
    {
        // Relation "belongsTo" vers le modèle "Terrain"
        return $this->belongsTo(Terrain::class, 'terrain_Bloquer');
    }

    public function user()
    {
        // Relation "belongsTo" vers le modèle "User"
        return $this->belongsTo(User::class, 'bloquer_Par');
    }
}
