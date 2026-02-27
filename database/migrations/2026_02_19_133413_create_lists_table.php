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
            $table->enum('unit', array_column(\App\Enums\ProductUnit::cases(), 'value'));
            $table->foreignUuid('category_id')->constrained('product_categories');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('lists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_template')->default('false');
            $table->enum('type', array_column(\App\Enums\ListType::cases(), 'value'));
            $table->timestamp('touched_at')->default(DB::raw('NOW()'));
            $table->string('short_url')->unique();
            $table->integer('access')->default(\App\Enums\ListAccess::Private->value);
            $table->foreignUuid('owner_id')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('list_users', function (Blueprint $table) {
            $table->foreignUuid('list_id')->constrained('lists');
            $table->foreignUuid('user_id')->constrained('users');
            $table->timestamps();
            $table->primary(['list_id', 'user_id']);
        });

        Schema::create('list_invites', function (Blueprint $table) {
            $table->foreignUuid('list_id')->constrained('lists');
            $table->string('email');
            $table->timestamps();
            $table->primary(['list_id', 'email']);
        });

        Schema::create('list_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->integer('version')->default(1);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->foreignUuid('completed_user_id')->nullable()->constrained('users');
            $table->jsonb('data')->default(json_encode([]));
            $table->foreignUuid('list_id')->constrained('lists');
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('product_id')->nullable()->constrained('products');
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
