<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnimalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('identifiant')->nullable();
            $table->string('photo')->nullable();
            $table->string('categorie')->nullable();
            $table->string('race')->nullable();
            $table->string('sexe')->nullable();
            $table->date('date_naiss')->nullable();
            $table->string('age')->nullable();
            $table->string('poids')->nullable();
            $table->string('type')->nullable();
            $table->string('observation')->nullable();
            $table->timestamps();
        });
        Schema::create('reproductions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Animal::class,'productrice')->nullable()
                ->index()
                ->references('id')->on('animals');
            $table->foreignIdFor(\App\Models\Animal::class,'belier')->nullable()
                ->index()
                ->references('id')->on('animals');
            $table->date('date')->nullable();
            $table->string('nbr_anneau')->nullable();
            $table->string('diagnostic')->nullable();
            $table->date('date_prevue')->nullable();
            $table->timestamps();
        });
        Schema::create('embauches', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Animal::class)->nullable()
                ->index()
                ->references('id')->on('animals');
            $table->string('prix_init')->nullable();
            $table->string('prix_vente')->nullable();
            $table->string('poids_entre')->nullable();
            $table->string('poids_sortie')->nullable();
            $table->boolean('statut')->nullable();
            $table->date('date_entre')->nullable();
            $table->date('date_prevue')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('animals');
    }
}
