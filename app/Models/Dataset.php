<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    protected $fillable = [
        'code', 'name', 'description', 'institution_id', 'department_id', 'department',
        'frequency', 'priority', 'source_system', 'consumers', 'fields', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /** Ministry department that owns this dataset (ministry-internal catalogue entries). */
    public function ministryDepartment()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /** Human-readable owner: institution, ministry department, or ministry-wide. */
    public function ownerLabel(): string
    {
        if ($this->institution) {
            return $this->institution->code;
        }
        if ($this->ministryDepartment) {
            return 'MIT / '.$this->ministryDepartment->code;
        }

        return 'Ministry';
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Laravel validation rules for one data row, built from the
     * data-dictionary field definitions.
     */
    public function validationRules(string $prefix = ''): array
    {
        $rules = [];
        foreach ($this->fields ?? [] as $field) {
            $rule = [$field['required'] ?? false ? 'required' : 'nullable'];
            match ($field['type'] ?? 'string') {
                'date' => $rule[] = 'date',
                'integer' => $rule[] = 'integer',
                'number' => $rule[] = 'numeric',
                'text' => $rule[] = 'string',
                default => array_push($rule, 'string', 'max:255'),
            };
            if (! empty($field['options'])) {
                $rule[] = 'in:'.implode(',', $field['options']);
            }
            $rules[$prefix.$field['name']] = $rule;
        }

        return $rules;
    }
}
