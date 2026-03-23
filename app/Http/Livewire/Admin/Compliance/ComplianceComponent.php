<?php

namespace App\Http\Livewire\Admin\Compliance;

use App\Models\Admin\Document;
use App\Models\Admin\Mou;
use Livewire\Component;

class ComplianceComponent extends Component
{
    public function render()
    {
        $activeSops = Document::where('type', 'sop')->where('status', 'final_archived')->count();
        $pendingHotApprovals = Document::where('status', 'submitted_hot')->count();
        $pendingExternal = Document::where('status', 'submitted_external')->count();
        $mousExpiring = Mou::where('status', 'active')->where('validity_end', '<=', now()->addDays(60))->count();
        $revisionDue = Document::where('issue_date', '<=', now()->subMonths(12))->count();

        return view('livewire.admin.compliance.compliance-component', [
            'activeSops' => $activeSops,
            'pendingHotApprovals' => $pendingHotApprovals,
            'pendingExternal' => $pendingExternal,
            'mousExpiring' => $mousExpiring,
            'revisionDue' => $revisionDue,
        ])->layout('layouts.admin.master');
    }
}
