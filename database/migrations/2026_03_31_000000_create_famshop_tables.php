<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamshopTables extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('gender', 20)->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('profile_photo')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name_member', 100);
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('avatar')->nullable();
            $table->timestamps();
        });

        Schema::create('allergy_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('family_members')->cascadeOnDelete();
            $table->string('allergy_type', 100);
            $table->enum('severity_level', ['mild', 'moderate', 'severe'])->default('moderate');
            $table->timestamps();
        });

        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->unique()->constrained('family_members')->cascadeOnDelete();
            $table->decimal('daily_budget', 10, 2)->default(0);
            $table->decimal('weekly_budget', 10, 2)->default(0);
            $table->decimal('monthly_budget', 10, 2)->default(0);
            $table->decimal('daily_spent', 10, 2)->default(0);
            $table->decimal('weekly_spent', 10, 2)->default(0);
            $table->decimal('monthly_spent', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('barcode', 50)->unique();
            $table->string('pr_name');
            $table->string('brand', 100)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->enum('halal_status', ['halal', 'haram', 'unknown'])->default('unknown');
            $table->text('raw_ingredients')->nullable();
            $table->timestamps();
        });

        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('aller_name', 100)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('products_ingredients', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained('ingredients')->cascadeOnDelete();
            $table->string('note')->nullable();
            $table->primary(['product_id', 'ingredient_id']);
        });

        Schema::create('alternative_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('alternative_product_id')->constrained('products')->cascadeOnDelete();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('scan_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('family_members')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('match_status', ['safe', 'unsafe', 'over_budget', 'unsafe_over_budget']);
            $table->string('reason')->nullable();
            $table->timestamp('scan_date');
            $table->timestamps();
        });

        Schema::create('shopping_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('family_members')->nullOnDelete();
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->string('match_status', 50)->nullable();
            $table->timestamp('purchase_date')->nullable();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('shopping_carts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedTinyInteger('quantity')->default(1);
            $table->decimal('total_price', 10, 2);
            $table->timestamp('purchase_date')->nullable();
            $table->timestamps();
        });

        Schema::create('shopping_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('family_members')->cascadeOnDelete();
            $table->string('item_name');
            $table->boolean('is_checked')->default(false);
            $table->timestamps();
        });

        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['rating', 'suggestion', 'bug'])->default('rating');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
            $table->timestamp('ticket_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('feedback');
        Schema::dropIfExists('shopping_lists');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('shopping_carts');
        Schema::dropIfExists('scan_history');
        Schema::dropIfExists('alternative_products');
        Schema::dropIfExists('products_ingredients');
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('products');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('allergy_profiles');
        Schema::dropIfExists('family_members');
        Schema::dropIfExists('users');
    }
}
