<?php

namespace App\Http\Livewire\Admin\Subjects;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admin\Subject;
use App\Models\Admin\Course;
use App\Models\Admin\Curriculum;

class SubjectsComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Form fields
    public $name, $code, $description, $course_id, $curriculum_id, $credits, $type;
    public $is_active = true;
    public $recordId;
    public $isEdit = false;
    public $confirmingDeleteId = null;

    // UI state
    public $search = '';
    public $filter = 'All';
    public $sortField = 'name';
    public $sortDirection = 'asc';
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
            'description' => 'nullable|string|max:1000',
            'course_id' => 'required|exists:courses,id',
            'curriculum_id' => 'required|exists:curriculums,id',
            'credits' => 'required|integer|min:0|max:10',
            'type' => 'required|in:theory,practical,lab,project',
            'is_active' => 'boolean',
        ];

        if (!$this->isEdit) {
            $rules['code'] .= '|unique:subjects,code';
        } else {
            $rules['code'] .= '|unique:subjects,code,' . $this->recordId;
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Subject name is required.',
        'name.max' => 'Name must not exceed 255 characters.',
        'code.required' => 'Subject code is required.',
        'code.unique' => 'This subject code already exists.',
        'code.max' => 'Code must not exceed 50 characters.',
        'course_id.required' => 'Course is required.',
        'course_id.exists' => 'Selected course is invalid.',
        'curriculum_id.required' => 'Curriculum is required.',
        'curriculum_id.exists' => 'Selected curriculum is invalid.',
        'credits.required' => 'Credits are required.',
        'credits.integer' => 'Credits must be a whole number.',
        'credits.min' => 'Credits must be at least 0.',
        'credits.max' => 'Credits cannot exceed 10.',
        'type.required' => 'Subject type is required.',
        'type.in' => 'Please select a valid subject type.',
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
        $this->dispatchBrowserEvent('open-subject-modal');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->isEdit = true;

        $subject = Subject::with(['course', 'curriculum'])->findOrFail($id);

        $this->fill([
            'recordId' => $subject->id,
            'name' => $subject->name,
            'code' => $subject->code,
            'description' => $subject->description,
            'course_id' => $subject->course_id,
            'curriculum_id' => $subject->curriculum_id,
            'credits' => $subject->credits,
            'type' => $subject->type,
            'is_active' => $subject->is_active,
        ]);

        $this->dispatchBrowserEvent('open-subject-modal');
    }

    public function closeModal()
    {
        $this->resetFields();
        $this->dispatchBrowserEvent('close-subject-modal');
    }

    protected function resetFields()
    {
        $this->reset([
            'recordId', 'name', 'code', 'description', 'course_id',
            'curriculum_id', 'credits', 'type', 'is_active', 'isEdit'
        ]);
        $this->is_active = true;
        $this->credits = 3;
        $this->type = 'theory';
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'course_id' => $this->course_id,
            'curriculum_id' => $this->curriculum_id,
            'credits' => $this->credits,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'created_by' => auth()->id(),
        ];

        Subject::create($data);

        $this->closeModal();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Subject created successfully.',
        ]);
    }

    public function update()
    {
        $this->validate();

        $subject = Subject::findOrFail($this->recordId);

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'course_id' => $this->course_id,
            'curriculum_id' => $this->curriculum_id,
            'credits' => $this->credits,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'updated_by' => auth()->id(),
        ];

        $subject->update($data);

        $this->closeModal();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Subject updated successfully.',
        ]);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeleteId = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function delete()
    {
        $subject = Subject::find($this->confirmingDeleteId);

        if ($subject) {
            $subject->delete();

            $this->emit('refreshTable');
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'danger',
                'message' => 'Subject deleted successfully.',
            ]);
        }

        $this->confirmingDeleteId = null;
        $this->dispatchBrowserEvent('close-delete-modal');
    }

    public function render()
    {
        return view('livewire.admin.subjects.subjects-component', [
            'courses' => Course::active()->orderBy('name')->get(),
            'curriculums' => Curriculum::active()->orderBy('name')->get(),
        ])->layout('layouts.admin.master');
    }
}
