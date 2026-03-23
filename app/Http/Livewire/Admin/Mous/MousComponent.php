<?php

namespace App\Http\Livewire\Admin\Mous;

use App\Models\Admin\Mou;
use Livewire\Component;
use Livewire\WithPagination;

class MousComponent extends Component
{
    use WithPagination;

    public function render()
    {
        $mous = Mou::with(['institute', 'creator'])->paginate(10);

        return view('livewire.admin.mous.mous-component', [
            'mous' => $mous,
        ])->layout('layouts.admin.master');
    }
}
