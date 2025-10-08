<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatusRow extends Component
{
    public string $label;

    public mixed $value;

    public function __construct(string $label, mixed $value = null)
    {
        $this->label = $label;
        $this->value = $value;
    }

    public function render(): View
    {
        return view('components.status-row');
    }
}
