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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('category_id')->constrained('product_categories');
            $table->enum('unit', array_column(\App\Enums\ProductUnit::cases(), 'value'));
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('lists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('short_url')->unique();
            $table->enum('type', array_column(\App\Enums\ListType::cases(), 'value'));
            $table->foreignUuid('owner_id')->constrained('users');
            $table->integer('access')->default(\App\Enums\ListAccess::PRIVATE->value);
            $table->softDeletes();
            $table->timestamps();
            $table->timestamp('touched_at')->default(DB::raw('NOW()'));
        });

        Schema::create('list_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('list_id')->constrained('lists');
            $table->foreignUuid('user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('list_invites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('list_id')->constrained('lists');
            $table->string('email');
            $table->timestamps();
        });

        Schema::create('list_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('list_id')->constrained('lists');
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('product_id')->nullable()->constrained('users');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('version')->default(1);
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->foreignUuid('completed_user_id')->nullable()->constrained('users');
            $table->jsonb('data')->default(json_encode([]));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_users');
        Schema::dropIfExists('list_invites');
        Schema::dropIfExists('list_items');
        Schema::dropIfExists('lists');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
    }
};
