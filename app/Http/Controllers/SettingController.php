<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Setting;

class SettingController extends Controller
{
    function save(Request $request) {
        DB::beginTransaction();
        try {
            $data = Setting::find(1);
            if(!$data) {
                DB::rollback();
                return setRes(null, 404);
            }

            $data->status = $request->status ?? $data->status ?? Setting::$inactive;
            $data->groom_fullname = $request->groom_fullname ?? $data->groom_fullname ?? "";
            $data->groom_nickname = $request->groom_nickname ?? $data->groom_nickname ?? "";
            $data->groom_about = $request->groom_about ?? $data->groom_about ?? "";
            $data->groom_tiktok = $request->groom_tiktok ?? $data->groom_tiktok ?? "";
            $data->groom_facebook = $request->groom_facebook ?? $data->groom_facebook ?? "";
            $data->groom_instagram = $request->groom_instagram ?? $data->groom_instagram ?? "";
            $data->groom_picture = $request->groom_picture ?? $data->groom_picture ?? "";
            $data->bride_fullname = $request->bride_fullname ?? $data->bride_fullname ?? "";
            $data->bride_nickname = $request->bride_nickname ?? $data->bride_nickname ?? "";
            $data->bride_about = $request->bride_about ?? $data->bride_about ?? "";
            $data->bride_tiktok = $request->bride_tiktok ?? $data->bride_tiktok ?? "";
            $data->bride_facebook = $request->bride_facebook ?? $data->bride_facebook ?? "";
            $data->bride_instagram = $request->bride_instagram ?? $data->bride_instagram ?? "";
            $data->bride_picture = $request->bride_picture ?? $data->bride_picture ?? "";
            $data->home_couple_picture = $request->home_couple_picture ?? $data->home_couple_picture ?? "";
            $data->about_us_bg = $request->about_us_bg ?? $data->about_us_bg ?? "";
            $data->first_meet_date = $request->first_meet_date ?? $data->first_meet_date ?? null;
            $data->first_meet_picture = $request->first_meet_picture ?? $data->first_meet_picture ?? "";
            $data->first_meet_about = $request->first_meet_about ?? $data->first_meet_about ?? "";
            $data->first_date_date = $request->first_date_date ?? $data->first_date_date ?? null;
            $data->first_date_picture = $request->first_date_picture ?? $data->first_date_picture ?? "";
            $data->first_date_about = $request->first_date_about ?? $data->first_date_about ?? "";
            $data->engagement_date = $request->engagement_date ?? $data->engagement_date ?? null;
            $data->engagement_picture = $request->engagement_picture ?? $data->engagement_picture ?? "";
            $data->engagement_about = $request->engagement_about ?? $data->engagement_about ?? "";
            $data->event_bg = $request->event_bg ?? $data->event_bg ?? "";
            $data->event_ceremonial_date = $request->event_ceremonial_date ?? $data->event_ceremonial_date ?? null;
            $data->event_ceremonial_start_time = $request->event_ceremonial_start_time ?? $data->event_ceremonial_start_time ?? "";
            $data->event_ceremonial_end_time = $request->event_ceremonial_end_time ?? $data->event_ceremonial_end_time ?? "";
            $data->event_ceremonial_address = $request->event_ceremonial_address ?? $data->event_ceremonial_address ?? "";
            $data->event_ceremonial_link = $request->event_ceremonial_link ?? $data->event_ceremonial_link ?? "";
            $data->event_party_date = $request->event_party_date ?? $data->event_party_date ?? null;
            $data->event_party_start_time = $request->event_party_start_time ?? $data->event_party_start_time ?? "";
            $data->event_party_end_time = $request->event_party_end_time ?? $data->event_party_end_time ?? "";
            $data->event_party_address = $request->event_party_address ?? $data->event_party_address ?? "";
            $data->event_party_link = $request->event_party_link ?? $data->event_party_link ?? "";
            $data->event_traditional_date = $request->event_traditional_date ?? $data->event_traditional_date ?? null;
            $data->event_traditional_start_time = $request->event_traditional_start_time ?? $data->event_traditional_start_time ?? "";
            $data->event_traditional_end_time = $request->event_traditional_end_time ?? $data->event_traditional_end_time ?? "";
            $data->event_traditional_address = $request->event_traditional_address ?? $data->event_traditional_address ?? "";
            $data->event_traditional_link = $request->event_traditional_link ?? $data->event_traditional_link ?? "";
            $data->accompanist_fullname_1 = $request->accompanist_fullname_1 ?? $data->accompanist_fullname_1 ?? "";
            $data->accompanist_type_1 = $request->accompanist_type_1 ?? $data->accompanist_type_1 ?? "";
            $data->accompanist_status_1 = $request->accompanist_status_1 ?? $data->accompanist_status_1 ?? "";
            $data->accompanist_tiktok_1 = $request->accompanist_tiktok_1 ?? $data->accompanist_tiktok_1 ?? "";
            $data->accompanist_facebook_1 = $request->accompanist_facebook_1 ?? $data->accompanist_facebook_1 ?? "";
            $data->accompanist_instagram_1 = $request->accompanist_instagram_1 ?? $data->accompanist_instagram_1 ?? "";
            $data->accompanist_picture_1 = $request->accompanist_picture_1 ?? $data->accompanist_picture_1 ?? "";
            $data->accompanist_fullname_2 = $request->accompanist_fullname_2 ?? $data->accompanist_fullname_2 ?? "";
            $data->accompanist_type_2 = $request->accompanist_type_2 ?? $data->accompanist_type_2 ?? "";
            $data->accompanist_status_2 = $request->accompanist_status_2 ?? $data->accompanist_status_2 ?? "";
            $data->accompanist_tiktok_2 = $request->accompanist_tiktok_2 ?? $data->accompanist_tiktok_2 ?? "";
            $data->accompanist_facebook_2 = $request->accompanist_facebook_2 ?? $data->accompanist_facebook_2 ?? "";
            $data->accompanist_instagram_2 = $request->accompanist_instagram_2 ?? $data->accompanist_instagram_2 ?? "";
            $data->accompanist_picture_2 = $request->accompanist_picture_2 ?? $data->accompanist_picture_2 ?? "";
            $data->accompanist_fullname_3 = $request->accompanist_fullname_3 ?? $data->accompanist_fullname_3 ?? "";
            $data->accompanist_type_3 = $request->accompanist_type_3 ?? $data->accompanist_type_3 ?? "";
            $data->accompanist_status_3 = $request->accompanist_status_3 ?? $data->accompanist_status_3 ?? "";
            $data->accompanist_tiktok_3 = $request->accompanist_tiktok_3 ?? $data->accompanist_tiktok_3 ?? "";
            $data->accompanist_facebook_3 = $request->accompanist_facebook_3 ?? $data->accompanist_facebook_3 ?? "";
            $data->accompanist_instagram_3 = $request->accompanist_instagram_3 ?? $data->accompanist_instagram_3 ?? "";
            $data->accompanist_picture_3 = $request->accompanist_picture_3 ?? $data->accompanist_picture_3 ?? "";
            $data->accompanist_fullname_4 = $request->accompanist_fullname_4 ?? $data->accompanist_fullname_4 ?? "";
            $data->accompanist_type_4 = $request->accompanist_type_4 ?? $data->accompanist_type_4 ?? "";
            $data->accompanist_status_4 = $request->accompanist_status_4 ?? $data->accompanist_status_4 ?? "";
            $data->accompanist_tiktok_4 = $request->accompanist_tiktok_4 ?? $data->accompanist_tiktok_4 ?? "";
            $data->accompanist_facebook_4 = $request->accompanist_facebook_4 ?? $data->accompanist_facebook_4 ?? "";
            $data->accompanist_instagram_4 = $request->accompanist_instagram_4 ?? $data->accompanist_instagram_4 ?? "";
            $data->accompanist_fullname_5 = $request->accompanist_fullname_5 ?? $data->accompanist_fullname_5 ?? "";
            $data->accompanist_type_5 = $request->accompanist_type_5 ?? $data->accompanist_type_5 ?? "";
            $data->accompanist_status_5 = $request->accompanist_status_5 ?? $data->accompanist_status_5 ?? "";
            $data->accompanist_tiktok_5 = $request->accompanist_tiktok_5 ?? $data->accompanist_tiktok_5 ?? "";
            $data->accompanist_facebook_5 = $request->accompanist_facebook_5 ?? $data->accompanist_facebook_5 ?? "";
            $data->accompanist_instagram_5 = $request->accompanist_instagram_5 ?? $data->accompanist_instagram_5 ?? "";
            $data->accompanist_picture_5 = $request->accompanist_picture_5 ?? $data->accompanist_picture_5 ?? "";
            $data->accompanist_fullname_6 = $request->accompanist_fullname_6 ?? $data->accompanist_fullname_6 ?? "";
            $data->accompanist_type_6 = $request->accompanist_type_6 ?? $data->accompanist_type_6 ?? "";
            $data->accompanist_status_6 = $request->accompanist_status_6 ?? $data->accompanist_status_6 ?? "";
            $data->accompanist_tiktok_6 = $request->accompanist_tiktok_6 ?? $data->accompanist_tiktok_6 ?? "";
            $data->accompanist_facebook_6 = $request->accompanist_facebook_6 ?? $data->accompanist_facebook_6 ?? "";
            $data->accompanist_instagram_6 = $request->accompanist_instagram_6 ?? $data->accompanist_instagram_6 ?? "";
            $data->accompanist_picture_6 = $request->accompanist_picture_6 ?? $data->accompanist_picture_6 ?? "";
            $data->gallery_1 = $request->gallery_1 ?? $data->gallery_1 ?? "";
            $data->gallery_2 = $request->gallery_2 ?? $data->gallery_2 ?? "";
            $data->gallery_3 = $request->gallery_3 ?? $data->gallery_3 ?? "";
            $data->gallery_4 = $request->gallery_4 ?? $data->gallery_4 ?? "";
            $data->gallery_5 = $request->gallery_5 ?? $data->gallery_5 ?? "";
            $data->reservation_bg = $request->reservation_bg ?? $data->reservation_bg ?? "";
            $data->save();
            DB::commit();
            unset($data['id']);
            unset($data['created_at']);
            unset($data['updated_at']);
            return setRes(null, 201);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function get() {
        try {
            $data = Setting::find(1);
            if (!$data) return setRes(null, 404);

            if (!$data->groom_fullname) $data->groom_fullname = "";
            if (!$data->groom_nickname) $data->groom_nickname = "";
            if (!$data->groom_about) $data->groom_about = "";
            if (!$data->groom_tiktok) $data->groom_tiktok = "";
            if (!$data->groom_facebook) $data->groom_facebook = "";
            if (!$data->groom_instagram) $data->groom_instagram = "";
            if (!$data->groom_picture) $data->groom_picture = "";
            if (!$data->bride_fullname) $data->bride_fullname = "";
            if (!$data->bride_nickname) $data->bride_nickname = "";
            if (!$data->bride_about) $data->bride_about = "";
            if (!$data->bride_tiktok) $data->bride_tiktok = "";
            if (!$data->bride_facebook) $data->bride_facebook = "";
            if (!$data->bride_instagram) $data->bride_instagram = "";
            if (!$data->bride_picture) $data->bride_picture = "";
            if (!$data->home_couple_picture) $data->home_couple_picture = "";
            if (!$data->about_us_bg) $data->about_us_bg = "";
            if (!$data->first_meet_date) $data->first_meet_date = null;
            if (!$data->first_meet_picture) $data->first_meet_picture = "";
            if (!$data->first_meet_about) $data->first_meet_about = "";
            if (!$data->first_date_date) $data->first_date_date = null;
            if (!$data->first_date_picture) $data->first_date_picture = "";
            if (!$data->first_date_about) $data->first_date_about = "";
            if (!$data->engagement_date) $data->engagement_date = null;
            if (!$data->engagement_picture) $data->engagement_picture = "";
            if (!$data->engagement_about) $data->engagement_about = "";
            if (!$data->event_bg) $data->event_bg = "";
            if (!$data->event_ceremonial_date) $data->event_ceremonial_date = null;
            if (!$data->event_ceremonial_start_time) $data->event_ceremonial_start_time = "";
            if (!$data->event_ceremonial_end_time) $data->event_ceremonial_end_time = "";
            if (!$data->event_ceremonial_address) $data->event_ceremonial_address = "";
            if (!$data->event_ceremonial_link) $data->event_ceremonial_link = "";
            if (!$data->event_party_date) $data->event_party_date = null;
            if (!$data->event_party_start_time) $data->event_party_start_time = "";
            if (!$data->event_party_end_time) $data->event_party_end_time = "";
            if (!$data->event_party_address) $data->event_party_address = "";
            if (!$data->event_party_link) $data->event_party_link = "";
            if (!$data->event_traditional_date) $data->event_traditional_date = null;
            if (!$data->event_traditional_start_time) $data->event_traditional_start_time = "";
            if (!$data->event_traditional_end_time) $data->event_traditional_end_time = "";
            if (!$data->event_traditional_address) $data->event_traditional_address = "";
            if (!$data->event_traditional_link) $data->event_traditional_link = "";
            if (!$data->accompanist_fullname_1) $data->accompanist_fullname_1 = "";
            if (!$data->accompanist_type_1) $data->accompanist_type_1 = "";
            if (!$data->accompanist_status_1) $data->accompanist_status_1 = "";
            if (!$data->accompanist_tiktok_1) $data->accompanist_tiktok_1 = "";
            if (!$data->accompanist_facebook_1) $data->accompanist_facebook_1 = "";
            if (!$data->accompanist_instagram_1) $data->accompanist_instagram_1 = "";
            if (!$data->accompanist_picture_1) $data->accompanist_picture_1 = "";
            if (!$data->accompanist_fullname_2) $data->accompanist_fullname_2 = "";
            if (!$data->accompanist_type_2) $data->accompanist_type_2 = "";
            if (!$data->accompanist_status_2) $data->accompanist_status_2 = "";
            if (!$data->accompanist_tiktok_2) $data->accompanist_tiktok_2 = "";
            if (!$data->accompanist_facebook_2) $data->accompanist_facebook_2 = "";
            if (!$data->accompanist_instagram_2) $data->accompanist_instagram_2 = "";
            if (!$data->accompanist_picture_2) $data->accompanist_picture_2 = "";
            if (!$data->accompanist_fullname_3) $data->accompanist_fullname_3 = "";
            if (!$data->accompanist_type_3) $data->accompanist_type_3 = "";
            if (!$data->accompanist_status_3) $data->accompanist_status_3 = "";
            if (!$data->accompanist_tiktok_3) $data->accompanist_tiktok_3 = "";
            if (!$data->accompanist_facebook_3) $data->accompanist_facebook_3 = "";
            if (!$data->accompanist_instagram_3) $data->accompanist_instagram_3 = "";
            if (!$data->accompanist_picture_3) $data->accompanist_picture_3 = "";
            if (!$data->accompanist_fullname_4) $data->accompanist_fullname_4 = "";
            if (!$data->accompanist_type_4) $data->accompanist_type_4 = "";
            if (!$data->accompanist_status_4) $data->accompanist_status_4 = "";
            if (!$data->accompanist_tiktok_4) $data->accompanist_tiktok_4 = "";
            if (!$data->accompanist_facebook_4) $data->accompanist_facebook_4 = "";
            if (!$data->accompanist_instagram_4) $data->accompanist_instagram_4 = "";
            if (!$data->accompanist_fullname_5) $data->accompanist_fullname_5 = "";
            if (!$data->accompanist_type_5) $data->accompanist_type_5 = "";
            if (!$data->accompanist_status_5) $data->accompanist_status_5 = "";
            if (!$data->accompanist_tiktok_5) $data->accompanist_tiktok_5 = "";
            if (!$data->accompanist_facebook_5) $data->accompanist_facebook_5 = "";
            if (!$data->accompanist_instagram_5) $data->accompanist_instagram_5 = "";
            if (!$data->accompanist_picture_5) $data->accompanist_picture_5 = "";
            if (!$data->accompanist_fullname_6) $data->accompanist_fullname_6 = "";
            if (!$data->accompanist_type_6) $data->accompanist_type_6 = "";
            if (!$data->accompanist_status_6) $data->accompanist_status_6 = "";
            if (!$data->accompanist_tiktok_6) $data->accompanist_tiktok_6 = "";
            if (!$data->accompanist_facebook_6) $data->accompanist_facebook_6 = "";
            if (!$data->accompanist_instagram_6) $data->accompanist_instagram_6 = "";
            if (!$data->accompanist_picture_6) $data->accompanist_picture_6 = "";
            if (!$data->gallery_1) $data->gallery_1 = "";
            if (!$data->gallery_2) $data->gallery_2 = "";
            if (!$data->gallery_3) $data->gallery_3 = "";
            if (!$data->gallery_4) $data->gallery_4 = "";
            if (!$data->gallery_5) $data->gallery_5 = "";
            if (!$data->reservation_bg) $data->reservation_bg = "";

            unset($data['id']);
            unset($data['created_at']);
            unset($data['updated_at']);
            return setRes($data, 200);
        } catch (\Exception $e) {
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }
}
