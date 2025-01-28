<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class AddMdmOrgDept extends Migration
{
    public function up()
    {
        $capsule = new Capsule();

        $capsule::schema()->table('mdm_status', function (Blueprint $table) {
            $table->string('org_department')->nullable();

            $table->index('org_department');
        });
    }

    public function down()
    {
        $capsule = new Capsule();
          $capsule::schema()->table('mdm_status', function (Blueprint $table) {
            $table->dropColumn('org_department');
        });
    }
}
