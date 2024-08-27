<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        $this->table = (new \Ars\Cashier\Models\Payment())->getTable();
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
            $table->nullableMorphs('paymentable');
            $table->decimal('amount', 16, 4)->default(0.00);
            $table->string('authority');
            $table->string('ref_id')->nullable();
            $table->string('status_code')->nullable();
            $table->string('gateway', 50);
            $table->timestamp('payed_at')->nullable();
            $table->timestamps();

            $table->index('payed_at');
            $table->index('authority');
            $table->unique(['amount', 'authority']);
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
