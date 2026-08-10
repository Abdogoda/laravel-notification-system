<?php

namespace NotificationSystem\View\Components;

use Illuminate\View\Component;

class NotificationForm extends Component
{
    public function __construct(
        public array $guards = []
    ) {}

    public function render()
    {
        return view('notification-system::components.form');
    }
}
