<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Institution;
use App\Models\SubmissionPeriod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Submission-period configuration. Institution admins configure periods for
 * their own institution (periods.manage-own); ministry managers configure
 * ministry-wide periods or any institution's (periods.manage).
 */
class SubmissionPeriodController extends Controller
{
    public function index()
    {
        $this->authorizeManage();

        return view('periods.index');
    }

    public function create()
    {
        $this->authorizeManage();

        return view('periods.form', [
            'period' => new SubmissionPeriod,
            'institutions' => $this->assignableInstitutions(),
            'lockedInstitution' => $this->isOwnOnly(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeManage();
        $data = $this->validated($request);
        $data = $this->enforceScope($data);

        $period = SubmissionPeriod::create($data);
        AuditLog::record('period.created', $period);

        return redirect()->route('periods.index')->with('success', "Submission period {$period->name} created.");
    }

    public function edit(SubmissionPeriod $period)
    {
        $this->authorizeManage();
        $this->authorizeTarget($period);

        return view('periods.form', [
            'period' => $period,
            'institutions' => $this->assignableInstitutions(),
            'lockedInstitution' => $this->isOwnOnly(),
        ]);
    }

    public function update(Request $request, SubmissionPeriod $period)
    {
        $this->authorizeManage();
        $this->authorizeTarget($period);
        $data = $this->validated($request, $period->id);
        $data = $this->enforceScope($data);

        $period->update($data);
        AuditLog::record('period.updated', $period);

        return redirect()->route('periods.index')->with('success', "Submission period {$period->name} updated.");
    }

    public function destroy(SubmissionPeriod $period)
    {
        $this->authorizeManage();
        $this->authorizeTarget($period);
        AuditLog::record('period.deleted', $period, ['name' => $period->name]);
        $period->delete();

        return back()->with('success', 'Submission period removed.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'frequency' => ['required', Rule::in(SubmissionPeriod::FREQUENCIES)],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'quarter' => ['nullable', 'required_if:frequency,quarterly', 'integer', 'min:1', 'max:4'],
            'month' => ['nullable', 'required_if:frequency,monthly', 'integer', 'min:1', 'max:12'],
            'opens_at' => ['nullable', 'date'],
            'closes_at' => ['nullable', 'date', 'after_or_equal:opens_at'],
            'is_active' => ['boolean'],
        ]);

        $data['name'] = SubmissionPeriod::makeName(
            $data['frequency'], (int) $data['year'],
            $data['quarter'] ?? null, $data['month'] ?? null
        );
        $data['is_active'] = $request->boolean('is_active');
        $data['institution_id'] = $data['institution_id'] ?? null;

        // One entry per period name within a scope.
        $exists = SubmissionPeriod::where('institution_id', $data['institution_id'])
            ->where('name', $data['name'])
            ->when($id, fn ($q) => $q->where('id', '!=', $id))
            ->exists();
        abort_if($exists, 422, "Period {$data['name']} already exists for this scope.");

        return $data;
    }

    private function authorizeManage(): void
    {
        abort_unless(
            auth()->user()->can('periods.manage') || auth()->user()->can('periods.manage-own'),
            403
        );
    }

    private function isOwnOnly(): bool
    {
        $user = auth()->user();

        return $user->can('periods.manage-own') && ! $user->can('periods.manage');
    }

    private function authorizeTarget(SubmissionPeriod $period): void
    {
        if ($this->isOwnOnly()) {
            abort_unless(
                $period->institution_id && $period->institution_id === auth()->user()->institution_id,
                403, 'You can only configure the submission periods of your own institution.'
            );
        }
    }

    private function enforceScope(array $data): array
    {
        if ($this->isOwnOnly()) {
            $data['institution_id'] = auth()->user()->institution_id;
        }

        return $data;
    }

    private function assignableInstitutions()
    {
        if ($this->isOwnOnly()) {
            return Institution::where('id', auth()->user()->institution_id)->get();
        }

        return Institution::orderBy('name')->get();
    }

    /** JSON feed for the AJAX submission-periods table (DataTables). */
    public function datatable()
    {
        $this->authorizeManage();

        $query = SubmissionPeriod::with('institution')->orderByDesc('year')->orderBy('quarter')->orderBy('month');
        if ($this->isOwnOnly()) {
            $query->where('institution_id', auth()->user()->institution_id);
        }

        $rows = $query->get()->map(function (SubmissionPeriod $p) {
            $window = ($p->opens_at?->format('d M Y') ?? '—').' → '.($p->closes_at?->format('d M Y') ?? '—');

            return [
                'name' => '<span class="fw-semibold">'.e($p->name).'</span>',
                'scope' => $p->institution
                    ? '<code>'.e($p->institution->code).'</code> '.e($p->institution->name)
                    : '<span class="badge text-bg-navy">Ministry-wide</span>',
                'frequency' => ucfirst($p->frequency),
                'window' => '<span class="small text-muted">'.$window.'</span>',
                'status' => $p->is_active
                    ? '<span class="badge text-bg-success">Active</span>'
                    : '<span class="badge text-bg-secondary">Inactive</span>',
                'actions' => '<a href="'.route('periods.edit', $p).'" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a> '
                    .'<form method="POST" action="'.route('periods.destroy', $p).'" class="d-inline" onsubmit="return confirm(\'Delete '.e($p->name).'?\')">'
                    .'<input type="hidden" name="_token" value="'.csrf_token().'">'
                    .'<input type="hidden" name="_method" value="DELETE">'
                    .'<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>',
            ];
        });

        return response()->json(['data' => $rows]);
    }
}
