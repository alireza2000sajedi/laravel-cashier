<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        $this->table = (new \Ars\Cashier\Models\Transaction())->getTable();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->morphs('transactionable');
            $table->decimal('amount', 16, 4)->default(0.00);
            $table->enum('type', ['deposit', 'withdraw']);
            $table->boolean('accepted')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('type');
            $table->index('accepted');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
};
