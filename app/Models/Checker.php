<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checker extends Model
{
    use HasFactory;
    protected array $guarded = ['id'];
    public function Maker()
    {
        return $this->belongsTo(Maker::class);

    }
}
