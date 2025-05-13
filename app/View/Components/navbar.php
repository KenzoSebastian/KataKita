<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class navbar extends Component
{
    /**
     * Create a new component instance.
     */

    public ?string $profileDefault;
    public $activeUser = [];
    public $allUser = [];
    public function __construct(string $profileDefault = null, array $activeUser, array $allUser)
    {
        $this->profileDefault = $profileDefault;
        $this->activeUser = $activeUser;
        $this->allUser = $allUser;

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navbar');
    }
}
