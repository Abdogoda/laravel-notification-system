<?php

namespace NotificationSystem\View\Components;

use Illuminate\View\Component;

class NotificationTable extends Component
{
    public function __construct(
        public mixed $notifications
    ) {}

    public function render()
    {
        return view('notification-system::components.table');
    }
}
