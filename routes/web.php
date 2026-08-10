<?php

use Illuminate\Support\Facades\Route;
use NotificationSystem\Http\Controllers\Admin\DashboardNotificationController;

$config = config('notification-system.routes.dashboard', []);

if (! empty($config['enabled'])) {
    Route::middleware($config['middleware'] ?? ['web', 'auth'])
        ->prefix($config['prefix'] ?? 'dashboard/notifications')
        ->as($config['as'] ?? 'dashboard.notifications.')
        ->group(function () {
            Route::get('/', [DashboardNotificationController::class, 'index'])->name('index');
            Route::get('/compose', [DashboardNotificationController::class, 'compose'])->name('compose');
            Route::post('/send', [DashboardNotificationController::class, 'send'])->name('send');
            Route::post('/mark-all-read', [DashboardNotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/clear', [DashboardNotificationController::class, 'clear'])->name('clear');
            Route::get('/unread-count', [DashboardNotificationController::class, 'unreadCount'])->name('unread-count');
            Route::get('/{id}', [DashboardNotificationController::class, 'show'])->name('show');
            Route::post('/{id}/mark-read', [DashboardNotificationController::class, 'markAsRead'])->name('mark-read');
            Route::delete('/{id}', [DashboardNotificationController::class, 'destroy'])->name('destroy');
        });
}
