<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionRecord extends Model
{
    protected $fillable = ['submission_id', 'row_number', 'data', 'errors'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'errors' => 'array',
        ];
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
