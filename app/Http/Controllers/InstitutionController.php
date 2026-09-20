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
}
