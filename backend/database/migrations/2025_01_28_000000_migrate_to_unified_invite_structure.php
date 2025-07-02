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
        // Drop old invite tables if they exist
        Schema::dropIfExists('conversation_invite');
        Schema::dropIfExists('invite_activite');
        Schema::dropIfExists('invite_temporaire');

        // Create the new unified invite table
        Schema::create('invite', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            
            // Foreign key constraint to personne table
            $table->foreign('email')->references('email')->on('personne')->onDelete('cascade');
        });

        // Modify personne table to make mot_de_passe nullable
        Schema::table('personne', function (Blueprint $table) {
            $table->string('mot_de_passe')->nullable()->change();
        });

        // Modify visite table to reference invite email
        Schema::table('visite', function (Blueprint $table) {
            // Drop existing foreign key if it exists
            $table->dropForeign(['email_invite']);
            
            // Add foreign key to invite table
            $table->foreign('email_invite')->references('email')->on('invite')->onDelete('cascade');
        });

        // Update message table to remove old invite references and use only personne
        // This assumes messages from invites are now handled through personne table
        Schema::table('message', function (Blueprint $table) {
            // Remove any old invite-related columns if they exist
            if (Schema::hasColumn('message', 'email_invite_temporaire')) {
                $table->dropColumn('email_invite_temporaire');
            }
            if (Schema::hasColumn('message', 'email_invite_activite')) {
                $table->dropColumn('email_invite_activite');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the invite table
        Schema::dropIfExists('invite');

        // Restore personne table changes
        Schema::table('personne', function (Blueprint $table) {
            $table->string('mot_de_passe')->nullable(false)->change();
        });

        // Note: We don't recreate the old tables as they are being phased out
        // This migration is designed to be a one-way migration to the new structure
    }
};
