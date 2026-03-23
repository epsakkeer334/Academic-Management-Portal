<?php

namespace App\Http\Livewire\Admin\Exams;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Admin\Exam;
use App\Models\Admin\Institute;
use App\Models\Admin\Course;
use App\Models\Admin\Curriculum;

class ExamsComponent extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // Form fields
    public $name, $code, $type, $institute_id, $course_id, $curriculum_id;
    public $exam_date, $start_time, $end_time, $passing_percentage, $description;
    public $status = 'scheduled';
    public $recordId;
    public $isEdit = false;
    public $confirmingDeleteId = null;

    // UI state
    public $search = '';
    public $filter = 'All';
    public $sortField = 'exam_date';
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
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'type' => 'required|in:mid-sem,semester,practical',
            'institute_id' => 'required|exists:institutes,id',
            'course_id' => 'required|exists:courses,id',
            'curriculum_id' => 'required|exists:curriculums,id',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'passing_percentage' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ];

        if (!$this->isEdit) {
            $rules['code'] .= '|unique:exams,code';
        } else {
            $rules['code'] .= '|unique:exams,code,' . $this->recordId;
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Exam name is required.',
        'name.max' => 'Name must not exceed 255 characters.',
        'code.required' => 'Exam code is required.',
        'code.unique' => 'This exam code already exists.',
        'code.max' => 'Code must not exceed 50 characters.',
        'type.required' => 'Exam type is required.',
        'type.in' => 'Please select a valid exam type.',
        'institute_id.required' => 'Institute is required.',
        'institute_id.exists' => 'Selected institute is invalid.',
        'course_id.required' => 'Course is required.',
        'course_id.exists' => 'Selected course is invalid.',
        'curriculum_id.required' => 'Curriculum is required.',
        'curriculum_id.exists' => 'Selected curriculum is invalid.',
        'exam_date.required' => 'Exam date is required.',
        'exam_date.date' => 'Please provide a valid date.',
        'start_time.required' => 'Start time is required.',
        'end_time.required' => 'End time is required.',
        'passing_percentage.required' => 'Passing percentage is required.',
        'passing_percentage.numeric' => 'Passing percentage must be a number.',
        'passing_percentage.min' => 'Passing percentage must be at least 0.',
        'passing_percentage.max' => 'Passing percentage cannot exceed 100.',
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
        $this->dispatchBrowserEvent('open-exam-modal');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->isEdit = true;

        $exam = Exam::with(['institute', 'course', 'curriculum'])->findOrFail($id);

        $this->fill([
            'recordId' => $exam->id,
            'name' => $exam->name,
            'code' => $exam->code,
            'type' => $exam->type,
            'institute_id' => $exam->institute_id,
            'course_id' => $exam->course_id,
            'curriculum_id' => $exam->curriculum_id,
            'exam_date' => $exam->exam_date instanceof \DateTime ? $exam->exam_date->format('Y-m-d') : $exam->exam_date,
            'start_time' => $exam->start_time,
            'end_time' => $exam->end_time,
            'passing_percentage' => $exam->passing_percentage,
            'description' => $exam->description,
            'status' => $exam->status,
        ]);

        $this->dispatchBrowserEvent('open-exam-modal');
    }

    public function closeModal()
    {
        $this->resetFields();
        $this->dispatchBrowserEvent('close-exam-modal');
    }

    protected function resetFields()
    {
        $this->reset([
            'recordId', 'name', 'code', 'type', 'institute_id', 'course_id',
            'curriculum_id', 'exam_date', 'start_time', 'end_time',
            'passing_percentage', 'description', 'status', 'isEdit'
        ]);
        $this->status = 'scheduled';
        $this->passing_percentage = 75.00;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'institute_id' => $this->institute_id,
            'course_id' => $this->course_id,
            'curriculum_id' => $this->curriculum_id,
            'exam_date' => $this->exam_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'passing_percentage' => $this->passing_percentage,
            'description' => $this->description,
            'status' => $this->status,
        ];

        Exam::create($data);

        $this->closeModal();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Exam created successfully.',
        ]);
    }

    public function update()
    {
        $this->validate();

        $exam = Exam::findOrFail($this->recordId);

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'institute_id' => $this->institute_id,
            'course_id' => $this->course_id,
            'curriculum_id' => $this->curriculum_id,
            'exam_date' => $this->exam_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'passing_percentage' => $this->passing_percentage,
            'description' => $this->description,
            'status' => $this->status,
        ];

        $exam->update($data);

        $this->closeModal();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Exam updated successfully.',
        ]);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeleteId = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function delete()
    {
        $exam = Exam::find($this->confirmingDeleteId);

        if ($exam) {
            $exam->delete();

            $this->emit('refreshTable');
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'danger',
                'message' => 'Exam deleted successfully.',
            ]);
        }

        $this->confirmingDeleteId = null;
        $this->dispatchBrowserEvent('close-delete-modal');
    }

    public function render()
    {
        return view('livewire.admin.exams.exams-component', [
            'institutes' => Institute::active()->get(),
            'courses' => Course::active()->get(),
            'curriculums' => Curriculum::active()->get(),
        ])->layout('layouts.admin.master');
    }
}
