<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    public const STATUS_LABELS = [
        'draft' => 'Draft',
        'internal_review' => 'Internal review',
        'returned_officer' => 'Returned to officer',
        'accounting_review' => 'Accounting review',
        'returned_supervisor' => 'Returned to supervisor',
        'submitted' => 'Submitted',
        'under_review' => 'Under review',
        'returned' => 'Returned',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'published' => 'Published',
    ];

    public const STATUSES = [
        'draft', 'internal_review', 'returned_officer', 'accounting_review', 'returned_supervisor',
        'submitted', 'under_review', 'returned', 'accepted', 'rejected', 'published',
    ];

    protected $fillable = [
        'reference', 'transaction_reference', 'institution_id', 'dataset_id',
        'reporting_period', 'channel', 'status', 'submitted_by', 'submitted_at',
        'reviewed_by', 'reviewed_at', 'review_comments', 'validation_errors',
        'published_at', 'records_count',
    ];

    protected function casts(): array
    {
        return [
            'validation_errors' => 'array',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function dataset()
    {
        return $this->belongsTo(Dataset::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function records()
    {
        return $this->hasMany(SubmissionRecord::class)->orderBy('row_number');
    }

    public static function nextReference(): string
    {
        $year = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('VP-%s-%05d', $year, $count);
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'returned_officer', 'returned'], true);
    }

    public function statusLabel(): string
    {
        return [
            'draft' => 'Draft',
            'internal_review' => 'Internal review',
            'returned_officer' => 'Returned to officer',
            'accounting_review' => 'Accounting review',
            'returned_supervisor' => 'Returned to supervisor',
            'submitted' => 'Submitted',
            'under_review' => 'Under review',
            'returned' => 'Returned',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'published' => 'Published',
        ][$this->status] ?? ucfirst($this->status);
    }

    public function statusBadge(): string
    {
        return [
            'draft' => 'secondary',
            'internal_review' => 'purple',
            'returned_officer' => 'orange',
            'accounting_review' => 'teal',
            'returned_supervisor' => 'orange',
            'submitted' => 'info',
            'under_review' => 'warning',
            'returned' => 'orange',
            'accepted' => 'success',
            'rejected' => 'danger',
            'published' => 'primary',
        ][$this->status] ?? 'secondary';
    }
}
