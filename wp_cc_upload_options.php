<?php

	add_filter("attachment_fields_to_edit", "cc_image_attachment_fields_to_edit", null, 2);
	add_filter("attachment_fields_to_save", "cc_image_attachment_fields_to_save", null, 2);

function cc_image_attachment_fields_to_save($post, $attachment) {
	if( isset($attachment['mc_cc_default_com_setting_name'])){
		update_post_meta($post['ID'], '_mc_cc_default_com_setting_name', $attachment['mc_cc_default_com_setting_name']) . "<br />";
		update_post_meta($post['ID'], '_mc_cc_default_com_setting_name_flag', true) . "<br />";
	}
	

	if( isset($attachment['mc_cc_default_mod_setting_name'])){
		update_post_meta($post['ID'], '_mc_cc_default_mod_setting_name', $attachment['mc_cc_default_mod_setting_name']);
		update_post_meta($post['ID'], '_mc_cc_default_mod_setting_name_flag', true);
	}


	update_post_meta($post['ID'], '_mc_cc_full_setting_name', $attachment['mc_cc_default_com_setting_name'] . $attachment['mc_cc_default_mod_setting_name']);
	return $post;
}

function cc_image_attachment_fields_to_edit($form_fields, $post) {
	// $form_fields is a special array of fields to include in the attachment form
	// $post is the attachment record in the database
	//     $post->post_type == 'attachment'
	// (attachments are treated as posts in WordPress)

	// add our custom field to the $form_fields array
	// input type="text" name/id="attachments[$attachment->ID][custom1]"

	if (true) {

		$form_fields["cc_custom_line"]["label"] = "Creative Commons Tag Options";
		$form_fields["cc_custom_line"]["input"] = "html";
		$form_fields["cc_custom_line"]["html"] = "<hr />";

		global $shareType;
		global $modType;
		$defaultArray = array("default" => __("Default (as defined in Media -> Options)"));
		$shareType = array_merge($defaultArray, $shareType);
		$modType = array_merge($defaultArray, $modType);

		$xxxx = printOptions($shareType, "mc_cc_default_com_setting_name", false , $post->ID);
		$form_fields["cc_com"]["label"] = __("Usage Rights");
		$form_fields["cc_com"]["input"] = "html";
		$form_fields["cc_com"]["html"] = $xxxx;


		$xxxx = printOptions($modType, "mc_cc_default_mod_setting_name", false , $post->ID);
		$form_fields["cc_mod"]["label"] = __("Modification Rights");
		$form_fields["cc_mod"]["input"] = "html";
		$form_fields["cc_mod"]["html"] = $xxxx;
	}
	return $form_fields;
}

?>
