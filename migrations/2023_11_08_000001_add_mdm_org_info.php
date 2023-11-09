<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class AddMdmOrgInfo extends Migration
{
    public function up()
    {
        $capsule = new Capsule();

        $capsule::schema()->table('mdm_status', function (Blueprint $table) {
            $table->integer('is_supervised')->nullable();
            $table->integer('enrolled_in_dep')->nullable();
            $table->integer('denies_activation_lock')->nullable();
            $table->integer('activation_lock_manageable')->nullable();
            $table->integer('is_user_approved')->nullable();
            $table->integer('is_user_enrollment')->nullable();
            $table->integer('managed_via_mdm')->nullable();
            $table->text('org_address_full')->nullable();
            $table->string('org_address')->nullable();
            $table->string('org_city')->nullable();
            $table->string('org_country')->nullable();
            $table->string('org_email')->nullable();
            $table->string('org_magic')->nullable();
            $table->string('org_name')->nullable();
            $table->string('org_phone')->nullable();
            $table->string('org_support_email')->nullable();
            $table->string('org_zip_code')->nullable();
            $table->string('original_os_version')->nullable();
            $table->string('mdm_server_url_full')->nullable();

            $table->index('is_supervised');
            $table->index('enrolled_in_dep');
            $table->index('denies_activation_lock');
            $table->index('activation_lock_manageable');
            $table->index('is_user_approved');
            $table->index('is_user_enrollment');
            $table->index('managed_via_mdm');
        });
    }

    public function down()
    {
        $capsule = new Capsule();
          $capsule::schema()->table('mdm_status', function (Blueprint $table) {
            $table->dropColumn('is_supervised');
            $table->dropColumn('enrolled_in_dep');
            $table->dropColumn('denies_activation_lock');
            $table->dropColumn('activation_lock_manageable');
            $table->dropColumn('is_user_approved');
            $table->dropColumn('is_user_enrollment');
            $table->dropColumn('managed_via_mdm');
            $table->dropColumn('org_address_full');
            $table->dropColumn('org_address');
            $table->dropColumn('org_city');
            $table->dropColumn('org_country');
            $table->dropColumn('org_email');
            $table->dropColumn('org_magic');
            $table->dropColumn('org_name');
            $table->dropColumn('org_phone');
            $table->dropColumn('org_support_email');
            $table->dropColumn('org_zip_code');
            $table->dropColumn('original_os_version');
            $table->dropColumn('mdm_server_url_full');
        });
    }
}
