<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Dataset;
use App\Models\Institution;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function index()
    {
        $query = Dataset::with('institution')->withCount('submissions')->orderBy('code');
        if ($this->isInstitutionAdmin()) {
            $query->where('institution_id', auth()->user()->institution_id);
        }

        return view('datasets.index', ['datasets' => $query->paginate(20)]);
    }

    public function show(Dataset $dataset)
    {
        $dataset->load('institution');
        $dataset->loadCount('submissions');
        $recent = $dataset->submissions()->latest()->limit(10)->get();

        return view('datasets.show', compact('dataset', 'recent'));
    }

    public function create()
    {
        $this->authorizeManage();

        return view('datasets.form', [
            'dataset' => new Dataset,
            'institutions' => $this->assignableInstitutions(),
            'lockedInstitution' => $this->isInstitutionAdmin(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeManage();
        $data = $this->validated($request);
        if ($this->isInstitutionAdmin()) {
            $data['institution_id'] = auth()->user()->institution_id;
        }
        $dataset = Dataset::create($data);
        AuditLog::record('dataset.created', $dataset);

        return redirect()->route('datasets.show', $dataset)->with('success', 'Dataset registered in the data catalogue.');
    }

    public function edit(Dataset $dataset)
    {
        $this->authorizeManage();
        $this->authorizeTarget($dataset);

        return view('datasets.form', [
            'dataset' => $dataset,
            'institutions' => $this->assignableInstitutions(),
            'lockedInstitution' => $this->isInstitutionAdmin(),
        ]);
    }

    public function update(Request $request, Dataset $dataset)
    {
        $this->authorizeManage();
        $this->authorizeTarget($dataset);
        $data = $this->validated($request, $dataset->id);
        if ($this->isInstitutionAdmin()) {
            $data['institution_id'] = auth()->user()->institution_id;
        }
        $dataset->update($data);
        AuditLog::record('dataset.updated', $dataset);

        return redirect()->route('datasets.show', $dataset)->with('success', 'Dataset updated.');
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
            'institution_id' => ['required', 'exists:institutions,id'],
            'frequency' => ['required', 'string', 'max:30'],
            'priority' => ['required', 'in:low,medium,high'],
            'source_system' => ['nullable', 'string', 'max:255'],
            'consumers' => ['nullable', 'string', 'max:255'],
            'fields' => ['required', 'json'],
            'is_active' => ['boolean'],
        ]);
        $data['fields'] = json_decode($data['fields'], true);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
