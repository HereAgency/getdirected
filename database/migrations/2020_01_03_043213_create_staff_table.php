<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('relationship')->nullable(); //EMERGENCIA
            $table->integer('id_user')->nullable();
            $table->string('name');
            $table->string('address');
            $table->string('mobile');
            $table->string('email');
            $table->string('vehicle_registration');
            $table->string('contact'); //EMERGENCIA
            $table->string('phone'); //EMERGENCIA
            $table->date('start_date');
            $table->boolean('vehicle')->default(false);
            // JOBS X STAFF
            // JOBS X ARCHIVOS
            $table->softDeletes();
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
        Schema::dropIfExists('staff');
    }
}
