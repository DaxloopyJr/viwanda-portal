<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionPeriod extends Model
{
    public const FREQUENCIES = ['quarterly', 'monthly', 'annually', 'weekly'];

    protected $fillable = [
        'institution_id', 'name', 'frequency', 'year', 'quarter', 'month',
        'opens_at', 'closes_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'opens_at' => 'date',
            'closes_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /** Periods visible to the given user: own institution's, or ministry-wide ones. */
    public static function forUser(User $user)
    {
        return static::where('is_active', true)
            ->when($user->isInstitutionUser(), fn ($q) => $q->where('institution_id', $user->institution_id))
            ->when($user->isMinistryDepartmentUser() || ! $user->isInstitutionUser(), fn ($q) => $q->whereNull('institution_id'))
            ->orderByDesc('year')->orderBy('quarter')->orderBy('month')
            ->get();
    }

    /** Auto-generated period label, e.g. "2026 - Q1", "2026 - Annually", "2026 - M03". */
    public static function makeName(string $frequency, int $year, ?int $quarter = null, ?int $month = null): string
    {
        return match ($frequency) {
            'quarterly' => sprintf('%d - Q%d', $year, $quarter),
            'monthly' => sprintf('%d - M%02d', $year, $month),
            'weekly' => sprintf('%d - Weekly', $year),
            default => sprintf('%d - Annually', $year),
        };
    }
}
