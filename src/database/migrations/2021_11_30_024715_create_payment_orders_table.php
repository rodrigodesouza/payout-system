<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('invoice')->comment('Identificador do cliente que deverá ser único para cada cliente e pagamento.');
            $table->bigInteger('user_id')->unsigned();
            $table->string('beneficiary_name', 255);
            $table->string('code_bank', 3);
            $table->string('number_agency', 4);
            $table->string('number_account', 15);
            $table->double('value', 15, 2)->unsigned();
            $table->enum('status', ['created', 'processing', 'processed', 'paid', 'rejected'])->default('created');
            $table->bigInteger('processor_bank_id')->nullable()->unsigned();
            $table->timestamps();

            $table->unique(['invoice', 'user_id']);
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_orders');
    }
}
