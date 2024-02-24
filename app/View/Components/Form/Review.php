<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Review extends Component
{
    public $req;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($req = null)
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
        return view('components.form.review');
    }
}
