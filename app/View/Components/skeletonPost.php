<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class skeletonPost extends Component
{
    /**
     * Create a new component instance.
     */
    public int $count;
    public function __construct(int $count)
    {
        $this->count = $count;   
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.skeleton-post');
    }
}
