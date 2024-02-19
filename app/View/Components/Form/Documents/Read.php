<?php

namespace App\View\Components\Form\Documents;

use Illuminate\View\Component;

class Read extends Component
{
    public $req;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($req)
    {
        $this->req = $req;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form.documents.read');
    }
}
