<?php

namespace App\Http\Livewire\Admin\Documents;

use App\Models\Admin\Document;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentsComponent extends Component
{
    use WithPagination;

    public function render()
    {
        $documents = Document::with('uploader')->paginate(10);

        return view('livewire.admin.documents.documents-component', [
            'documents' => $documents,
        ])->layout('layouts.admin.master');
    }
}
