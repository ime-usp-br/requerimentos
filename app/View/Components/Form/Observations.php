<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Observations extends Component
{
    public $req;
    
    public $readOnly;

    public $shownInternally;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($req = null, $readOnly = True, $shownInternally = True)
    {
        $this->req = $req;
        $this->readOnly = $readOnly;
        $this->shownInternally = $shownInternally;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form.observations');
    }
}
