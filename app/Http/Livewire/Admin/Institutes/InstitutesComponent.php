<?php

namespace App\Http\Livewire\Admin\Institutes;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Admin\Institute;
use App\Models\Admin\Country;
use App\Models\Admin\State;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

class InstitutesComponent extends Component
{
    use WithPagination, WithFileUploads, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    // Institute properties
    public $name, $code, $email, $phone, $address, $city, $state, $country = 1;
    public $pincode, $logo, $website, $status = 1;
    public $recordId;
    public $isEdit = false;
    public $confirmingDeleteId = null;

    // UI state
    public $search = '';
    public $filter = 'All';
    public $stateFilter = '';
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
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('institutes', 'name')->ignore($this->recordId)->whereNull('deleted_at'),
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('institutes', 'code')->ignore($this->recordId)->whereNull('deleted_at'),
            ],
            'email' => [
                'required',
                'email:rfc',
                'max:255',
                Rule::unique('institutes', 'email')->ignore($this->recordId)->whereNull('deleted_at'),
            ],
            'phone' => [
                'required',
                'digits:10',
                Rule::unique('institutes', 'phone')->ignore($this->recordId)->whereNull('deleted_at'),
            ],
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|exists:states,id',
            'country' => 'required|exists:countries,id',
            'pincode' => 'nullable|digits_between:4,10',
            'website' => 'nullable|url|max:255',
            'status' => 'boolean',

            'logo' => $this->isEdit
                ? 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
                : 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    protected function messages()
    {
        return [

            'name.required' => 'Institute name is required.',
            'name.unique' => 'This institute name already exists.',

            'code.required' => 'Institute code is required.',
            'code.unique' => 'This institute code already exists.',

            'email.email' => 'Enter a valid email address.',
            'email.unique' => 'This email already exists.',

            'phone.digits' => 'Phone number must be exactly 10 digits.',
            'phone.unique' => 'This phone number already exists.',

            'state.exists' => 'Selected state is invalid.',
            'country.required' => 'Country is required.',
            'country.exists' => 'Selected country is invalid.',

            'pincode.digits_between' => 'Pincode must be between 4 and 10 digits.',

            'website.url' => 'Enter a valid website URL.',

            'logo.image' => 'Logo must be an image.',
            'logo.mimes' => 'Logo must be jpeg, png, jpg, gif or webp.',
            'logo.max' => 'Logo must be less than 2MB.',
        ];
    }

    // Real-time validation
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rules());
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function updatingStateFilter()
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
        $this->country = 1; // Default to India
        $this->dispatchBrowserEvent('open-institute-modal');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->isEdit = true;

        $institute = Institute::with(['state', 'country'])->findOrFail($id);

        $this->fill([
            'recordId' => $institute->id,
            'name' => $institute->name,
            'code' => $institute->code,
            'email' => $institute->email,
            'phone' => $institute->phone,
            'address' => $institute->address,
            'city' => $institute->city,
            'state' => $institute->state,
            'country' => $institute->country ?? 1,
            'pincode' => $institute->pincode,
            'website' => $institute->website,
            'status' => $institute->status ? 1 : 0,
        ]);

        $this->dispatchBrowserEvent('open-institute-modal');
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->resetFields();
        $this->dispatchBrowserEvent('close-institute-modal');
    }

    protected function resetFields()
    {
        $this->reset([
            'recordId', 'name', 'code', 'email', 'phone', 'address', 'city',
            'state', 'country', 'pincode', 'logo', 'website', 'status', 'isEdit'
        ]);
        $this->country = 1;
        $this->status = 1;
    }

    protected function storeLogo($logo)
    {
        $logoName = time() . '_' . uniqid() . '.' . $logo->getClientOriginalExtension();
        $logo->storeAs('public/institutes', $logoName);
        return $logoName;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'pincode' => $this->pincode,
            'website' => $this->website,
            'status' => $this->status,
        ];

        if ($this->logo) {
            $data['logo'] = $this->storeLogo($this->logo);
        }

        Institute::create($data);

        $this->closeModal();
        $this->emit('refreshTable');

        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Institute created successfully.',
        ]);
    }

    public function update()
    {
        $this->validate();

        $institute = Institute::findOrFail($this->recordId);

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'pincode' => $this->pincode,
            'website' => $this->website,
            'status' => $this->status,
        ];

        if ($this->logo) {

            if ($institute->logo) {
                Storage::delete('public/institutes/' . $institute->logo);
            }

            $data['logo'] = $this->storeLogo($this->logo);
        }

        $institute->update($data);

        $this->closeModal();
        $this->emit('refreshTable');

        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Institute updated successfully.',
        ]);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeleteId = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function delete()
    {
        $institute = Institute::find($this->confirmingDeleteId);

        if ($institute) {
            if ($institute->logo) {
                Storage::delete('public/institutes/' . $institute->logo);
            }

            $institute->delete();

            $this->emit('refreshTable');
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'danger',
                'message' => 'Institute deleted successfully.',
            ]);
        }

        $this->confirmingDeleteId = null;
        $this->dispatchBrowserEvent('close-delete-modal');
    }

    public function toggleStatus($id)
    {
        $institute = Institute::find($id);

        if ($institute) {
            $institute->update([
                'status' => !$institute->status
            ]);

            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'success',
                'message' => 'Institute status updated successfully.',
            ]);
        }
    }

    // Get institutes with filters
    public function getInstitutesProperty()
    {
        $query = Institute::with(['state', 'country', 'users'])
            ->search($this->search)
            ->byState($this->stateFilter);

        if ($this->filter !== 'All') {
            $query->where('status', $this->filter === 'Active' ? 1 : 0);
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    // Get all states for dropdown
    public function getStatesProperty()
    {
        return State::where('status', true)
            ->where('country_id', 1) // India
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    // Get country (India)
    public function getCountryProperty()
    {
        return Country::find(1);
    }

    // Get counts for stats
    public function getStatsProperty()
    {
        return [
            'total' => Institute::count(),
            'active' => Institute::where('status', true)->count(),
            'inactive' => Institute::where('status', false)->count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.institutes.institutes-component', [
            'institutes' => $this->institutes,
            'states' => $this->states,
            'country' => $this->country,
            'stats' => $this->stats,
        ])->layout('layouts.admin.master');
    }
}
