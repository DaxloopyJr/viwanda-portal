<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consumer extends Model
{
    protected $fillable = ['institution_id', 'code', 'name', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Consumers available at submission time: global (ministry-configured)
     * entries plus the institution's own additions.
     */
    public static function forUser(User $user)
    {
        return static::where('is_active', true)
            ->when($user->isInstitutionUser(), fn ($q) => $q->where(function ($qq) use ($user) {
                $qq->whereNull('institution_id')->orWhere('institution_id', $user->institution_id);
            }))
            ->when(! $user->isInstitutionUser(), fn ($q) => $q->whereNull('institution_id'))
            ->orderBy('code')
            ->get();
    }
}
