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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_section_id')->constrained()->onDelete('cascade'); // If a section is deleted, its fields are also deleted.
            $table->string('label');
            $table->string('name'); // The machine-readable name (e.g., 'club_name')
            $table->string('type');
            $table->json('options')->nullable();
            $table->json('validation_rules')->nullable();
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('order')->default(1);
            
            // Columns for conditional logic
            $table->foreignId('depends_on_field_id')
                  ->nullable()
                  ->constrained('form_fields') // Self-referencing foreign key
                  ->onDelete('set null'); // If the parent field is deleted, this becomes a normal field
            $table->string('depends_on_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
