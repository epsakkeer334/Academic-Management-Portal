<?php

namespace App\Http\Livewire\Admin\Courses;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Admin\Course;
use Illuminate\Support\Facades\Storage;

class CoursesComponent extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $name, $description, $duration, $image, $status = 1;
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
            'description' => 'nullable|string',
            'duration' => 'nullable|string|max:50',
            'status' => 'boolean',
        ];

        if (!$this->isEdit || $this->image) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Course name is required.',
        'name.max' => 'Name must not exceed 255 characters.',
        'duration.max' => 'Duration must not exceed 50 characters.',
        'image.required' => 'Course image is required.',
        'image.image' => 'The course must be an image.',
        'image.mimes' => 'The course must be a jpeg, png, jpg, gif, or webp.',
        'image.max' => 'The image size must not exceed 2MB.',
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
        $this->dispatchBrowserEvent('open-course-modal');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->isEdit = true;

        $course = Course::findOrFail($id);

        $this->fill([
            'recordId' => $course->id,
            'name' => $course->name,
            'description' => $course->description,
            'duration' => $course->duration,
            'status' => $course->status ? 1 : 0,
        ]);

        $this->dispatchBrowserEvent('open-course-modal');
    }

    public function closeModal()
    {
        $this->resetFields();
        $this->dispatchBrowserEvent('close-course-modal');
    }

    protected function resetFields()
    {
        $this->reset([
            'recordId', 'name', 'description', 'duration', 'image', 'status', 'isEdit'
        ]);
        $this->status = 1;
    }

    protected function storeImage($image)
    {
        // Generate unique filename
        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

        // Store the image
        $image->storeAs('public/courses', $imageName);

        return $imageName;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'duration' => $this->duration,
            'status' => $this->status,
        ];

        // Handle course image upload
        if ($this->image) {
            $imageName = $this->storeImage($this->image);
            $data['image'] = $imageName;
        }

        Course::create($data);

        $this->closeModal();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Course created successfully.',
        ]);
    }

    public function update()
    {
        $this->validate();

        $course = Course::findOrFail($this->recordId);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'duration' => $this->duration,
            'status' => $this->status,
        ];

        // Handle course image upload
        if ($this->image) {
            // Delete old course image
            if ($course->image) {
                Storage::delete('public/courses/' . $course->image);
            }
            $imageName = $this->storeImage($this->image);
            $data['image'] = $imageName;
        }

        $course->update($data);

        $this->closeModal();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Course updated successfully.',
        ]);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeleteId = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function delete()
    {
        $course = Course::find($this->confirmingDeleteId);

        if ($course) {
            // Delete course image if exists
            if ($course->image) {
                Storage::delete('public/courses/' . $course->image);
            }

            $course->delete();

            $this->emit('refreshTable');
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'danger',
                'message' => 'Course deleted successfully.',
            ]);
        }

        $this->confirmingDeleteId = null;
        $this->dispatchBrowserEvent('close-delete-modal');
    }

    public function getCoursesProperty()
    {
        $query = Course::query();

        // Filter
        if ($this->filter !== 'All') {
            $query->where('status', $this->filter === 'Active' ? 1 : 0);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhere('duration', 'like', "%{$this->search}%");
            });
        }

        // Sort
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin.courses.courses-component', [
            'courses' => $this->courses,
        ])->layout('layouts.admin.master');
    }
}
