<?php

namespace App\Http\Livewire\Admin\Curriculums;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admin\Curriculum;
use App\Models\Admin\Course;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CurriculumsComponent extends Component
{
    use WithPagination, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public $course_id, $name, $status = 1;
    public $recordId;
    public $isEdit = false;
    public $confirmingDeleteId = null;

    // UI state
    public $search = '';
    public $filter = 'All';
    public $courseFilter = '';
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
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'status' => 'boolean',
        ];

        // Add unique rule for curriculum name per course
        if ($this->isEdit) {
            $rules['name'] = 'required|string|max:255|unique:curriculums,name,' . $this->recordId . ',id,course_id,' . $this->course_id;
        } else {
            $rules['name'] = 'required|string|max:255|unique:curriculums,name,NULL,id,course_id,' . $this->course_id;
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'course_id.required' => 'Please select a course.',
            'course_id.exists' => 'Selected course is invalid.',
            'name.required' => 'Curriculum name is required.',
            'name.unique' => 'This curriculum name already exists for the selected course.',
            'name.max' => 'Name must not exceed 255 characters.',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function updatingCourseFilter()
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
        $this->dispatchBrowserEvent('open-curriculum-modal');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->isEdit = true;

        $curriculum = Curriculum::findOrFail($id);

        $this->fill([
            'recordId' => $curriculum->id,
            'course_id' => $curriculum->course_id,
            'name' => $curriculum->name,
            'status' => $curriculum->status ? 1 : 0,
        ]);

        $this->dispatchBrowserEvent('open-curriculum-modal');
    }

    public function closeModal()
    {
        $this->resetFields();
        $this->dispatchBrowserEvent('close-curriculum-modal');
    }

    protected function resetFields()
    {
        $this->reset([
            'recordId', 'course_id', 'name', 'status', 'isEdit'
        ]);
        $this->status = 1;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        Curriculum::create([
            'course_id' => $this->course_id,
            'name' => $this->name,
            'status' => $this->status,
        ]);

        $this->closeModal();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Curriculum created successfully.',
        ]);
    }

    public function update()
    {
        $this->validate();

        $curriculum = Curriculum::findOrFail($this->recordId);

        $curriculum->update([
            'course_id' => $this->course_id,
            'name' => $this->name,
            'status' => $this->status,
        ]);

        $this->closeModal();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Curriculum updated successfully.',
        ]);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeleteId = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function delete()
    {
        $curriculum = Curriculum::find($this->confirmingDeleteId);

        if ($curriculum) {
            $curriculum->delete();

            $this->emit('refreshTable');
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'danger',
                'message' => 'Curriculum deleted successfully.',
            ]);
        }

        $this->confirmingDeleteId = null;
        $this->dispatchBrowserEvent('close-delete-modal');
    }

    // Toggle status
    public function toggleStatus($id)
    {
        $curriculum = Curriculum::find($id);

        if ($curriculum) {
            $curriculum->update([
                'status' => !$curriculum->status
            ]);

            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'success',
                'message' => 'Curriculum status updated successfully.',
            ]);
        }
    }

    // Get curriculums with filters
    public function getCurriculumsProperty()
    {
        $query = Curriculum::with('course')->search($this->search)->byCourse($this->courseFilter);

        // Status filter
        if ($this->filter !== 'All') {
            $query->where('status', $this->filter === 'Active' ? 1 : 0);
        }

        // Sort
        if ($this->sortField === 'course') {
            $query->orderBy(Course::select('name')->whereColumn('courses.id', 'curriculums.course_id'), $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        return $query->paginate($this->perPage);
    }

    // Get all courses for dropdown
    public function getCoursesProperty()
    {
        return Course::where('status', true)->orderBy('name')->get(['id', 'name']);
    }

    // Get counts for stats
    public function getStatsProperty()
    {
        return [
            'total' => Curriculum::count(),
            'active' => Curriculum::where('status', true)->count(),
            'inactive' => Curriculum::where('status', false)->count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.curriculums.curriculums-component', [
            'curriculums' => $this->curriculums,
            'courses' => $this->courses,
            'stats' => $this->stats,
        ])->layout('layouts.admin.master');
    }
}
