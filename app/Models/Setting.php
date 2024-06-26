<?php

namespace App\Models;

use App\Models\Contact;
use App\Models\Layout;
use App\Models\Navigation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Setting extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Summary of contact
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function contact(): HasOne
    {
        return $this->hasOne(Contact::class);
    }

    /**
     * Summary of layout
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function layout(): HasOne
    {
        return $this->hasOne(Layout::class);
    }

    /**
     * Summary of navigation
     * @return HasOne
     */
    public function navigation(): HasOne
    {
        return $this->hasOne(Navigation::class);
    }
}
