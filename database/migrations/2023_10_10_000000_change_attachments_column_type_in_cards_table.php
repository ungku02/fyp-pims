
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAttachmentsColumnTypeInCardsTable extends Migration
{
    public function up()
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->json('attachments')->change();
        });
    }

    public function down()
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->string('attachments')->change();
        });
    }
}