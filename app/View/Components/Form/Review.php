<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Review extends Component
{
    public $req;
    public $review;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($req = null, $review = null)
    {
        $this->req = $req;
        $this->review = $review;
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
