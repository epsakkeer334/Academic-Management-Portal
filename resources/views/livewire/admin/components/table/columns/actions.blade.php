@php
    $allowed = $column['actions'] ?? ['edit', 'delete'];
@endphp

<div class="d-flex align-items-center">
    {{-- 🟩 View button --}}
    @if(in_array('view', $allowed))
        <button wire:click="$emit('viewRecord', {{ $row->id }})"
                class="btn btn-sm btn-outline-info me-1"
                data-bs-toggle="tooltip" title="View Record">
            <i class="ti ti-eye"></i>
        </button>
    @endif

    {{-- 🟨 Edit button --}}
    @if(in_array('edit', $allowed))
        <button wire:click="$emit('editRecord', {{ $row->id }})"
                class="btn btn-sm btn-outline-warning me-1"
                data-bs-toggle="tooltip" title="Edit Record">
            <i class="ti ti-edit"></i>
        </button>
    @endif

    {{-- 🟥 Delete button --}}
    @if(in_array('delete', $allowed))
        <button wire:click="$emit('deleteRecord', {{ $row->id }})"
                class="btn btn-sm btn-outline-danger"
                data-bs-toggle="tooltip" title="Delete Record">
            <i class="ti ti-trash"></i>
        </button>
    @endif
</div>
