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
            $table->integer('job_type')->nullable();
            $table->integer('shift_type')->nullable();
            $table->integer('number_utes')->nullable();
            $table->integer('number_trafic')->nullable();
            $table->string('address')->nullable();
            $table->string('location')->nullable();
            $table->string('setup_required')->nullable();
            $table->string('notes')->nullable();
            $table->string('gtdc')->nullable();
            $table->string('booking_name')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('time_req_site')->nullable();
            $table->date('date')->nullable();
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
