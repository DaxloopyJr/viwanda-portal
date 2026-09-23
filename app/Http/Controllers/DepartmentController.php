<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Department;
use Illuminate\Http\Request;

/**
 * Ministry departments. The ministry admin configures the departments whose
 * data officers submit data within the ministry approval chain.
 */
class DepartmentController extends Controller
{
    public function index()
    {
        $this->authorize('departments.manage');

        return view('departments.index');
    }

    public function create()
    {
        $this->authorize('departments.manage');

        return view('departments.form', ['department' => new Department]);
    }

    public function store(Request $request)
    {
        $this->authorize('departments.manage');
        $department = Department::create($this->validated($request));
        AuditLog::record('department.created', $department);

        return redirect()->route('departments.index')->with('success', "Department {$department->name} created.");
    }

    public function edit(Department $department)
    {
        $this->authorize('departments.manage');

        return view('departments.form', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $this->authorize('departments.manage');
        $department->update($this->validated($request, $department->id));
        AuditLog::record('department.updated', $department);

        return redirect()->route('departments.index')->with('success', "Department {$department->name} updated.");
    }

    public function destroy(Department $department)
    {
        $this->authorize('departments.manage');
        abort_if($department->users()->exists(), 422, 'This department still has assigned users.');
        abort_if($department->datasets()->exists(), 422, 'This department still has assigned datasets.');
        AuditLog::record('department.deleted', $department, ['name' => $department->name]);
        $department->delete();

        return back()->with('success', 'Department removed.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:departments,code'.($id ? ','.$id : '')],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /** JSON feed for the AJAX departments table (DataTables). */
    public function datatable()
    {
        $this->authorize('departments.manage');

        $rows = Department::withCount(['users', 'datasets'])->orderBy('code')->get()
            ->map(function (Department $d) {
                return [
                    'code' => '<code>'.e($d->code).'</code>',
                    'name' => '<span class="fw-semibold">'.e($d->name).'</span>'
                        .'<div class="text-muted small">'.e(\Illuminate\Support\Str::limit($d->description, 70)).'</div>',
                    'users' => (int) $d->users_count,
                    'datasets' => (int) $d->datasets_count,
                    'status' => $d->is_active
                        ? '<span class="badge text-bg-success">Active</span>'
                        : '<span class="badge text-bg-secondary">Inactive</span>',
                    'actions' => '<a href="'.route('departments.edit', $d).'" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a> '
                        .'<form method="POST" action="'.route('departments.destroy', $d).'" class="d-inline" onsubmit="return confirm(\'Delete '.e($d->name).'?\')">'
                        .'<input type="hidden" name="_token" value="'.csrf_token().'">'
                        .'<input type="hidden" name="_method" value="DELETE">'
                        .'<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>',
                ];
            });

        return response()->json(['data' => $rows]);
    }
}
