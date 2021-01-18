<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'check_table';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'check_id';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function servers()
    {
        return $this->belongsToMany('App\Models\Server', 'check_servers', 'check_id', 'server_id');
    }

    public function status()
    {
        return $this->hasOne('App\Models\CheckStatus', 'status_id', 'check_status_id');
    }
    public function decrees()
    {
        return $this->hasMany('App\Models\ViolationDecree', 'check_id', 'check_id');
    }
}
