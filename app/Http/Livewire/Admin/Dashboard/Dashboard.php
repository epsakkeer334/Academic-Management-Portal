<?php

namespace App\Http\Livewire\Admin\Dashboard;

use Livewire\Component;

class Dashboard extends Component
{

    protected $listeners = ['refreshDashboard' => '$refresh'];

    public function mount()
    {
        $this->year = date('Y');
    }

    public function render()
    {
        return view('livewire.admin.dashboard.dashboard', [
        ])->layout('layouts.admin.master');
    }
}
