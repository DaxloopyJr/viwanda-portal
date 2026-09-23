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
        'pending_approval' => 'Pending final approval',
        'returned' => 'Returned',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'published' => 'Published',
    ];

    public const STATUSES = [
        'draft', 'internal_review', 'returned_officer', 'accounting_review', 'returned_supervisor',
        'submitted', 'under_review', 'pending_approval', 'returned', 'accepted', 'rejected', 'published',
    ];

    protected $fillable = [
        'reference', 'batch_reference', 'transaction_reference', 'institution_id', 'dataset_id',
        'reporting_period', 'channel', 'consumers', 'status', 'submitted_by', 'submitted_at',
        'reviewed_by', 'reviewed_at', 'review_comments', 'validation_errors',
        'published_at', 'records_count',
    ];

    protected function casts(): array
    {
        return [
            'validation_errors' => 'array',
            'consumers' => 'array',
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

    public static function nextBatchReference(): string
    {
        $year = now()->format('Y');
        $count = static::whereNotNull('batch_reference')
            ->whereYear('created_at', $year)
            ->distinct()->count('batch_reference') + 1;

        return sprintf('VPB-%s-%05d', $year, $count);
    }

    /**
     * All submissions that were created together with this one as a batch
     * (one per dataset). Returns just this submission when it stands alone.
     */
    public function batchSiblings()
    {
        if (! $this->batch_reference) {
            return collect([$this]);
        }

        return static::with('dataset')->where('batch_reference', $this->batch_reference)
            ->orderBy('id')->get();
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
            'pending_approval' => 'Pending final approval',
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
            'pending_approval' => 'purple',
            'returned' => 'orange',
            'accepted' => 'success',
            'rejected' => 'danger',
            'published' => 'primary',
        ][$this->status] ?? 'secondary';
    }
}
