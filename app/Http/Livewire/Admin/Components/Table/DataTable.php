<?php

namespace App\Http\Livewire\Admin\Components\Table;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class DataTable extends Component
{
    use WithPagination;

    public $columns = [];
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $filters = [];
    public $filter = 'All Status';
    public $modelClass;
    public $actionView = null;
    public $title = 'Data List';

    // Vendor filtering properties
    public $vendorFilterEnabled = false;
    public $vendorId = null;
    public $showVendorColumn = false;

    // Soft delete properties
    public bool $showDeletedButton = false;
    public ?string $deletedModelClass = null;
    public array $deletedColumns = [];
    public string $deletedTitle = 'Deleted Records';
    public array $deletedConfig = [];

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['refreshTable' => '$refresh', 'filterUpdated' => 'setFilter'];

    // --- Mount component ---
    public function mount($modelClass, $columns, $filters = [], $actionView = null, $title = null, $vendorFilterEnabled = false, $showVendorColumn = null)
    {
        $this->modelClass = $modelClass;
        $this->columns = $columns;
        $this->filters = $filters;
        $this->actionView = $actionView;
        if ($title) $this->title = $title;

        $this->deletedConfig = $this->deletedConfig ?: ['enabled' => false];

        // Vendor filtering setup
        $this->vendorFilterEnabled = $vendorFilterEnabled;
        $this->showVendorColumn = $showVendorColumn ?? $this->shouldShowVendorColumn();

        // Set vendor ID if filtering is enabled
        if ($this->vendorFilterEnabled) {
            $this->vendorId = $this->getVendorId();
        }
    }

    // Check if user is super admin
    public function isSuperAdmin()
    {
        $user = auth()->user();
        if (!$user) return false;

        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('super-admin');
        }

        return strtolower($user->role ?? '') === 'super-admin';
    }

    // Get vendor ID for vendor users
    public function getVendorId()
    {
        if ($this->isSuperAdmin()) {
            return null; // Super admin sees all data
        }

        return auth()->user()->vendor->id ?? null;
    }

    // Determine if vendor column should be shown
    public function shouldShowVendorColumn()
    {
        return $this->isSuperAdmin() && Schema::hasColumn((new $this->modelClass)->getTable(), 'vendor_id');
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    // --- Sorting ---
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // --- Render ---
    public function render()
    {
        $query = app($this->modelClass)::query();

        // Collect relations to eager load
        $relationsToLoad = [];
        foreach ($this->columns as $column) {
            if (Str::contains($column['field'], '.')) {
                $relation = explode('.', $column['field'])[0];
                $relationsToLoad[] = $relation;
            }
        }

        // Add vendor relation if needed
        if ($this->showVendorColumn || $this->vendorFilterEnabled) {
            $relationsToLoad[] = 'vendor';
        }

        $query->with(array_unique($relationsToLoad));

        // --- Vendor Filtering ---
        if ($this->vendorFilterEnabled && $this->vendorId && !$this->isSuperAdmin()) {
            $query->where('vendor_id', $this->vendorId);
        }

        // --- Filter handling ---
        if ($this->filter !== 'All Status' && $this->filter !== 'All Category') {
            if ($this->filter === 'Active') {
                if (Schema::hasColumn((new $this->modelClass)->getTable(), 'status')) {
                    $query->where('status', 1);
                } else {
                    foreach ($relationsToLoad as $relation) {
                        $query->whereHas($relation, fn($q) => $q->where('status', 1));
                    }
                }
            } elseif ($this->filter === 'Inactive') {
                if (Schema::hasColumn((new $this->modelClass)->getTable(), 'status')) {
                    $query->where('status', 0);
                } else {
                    foreach ($relationsToLoad as $relation) {
                        $query->whereHas($relation, fn($q) => $q->where('status', 0));
                    }
                }
            }
        }

        // --- Searching ---
        if ($this->search) {
            $query->where(function ($q) {
                $table = (new $this->modelClass)->getTable();

                foreach ($this->columns as $column) {
                    if ($column['field'] === 'actions') continue;

                    // Relation search (country.name)
                    if (Str::contains($column['field'], '.')) {
                        [$relation, $field] = explode('.', $column['field']);
                        $q->orWhereHas($relation, fn($rel) =>
                            $rel->where($field, 'like', '%' . $this->search . '%')
                        );
                    } else {
                        // Normal column search WITH TABLE PREFIX to avoid ambiguity
                        $q->orWhere("{$table}.{$column['field']}", 'like', '%' . $this->search . '%');
                    }
                }
            });
        }

        // --- Sorting ---
        if (Str::contains($this->sortField, '.')) {
            $relation = explode('.', $this->sortField)[0];
            $field = explode('.', $this->sortField)[1];

            $relatedModel = app($this->modelClass)->{$relation}()->getRelated();
            $query->join($relatedModel->getTable(), $relatedModel->getTable() . '.id', '=', $relation . '_id')
                ->orderBy($relatedModel->getTable() . '.' . $field, $this->sortDirection)
                ->select($this->modelClass::getModel()->getTable() . '.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $rows = $query->paginate($this->perPage);

        return view('livewire.admin.components.table.data-table', compact('rows'));
    }

    // --- Render action buttons for each row ---
    public function getActionHtml($row)
    {
        if ($this->actionView) {
            return view($this->actionView, ['row' => $row])->render();
        }
        return '';
    }
}
