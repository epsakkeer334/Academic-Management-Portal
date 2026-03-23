<?php

namespace App\Http\Livewire\Admin\ExamApplications;

use App\Models\Admin\ExamApplication;
use Livewire\Component;
use Livewire\WithPagination;

class ExamApplicationsComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $applications = ExamApplication::query()
            ->when($this->search, function($query) {
                $query->whereHas('student', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                })->orWhereHas('exam', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->with(['student', 'exam'])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.exam-applications.exam-applications-component', [
            'applications' => $applications,
        ])->layout('layouts.admin.master');
    }

    public function approveApplication($id)
    {
        $application = ExamApplication::findOrFail($id);
        $application->update(['status' => 'approved']);
        session()->flash('message', 'Application approved successfully.');
    }

    public function rejectApplication($id)
    {
        $application = ExamApplication::findOrFail($id);
        $application->update(['status' => 'rejected']);
        session()->flash('message', 'Application rejected successfully.');
    }
}
