<?php

function fbfbi_db_to_1_1()
{
    update_option( 'fbfpi_fanpage_id',              get_option( 'skip_framework_value_fbfpi_settings_page_id' ) );
    update_option( 'fbfpi_fanpage_stream_language', get_option( 'skip_framework_value_fbfpi_settings_stream_language' ) );
    update_option( 'fbfpi_import_interval',         get_option( 'skip_framework_value_fbfpi_settings_update_interval' ) );
    update_option( 'fbfpi_import_num',              get_option( 'skip_framework_value_fbfpi_settings_update_num' ) );
    update_option( 'fbfpi_insert_post_type',        get_option( 'skip_framework_value_fbfpi_settings_insert_post_type' ) );
    update_option( 'fbfpi_insert_term_id',          get_option( 'skip_framework_value_fbfpi_settings_insert_term_id' ) );
    update_option( 'fbfpi_insert_user_id',          get_option( 'skip_framework_value_fbfpi_settings_insert_user_id' ) );
    update_option( 'fbfpi_insert_post_status',      get_option( 'skip_framework_value_fbfpi_settings_insert_post_status' ) );
    update_option( 'fbfpi_insert_link_target',      get_option( 'skip_framework_value_fbfpi_settings_link_target' ) );
    update_option( 'fbfpi_insert_post_format',      get_option( 'skip_framework_value_fbfpi_settings_insert_post_format' ) );
    update_option( 'fbfpi_deactivate_css',          get_option( 'skip_framework_value_fbfpi_settings_update_interval' ) );
    update_option( 'fbfpi_deactivate_css',          get_option( 'skip_framework_value_fbfpi_settings_own_css' ) );
}