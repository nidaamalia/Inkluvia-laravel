<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Badge extends Component
{
    public string $color;

    public function __construct(string $color = 'gray')
    {
        $this->color = $color;
    }

    public function render(): View
    {
        return view('components.badge');
    }
}
