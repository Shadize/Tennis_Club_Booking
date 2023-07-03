<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fait_Partie_Catégorie extends Model
{
    use HasFactory;

    protected $table = 'fait_partie_catégories';

    protected $fillable = [
        'categorie_Id',
        'membre_Id',
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class, 'membre_Id', 'numero_Affiliation');
    }
}
