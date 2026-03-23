<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            // 'students',
            // 'subjects',
            // 'exam_applications',
            // 'exam_results',
            // 'marksheets',
            // 'documents',
            // 'mous',
            // 'notifications',
            // 'audit_logs',
            // 'document_checklists',
            // 'activity_logs',
            // 'exam_subjects',
            // 'syllabus_mappings',
            // 'serial_number_generators'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('created_by')->nullable()->after('id');
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            });
        }
    }

    public function down()
    {
        $tables = [
            'students',
            'subjects',
            'exam_applications',
            'exam_results',
            'marksheets',
            'documents',
            'mous',
            'notifications',
            'audit_logs',
            'document_checklists',
            'activity_logs',
            'exam_subjects',
            'syllabus_mappings',
            'serial_number_generators'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['created_by', 'updated_by']);
            });
        }
    }
};
