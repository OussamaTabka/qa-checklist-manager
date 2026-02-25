<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->stabilizeChecklists();
        $this->stabilizeChecklistItems();
        $this->stabilizeProjects();
        $this->stabilizeProjectChecklists();
        $this->stabilizeProjectChecklistItems();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left non-destructive for data safety.
    }

    private function stabilizeChecklists(): void
    {
        Schema::table('checklists', function (Blueprint $table) {
            if (!Schema::hasColumn('checklists', 'title')) {
                $table->string('title')->default('Untitled checklist');
            }

            if (!Schema::hasColumn('checklists', 'description')) {
                $table->text('description')->nullable();
            }

            if (!Schema::hasColumn('checklists', 'category')) {
                $table->string('category')->nullable();
            }

            if (!Schema::hasColumn('checklists', 'version')) {
                $table->string('version')->default('v1');
            }

            if (!Schema::hasColumn('checklists', 'priority')) {
                $table->unsignedBigInteger('priority')->default(0);
            }

            if (!Schema::hasColumn('checklists', 'criticality')) {
                $table->unsignedBigInteger('criticality')->default(0);
            }

            if (!Schema::hasColumn('checklists', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }

            if (!Schema::hasColumn('checklists', 'created_by')) {
                $table->foreignId('created_by')->nullable();
            }
        });

        if (Schema::hasColumn('checklists', 'created_by') && !$this->hasForeignKey('checklists', 'checklists_created_by_foreign')) {
            Schema::table('checklists', function (Blueprint $table) {
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (Schema::hasColumn('checklists', 'created_by') && !$this->hasIndex('checklists', 'checklists_created_by_index')) {
            Schema::table('checklists', function (Blueprint $table) {
                $table->index('created_by');
            });
        }

        $this->ensureTimestamps('checklists');
    }

    private function stabilizeChecklistItems(): void
    {
        Schema::table('checklist_items', function (Blueprint $table) {
            if (!Schema::hasColumn('checklist_items', 'checklist_id')) {
                $table->foreignId('checklist_id')->nullable();
            }

            if (!Schema::hasColumn('checklist_items', 'label')) {
                $table->string('label')->default('');
            }

            if (!Schema::hasColumn('checklist_items', 'description')) {
                $table->text('description')->nullable();
            }

            if (!Schema::hasColumn('checklist_items', 'priority')) {
                $table->unsignedBigInteger('priority')->default(0);
            }

            if (!Schema::hasColumn('checklist_items', 'criticality')) {
                $table->unsignedBigInteger('criticality')->default(0);
            }

            if (!Schema::hasColumn('checklist_items', 'order')) {
                $table->unsignedBigInteger('order')->default(0);
            }
        });

        if (Schema::hasColumn('checklist_items', 'checklist_id') && !$this->hasForeignKey('checklist_items', 'checklist_items_checklist_id_foreign')) {
            Schema::table('checklist_items', function (Blueprint $table) {
                $table->foreign('checklist_id')->references('id')->on('checklists')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('checklist_items', 'checklist_id') && !$this->hasIndex('checklist_items', 'checklist_items_checklist_id_index')) {
            Schema::table('checklist_items', function (Blueprint $table) {
                $table->index('checklist_id');
            });
        }

        if (
            Schema::hasColumn('checklist_items', 'checklist_id') &&
            Schema::hasColumn('checklist_items', 'order') &&
            !$this->hasIndex('checklist_items', 'checklist_items_checklist_id_order_unique')
        ) {
            Schema::table('checklist_items', function (Blueprint $table) {
                $table->unique(['checklist_id', 'order']);
            });
        }

        $this->ensureTimestamps('checklist_items');
    }

    private function stabilizeProjects(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'name')) {
                $table->string('name')->default('Untitled project');
            }

            if (!Schema::hasColumn('projects', 'description')) {
                $table->text('description')->nullable();
            }

            if (!Schema::hasColumn('projects', 'owner_id')) {
                $table->foreignId('owner_id')->nullable();
            }
        });

        if (Schema::hasColumn('projects', 'owner_id') && !$this->hasForeignKey('projects', 'projects_owner_id_foreign')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->foreign('owner_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (Schema::hasColumn('projects', 'owner_id') && !$this->hasIndex('projects', 'projects_owner_id_index')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->index('owner_id');
            });
        }

        $this->ensureTimestamps('projects');
    }

    private function stabilizeProjectChecklists(): void
    {
        Schema::table('project_checklists', function (Blueprint $table) {
            if (!Schema::hasColumn('project_checklists', 'project_id')) {
                $table->foreignId('project_id')->nullable();
            }

            if (!Schema::hasColumn('project_checklists', 'checklist_id')) {
                $table->foreignId('checklist_id')->nullable();
            }

            if (!Schema::hasColumn('project_checklists', 'version')) {
                $table->string('version')->default('v1');
            }

            if (!Schema::hasColumn('project_checklists', 'status')) {
                $table->string('status')->default('draft');
            }

            if (!Schema::hasColumn('project_checklists', 'started_at')) {
                $table->timestamp('started_at')->nullable();
            }

            if (!Schema::hasColumn('project_checklists', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
            }
        });

        if (Schema::hasColumn('project_checklists', 'project_id') && !$this->hasForeignKey('project_checklists', 'project_checklists_project_id_foreign')) {
            Schema::table('project_checklists', function (Blueprint $table) {
                $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('project_checklists', 'checklist_id') && !$this->hasForeignKey('project_checklists', 'project_checklists_checklist_id_foreign')) {
            Schema::table('project_checklists', function (Blueprint $table) {
                $table->foreign('checklist_id')->references('id')->on('checklists')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('project_checklists', 'project_id') && !$this->hasIndex('project_checklists', 'project_checklists_project_id_index')) {
            Schema::table('project_checklists', function (Blueprint $table) {
                $table->index('project_id');
            });
        }

        if (Schema::hasColumn('project_checklists', 'checklist_id') && !$this->hasIndex('project_checklists', 'project_checklists_checklist_id_index')) {
            Schema::table('project_checklists', function (Blueprint $table) {
                $table->index('checklist_id');
            });
        }

        $this->ensureTimestamps('project_checklists');
    }

    private function stabilizeProjectChecklistItems(): void
    {
        Schema::table('project_checklist_items', function (Blueprint $table) {
            if (!Schema::hasColumn('project_checklist_items', 'project_checklist_id')) {
                $table->foreignId('project_checklist_id')->nullable();
            }

            if (!Schema::hasColumn('project_checklist_items', 'checklist_item_id')) {
                $table->foreignId('checklist_item_id')->nullable();
            }

            if (!Schema::hasColumn('project_checklist_items', 'label')) {
                $table->string('label')->default('');
            }

            if (!Schema::hasColumn('project_checklist_items', 'status')) {
                $table->string('status')->default('pending');
            }

            if (!Schema::hasColumn('project_checklist_items', 'comment')) {
                $table->string('comment')->nullable();
            }

            if (!Schema::hasColumn('project_checklist_items', 'order')) {
                $table->unsignedBigInteger('order')->default(0);
            }
        });

        if (Schema::hasColumn('project_checklist_items', 'project_checklist_id') && !$this->hasForeignKey('project_checklist_items', 'project_checklist_items_project_checklist_id_foreign')) {
            Schema::table('project_checklist_items', function (Blueprint $table) {
                $table->foreign('project_checklist_id')->references('id')->on('project_checklists')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('project_checklist_items', 'checklist_item_id') && !$this->hasForeignKey('project_checklist_items', 'project_checklist_items_checklist_item_id_foreign')) {
            Schema::table('project_checklist_items', function (Blueprint $table) {
                $table->foreign('checklist_item_id')->references('id')->on('checklist_items')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('project_checklist_items', 'project_checklist_id') && !$this->hasIndex('project_checklist_items', 'project_checklist_items_project_checklist_id_index')) {
            Schema::table('project_checklist_items', function (Blueprint $table) {
                $table->index('project_checklist_id');
            });
        }

        if (
            Schema::hasColumn('project_checklist_items', 'project_checklist_id') &&
            Schema::hasColumn('project_checklist_items', 'order') &&
            !$this->hasIndex('project_checklist_items', 'project_checklist_items_project_checklist_id_order_unique')
        ) {
            Schema::table('project_checklist_items', function (Blueprint $table) {
                $table->unique(['project_checklist_id', 'order']);
            });
        }

        $this->ensureTimestamps('project_checklist_items');
    }

    private function ensureTimestamps(string $table): void
    {
        Schema::table($table, function (Blueprint $blueprint) use ($table) {
            if (!Schema::hasColumn($table, 'created_at')) {
                $blueprint->timestamp('created_at')->nullable();
            }

            if (!Schema::hasColumn($table, 'updated_at')) {
                $blueprint->timestamp('updated_at')->nullable();
            }
        });
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            $database = DB::getDatabaseName();
            $result = DB::selectOne(
                'SELECT COUNT(*) AS aggregate FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
                [$database, $table, $indexName]
            );

            return ((int) ($result->aggregate ?? 0)) > 0;
        }

        if ($driver === 'sqlite') {
            $result = DB::selectOne(
                'SELECT COUNT(*) AS aggregate FROM sqlite_master WHERE type = ? AND tbl_name = ? AND name = ?',
                ['index', $table, $indexName]
            );

            return ((int) ($result->aggregate ?? 0)) > 0;
        }

        return false;
    }

    private function hasForeignKey(string $table, string $constraintName): bool
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            $database = DB::getDatabaseName();
            $result = DB::selectOne(
                'SELECT COUNT(*) AS aggregate FROM information_schema.table_constraints WHERE table_schema = ? AND table_name = ? AND constraint_name = ? AND constraint_type = ?',
                [$database, $table, $constraintName, 'FOREIGN KEY']
            );

            return ((int) ($result->aggregate ?? 0)) > 0;
        }

        return false;
    }
};
