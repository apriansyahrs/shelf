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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('letter_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->foreignId('business_entity_id')->constrained()->onDelete('cascade');
            $table->string('item_name')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('brand')->nullable();
            $table->string('type')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('imei1')->nullable();
            $table->string('imei2')->nullable();
            $table->decimal('item_price', 15, 2)->nullable();
            $table->string('inventory_holder_name')->nullable();
            $table->string('inventory_holder_position')->nullable();
            $table->string('item_location')->nullable();
            $table->string('status')->nullable();
            $table->string('upload_bast')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
