<?php

namespace App\Http\Controllers;

use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class BackupController extends Controller
{
    public function __construct(private BackupService $backups) {}

    public function index()
    {
        return view('backups.index', [
            'backups' => $this->backups->files(),
            'webRestoreEnabled' => (bool) config('erp_backup.allow_web_restore', false),
            'offsiteStatus' => app(\App\Services\BackupOffsiteService::class)->status(),
        ]);
    }

    public function store(\App\Services\BackupOffsiteService $offsite)
    {
        if (config('erp_backup.require_offsite', false) && !$offsite->configured()) {
            return back()->withErrors(['backup' => __('Off-site backup is required but not configured. Please contact the administrator.')]);
        }
        $name = $this->backups->create();
        if ($offsite->configured()) {
            try {
                $offsite->replicate($name);
            } catch (\Throwable $e) {
                report($e);
                return back()->withErrors(['backup' => __('Local encrypted backup saved, but off-site verification FAILED. Please review server logs and retry: :name', ['name' => $name])]);
            }
            $this->backups->prune();
            return back()->with('success', __('Encrypted backup created and verified remotely: :name', ['name' => $name]));
        }
        $this->backups->prune();
        return back()->with('success', __('LOCAL encrypted backup created: :name. Off-site disaster recovery is NOT configured.', ['name' => $name]));
    }

    public function verifyOffsite(string $backup, \App\Services\BackupOffsiteService $offsite)
    {
        try {
            $details = $offsite->verify($backup);
            return back()->with('success', __('Off-site encrypted backup verified. SHA-256: :sha', ['sha' => $details['sha256']]));
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['backup' => __('Off-site verification failed. See server logs.')]);
        }
    }

    public function download(string $backup)
    {
        return response()->download($this->backups->path($backup));
    }

    public function verify(string $backup)
    {
        $result = $this->backups->verify($backup);
        return back()->with('success', __('Backup verified: :tables tables, :files files.', $result));
    }

    public function restore(Request $request, string $backup)
    {
        abort_unless(config('erp_backup.allow_web_restore', false) && !app()->environment('production'), 403, 'Browser restore is disabled.');
        $input = $request->validate([
            'confirm' => 'required|in:RESTORE',
            'password' => 'required|string',
        ]);
        if (!Hash::check($input['password'], $request->user()->password)) {
            throw ValidationException::withMessages(['password' => __('Your password is incorrect.')]);
        }
        $this->backups->restore($backup);
        return back()->with('success', __('Backup restored successfully.'));
    }

    public function destroy(string $backup)
    {
        $this->backups->delete($backup);
        return back()->with('success', __('Backup deleted.'));
    }
}
