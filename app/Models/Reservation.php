<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'terrain_Id',
        'date',
        'heureDebut',
        'heureFin',
        'actif',
        'reservation_type',
        'reserver_par',
        'supprimer_par',
        'membre1',
        'membre2',
        'membre3',
        'membre4',
    ];

    public function supprimer_par()
    {
        return $this->belongsTo(User::class, 'supprimer_par');
    }

    public function reserver_par()
    {
        return $this->belongsTo(User::class, 'reserver_par');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'membre1');
    }

    public function user2()
{
    return $this->belongsTo(User::class, 'membre2');
}

public function user3()
{
    return $this->belongsTo(User::class, 'membre3');
}

public function user4()
{
    return $this->belongsTo(User::class, 'membre4');
}


    public function terrain()
    {
        return $this->belongsTo(Terrain::class, 'terrain_Id');
    }
}
