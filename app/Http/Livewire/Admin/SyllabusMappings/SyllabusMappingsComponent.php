<?php

namespace App\Http\Livewire\Admin\SyllabusMappings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admin\SyllabusMapping;
use App\Models\Admin\Curriculum;
use App\Models\Admin\Subject;
use App\Models\Admin\Institute;

class SyllabusMappingsComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Form fields
    public $curriculum_id, $subject_id, $institute_id;
    public $coverage_percentage, $status, $start_date, $planned_completion, $remarks;
    public $recordId;
    public $isEdit = false;
    public $confirmingDeleteId = null;

    // UI state
    public $search = '';
    public $filter = 'All';
    public $sortField = 'start_date';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $listeners = [
        'editRecord' => 'edit',
        'deleteRecord' => 'confirmDelete',
        'refreshTable' => '$refresh',
    ];

    protected function rules()
    {
        $rules = [
            'curriculum_id' => 'required|exists:curriculums,id',
            'subject_id' => 'required|exists:subjects,id',
            'institute_id' => 'required|exists:institutes,id',
            'coverage_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:in_progress,completed',
            'start_date' => 'required|date',
            'planned_completion' => 'required|date|after_or_equal:start_date',
            'remarks' => 'nullable|string|max:500',
        ];

        return $rules;
    }

    protected $messages = [
        'curriculum_id.required' => 'Curriculum is required.',
        'curriculum_id.exists' => 'Selected curriculum is invalid.',
        'subject_id.required' => 'Subject is required.',
        'subject_id.exists' => 'Selected subject is invalid.',
        'institute_id.required' => 'Institute is required.',
        'institute_id.exists' => 'Selected institute is invalid.',
        'coverage_percentage.required' => 'Coverage percentage is required.',
        'coverage_percentage.numeric' => 'Coverage percentage must be a number.',
        'coverage_percentage.min' => 'Coverage percentage must be at least 0.',
        'coverage_percentage.max' => 'Coverage percentage cannot exceed 100.',
        'status.required' => 'Status is required.',
        'start_date.required' => 'Start date is required.',
        'planned_completion.required' => 'Planned completion date is required.',
        'planned_completion.after_or_equal' => 'Planned completion date must be after or equal to start date.',
        'remarks.max' => 'Remarks must not exceed 500 characters.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->resetFields();
        $this->isEdit = false;
        $this->dispatchBrowserEvent('open-syllabus-modal');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->isEdit = true;

        $mapping = SyllabusMapping::with(['curriculum', 'subject', 'institute'])->findOrFail($id);

        $this->fill([
            'recordId' => $mapping->id,
            'curriculum_id' => $mapping->curriculum_id,
            'subject_id' => $mapping->subject_id,
            'institute_id' => $mapping->institute_id,
            'coverage_percentage' => $mapping->coverage_percentage,
            'status' => $mapping->status,
            'start_date' => $mapping->start_date instanceof \DateTime ? $mapping->start_date->format('Y-m-d') : $mapping->start_date,
            'planned_completion' => $mapping->planned_completion instanceof \DateTime ? $mapping->planned_completion->format('Y-m-d') : $mapping->planned_completion,
            'remarks' => $mapping->remarks,
        ]);

        $this->dispatchBrowserEvent('open-syllabus-modal');
    }

    public function closeModal()
    {
        $this->resetFields();
        $this->dispatchBrowserEvent('close-syllabus-modal');
    }

    protected function resetFields()
    {
        $this->reset([
            'recordId', 'curriculum_id', 'subject_id', 'institute_id',
            'coverage_percentage', 'status', 'start_date', 'planned_completion',
            'remarks', 'isEdit'
        ]);
        $this->status = 'in_progress';
        $this->coverage_percentage = 0;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'curriculum_id' => $this->curriculum_id,
            'subject_id' => $this->subject_id,
            'institute_id' => $this->institute_id,
            'coverage_percentage' => $this->coverage_percentage,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'planned_completion' => $this->planned_completion,
            'remarks' => $this->remarks,
            'created_by' => auth()->id(),
        ];

        SyllabusMapping::create($data);

        $this->closeModal();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Syllabus mapping created successfully.',
        ]);
    }

    public function update()
    {
        $this->validate();

        $mapping = SyllabusMapping::findOrFail($this->recordId);

        $data = [
            'curriculum_id' => $this->curriculum_id,
            'subject_id' => $this->subject_id,
            'institute_id' => $this->institute_id,
            'coverage_percentage' => $this->coverage_percentage,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'planned_completion' => $this->planned_completion,
            'remarks' => $this->remarks,
            'updated_by' => auth()->id(),
        ];

        $mapping->update($data);

        $this->closeModal();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Syllabus mapping updated successfully.',
        ]);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeleteId = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function delete()
    {
        $mapping = SyllabusMapping::find($this->confirmingDeleteId);

        if ($mapping) {
            $mapping->delete();

            $this->emit('refreshTable');
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'danger',
                'message' => 'Syllabus mapping deleted successfully.',
            ]);
        }

        $this->confirmingDeleteId = null;
        $this->dispatchBrowserEvent('close-delete-modal');
    }

    public function render()
    {
        return view('livewire.admin.syllabus-mappings.syllabus-mappings-component', [
            'curriculums' => Curriculum::active()->orderBy('name')->get(),
            'subjects' => Subject::active()->orderBy('name')->get(),
            'institutes' => Institute::active()->orderBy('name')->get(),
        ])->layout('layouts.admin.master');
    }
}
