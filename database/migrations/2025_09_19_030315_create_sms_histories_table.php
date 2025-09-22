<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('sms_histories', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('manual'); // manual/upload
            $table->string('file_name')->nullable();   // jika upload excel
            $table->text('template')->nullable();      // jika upload excel
            $table->integer('total')->default(0);
            $table->integer('success')->default(0);
            $table->integer('failed')->default(0);
            $table->json('details');                   // detail per nomor
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sms_histories');
    }
}