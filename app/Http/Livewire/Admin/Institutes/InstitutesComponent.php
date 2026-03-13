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

class InstitutesComponent extends Component
{
    use WithPagination, WithFileUploads, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public $name, $code, $email, $phone, $address, $city, $state_id, $country_id = 1;
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
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:institutes,code,' . $this->recordId,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state_id' => 'nullable|exists:states,id',
            'country_id' => 'required|exists:countries,id',
            'pincode' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
            'status' => 'boolean',
        ];

        if (!$this->isEdit || $this->logo) {
            $rules['logo'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'name.required' => 'Institute name is required.',
            'code.required' => 'Institute code is required.',
            'code.unique' => 'This institute code is already taken.',
            'email.email' => 'Please enter a valid email address.',
            'state_id.exists' => 'Selected state is invalid.',
            'country_id.required' => 'Country is required.',
            'country_id.exists' => 'Selected country is invalid.',
            'logo.image' => 'The logo must be an image.',
            'logo.mimes' => 'The logo must be a jpeg, png, jpg, gif, or webp.',
            'logo.max' => 'The logo size must not exceed 2MB.',
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
        $this->country_id = 1; // Default to India
        $this->dispatchBrowserEvent('open-institute-modal');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->isEdit = true;

        $institute = Institute::findOrFail($id);

        $this->fill([
            'recordId' => $institute->id,
            'name' => $institute->name,
            'code' => $institute->code,
            'email' => $institute->email,
            'phone' => $institute->phone,
            'address' => $institute->address,
            'city' => $institute->city,
            'state_id' => $institute->state_id,
            'country_id' => $institute->country_id ?? 1,
            'pincode' => $institute->pincode,
            'website' => $institute->website,
            'status' => $institute->status ? 1 : 0,
        ]);

        $this->dispatchBrowserEvent('open-institute-modal');
    }

    public function closeModal()
    {
        $this->resetFields();
        $this->dispatchBrowserEvent('close-institute-modal');
    }

    protected function resetFields()
    {
        $this->reset([
            'recordId', 'name', 'code', 'email', 'phone', 'address', 'city',
            'state_id', 'country_id', 'pincode', 'logo', 'website', 'status', 'isEdit'
        ]);
        $this->country_id = 1;
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
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state_id' => $this->state_id,
            'country_id' => $this->country_id,
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
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state_id' => $this->state_id,
            'country_id' => $this->country_id,
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
        $query = Institute::with(['state', 'country'])
            ->search($this->search)
            ->byState($this->stateFilter)
            ->byCountry(1); // Default to India

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
