<?php

namespace App\View\Components\Form\Documents;

use Illuminate\View\Component;

class Read extends Component
{
    public $takenDiscsRecords; 
    public $currentCourseRecords;
    public $takenDiscSyllabi;
    public $requestedDiscSyllabi;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($takenDiscsRecords, $currentCourseRecords, $takenDiscSyllabi, $requestedDiscSyllabi)
    {
        $this->takenDiscsRecords = $takenDiscsRecords;
        $this->currentCourseRecords = $currentCourseRecords;
        $this->takenDiscSyllabi = $takenDiscSyllabi;
        $this->requestedDiscSyllabi = $requestedDiscSyllabi;
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
