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
            $table->multiPolygon('geom', 4326)->nullable();
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
            $table->point('geom', 4326)->nullable();
            $table->spatialIndex('geom');
        });

        Schema::create('geojson_states', function(Blueprint $table)
        {
            $table->uuid('id')->primary();
            $table->string('state_acronym')->notNullable()->unique();
            $table->string('geojson_link')->notNullable();
        });
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
        Schema::dropIfExists('geojson_states');
        DB::statement('DROP EXTENSION IF EXISTS postgis;');
    }
};