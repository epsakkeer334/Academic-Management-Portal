
@if($value)

<button type="button"
        class="btn btn-sm btn-outline-primary border-0 text-decoration-none"
        wire:click="showAreas({{ $value['category_id'] }}, {{ $value['governorate_id'] }})"
        title="View {{ $value['area_count'] }} areas">
    <div class="d-flex align-items-center gap-2">
        <i class="ti ti-map-pin"></i>
        <span>{{ $value['first_area_name'] }}</span>
        @if($value['area_count'] > 1)
            <span class="badge bg-primary rounded-pill">+{{ $value['area_count'] - 1 }}</span>
        @endif
    </div>
</button>
@endif
