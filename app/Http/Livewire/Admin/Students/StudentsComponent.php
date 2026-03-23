<?php

namespace App\Http\Livewire\Admin\Students;

use App\Models\Admin\Student;
use App\Models\Admin\Institute;
use Livewire\Component;
use Livewire\WithPagination;

class StudentsComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $instituteFilter = '';
    public $statusFilter = '';
    public $perPage = 10;

    public $showModal = false;
    public $editing = false;
    public $studentId;

    public $name, $email, $phone, $dob, $gender, $address, $city, $state_id, $country_id, $pincode;
    public $qualification, $course_applied, $institute_id;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:students,email',
        'phone' => 'required|string|max:15',
        'dob' => 'required|date',
        'gender' => 'required|in:male,female,other',
        'address' => 'required|string',
        'city' => 'required|string',
        'state_id' => 'required|exists:states,id',
        'country_id' => 'required|exists:countries,id',
        'pincode' => 'required|string|max:10',
        'qualification' => 'required|string',
        'course_applied' => 'required|string',
        'institute_id' => 'required|exists:institutes,id',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingInstituteFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $students = Student::query()
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('er_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->instituteFilter, function($query) {
                $query->where('institute_id', $this->instituteFilter);
            })
            ->when($this->statusFilter, function($query) {
                $query->where('er_status', $this->statusFilter);
            })
            ->with(['institute', 'state', 'country'])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $institutes = Institute::active()->get();

        return view('livewire.admin.students.students-component', [
            'students' => $students,
            'institutes' => $institutes,
        ])->layout('layouts.admin.master');
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->editing = false;
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $this->studentId = $id;
        $this->name = $student->name;
        $this->email = $student->email;
        $this->phone = $student->phone;
        $this->dob = $student->dob;
        $this->gender = $student->gender;
        $this->address = $student->address;
        $this->city = $student->city;
        $this->state_id = $student->state_id;
        $this->country_id = $student->country_id;
        $this->pincode = $student->pincode;
        $this->qualification = $student->qualification;
        $this->course_applied = $student->course_applied;
        $this->institute_id = $student->institute_id;

        $this->showModal = true;
        $this->editing = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editing) {
            $student = Student::findOrFail($this->studentId);
            $student->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'dob' => $this->dob,
                'gender' => $this->gender,
                'address' => $this->address,
                'city' => $this->city,
                'state_id' => $this->state_id,
                'country_id' => $this->country_id,
                'pincode' => $this->pincode,
                'qualification' => $this->qualification,
                'course_applied' => $this->course_applied,
                'institute_id' => $this->institute_id,
            ]);
            session()->flash('message', 'Student updated successfully.');
        } else {
            Student::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'dob' => $this->dob,
                'gender' => $this->gender,
                'address' => $this->address,
                'city' => $this->city,
                'state_id' => $this->state_id,
                'country_id' => $this->country_id,
                'pincode' => $this->pincode,
                'qualification' => $this->qualification,
                'course_applied' => $this->course_applied,
                'institute_id' => $this->institute_id,
            ]);
            session()->flash('message', 'Student created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        Student::findOrFail($id)->delete();
        session()->flash('message', 'Student deleted successfully.');
    }

    private function resetForm()
    {
        $this->studentId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->dob = '';
        $this->gender = '';
        $this->address = '';
        $this->city = '';
        $this->state_id = '';
        $this->country_id = '';
        $this->pincode = '';
        $this->qualification = '';
        $this->course_applied = '';
        $this->institute_id = '';
    }
}
