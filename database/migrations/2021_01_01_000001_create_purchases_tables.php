<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_customers', function (Blueprint $table) {
            $table->id();
            $table->string('account')->unique()->comment('Уникальный идентификатор Плательщика.');
            $table->string('email')->nullable()->default('')->comment('Контактный e-mail Плательщика.');
            $table->string('phone')->nullable()->default('')->comment('Контактный телефон Плательщика в международном формате.');
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->comment('Уникальный идентификатор заказа');
            $table->integer('total')->default(0)->comment('Количество позиций в заказе.');
            $table->decimal('amount')->default(0.00)->comment('Сумма заказа в рублях');

            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Наименование позиции.');
            $table->text('details')->nullable()->comment('Детали позиции в Json формате');
            $table->decimal('price')->default(0.00)->comment('Цена позиции (с учётом НДС).');
            $table->integer('quantity')->default(1)->comment('Количество позиций.');

            $table->uuid('order_id')->nullable();
            $table->foreign('order_id')
                ->references('uuid')
                ->on('purchase_orders')
                ->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('target')->nullable()->default('')->comment('Назначение платежа');
            $table->text('payment')->nullable()->comment('Детали платежа в json формате');
            $table->string('status')->nullable()->comment('Статус оплаты заказа');
            $table->timestamp('closed_at')->nullable()->comment('Дата закрытия счета');

            $table->timestamps();

            $table->foreignId('customer_id')
                ->references('id')
                ->on('purchase_customers');

            $table->foreignUuid('order_id')
                ->references('uuid')
                ->on('purchase_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_invoices');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_customers');
    }
}
