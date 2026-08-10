<?php

namespace NotificationSystem\View\Components;

use Illuminate\View\Component;

class ChannelSelector extends Component
{
    public function __construct(
        public array $selectedChannels = ['database', 'mail', 'fcm']
    ) {}

    public function render()
    {
        return view('notification-system::components.channel-selector');
    }
}
