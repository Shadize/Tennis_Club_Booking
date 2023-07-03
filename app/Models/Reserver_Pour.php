<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserver_Pour extends Model
{
    use HasFactory;

    protected $table = 'reserver__pours';

    protected $fillable = [
        'membre_Id',
        'reservation_Id',
    ];

    public function membre()
    {
        return $this->belongsTo(Membre::class, 'membre_Id', 'numero_Affiliation');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_Id');
    }
}
