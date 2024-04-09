<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();  
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('pivot_id')->constrained('orders')->onDelete('cascade');
            $table->string('transaction_id');
            $table->string('name');
            $table->string('email');
            $table->string('order_id');
            $table->string('transaction_status');
            $table->string('transaction_time');
            $table->string('gross_amount');
            $table->string('fraud_status');
            $table->string('payment_type');
            $table->string('bank');
            $table->string('no_va');
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
        Schema::dropIfExists('transaksis');
    }
}
