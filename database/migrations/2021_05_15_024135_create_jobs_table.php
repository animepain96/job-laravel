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
        Schema::create('Job', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->string('Name');
            $table->unsignedBigInteger('CustomerID');
            $table->unsignedBigInteger('TypeID');
            $table->date('StartDate');
            $table->boolean('RealJob')->default(1);
            $table->date('Deadline')->nullable()->default('1111-11-11');
            $table->unsignedBigInteger('Price');
            $table->unsignedBigInteger('PriceYen');
            $table->unsignedBigInteger('MethodID');
            $table->date('Paydate')->nullable()->default('1111-11-11');
            $table->date('FinishDate')->nullable()->default('1111-11-11');
            $table->boolean('Paid')->default(0);
            $table->text('Note')->nullable();
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
        Schema::dropIfExists('Job');
    }
}
