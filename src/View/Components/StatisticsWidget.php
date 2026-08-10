<?php

namespace NotificationSystem\View\Components;

use Illuminate\View\Component;

class StatisticsWidget extends Component
{
    public function __construct(
        public int $total = 0,
        public int $unread = 0,
        public int $delivered = 0,
        public int $failed = 0
    ) {}

    public function render()
    {
        return view('notification-system::components.statistics-widget');
    }
}
