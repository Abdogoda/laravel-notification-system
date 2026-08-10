<?php

namespace NotificationSystem\View\Components;

use Illuminate\View\Component;

/**
 * Blade component for rendering the recipient selector
 * dynamically from configured guards.
 *
 * Usage: <x-recipient-selector :guards="$guardModels" />
 */
class RecipientSelector extends Component
{
    /**
     * @param  array  $guards  Associative array of guard data from config.
     *                         Each key => ['label' => string, 'items' => Collection]
     */
    public function __construct(
        public array $guards = []
    ) {}

    public function render()
    {
        return view('notification-system::components.recipient-selector');
    }
}
