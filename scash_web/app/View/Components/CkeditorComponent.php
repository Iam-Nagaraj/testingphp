<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CkeditorComponent extends Component
{
    public $name,$value;

    /**
     * Create a new component instance.
     */
    public function __construct($name,$value)
    {
        $this->name = $name;
        $this->value = $value;

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ckeditor-component');
    }
}
