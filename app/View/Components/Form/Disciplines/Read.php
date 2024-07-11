<?php

namespace App\View\Components\Form\Disciplines;

use Illuminate\View\Component;

class Read extends Component
{
    public $takenDiscs;
    public $req;
    public $withRecordButton;
    public $readOnly;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($takenDiscs, $req, $withRecordButton, $readOnly = True)
    {
        $this->takenDiscs = $takenDiscs;
        $this->req = $req;
        $this->withRecordButton = $withRecordButton;
        $this->readOnly = $readOnly;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form.disciplines.read');
    }
}
