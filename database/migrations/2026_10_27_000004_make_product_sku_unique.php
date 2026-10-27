<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $duplicates = DB::select('select sku, min(id) as keep_id from products where sku is not null group by sku having count(*) > 1');
        foreach ($duplicates as $duplicate) {
            DB::table('products')->where('sku', $duplicate->sku)->where('id', '!=', $duplicate->keep_id)->delete();
        }

        Schema::table('products', function (Blueprint $table) {
            $table->unique('sku', 'products_sku_unique');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_sku_unique');
        });
    }
};
