<?php

namespace App\Models;


use App\Models\Layout;
use App\Models\Module;
use App\Models\Contact;
use App\Models\Partner;
use App\Models\Navigation;
use App\Models\CompanySetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected function casts(): array
    {
        return [
            'budget' => 'array',
            'social' => 'array',
            'whatsapp' => 'array',
        ];
    }

    /**
     * Summary of company
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function companySetting(): HasOne
    {
        return $this->hasOne(CompanySetting::class);
    }

    /**
     * Summary of module
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function module(): HasOne
    {
        return $this->hasOne(Module::class);
    }

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

    public function partner(): HasMany
    {
        return $this->hasMany(Partner::class);
    }
}
