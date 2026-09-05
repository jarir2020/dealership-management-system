<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixShopkeepersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * The Shopkeeper model uses Laravel's SoftDeletes trait, but the original
     * dms.sql dump did not include a deleted_at column. This migration adds it.
     *
     * Business code in ShopRegistrationController / DeliveryController /
     * ReturnProductController also reads Shopkeeper->area_id, but the original
     * dump omitted that column too. This migration adds it (nullable, FK to
     * areas.id with ON DELETE SET NULL — deleting an area should not cascade-
     * delete shopkeepers, only unassign them).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shopkeepers', function (Blueprint $table) {
            // Required by SoftDeletes trait on App\Shopkeeper
            $table->softDeletes();

            // Required by ShopRegistrationController, DeliveryController,
            // ReturnProductController (all read $shopkeeper->area_id)
            $table->unsignedBigInteger('area_id')
                  ->nullable()
                  ->after('address');
            $table->foreign('area_id')
                  ->references('id')->on('areas')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shopkeepers', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropColumn(['area_id', 'deleted_at']);
        });
    }
}
