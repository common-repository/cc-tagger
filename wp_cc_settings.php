<?php

function mc_cc_activate() {
	/*
		register_setting('media','mc_cc_area_setting_name');
		register_setting('media','mc_cc_image_setting_name');
		register_setting('media','mc_cc_default_com_setting_name');
		register_setting('media','mc_cc_default_mod_setting_name');
	*/

	if (!trim(get_option('mc_cc_area_setting_name'))){
		update_option('mc_cc_area_setting_name', "");

	}
	if (!trim(get_option('mc_cc_image_setting_name'))){
		update_option('mc_cc_image_setting_name', "80x85");

	}
	if (!trim(get_option('mc_cc_default_com_setting_name'))){
		update_option('mc_cc_default_com_setting_name', "-nc");

	}


	if (!trim(get_option('mc_cc_default_mod_setting_name'))){
		update_option('mc_cc_default_mod_setting_name', "-nd");

	}


}
?>