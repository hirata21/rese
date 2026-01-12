<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('shop_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->date('reserved_date');
            $table->time('reserved_time');
            $table->unsignedTinyInteger('number_of_people');

            $table->unsignedInteger('price')->nullable();
            $table->enum('payment_method', ['card', 'cash'])
                ->nullable()
                ->index();

            $table->enum('status', ['reserved', 'visited', 'cancelled'])
                ->default('reserved')
                ->index();

            $table->string('qr_token', 64)->unique();
            $table->timestamp('checked_in_at')->nullable();

            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])
                ->default('unpaid')
                ->index();

            $table->string('stripe_session_id')->nullable()->unique();

            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('review_comment')->nullable();

            $table->timestamps();

            $table->index(['reserved_date', 'reserved_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
}