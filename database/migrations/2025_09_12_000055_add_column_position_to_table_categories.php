<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1) أضف العمود (default 0)
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('position')->default(0)->after('title');
        });

        // 2) جلب الـ ids بالترتيب اللي تحبه (هنا ORDER BY id ASC)
        $ids = DB::table('categories')
            ->orderBy('id') // لو عايز ترتيب تاني: ->orderBy('created_at') أو ->orderBy('name')
            ->pluck('id')
            ->toArray();

        if (empty($ids)) {
            return;
        }

        // 3) بناء CASE WHEN لتحديث كل القيم باستعلام واحد
        $caseSql = 'CASE id ';
        foreach ($ids as $index => $id) {
            $position = $index + 1; // يبدأ من 1
            $caseSql .= "WHEN {$id} THEN {$position} ";
        }
        $caseSql .= 'END';

        // 4) تنفيذ استعلام واحد لتحديث كل الـ sort_order
        DB::update("
            UPDATE categories
            SET position = {$caseSql}
            WHERE id IN (" . implode(',', $ids) . ")
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
