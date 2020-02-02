<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_client')->default(0);
            $table->integer('job_type');
            $table->integer('shift_type');
            $table->integer('number_utes');
            $table->integer('number_trafic');
            $table->string('address');
            $table->string('location');
            $table->string('setup_required');
            $table->string('notes');
            $table->date('date');
            $table->date('time_start');
            $table->integer('status')->default(0);
            $table->boolean('tbc')->default(false);
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
        Schema::dropIfExists('jobs');
    }
}
