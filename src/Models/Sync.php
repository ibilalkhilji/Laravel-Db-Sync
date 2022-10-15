<?php

namespace Khaleejinfotech\LaravelDbSync\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sync extends Model
{
    use HasFactory;

    protected $fillable = ['model', 'payload', 'action', 'job_id', 'synced'];

    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_UPLOAD = 'upload';
}
