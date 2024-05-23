<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Personal extends Component
{
    public $withRecordButton;
    public $req;
    public $readOnly;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($withRecordButton, $req = null, $readOnly = True)
    {
        $this->withRecordButton = $withRecordButton;
        $this->req = $req;
        $this->readOnly = $readOnly;
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form.personal');
    }
}
