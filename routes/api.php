<?php

use Illuminate\Support\Facades\Route;
use NotificationSystem\Http\Controllers\Api\NotificationApiController;

$config = config('notification-system.routes.api', []);

if (! empty($config['enabled'])) {
    Route::middleware($config['middleware'] ?? ['api', 'auth'])
        ->prefix($config['prefix'] ?? 'api/v1/notifications')
        ->as($config['as'] ?? 'api.notifications.')
        ->group(function () {
            Route::get('/', [NotificationApiController::class, 'index'])->name('index');
            Route::get('/unread-count', [NotificationApiController::class, 'unreadCount'])->name('unread-count');
            Route::post('/mark-all-read', [NotificationApiController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::post('/{id}/mark-read', [NotificationApiController::class, 'markAsRead'])->name('mark-read');
            Route::delete('/', [NotificationApiController::class, 'clear'])->name('clear');
            Route::delete('/{id}', [NotificationApiController::class, 'destroy'])->name('destroy');
        });
}
