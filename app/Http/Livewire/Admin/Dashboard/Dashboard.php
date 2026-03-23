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
        $user = auth()->user();
        $roleNames = [];
        if ($user) {
            if (method_exists($user, 'getRoleNames')) {
                $roleNames = $user->getRoleNames()->toArray();
            } else {
                $roleNames = [$user->role ?? ''];
            }
        }

        return view('livewire.admin.dashboard.dashboard', [
            'roles' => $roleNames,
        ])->layout('layouts.admin.master');
    }
}
