<?php

// search and replace 'cctag_' with something.

if(is_admin() && trim($_GET['page']) =='cctag-options' && trim($_GET['updated'])){
	add_action('admin_footer','cctag_batch_SQL');	

		global $is_batch_page, $batch_dont_overwrite;
		$is_batch_page = true;
		$batch_dont_overwrite = !get_option('cc_batch_overwrite');
	
		function cctag_batch_SQL(){
			global $is_batch_page, $batch_dont_overwrite, $range_list;	
			$meta_key = '_mc_cc_full_setting_name';
			$excludelist = "";

			$start = get_option('cc_start');
			$end = get_option('cc_end');

			$meta_value = get_option('cc_batch_share') . get_option('cc_batch_mod');
			$meta_value1 = get_option('cc_batch_share');
			$meta_value2 = get_option('cc_batch_mod');
			update_option('cc_batch_mod', 'default');
			update_option('cc_batch_share', 'default');
			update_option('cc_start', '');
			update_option('cc_end'	, '');
			update_option('cc_batch_overwrite', '');
			$range_list="";


			if(!trim($start) || !(sprintf("%d", $start) == $start)){
				$start = 0;
			} else 

			if(!trim($end) || !(sprintf("%d", $end) == $end)){
				$end = get_posts("post_type=any&showposts=1&orderby=ID");


				if(sizeof($end) >= 1){
					$end = $end[0]->ID;
				} elseif ($start == 0){
					$end=0;
				} else {
					$end=0;
				}

			}

			$includelist = "";
			if($end != 0){
				$range_list.= " AND (wp_posts.ID";
				$range_list.= " BETWEEN " . $start . " AND " . $end;
				$range_list.= ")";
			} 


			$excludelist = "";			
			if($batch_dont_overwrite){
				$tagged = get_posts("post_type=attachment&suppress_filters=0&numberposts=-1");
				$id_array = array();
				$excludelist = "&post__not_in=";
				foreach($tagged as $posts){
					$id_array[] = $posts->ID;
				}
				$id_array = array_values(array_unique($id_array));
				$excludelist .= implode(",", $id_array);
			}

			$is_batch_page = "range";
			$tagged = get_posts(array(
				"post_type"=> "attachment",
				"suppress_filters"=>"0",
				"numberposts"=> "-1",
				"post__not_in" => $id_array,));
			$is_batch_page = false;

			$post_array = array();
			foreach($tagged as $posts){
					$post_array[] = $posts->ID;
			}
			$post_array = array_values(array_unique($post_array));
			asort($post_array);
			foreach($post_array as $posts){
					////
					////
					echo "Post #" . $posts . " $meta_key updated to $meta_value <br />";
					update_post_meta($posts, $meta_key, $meta_value);

					$meta_key1 = '_mc_cc_default_com_setting_name';
					echo "Post #" . $posts . " $meta_key1 updated to $meta_value1 <br />";
					update_post_meta($posts, $meta_key1, $meta_value1);

					$meta_key2 = '_mc_cc_default_mod_setting_name';
					echo "Post #" . $posts . " $meta_key2 updated to $meta_value2 <br /><br />";
					update_post_meta($posts, $meta_key2, $meta_value2);


			}

		}

}





function cctag_create_menu() {
  cctag_add_submenu_page('tools.php','Run CC-Tag Batch Index', 'cc-tagger batch index', 'manage_options', "cctag-options");
}


function cctag_api_init(){

	global $shareType, $modType;

	$defaultArray = array("default" => __("Default (as defined in Media -> Options)"));
	$shareType2 = array_merge($defaultArray, $shareType);
	$modType2 = array_merge($defaultArray, $modType);


	cctag_add_settings_section('cctag_setting_section', '', 'when you submit this form, the posts between the starting and ending IDs will be tagged with the settings below. Media that is already tagged will not be changed unless the \'overwrite existing license option\' is checked.', "cctag-options");
	cctag_add_settings_field('cc_start', 'batch starting post-id <br />(leaving this blank will select the first media)', 'text', "cctag-options", 'cctag_setting_section', array (
	));
	cctag_add_settings_field('cc_end', 'batch ending post-id <br />(leaving this blank will select the latest uploaded media)', 'text', "cctag-options", 'cctag_setting_section', array (
	));

 	cctag_add_settings_field('cc_batch_mod', __('Allowed modification of untagged media:'), 'select', "cctag-options", 'cctag_setting_section', $modType2);
 	cctag_add_settings_field('cc_batch_share', __('Allowed use of untagged media:'), 'select', "cctag-options", 'cctag_setting_section', $shareType2);
 	cctag_add_settings_field('cc_batch_overwrite', __('Should these settings overwrite existing settings on the media'), 'radio', "cctag-options", 'cctag_setting_section', array("" => "don't overwrite existing license data", "true" => "<strong>overwrite</strong> existing license data<br />"));





	cctag_add_settings_section('mc_cc_setting_section', '<hr />CC-Tagger Options', __("This section describes how the Creative Commons License settings work. The default settings are applied to any media that has its settings set to 'default'."), "media");

	global $stateFullArray, $versionArray, $imageSizeArray, $linkTypeArray, $shareType, $modType;
	$list_of_states = $stateFullArray;
	
	foreach($versionArray as $version){
		unset($list_of_states[$version]);
	}

	asort($list_of_states);

 	cctag_add_settings_field('mc_cc_area_setting_name', __('Jurisdiction of your license information:'), 'select', 'media', 'mc_cc_setting_section', $list_of_states);
 	cctag_add_settings_field('mc_cc_image_setting_name', __('Badge size:'), 'select', 'media', 'mc_cc_setting_section',  $imageSizeArray);
 	cctag_add_settings_field('mc_cc_link_setting_name', __('Display a text link to the license:'), 'radio', 'media', 'mc_cc_setting_section', $linkTypeArray);
 	cctag_add_settings_field('mc_cc_default_"_setting_name', __('By Default allowed use of untagged media:'), 'select', 'media', 'mc_cc_setting_section', $shareType);
 	cctag_add_settings_field('mc_cc_default_mod_setting_name', __('By Default allowed modification of untagged media:'), 'select', 'media', 'mc_cc_setting_section', $modType);
 	cctag_add_settings_field('mc_cc_search_setting_name', __('Add search options to the search form'), 'select', 'media', 'mc_cc_setting_section', array("Yes" => __("Yes"), "" => __("No") ));



}


// CHANGE THE LINES BELOW AT YOUR OWN RISK
// THE SKY WILL FALL ON YOUR HEAD

add_action('admin_menu', 'cctag_create_menu');
add_action('admin_init', 'cctag_api_init');

if ( ! function_exists( 'cctag_plugin_options' ) ){
function cctag_plugin_options() {
	global $cctag_page_title;
	global $cctag_page_parent;
	$page = $_GET["page"];
	echo '<div class="wrap">';
	echo "<div class='icon32 icon_$page' id='icon-options-general'><br/></div>";
	echo '<h2>' . $cctag_page_title[$page] . '</h2>';
	echo '</div>';
////	echo '<table class="form-table"><tr><td>';
	echo "<br /><a href='" .get_bloginfo("url"). "/wp-admin/plugin-install.php?tab=search&mc_find_plugins=TRUE'>" .__("Find more plugins by this author"). "</a>";
////	echo "</td></tr></table>";
	echo '<form action="options.php" method="post">';
	settings_fields( $page );
	do_settings_sections($page);
	echo '<br /><input type="submit" class="button-primary" value="' .  __('Save Changes') . '" />';
	echo '</form>';
}
}

if ( ! function_exists( 'cctag_add_submenu_page' ) ){
function cctag_add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function="", $icon_url="", $position=""){
	global $cctag_page_title;
	global $cctag_page_parent;
////	$temp_array = array("".$menu_slug => $page_title);
////	$cctag_page_title = array_merge($cctag_page_title, $temp_array);
	$cctag_page_title[$menu_slug] = $page_title;
	$parent_slug = explode(",", $parent_slug);
	foreach ($parent_slug as $parent_slugX) {
		if($parent_slugX) {
			add_submenu_page($parent_slugX, $page_title, $menu_title, $capability, $menu_slug, "cctag_plugin_options");
		} else {
			add_menu_page($page_title, $menu_title, $capability, $menu_slug, "cctag_plugin_options", $icon_url, $position);
		}
	}
}
}

if ( ! function_exists( 'cctag_add_settings_section' ) ){
function cctag_add_settings_section($id, $title, $text, $pageX) {
	global $cctag_setting_section_text;
	$cctag_setting_section_text[$id] = $text;
	$pageX = explode(",", $pageX);
	foreach ($pageX as $page) {
		add_settings_section($id, $title, "cctag_section_callback_function", $page);
	}
}
}


if ( ! function_exists( 'cctag_add_settings_field' ) ){
function cctag_add_settings_field($idSettingName, $title, $type, $pageX, $section, $args	){
	global $cctag_color_picker_count;
	if ($type=="colorpicker"){
		$cctag_color_picker_count++;
		if (!isset($cctag_color_picker_count)){
			$cctag_color_picker_count = 0;
		}
	}
	$args[] = $idSettingName;
	$args[] = $type;
	$pageX = explode(",", $pageX);
	foreach ($pageX as $page) {
		add_settings_field($idSettingName, $title, 'cctag_field_callback_function', $page, $section, $args	);
		register_setting($page,$idSettingName);
	}
}
}

if ( ! function_exists( 'cctag_section_callback_function' ) ){
function cctag_section_callback_function($x) {
	global $cctag_setting_section_text;
	echo $cctag_setting_section_text[$x["id"]];
///	settings_fields( $x["id"] );
}
}

if ( ! function_exists( 'cctag_field_callback_function' ) ){
function cctag_field_callback_function($x){
	$type = array_pop($x);
	$id = array_pop($x);
	makeAdminOption($x, $id, $type);
}
}



if ( ! function_exists( 'makeAdminOption' ) ){

function makeAdminOption($vals, $my_field, $type) {
	global $cctag_color_picker_count;
	$tag = "input";
	$option_test = get_option($my_field);
	if ($type=="checkbox"){
		echo "<input type='hidden' value='' name='$my_field' />";

	}
	elseif ($type=="dropdown_pages"){
		wp_dropdown_pages(array('name' => $my_field, 'selected' => $option_test));
		return;
	}
	elseif ($type=="dropdown_posts"){
		wp_dropdown_pages(array('name' => $my_field, 'selected' => $option_test, 'taxonomy' => $vals));
		return;
	}
	elseif ($type=="dropdown_author"){
		wp_dropdown_users(array('name' => $my_field, 'selected' => $option_test));
		return;
	}
	elseif ($type=="dropdown_terms"){
		wp_dropdown_categories(array('name' => $my_field, 'selected' => $option_test, 'taxonomy' => $vals));
		return;
	}
	elseif ($type=="dropdown_link" || $type=="dropdown_links"){
		wp_dropdown_categories(array('name' => $my_field, 'selected' => $option_test, 'taxonomy' => 'link_category'));
		return;
	}
	elseif ($type=="dropdown_categories" || $type=="dropdown_cat" || $type=="dropdown_cats"){
		wp_dropdown_categories(array('name' => $my_field, 'selected' => $option_test));
		return;
	}elseif ($type=="textarea"){
		echo "<textarea class='$my_field' name='$my_field'>" . $option_test . "</textarea>";
		return;
	}
	elseif ($type=="text" || $type=="password"){
		echo "<input type='$type' name='$my_field' value='$option_test' />";
		return;
	}
	elseif ($type=="colorpicker"){
		echo " <div id='colorpicker$cctag_color_picker_countX'></div>";
		echo "<input type='$text' id='color$cctag_color_picker_countX' name='$my_field' value='$option_test'/>" . $option_test, $my_field;
		$cctag_color_picker_count++;
		return;
	}
	elseif ($type=="select"){
		echo "<select id='$my_field' name='$my_field'>";
		$tag = "option";
	}

	foreach ($vals as $stateKey => $stateValue) {
		$is_selected = "";
		$option_test = get_option($my_field);
		if ($option_test== $stateKey) {
			if ($type == "radio" || $type == "checkbox"){
				$is_selected = "checked='checked'";
			} else {
				$is_selected = "selected='selected'";
			}
		}
		if ($type == "radio" || $type == "checkbox"){
			$is_selected .= "type='$type' name = '$my_field' id='" . $type . $stateKey . $stateValue . "' /";
			$labelStart = " &nbsp;<label for='"	.$type . $stateKey . $stateValue.	"'>";
			$labelEnd   = "</label> &nbsp;";
		}
		echo "<$tag value='$stateKey' $is_selected>";
		echo $labelStart . $stateValue . $labelEnd;	
		if ($type != "radio" && $type != "checkbox"){
			echo "</$tag>";	
		} elseif ($type == "checkbox") {
			return;
		}
	}
	if ($type=="select"){
		echo "</select>";
	}
	return $my_string;
}
}

?>
