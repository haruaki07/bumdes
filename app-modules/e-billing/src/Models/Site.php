<?php

namespace Modules\EBilling\Models;

use App\Traits\Datatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use Datatable, HasFactory, SoftDeletes;

    protected $table = 'ebil_sites';

    protected $fillable = [
        'name',
        'description',
    ];
}
