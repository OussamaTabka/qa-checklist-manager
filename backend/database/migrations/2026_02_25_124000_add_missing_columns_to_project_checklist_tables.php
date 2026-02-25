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
        Schema::table('project_checklists', function (Blueprint $table) {
            if (! Schema::hasColumn('project_checklists', 'project_id')) {
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete()->after('id');
            }

            if (! Schema::hasColumn('project_checklists', 'checklist_id')) {
                $table->foreignId('checklist_id')->constrained('checklists')->cascadeOnDelete()->after('project_id');
            }

            if (! Schema::hasColumn('project_checklists', 'version')) {
                $table->string('version')->default('v1')->after('checklist_id');
            }

            if (! Schema::hasColumn('project_checklists', 'status')) {
                $table->string('status')->default('draft')->after('version');
            }

            if (! Schema::hasColumn('project_checklists', 'project_id') || ! Schema::hasColumn('project_checklists', 'checklist_id')) {
                return;
            }

            $table->index(['project_id']);
            $table->index(['checklist_id']);
        });

        Schema::table('project_checklist_items', function (Blueprint $table) {
            if (! Schema::hasColumn('project_checklist_items', 'project_checklist_id')) {
                $table->foreignId('project_checklist_id')->constrained('project_checklists')->cascadeOnDelete()->after('id');
            }

            if (! Schema::hasColumn('project_checklist_items', 'checklist_item_id')) {
                $table->foreignId('checklist_item_id')->constrained('checklist_items')->cascadeOnDelete()->after('project_checklist_id');
            }

            if (! Schema::hasColumn('project_checklist_items', 'label')) {
                $table->string('label')->after('checklist_item_id');
            }

            if (! Schema::hasColumn('project_checklist_items', 'status')) {
                $table->string('status')->default('pending')->after('label');
            }

            if (! Schema::hasColumn('project_checklist_items', 'comment')) {
                $table->string('comment')->nullable()->after('status');
            }

            if (! Schema::hasColumn('project_checklist_items', 'order')) {
                $table->unsignedBigInteger('order')->default(0)->after('comment');
            }

            if (! Schema::hasColumn('project_checklist_items', 'project_checklist_id') || ! Schema::hasColumn('project_checklist_items', 'order')) {
                return;
            }

            $table->index(['project_checklist_id']);
            $table->unique(['project_checklist_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_checklist_items', function (Blueprint $table) {
            if (Schema::hasColumn('project_checklist_items', 'project_checklist_id') && Schema::hasColumn('project_checklist_items', 'order')) {
                $table->dropUnique(['project_checklist_id', 'order']);
            }

            if (Schema::hasColumn('project_checklist_items', 'project_checklist_id')) {
                $table->dropIndex(['project_checklist_id']);
                $table->dropConstrainedForeignId('project_checklist_id');
            }

            if (Schema::hasColumn('project_checklist_items', 'checklist_item_id')) {
                $table->dropConstrainedForeignId('checklist_item_id');
            }

            if (Schema::hasColumn('project_checklist_items', 'order')) {
                $table->dropColumn('order');
            }

            if (Schema::hasColumn('project_checklist_items', 'comment')) {
                $table->dropColumn('comment');
            }

            if (Schema::hasColumn('project_checklist_items', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('project_checklist_items', 'label')) {
                $table->dropColumn('label');
            }
        });

        Schema::table('project_checklists', function (Blueprint $table) {
            if (Schema::hasColumn('project_checklists', 'project_id')) {
                $table->dropIndex(['project_id']);
                $table->dropConstrainedForeignId('project_id');
            }

            if (Schema::hasColumn('project_checklists', 'checklist_id')) {
                $table->dropIndex(['checklist_id']);
                $table->dropConstrainedForeignId('checklist_id');
            }

            if (Schema::hasColumn('project_checklists', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('project_checklists', 'version')) {
                $table->dropColumn('version');
            }
        });
    }
};
