<?php

namespace App;

use App\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'id_mst_role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getIdRole()
    {
        return $this->id_mst_role;
    }

    public function verifyUser()
    {
        return $this->hasOne('App\VerifyUser');
    }

    public static function getListUsers($id)
    {
        $query = DB::table('users')
            ->select('users.id', 'users.name', 'users.email')
            ->where('users.verified', 1)
            ->get();

        return $result = ($query) ? $query : false;
    }

}
