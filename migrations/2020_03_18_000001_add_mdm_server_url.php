<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class AddMdmServerUrl extends Migration
{
    public function up()
    {
        $capsule = new Capsule();
        $capsule::schema()->table('mdm_status', function (Blueprint $table) {
            $table->string('mdm_server_url')->nullable()->default('');

            $table->index('mdm_server_url');
        });
    }

    public function down()
    {
        $capsule = new Capsule();
          $capsule::schema()->table('mdm_status', function (Blueprint $table) {
            $table->dropColumn('mdm_server_url');
        });
    }
}