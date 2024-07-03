<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->decimal('total', 10, 2);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
