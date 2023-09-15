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
        Schema::create("store_cover_image_relations", function (
            Blueprint $table
        ) {
            $table->id();
            $table
                ->foreignId("media_library_id")
                ->references("id")
                ->on("media_libraries")
                ->onDelete("cascade")
                ->constrained();

            $table
                ->foreignId("store_id")
                ->references("id")
                ->on("stores")
                ->onDelete("cascade")
                ->constrained();
            $table->boolean("primary")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("store_cover_image_relations");
    }
};
