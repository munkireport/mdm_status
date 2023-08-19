<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class AddMdmWatchdog extends Migration
{
    public function up()
    {
        $capsule = new Capsule();

        $capsule::schema()->table('mdm_status', function (Blueprint $table) {
            $table->bigInteger('last_mdm_kickstart')->nullable();
            $table->bigInteger('last_software_update_kickstart')->nullable();
        });
    }

    public function down()
    {
        $capsule = new Capsule();
          $capsule::schema()->table('mdm_status', function (Blueprint $table) {
            $table->dropColumn('last_mdm_kickstart');
            $table->dropColumn('last_software_update_kickstart');
        });
    }
}
