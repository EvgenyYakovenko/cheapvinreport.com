<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deleted_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('role')->default('user')->comment('user, admin');

            $table->float('balance')->default(0);
            $table->integer('report_balance')->default(0);

            $table->string('password');
            $table->rememberToken();

            $table->timestamps();

            $table->index('name');
            $table->index('email');
            $table->index('role');
            $table->index('report_balance');
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deleted_users');
    }
};
