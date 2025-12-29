<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {

            // Primary key (Auto Increment ID)
            $table->id();

            // Original file name uploaded by the user
            // Example: my_photo.jpg
            $table->string('original_name');

            // Stored file name used internally to avoid conflicts
            // Example: 1712345678_my_photo.jpg
            $table->string('file_name');

            // File storage path inside storage/app/public
            // Example: uploads/1712345678_my_photo.jpg
            $table->string('file_path');

            // File size (stored in KB for display purpose)
            $table->string('file_size')->nullable();

            // Status column (1 = active, 0 = inactive)
            $table->boolean('status')->default(1);

            // User ID who created/uploaded the file
            $table->unsignedBigInteger('created_by')->nullable();

            // User ID who last updated the file record
            $table->unsignedBigInteger('updated_by')->nullable();

            // created_at and updated_at timestamps
            $table->timestamps();

            // Soft delete column (deleted_at)
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the images table if rollback is executed
        Schema::dropIfExists('images');
    }
};
