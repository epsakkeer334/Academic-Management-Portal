<div class="content">
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h2 class="mb-1 fw-semibold">Syllabus Mapping</h2>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                    <li class="breadcrumb-item">Admin</li>
                    <li class="breadcrumb-item active">Syllabus Mapping</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-3">
            <button type="button" wire:click="$emit('open-syllabus-modal')" class="btn btn-primary d-flex align-items-center shadow-sm">
                <i class="ti ti-circle-plus me-2"></i> Add Mapping
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <livewire:admin.components.table.data-table
            :model-class="\App\Models\Admin\SyllabusMapping::class"
            :columns="[
                ['label' => '#', 'field' => 'id', 'sortable' => true],
                ['label' => 'Curriculum', 'field' => 'curriculum.name', 'sortable' => true],
                ['label' => 'Subject', 'field' => 'subject.name', 'sortable' => true],
                ['label' => 'Institute', 'field' => 'institute.name', 'sortable' => true],
                ['label' => 'Coverage', 'field' => 'coverage_percentage', 'type' => 'text', 'sortable' => true],
                ['label' => 'Status', 'field' => 'status', 'type' => 'status', 'sortable' => true],
                ['label' => 'Start Date', 'field' => 'start_date', 'type' => 'datetime', 'format' => 'd M Y', 'sortable' => true],
                ['label' => 'Planned Completion', 'field' => 'planned_completion', 'type' => 'datetime', 'format' => 'd M Y', 'sortable' => true],
                ['label' => 'Actions', 'field' => 'actions', 'type' => 'actions', 'actions' => ['edit', 'delete']]
            ]"
            :filters="['All' => 'All', 'In Progress' => 'in_progress', 'Completed' => 'completed']"
            title="Syllabus Mappings"
        />
    </div>
</div>
