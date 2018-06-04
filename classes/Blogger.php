<?php

require_once __DIR__ .'/Adapter.php';

class Blogger extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'ac_id' => '',
        'display_name' => '',
        'photo' => '',
        'sex' => '',
        'class' => '',
        'description' => '',
        'payment_method' => '',
        'payment_ticket' => '',
        'main_bank_id' => '',
        'shared_bank_id' => '',
        'personnel_info' => '',
        'personnel_comment' => '',
        'true_name' => '',
        'contact' => '',
        'telephone1' => '',
        'telephone2' => '',
        'address1' => '',
        'address2' => '',
        'address3' => '',
        'email1' => '',
        'email2' => '',
        'email3' => '',
        'birthday' => '',
        'idnumber' => '',
        'registration' => '',
        'comment' => '',

        'blog_name' => '',
        'blog_link' => '',
        'blog_flow' => '',
        'blog_article_invite_price' => '',
        'blog_article_price' => '',
        'blog_article_attend_invite_price' => '',
        'blog_article_attend_price' => '',
        'blog_other_price' => '',
        'blog_definition' => '',

        'fb_name' => '',
        'fb_link' => '',
        'fb_fans' => '',
        'fb_post_invite_price' => '',
        'fb_post_price' => '',
        'fb_video_invite_price' => '',
        'fb_video_price' => '',
        'fb_live_invite_price' => '',
        'fb_live_price' => '',
        'fb_share_invite_price' => '',
        'fb_share_price' => '',
        'fb_checkin_attend_invite_price' => '',
        'fb_checkin_attend_price' => '',
        'fb_post_attend_invite_price' => '',
        'fb_post_attend_price' => '',
        'fb_video_attend_invite_price' => '',
        'fb_video_attend_price' => '',
        'fb_live_attend_invite_price' => '',
        'fb_live_attend_price' => '',
        'fb_other_price' => '',
        'fb_definition' => '',

        'ig_name' => '',
        'ig_link' => '',
        'ig_fans' => '',
        'ig_image_invite_price' => '',
        'ig_image_price' => '',
        'ig_sidecar_invite_price' => '',
        'ig_sidecar_price' => '',
        'ig_video_invite_price' => '',
        'ig_video_price' => '',
        'ig_live_invite_price' => '',
        'ig_live_price' => '',
        'ig_limited_post_invite_price' => '',
        'ig_limited_post_price' => '',
        'ig_image_attend_invite_price' => '',
        'ig_image_attend_price' => '',
        'ig_sidecar_attend_invite_price' => '',
        'ig_sidecar_attend_price' => '',
        'ig_video_attend_invite_price' => '',
        'ig_video_attend_price' => '',
        'ig_live_attend_invite_price' => '',
        'ig_live_attend_price' => '',
        'ig_limited_post_attend_invite_price' => '',
        'ig_limited_post_attend_price' => '',
        'ig_other_price' => '',
        'ig_definition' => '',

        'youtube_name' => '',
        'youtube_link' => '',
        'youtube_fans' => '',
        'youtube_video_invite_price' => '',
        'youtube_video_price' => '',
        'youtube_live_invite_price' => '',
        'youtube_live_price' => '',
        'youtube_post_to_fb_price' => '',
        'youtube_post_to_fb_unit' => '',
        'youtube_auth_to_net_price' => '',
        'youtube_auth_to_net_unit' => '',
        'youtube_raw_editable_auth_price' => '',
        'youtube_raw_editable_auth_unit' => '',
        'youtube_raw_readable_auth_price' => '',
        'youtube_raw_readable_auth_unit' => '',
        'youtube_other_price' => '',
        'youtube_definition' => '',

        'fbads_share_to_self_fb_price' => '',
        'fbads_share_to_self_fb_unit' => '',
        'fbads_share_to_self_fb_invite_price' => '',
        'fbads_share_to_self_fb_invite_unit' => '',
        'fbads_share_to_self_ig_price' => '',
        'fbads_share_to_self_ig_unit' => '',
        'fbads_share_to_self_ig_invite_price' => '',
        'fbads_share_to_self_ig_invite_unit' => '',
        'fbads_share_to_customer_fb_price' => '',
        'fbads_share_to_customer_fb_unit' => '',
        'fbads_share_to_customer_fb_invite_price' => '',
        'fbads_share_to_customer_fb_invite_unit' => '',
        'fbads_share_to_client_fb_with_ad_price' => '',
        'fbads_share_to_client_fb_with_ad_unit' => '',
        'fbads_share_to_client_fb_with_ad_invite_price' => '',
        'fbads_share_to_client_fb_with_ad_invite_unit' => '',
        'fbads_client_with_js_price' => '',
        'fbads_client_with_js_unit' => '',
        'fbads_client_with_customer_price' => '',
        'fbads_client_with_customer_unit' => '',
        'fbads_do_it_self_price' => '',
        'fbads_do_it_self_unit' => '',
        'fbads_to_sponsor_price' => '',
        'fbads_to_sponsor_unit' => '',
        'fbads_definition' => '',
        
        'auth_quote_to_website_with_feedback_price' => '',
        'auth_quote_to_website_with_feedback_unit' => '',
        'auth_quote_to_website_with_feedback_invite_price' => '',
        'auth_quote_to_website_with_feedback_invite_unit' => '',
        'auth_quote_to_website_without_feedback_price' => '',
        'auth_quote_to_website_without_feedback_unit' => '',
        'auth_quote_to_website_without_feedback_invite_price' => '',
        'auth_quote_to_website_without_feedback_invite_unit' => '',
        'auth_quote_to_ec_with_feedback_price' => '',
        'auth_quote_to_ec_with_feedback_unit' => '',
        'auth_quote_to_ec_with_feedback_invite_price' => '',
        'auth_quote_to_ec_with_feedback_invite_unit' => '',
        'auth_quote_to_ec_without_feedback_price' => '',
        'auth_quote_to_ec_without_feedback_unit' => '',
        'auth_quote_to_ec_without_feedback_invite_price' => '',
        'auth_quote_to_ec_without_feedback_invite_unit' => '',
        'auth_quote_to_dm_price' => '',
        'auth_quote_to_dm_unit' => '',
        'auth_quote_to_dm_invite_price' => '',
        'auth_quote_to_dm_invite_unit' => '',
        'auth_single_photo_price' => '',
        'auth_single_photo_unit' => '',
        'auth_single_photo_invite_price' => '',
        'auth_single_photo_invite_unit' => '',
        'auth_dispaly_network_price' => '',
        'auth_dispaly_network_unit' => '',
        'auth_dispaly_network_invite_price' => '',
        'auth_dispaly_network_invite_unit' => '',
        'auth_native_ads_price' => '',
        'auth_native_ads_unit' => '',
        'auth_native_ads_invite_price' => '',
        'auth_native_ads_invite_unit' => '',
        'auth_definition' => '',

        'other_attend_without_interview_invite_price' => '',
        'other_attend_without_interview_price' => '',
        'other_shoot_invite_price' => '',
        'other_shoot_price' => '',
        'other_annual_endorse' => '',
        'other_more_cooperation' => '',

        'history' => '',
    ];

    const PAYMENT_METHOD = [
        'tax_included' => 1,
        'real_amount' => 2,
        'tax_excluded_with_invoice' => 3,
        'real_amount_without_2nhi' => 4,
    ];

    const PAYMENT_METHOD_TEXT = [
        1 => '含稅',
        2 => '實拿',
        3 => '未稅開發票',
        4 => '實拿(免二代健保)',
    ];

    const PAYMENT_TICKET = [
        'in_60_days' => 1,
        'in_30_days' => 2,
        'in_15_days' => 3,
        'in_7_days' => 4,
        'current_month' => 5,
        'twice' => 6,
        'cuurent_day' => 7,
    ];

    const PAYMENT_TICKET_TEXT = [
        1 => '上線後60天內',
        2 => '上線後30天內',
        3 => '上線後15天內',
        4 => '上線後7天內',
        5 => '上線當月(次月5日前)',
        6 => '刊前、刊後付款50%',
        7 => '上線前/當天付款完成',
    ];

    public function __construct($id=0, $key='id')
    {
        parent::__construct($id, $key);
    }

    public function getName()
    {
        if ($this->getId()) {
            $name = $this->getVar('fb_name');

            if (empty($name)) {
                $name = $this->getVar('blogger_name');
            }

            if (empty($name)) {
                $name = $this->getVar('ig_name');
            }

            if (empty($name)) {
                $name = $this->getVar('youtube_name');
            }

            if (empty($name)) {
                $name = $this->getVar('display_name');
            }

            return $name;
        }

        return '';
    }
}
