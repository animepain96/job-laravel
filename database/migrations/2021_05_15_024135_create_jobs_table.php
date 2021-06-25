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
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('method_id');
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('price_yen')->default(0);
            $table->date('start_date')->nullable()->default('1111-01-01');
            $table->date('finish_date')->nullable()->default('1111-01-01');
            $table->date('deadline')->nullable()->default('1111-01-01');
            $table->date('pay_date')->nullable()->default('1111-01-01');
            $table->boolean('paid')->default(false);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
