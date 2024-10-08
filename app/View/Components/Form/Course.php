<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Course extends Component
{
    public $req;

    public $readOnly;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($req = null, $readOnly = True)
    {   
        $this->req = $req;
        $this->readOnly = $readOnly;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form.course');
    }
}
