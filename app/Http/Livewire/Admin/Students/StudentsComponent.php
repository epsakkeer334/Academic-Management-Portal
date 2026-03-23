<?php

namespace App\Http\Livewire\Admin\Students;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Admin\Student;
use App\Models\Admin\Institute;
use App\Models\Admin\State;
use App\Models\Admin\Country;

class StudentsComponent extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // Form fields
    public $name, $email, $phone, $dob, $gender, $address, $city, $state_id, $country_id, $pincode;
    public $qualification, $course_applied, $institute_id;
    public $father_name, $father_phone, $mother_name, $mother_phone;
    public $er_status = 'pending';
    public $recordId;
    public $isEdit = false;
    public $confirmingDeleteId = null;

    // File uploads
    public $kyc_documents = [];
    public $certificates = [];

    // UI state
    public $search = '';
    public $filter = 'All';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Stats
    public $totalStudents = 0;
    public $approvedStudents = 0;
    public $pendingStudents = 0;
    public $rejectedStudents = 0;

    protected $listeners = [
        'editRecord' => 'edit',
        'deleteRecord' => 'confirmDelete',
        'refreshTable' => '$refresh',
    ];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state_id' => 'required|exists:states,id',
            'country_id' => 'required|exists:countries,id',
            'pincode' => 'required|string|max:10',
            'qualification' => 'required|string|max:255',
            'course_applied' => 'required|string|max:255',
            'institute_id' => 'required|exists:institutes,id',
            'father_name' => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|max:15',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone' => 'nullable|string|max:15',
            'er_status' => 'nullable|in:pending,approved,rejected,generated',
        ];

        if (!$this->isEdit) {
            $rules['email'] .= '|unique:students,email';
        } else {
            $rules['email'] .= '|unique:students,email,' . $this->recordId;
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Student name is required.',
        'email.required' => 'Email address is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'This email is already registered.',
        'phone.required' => 'Phone number is required.',
        'dob.required' => 'Date of birth is required.',
        'gender.required' => 'Gender is required.',
        'address.required' => 'Address is required.',
        'city.required' => 'City is required.',
        'state_id.required' => 'State is required.',
        'country_id.required' => 'Country is required.',
        'pincode.required' => 'Pincode is required.',
        'qualification.required' => 'Qualification is required.',
        'course_applied.required' => 'Course applied is required.',
        'institute_id.required' => 'Institute is required.',
        'institute_id.exists' => 'Selected institute is invalid.',
    ];

    public function mount()
    {
        $this->loadStats();
    }

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
        $this->dispatchBrowserEvent('open-student-modal');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->isEdit = true;

        $student = Student::with(['institute', 'state', 'country'])->findOrFail($id);

        $this->fill([
            'recordId' => $student->id,
            'name' => $student->name,
            'email' => $student->email,
            'phone' => $student->phone,
            'dob' => $student->dob instanceof \DateTime ? $student->dob->format('Y-m-d') : $student->dob,
            'gender' => $student->gender,
            'address' => $student->address,
            'city' => $student->city,
            'state_id' => $student->state_id,
            'country_id' => $student->country_id,
            'pincode' => $student->pincode,
            'qualification' => $student->qualification,
            'course_applied' => $student->course_applied,
            'institute_id' => $student->institute_id,
            'father_name' => $student->father_name,
            'father_phone' => $student->father_phone,
            'mother_name' => $student->mother_name,
            'mother_phone' => $student->mother_phone,
            'er_status' => $student->er_status ?? 'pending',
        ]);

        $this->dispatchBrowserEvent('open-student-modal');
    }

    public function closeModal()
    {
        $this->resetFields();
        $this->dispatchBrowserEvent('close-student-modal');
    }

    protected function resetFields()
    {
        $this->reset([
            'recordId', 'name', 'email', 'phone', 'dob', 'gender', 'address', 'city',
            'state_id', 'country_id', 'pincode', 'qualification', 'course_applied',
            'institute_id', 'father_name', 'father_phone', 'mother_name', 'mother_phone',
            'er_status', 'kyc_documents', 'certificates', 'isEdit'
        ]);
        $this->er_status = 'pending';
    }

    protected function storeDocuments($documents, $folder)
    {
        $uploadedFiles = [];

        if ($documents && is_array($documents)) {
            foreach ($documents as $document) {
                $fileName = time() . '_' . uniqid() . '.' . $document->getClientOriginalExtension();
                $document->storeAs("public/students/{$folder}", $fileName);
                $uploadedFiles[] = $fileName;
            }
        }

        return $uploadedFiles;
    }

    public function save()
    {
        $this->validate();

        $data = [
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
            'father_name' => $this->father_name,
            'father_phone' => $this->father_phone,
            'mother_name' => $this->mother_name,
            'mother_phone' => $this->mother_phone,
            'er_status' => $this->er_status,
        ];

        // Handle file uploads if any
        if (!empty($this->kyc_documents)) {
            $data['kyc_documents'] = $this->storeDocuments($this->kyc_documents, 'kyc');
        }

        if (!empty($this->certificates)) {
            $data['certificates'] = $this->storeDocuments($this->certificates, 'certificates');
        }

        Student::create($data);

        $this->closeModal();
        $this->loadStats();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Student created successfully.',
        ]);
    }

    public function update()
    {
        $this->validate();

        $student = Student::findOrFail($this->recordId);

        $data = [
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
            'father_name' => $this->father_name,
            'father_phone' => $this->father_phone,
            'mother_name' => $this->mother_name,
            'mother_phone' => $this->mother_phone,
            'er_status' => $this->er_status,
        ];

        // Handle file uploads if any
        if (!empty($this->kyc_documents)) {
            $data['kyc_documents'] = $this->storeDocuments($this->kyc_documents, 'kyc');
        }

        if (!empty($this->certificates)) {
            $data['certificates'] = $this->storeDocuments($this->certificates, 'certificates');
        }

        $student->update($data);

        $this->closeModal();
        $this->loadStats();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Student updated successfully.',
        ]);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeleteId = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function delete()
    {
        $student = Student::find($this->confirmingDeleteId);

        if ($student) {
            $student->delete();

            $this->loadStats();
            $this->emit('refreshTable');
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'danger',
                'message' => 'Student deleted successfully.',
            ]);
        }

        $this->confirmingDeleteId = null;
        $this->dispatchBrowserEvent('close-delete-modal');
    }

    protected function loadStats()
    {
        $this->totalStudents = Student::count();
        $this->approvedStudents = Student::where('er_status', 'approved')->count();
        $this->pendingStudents = Student::where('er_status', 'pending')->count();
        $this->rejectedStudents = Student::where('er_status', 'rejected')->count();
    }

    public function render()
    {
        return view('livewire.admin.students.students-component', [
            'institutes' => Institute::active()->get(),
            'states' => State::orderBy('name')->get(),
            'countries' => Country::orderBy('name')->get(),
        ])->layout('layouts.admin.master');
    }
}
