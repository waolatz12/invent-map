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

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('currency')->nullable();
            $table->string('timezone')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('status')->default('active'); //active, suspended, trial
            $table->timestamps();

            $table->index('slug');
            $table->index('status');
            $table->index(['name', 'status']);
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->cascade()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('sku')->nullable();
            $table->string('description')->nullable();
            $table->decimal('cost_price', 18, 2)->default(0);
            $table->decimal('selling_price', 18, 2)->default(0);
            $table->decimal('dimension', 10, 2)->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->text('barcode')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('has_variants')->default(false);
            $table->timestamps();

            $table->index('company_id');
            $table->index(['company_id', 'sku']);
            $table->index(['company_id', 'category_id'], 'company_category_idx');
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('address')->nullable();
            $table->string('location')->nullable();
            $table->string('manager')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'name']);
        });

        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained();
            $table->foreignId('company_id')->constrained();
            $table->integer('quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->integer('available_quantity')->default(0);
            $table->integer('reorder_level')->default(0);
            $table->integer('reorder_quantity')->default(0);
            $table->date('last_restocked_at')->nullable();
            $table->timestamps();

            $table->index('product_id');
            $table->index('warehouse_id');
            $table->index('company_id');
            $table->unique(['product_id', 'warehouse_id', 'company_id'], 'unique_inventory');
            $table->index(['product_id', 'warehouse_id', 'company_id'], 'inventory_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('products');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('inventories');
    }
};
