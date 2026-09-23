<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function index()
    {
        $this->authorize('institutions.manage');

        return view('institutions.index', [
            'institutions' => Institution::withCount(['datasets', 'submissions'])->orderBy('code')->paginate(20),
        ]);
    }

    public function create()
    {
        $this->authorize('institutions.manage');

        return view('institutions.form', ['institution' => new Institution]);
    }

    public function store(Request $request)
    {
        $this->authorize('institutions.manage');
        $data = $this->validated($request);
        $institution = Institution::create($data);
        AuditLog::record('institution.created', $institution, null, $data);

        return redirect()->route('institutions.index')->with('success', 'Institution registered.');
    }

    public function edit(Institution $institution)
    {
        $this->authorize('institutions.manage');

        return view('institutions.form', compact('institution'));
    }

    public function update(Request $request, Institution $institution)
    {
        $this->authorize('institutions.manage');
        $data = $this->validated($request, $institution->id);
        $old = $institution->only(array_keys($data));
        $institution->update($data);
        AuditLog::record('institution.updated', $institution, $old, $data);

        return redirect()->route('institutions.index')->with('success', 'Institution updated.');
    }

    public function destroy(Institution $institution)
    {
        $this->authorize('institutions.manage');
        abort_if($institution->submissions()->exists(), 422, 'Cannot delete an institution with submissions. Deactivate it instead.');
        AuditLog::record('institution.deleted', $institution, $institution->toArray());
        $institution->delete();

        return back()->with('success', 'Institution deleted.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:institutions,code'.($id ? ','.$id : '')],
            'name' => ['required', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'integration_mode' => ['required', 'in:api,manual'],
            'is_active' => ['boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /** JSON feed for the AJAX institutions table (DataTables). */
    public function datatable()
    {
        $this->authorize('institutions.manage');

        $rows = Institution::withCount(['datasets', 'submissions'])->orderBy('code')->get()
            ->map(function (Institution $i) {
                $delete = '<form method="POST" action="'.route('institutions.destroy', $i).'" class="d-inline" onsubmit="return confirm(\'Delete '.e($i->code).'? This cannot be undone.\')">'
                    .'<input type="hidden" name="_token" value="'.csrf_token().'">'
                    .'<input type="hidden" name="_method" value="DELETE">'
                    .'<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>';

                return [
                    'code' => '<code>'.e($i->code).'</code>',
                    'name' => '<span class="fw-semibold">'.e($i->name).'</span>',
                    'contact' => '<div class="small">'.e($i->contact_email ?: '—').'</div><div class="text-muted small">'.e($i->contact_phone ?: '').'</div>',
                    'integration' => '<span class="badge text-bg-'.($i->integration_mode === 'api' ? 'info' : 'secondary').'">'.strtoupper($i->integration_mode).'</span>',
                    'datasets' => (int) $i->datasets_count,
                    'submissions' => (int) $i->submissions_count,
                    'status' => $i->is_active
                        ? '<span class="badge text-bg-success">Active</span>'
                        : '<span class="badge text-bg-secondary">Inactive</span>',
                    'actions' => '<a href="'.route('institutions.edit', $i).'" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a> '.$delete,
                ];
            });

        return response()->json(['data' => $rows]);
    }

}
