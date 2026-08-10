<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('notification-system.table_name', 'notification_logs');

        if (! Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('notification_id')->index();
                $table->string('recipient_type')->nullable();
                $table->string('recipient_id')->nullable();
                $table->string('channel')->index();
                $table->string('status')->default('pending')->index(); // pending, sending, delivered, failed
                $table->integer('attempts')->default(1);
                $table->timestamp('delivered_at')->nullable();
                $table->timestamp('failed_at')->nullable();
                $table->json('response')->nullable();
                $table->text('exception')->nullable();
                $table->integer('duration_ms')->nullable();
                $table->json('data')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        $tableName = config('notification-system.table_name', 'notification_logs');
        Schema::dropIfExists($tableName);
    }
};
