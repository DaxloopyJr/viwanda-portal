<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Consumer;
use App\Models\Dataset;
use App\Models\Department;
use App\Models\Institution;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function index()
    {
        return view('datasets.index');
    }

    public function show(Dataset $dataset)
    {
        $this->authorizeView($dataset);
        $dataset->load(['institution', 'ministryDepartment']);
        $dataset->loadCount('submissions');
        $recent = $dataset->submissions()->latest()->limit(10)->get();
        $periods = \App\Models\SubmissionPeriod::forUser(auth()->user())
            ->where('frequency', strtolower($dataset->frequency));

        return view('datasets.show', compact('dataset', 'recent', 'periods'));
    }

    public function create()
    {
        $this->authorizeManage();

        return view('datasets.form', $this->formData(new Dataset));
    }

    public function store(Request $request)
    {
        $this->authorizeManage();
        $data = $this->validated($request);
        if ($this->isInstitutionAdmin()) {
            $data['institution_id'] = auth()->user()->institution_id;
            $data['department_id'] = null;
        }
        $dataset = Dataset::create($data);
        AuditLog::record('dataset.created', $dataset);

        return redirect()->route('datasets.show', $dataset)->with('success', 'Dataset registered in the data catalogue.');
    }

    public function edit(Dataset $dataset)
    {
        $this->authorizeManage();
        $this->authorizeTarget($dataset);

        return view('datasets.form', $this->formData($dataset));
    }

    public function update(Request $request, Dataset $dataset)
    {
        $this->authorizeManage();
        $this->authorizeTarget($dataset);
        $data = $this->validated($request, $dataset->id);
        if ($this->isInstitutionAdmin()) {
            $data['institution_id'] = auth()->user()->institution_id;
            $data['department_id'] = null;
        }
        $dataset->update($data);
        AuditLog::record('dataset.updated', $dataset);

        return redirect()->route('datasets.show', $dataset)->with('success', 'Dataset updated.');
    }

    private function formData(Dataset $dataset): array
    {
        $user = auth()->user();

        return [
            'dataset' => $dataset,
            'institutions' => $this->assignableInstitutions(),
            'departments' => $this->isInstitutionAdmin() ? collect() : Department::where('is_active', true)->orderBy('name')->get(),
            'consumerOptions' => Consumer::forUser($user),
            'lockedInstitution' => $this->isInstitutionAdmin(),
        ];
    }

    /** Ministry managers configure all catalogues; institution admins only their own. */
    private function authorizeManage(): void
    {
        abort_unless(
            auth()->user()->can('datasets.manage') || auth()->user()->can('datasets.manage-own'),
            403
        );
    }

    private function isInstitutionAdmin(): bool
    {
        $user = auth()->user();

        return $user->can('datasets.manage-own') && ! $user->can('datasets.manage');
    }

    private function authorizeTarget(Dataset $dataset): void
    {
        if ($this->isInstitutionAdmin()) {
            abort_unless(
                $dataset->institution_id === auth()->user()->institution_id,
                403, 'You can only configure the data catalogue of your own institution.'
            );
        }
    }

    private function authorizeView(Dataset $dataset): void
    {
        $user = auth()->user();
        abort_if($user->isInstitutionUser() && $dataset->institution_id !== $user->institution_id, 403);
        abort_if($user->isMinistryDepartmentUser() && $dataset->department_id !== $user->department_id, 403);
    }

    private function assignableInstitutions()
    {
        if ($this->isInstitutionAdmin()) {
            return Institution::where('id', auth()->user()->institution_id)->get();
        }

        return Institution::orderBy('name')->get();
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:datasets,code'.($id ? ','.$id : '')],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'institution_id' => ['nullable', 'exists:institutions,id', 'required_without:department_id'],
            'department_id' => ['nullable', 'exists:departments,id', 'required_without:institution_id'],
            'department' => ['nullable', 'string', 'max:255'],
            'frequency' => ['required', 'string', 'max:30'],
            'priority' => ['required', 'in:low,medium,high'],
            'source_system' => ['nullable', 'string', 'max:255'],
            'consumers' => ['nullable', 'array'],
            'consumers.*' => ['string', 'max:60'],
            'fields' => ['required', 'json'],
            'is_active' => ['boolean'],
        ]);
        $data['fields'] = json_decode($data['fields'], true);
        $data['is_active'] = $request->boolean('is_active');
        // Consumers are stored as a comma-separated list built from the multi-select.
        $data['consumers'] = ! empty($data['consumers']) ? implode(', ', $data['consumers']) : null;
        // A dataset belongs either to an institution or to a ministry department.
        if (! empty($data['department_id'])) {
            $data['institution_id'] = null;
        } else {
            $data['department_id'] = null;
        }

        return $data;
    }

    /** JSON feed for the AJAX data-catalogue table (DataTables). */
    public function datatable()
    {
        $user = auth()->user();
        $query = Dataset::with(['institution', 'ministryDepartment'])->withCount('submissions')->orderBy('code');
        if ($user->isInstitutionUser()) {
            $query->where('institution_id', $user->institution_id);
        } elseif ($user->isMinistryDepartmentUser()) {
            $query->where('department_id', $user->department_id);
        }
        $canManage = $user->can('datasets.manage') || $user->can('datasets.manage-own');

        $rows = $query->get()->map(function (Dataset $d) use ($canManage) {
            $priorityClass = $d->priority === 'high' ? 'danger' : ($d->priority === 'medium' ? 'warning' : 'secondary');

            return [
                'code' => '<code>'.e($d->code).'</code>',
                'name' => '<a href="'.route('datasets.show', $d).'" class="fw-semibold text-decoration-none">'.e($d->name).'</a>'
                    .'<div class="text-muted small">'.e(\Illuminate\Support\Str::limit($d->description, 60)).'</div>',
                'institution' => e($d->ownerLabel()).($d->department ? '<div class="text-muted small">'.e($d->department).'</div>' : ''),
                'frequency' => ucfirst($d->frequency),
                'priority' => '<span class="badge text-bg-'.$priorityClass.'">'.ucfirst($d->priority).'</span>',
                'fields' => count($d->fields ?? []),
                'submissions' => (int) $d->submissions_count,
                'status' => $d->is_active
                    ? '<span class="badge text-bg-success">Active</span>'
                    : '<span class="badge text-bg-secondary">Inactive</span>',
                'actions' => $canManage
                    ? '<a href="'.route('datasets.edit', $d).'" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>'
                    : '',
            ];
        });

        return response()->json(['data' => $rows]);
    }

}
