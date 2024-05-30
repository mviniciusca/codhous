<?php

namespace App\Models;

use App\Models\Contact;
use App\Models\Layout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Setting extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function contact(): HasOne
    {
        return $this->hasOne(Contact::class);
    }

    public function layout(): HasOne
    {
        return $this->hasOne(Layout::class);
    }
}
