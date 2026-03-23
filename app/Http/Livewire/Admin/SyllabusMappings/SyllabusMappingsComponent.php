<?php

namespace App\Http\Livewire\Admin\SyllabusMappings;

use Livewire\Component;

class SyllabusMappingsComponent extends Component
{
    public function render()
    {
        return view('livewire.admin.syllabus-mappings.syllabus-mappings-component')
            ->layout('layouts.admin.master');
    }
}
