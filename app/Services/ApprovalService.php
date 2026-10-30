<?php
namespace App\Services;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ApprovalService
{
    public function required(): bool
    {
        return Schema::hasTable('approval_requests')
            && (string) Setting::getValue('maker_checker_enabled', '0') === '1';
    }

    public function assertApproved(Model $model, string $type, ?string $reference = null): void
    {
        if (! $this->required()) {
            return;
        }

        $row = DB::table('approval_requests')
            ->where('approvable_type', $type)
            ->where('approvable_id', $model->getKey())
            ->first();

        if ($row?->status === 'approved') {
            return;
        }

        if (! $row) {
            DB::table('approval_requests')->insert([
                'approvable_type' => $type,
                'approvable_id' => $model->getKey(),
                'reference_no' => $reference,
                'status' => 'pending',
                'requested_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } elseif ($row->status === 'rejected') {
            DB::table('approval_requests')->where('id', $row->id)->update([
                'status' => 'pending',
                'requested_by' => auth()->id(),
                'decided_by' => null,
                'decided_at' => null,
                'decision_notes' => null,
                'updated_at' => now(),
            ]);
        }

        throw ValidationException::withMessages([
            'approval' => __('Approval is required before posting. A pending approval request is available under Production Controls → Approvals.'),
        ]);
    }
}
