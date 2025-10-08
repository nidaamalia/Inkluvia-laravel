<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Detail extends Component
{
    /**
     * The label for the detail item.
     */
    public string $label;

    /**
     * The value for the detail item.
     */
    public mixed $value;

    /**
     * Create a new component instance.
     */
    public function __construct(string $label, mixed $value = null)
    {
        $this->label = $label;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.detail');
    }
}