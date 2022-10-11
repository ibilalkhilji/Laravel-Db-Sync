<?php

namespace Khaleejinfotech\LaravelDbSync\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sync extends Model
{
    use HasFactory;

    protected $fillable = ['model', 'payload', 'action', 'job_id', 'synced'];

}
