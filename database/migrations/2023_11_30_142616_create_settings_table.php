<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->integer('status')->default(1);

            $table->string('groom_fullname')->nullable();
            $table->string('groom_nickname')->nullable();
            $table->text('groom_about')->nullable();
            $table->text('groom_tiktok')->nullable();
            $table->text('groom_facebook')->nullable();
            $table->text('groom_instagram')->nullable();
            $table->longText('groom_picture')->nullable();

            $table->string('bride_fullname')->nullable();
            $table->string('bride_nickname')->nullable();
            $table->text('bride_about')->nullable();
            $table->text('bride_tiktok')->nullable();
            $table->text('bride_facebook')->nullable();
            $table->text('bride_instagram')->nullable();
            $table->longText('bride_picture')->nullable();

            $table->longText('about_us_bg')->nullable();

            $table->string('first_meet_date')->nullable();
            $table->longText('first_meet_picture')->nullable();
            $table->text('first_meet_about')->nullable();
            $table->string('first_date_date')->nullable();
            $table->longText('first_date_picture')->nullable();
            $table->text('first_date_about')->nullable();
            $table->string('engagement_date')->nullable();
            $table->longText('engagement_picture')->nullable();
            $table->text('engagement_about')->nullable();

            $table->longText('event_bg')->nullable();
            $table->string('event_ceremonial_date')->nullable();
            $table->string('event_ceremonial_start_time')->nullable();
            $table->string('event_ceremonial_end_time')->nullable();
            $table->text('event_ceremonial_address')->nullable();
            $table->text('event_ceremonial_link')->nullable();
            $table->string('event_party_date')->nullable();
            $table->string('event_party_start_time')->nullable();
            $table->string('event_party_end_time')->nullable();
            $table->text('event_party_address')->nullable();
            $table->text('event_party_link')->nullable();
            $table->string('event_traditional_date')->nullable();
            $table->string('event_traditional_start_time')->nullable();
            $table->string('event_traditional_end_time')->nullable();
            $table->text('event_traditional_address')->nullable();
            $table->text('event_traditional_link')->nullable();

            $table->string('accompanist_fullname_1')->nullable();
            $table->string('accompanist_type_1')->nullable();
            $table->string('accompanist_status_1')->nullable();
            $table->text('accompanist_tiktok_1')->nullable();
            $table->text('accompanist_facebook_1')->nullable();
            $table->text('accompanist_instagram_1')->nullable();
            $table->longText('accompanist_picture_1')->nullable();
            $table->string('accompanist_fullname_2')->nullable();
            $table->string('accompanist_type_2')->nullable();
            $table->string('accompanist_status_2')->nullable();
            $table->text('accompanist_tiktok_2')->nullable();
            $table->text('accompanist_facebook_2')->nullable();
            $table->text('accompanist_instagram_2')->nullable();
            $table->longText('accompanist_picture_2')->nullable();
            $table->string('accompanist_fullname_3')->nullable();
            $table->string('accompanist_type_3')->nullable();
            $table->string('accompanist_status_3')->nullable();
            $table->text('accompanist_tiktok_3')->nullable();
            $table->text('accompanist_facebook_3')->nullable();
            $table->text('accompanist_instagram_3')->nullable();
            $table->longText('accompanist_picture_3')->nullable();
            $table->string('accompanist_fullname_4')->nullable();
            $table->string('accompanist_type_4')->nullable();
            $table->string('accompanist_status_4')->nullable();
            $table->text('accompanist_tiktok_4')->nullable();
            $table->text('accompanist_facebook_4')->nullable();
            $table->text('accompanist_instagram_4')->nullable();
            $table->longText('accompanist_picture_4')->nullable();
            $table->string('accompanist_fullname_5')->nullable();
            $table->string('accompanist_type_5')->nullable();
            $table->string('accompanist_status_5')->nullable();
            $table->text('accompanist_tiktok_5')->nullable();
            $table->text('accompanist_facebook_5')->nullable();
            $table->text('accompanist_instagram_5')->nullable();
            $table->longText('accompanist_picture_5')->nullable();
            $table->string('accompanist_fullname_6')->nullable();
            $table->string('accompanist_type_6')->nullable();
            $table->string('accompanist_status_6')->nullable();
            $table->text('accompanist_tiktok_6')->nullable();
            $table->text('accompanist_facebook_6')->nullable();
            $table->text('accompanist_instagram_6')->nullable();
            $table->longText('accompanist_picture_6')->nullable();

            $table->longText('gallery_1')->nullable();
            $table->longText('gallery_2')->nullable();
            $table->longText('gallery_3')->nullable();
            $table->longText('gallery_4')->nullable();
            $table->longText('gallery_5')->nullable();

            $table->longText('reservation_bg')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
