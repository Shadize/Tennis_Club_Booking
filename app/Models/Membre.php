<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membre extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'numero_Affiliation';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'numero_Affiliation',
        'user_Id',
        'nom',
        'prenom',
        'actif',
        'supprimé_Par',
        'classement',
        'ordre_De_Cotisation',
        'sexe',
        'date_De_Naissance',
        'telephone',
        'email',
        'rue',
        'code_Postal',
        'localité',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_De_Naissance' => 'date',
    ];

    /**
     * Get the user associated with the member.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who deleted the member.
     */
    public function supprimePar()
    {
        return $this->belongsTo(User::class, 'supprimé_Par');
    }
}
