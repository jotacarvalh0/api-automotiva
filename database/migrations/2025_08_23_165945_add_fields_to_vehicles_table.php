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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('titulo')->after('id');
            $table->string('marca')->after('titulo');
            $table->string('modelo')->after('marca');
            $table->integer('ano')->after('modelo');
            $table->decimal('preco', 10, 2)->after('ano');
            $table->string('cor')->after('preco');
            $table->string('combustivel')->after('cor');
            $table->string('url_imagem')->nullable()->after('combustivel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['titulo','marca','modelo','ano','preco','cor','combustivel','url_imagem']);

        });
    }
};
