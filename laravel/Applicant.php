<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'violation_applicant_name';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'applicant_id';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function violation_protocols()
    {
        return $this->hasMany('App\Models\ViolationProtocol', 'applicant_id', 'applicant_id');
    }
}
