<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis;');

        Schema::create('estados_geometria', function(Blueprint $table)
        {
            $table->uuid('id')->primary();
            $table->string('nome_estado')->notNullable()->unique();
            $table->polygon('geom', 4326)->nullable();
            $table->spatialIndex('geom');
        });

        Schema::create('municipios_geometria', function (Blueprint $table)
        {
            $table->uuid('id')->primary();
            $table->string('nome_municipio')->notNullable();
            $table->polygon('geom', 4326)->notNullable();
            $table->uuid('id_state');
            $table->foreign('id_state')->references('id')->on('estados_geometria');
            $table->spatialIndex('geom');
        });

        Schema::create('pontos_usuario', function(Blueprint $table)
        {
            $table->uuid('id')->primary();
            $table->double('latitude', 15, 8);
            $table->double('longitude', 15, 8);
            $table->uuid('municipio_id');
            $table->foreign('municipio_id')->references('id')->on('municipios_geometria');
            $table->polygon('geom', 4326)->notNullable();
            $table->spatialIndex('geom');
        });

        DB::statement('CREATE INDEX idx_municipios_geometria_geom ON municipios_geometria USING gist(geom)');
        DB::statement('CREATE INDEX idx_estados_geometria_geom ON estados_geometria USING gist(geom)');
        DB::statement('CREATE INDEX idx_pontos_usuario_geom ON pontos_usuario USING gist(geom)');

        DB::table('estados_geometria')->insert([
            ['id' => uuid_create(UUID_TYPE_RANDOM), 'nome_estado' => 'MG', 'geom' => null],
            ['id' => uuid_create(UUID_TYPE_RANDOM), 'nome_estado' => 'SP', 'geom' => null]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estados_geometria', function(Blueprint $table){ $table->dropSpatialIndex(['geom']); });
        Schema::table('municipios_geometria', function(Blueprint $table){ $table->dropSpatialIndex(['geom']); });
        Schema::dropIfExists('pontos_usuario');
        Schema::dropIfExists('municipios_geometria');
        Schema::dropIfExists('estados_geometria');
        DB::statement('DROP EXTENSION IF EXISTS postgis;');
    }
};