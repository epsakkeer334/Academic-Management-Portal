<?php

namespace App\Http\Livewire\Admin\Reports;

use App\Models\Admin\Student;
use App\Models\Admin\Exam;
use App\Models\Admin\Document;
use App\Models\Admin\Mou;
use Livewire\Component;

class ReportsComponent extends Component
{
    public $reportType = 'students';

    public function render()
    {
        $data = [];

        switch ($this->reportType) {
            case 'students':
                $data = [
                    'total_students' => Student::count(),
                    'approved_students' => Student::where('er_status', 'approved')->count(),
                    'pending_students' => Student::where('er_status', 'pending')->count(),
                ];
                break;
            case 'exams':
                $data = [
                    'total_exams' => Exam::count(),
                    'completed_exams' => Exam::where('status', 'completed')->count(),
                    'upcoming_exams' => Exam::where('exam_date', '>=', now())->count(),
                ];
                break;
            case 'documents':
                $data = [
                    'total_documents' => Document::count(),
                    'archived_documents' => Document::where('status', 'final_archived')->count(),
                ];
                break;
            case 'mous':
                $data = [
                    'total_mous' => Mou::count(),
                    'active_mous' => Mou::where('status', 'active')->count(),
                    'expiring_mous' => Mou::where('status', 'active')->where('validity_end', '<=', now()->addDays(60))->count(),
                ];
                break;
        }

        return view('livewire.admin.reports.reports-component', [
            'data' => $data,
        ])->layout('layouts.admin.master');
    }
}
