<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Personal extends Component
{
    public $withRecordButton;
    public $req;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($withRecordButton, $req = null)
    {
        $this->withRecordButton = $withRecordButton;
        $this->req = $req;
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
