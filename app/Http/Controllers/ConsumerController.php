<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Consumer;
use App\Models\Institution;
use Illuminate\Http\Request;

/**
 * Data-consumer configuration (e.g. TR, MIT, Public). Institution admins add
 * consumers for their own institution (consumers.manage-own); ministry
 * managers maintain the global list (consumers.manage).
 */
class ConsumerController extends Controller
{
    public function index()
    {
        $this->authorizeManage();

        return view('consumers.index');
    }

    public function create()
    {
        $this->authorizeManage();

        return view('consumers.form', [
            'consumer' => new Consumer,
            'institutions' => $this->assignableInstitutions(),
            'lockedInstitution' => $this->isOwnOnly(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeManage();
        $data = $this->validated($request);
        $data = $this->enforceScope($data);

        $consumer = Consumer::create($data);
        AuditLog::record('consumer.created', $consumer);

        return redirect()->route('consumers.index')->with('success', "Consumer {$consumer->code} added.");
    }

    public function edit(Consumer $consumer)
    {
        $this->authorizeManage();
        $this->authorizeTarget($consumer);

        return view('consumers.form', [
            'consumer' => $consumer,
            'institutions' => $this->assignableInstitutions(),
            'lockedInstitution' => $this->isOwnOnly(),
        ]);
    }

    public function update(Request $request, Consumer $consumer)
    {
        $this->authorizeManage();
        $this->authorizeTarget($consumer);
        $data = $this->validated($request, $consumer->id);
        $data = $this->enforceScope($data);

        $consumer->update($data);
        AuditLog::record('consumer.updated', $consumer);

        return redirect()->route('consumers.index')->with('success', "Consumer {$consumer->code} updated.");
    }

    public function destroy(Consumer $consumer)
    {
        $this->authorizeManage();
        $this->authorizeTarget($consumer);
        AuditLog::record('consumer.deleted', $consumer, ['code' => $consumer->code]);
        $consumer->delete();

        return back()->with('success', 'Consumer removed.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'code' => ['required', 'string', 'max:60'],
            'name' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);
        $data['code'] = trim($data['code']);
        $data['is_active'] = $request->boolean('is_active');
        $data['institution_id'] = $data['institution_id'] ?? null;

        $exists = Consumer::where('institution_id', $data['institution_id'])
            ->where('code', $data['code'])
            ->when($id, fn ($q) => $q->where('id', '!=', $id))
            ->exists();
        abort_if($exists, 422, "Consumer {$data['code']} already exists in this scope.");

        return $data;
    }

    private function authorizeManage(): void
    {
        abort_unless(
            auth()->user()->can('consumers.manage') || auth()->user()->can('consumers.manage-own'),
            403
        );
    }

    private function isOwnOnly(): bool
    {
        $user = auth()->user();

        return $user->can('consumers.manage-own') && ! $user->can('consumers.manage');
    }

    private function authorizeTarget(Consumer $consumer): void
    {
        if ($this->isOwnOnly()) {
            abort_unless(
                $consumer->institution_id && $consumer->institution_id === auth()->user()->institution_id,
                403, 'You can only configure consumers of your own institution.'
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

    /** JSON feed for the AJAX consumers table (DataTables). */
    public function datatable()
    {
        $this->authorizeManage();

        $query = Consumer::with('institution')->orderBy('code');
        if ($this->isOwnOnly()) {
            $query->where(fn ($q) => $q->whereNull('institution_id')
                ->orWhere('institution_id', auth()->user()->institution_id));
        }

        $rows = $query->get()->map(function (Consumer $c) {
            $editable = ! $this->isOwnOnly() || $c->institution_id === auth()->user()->institution_id;

            return [
                'code' => '<code>'.e($c->code).'</code>',
                'name' => e($c->name ?? '—'),
                'scope' => $c->institution
                    ? '<code>'.e($c->institution->code).'</code> '.e($c->institution->name)
                    : '<span class="badge text-bg-navy">Global (ministry)</span>',
                'status' => $c->is_active
                    ? '<span class="badge text-bg-success">Active</span>'
                    : '<span class="badge text-bg-secondary">Inactive</span>',
                'actions' => $editable
                    ? '<a href="'.route('consumers.edit', $c).'" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a> '
                        .'<form method="POST" action="'.route('consumers.destroy', $c).'" class="d-inline" onsubmit="return confirm(\'Delete '.e($c->code).'?\')">'
                        .'<input type="hidden" name="_token" value="'.csrf_token().'">'
                        .'<input type="hidden" name="_method" value="DELETE">'
                        .'<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>'
                    : '<span class="text-muted small">global</span>',
            ];
        });

        return response()->json(['data' => $rows]);
    }
}
