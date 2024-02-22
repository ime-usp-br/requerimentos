<?php

namespace App\View\Components\Form\Disciplines;

use Illuminate\View\Component;

class Read extends Component
{
    public $takenDiscs;
    public $req;
    public $withRecordButton;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($takenDiscs, $req, $withRecordButton)
    {
        $this->takenDiscs = $takenDiscs;
        $this->req = $req;
        $this->withRecordButton = $withRecordButton;
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
