<?php
 
/*
Plugin Name: Creative Commons Media Tagger
Plugin URI: http://www.codeandreload.com/wp-plugins/creative-commons-tagger/
Description: This plugin provides the ability to tag media in the media library as having a Creative Commons (CC) license.  The license shows up on the attachment page and is optimized for search engines (SEO) using RDFa metadata.  It optionally extends the search form to allow searches for CC-tagged media.  It can display a text link to the license, an image link to the license, or both.
Author: Robert Wise
Version: 2.2
Author URI: http://www.codeandreload.com
*/


 // ------------------------------------------------------------------
 // Settings for media boxes.
 // ------------------------------------------------------------------

global $is_batch_page;
$is_batch_page = false;

global $imageSizeArray;
 
$imageSizeArray = array(
	"80x15"    => 'image 80x15px',
	"88x31"    => 'image 88x31px',
	NULL    => 'No image',
);

$linkTypeArray = array(
	true    => 'Link',
	NULL    => 'No Link',
);


global $shareType;

$shareType = array(
	"com"    => 'Creative Commons Attribution',
	"-nc"    => 'Non-Commercial Attribution',
	"---"    => 'Use prohibited',
);

$addToSearch = array(
	"True"    => 'Yes',
	NULL    => 'No',
);

global $modType;

$modType = array(
	"deriv"    => 'Yes',
	"-sa"    => 'Yes, Share Alike',
	"-nd"    => 'no',
);

global $stateFullArray;


$versionArray= array(
	"1.0",
	"2.0",
	"2.1",
	"2.5",
	"3.0"
);

$stateFullArray = array(
	"fi"    => 'Finland',
	"1.0"    => 	"1.0",
	"be"    => 'Belgium',
	"cl"    => 'Chile',
	"fr"    => 'France',
	"kr"    => 'South Korea',
	"uk"    => 'UK: England &amp; Wales',
	"2.0"    => 	"2.0",
	"jp"    => 'Japan',
	"2.1"    => 	"2.1",
	"ar"    => 'Argentina',
	"bg"    => 'Bulgaria',
	"ca"    => 'Canada',
	"cn"    => 'China Mainland',
	"co"    => 'Colombia',
	"dk"    => 'Denmark',
	"hu"    => 'Hungary',
	"in"    => 'India',
	"il"    => 'Israel',
	"mk"    => 'Macedonia',
	"my"    => 'Malaysia',
	"mt"    => 'Malta',
	"mx"    => 'Mexico',
	"pe"    => 'Peru',
	"pt"    => 'Portugal',
	"si"    => 'Slovenia',
	"za"    => 'South Africa',
	"se"    => 'Sweden',
	"ch"    => 'Switzerland',
	"scotland"    => 'UK: Scotland',
	"it"    => 'Italy',
	"2.5"    => 	"2.5",
	"au"    => 'Australia',
	"at"    => 'Austria',
	"br"    => 'Brazil',
	"hr"    => 'Croatia',
	"cz"    => 'Czech Republic',
	"ec"    => 'Ecuador',
	"de"    => 'Germany',
	"gr"    => 'Greece',
	"gt"    => 'Guatemala',
	"hk"    => 'Hong Kong',
	"lu"    => 'Luxembourg',
	"nl"    => 'Netherlands',
	"nz"    => 'New Zealand',
	"no"    => 'Norway',
	"ph"    => 'Philippines',
	"pl"    => 'Poland',
	"pr"    => 'Puerto Rico',
	"ro"    => 'Romania',
	"rs"    => 'Serbia',
	"sg"    => 'Singapore',
	"es"    => 'Spain',
	"tw"    => 'Taiwan',
	"th"    => 'Thailand',
	"us"    => 'United States',
	NULL    => 'International',
	"vn"    => 'Vietnam',
	"3.0"    => 	"3.0"
);




 // ------------------------------------------------------------------

register_activation_hook( __FILE__, 'mc_cc_activate' );

require_once("wp_cc_shortcode.php");
require_once("wp_cc_settings.php");
require_once("wp_cc_upload_options.php");


add_filter('posts_where', 'cc_posts_where' );
add_filter('posts_join', 'cc_posts_fields' );

function cc_posts_fields ($join) {
	global $is_batch_page,  $batch_dont_overwrite;
	if ((get_query_var('commercial-use') && get_query_var('modification_rights')) || $is_batch_page) {
		add_filter('post_link','cc_external_permalink');
		add_action('the_post', 'add_special_posts');
		add_filter('get_search_query','cc_get_search_query');
		$join .= " JOIN wp_postmeta ON (wp_posts.ID = wp_postmeta.post_id)";
	}
	return $join;
}

function cc_posts_where( $join ) {
	$com = get_query_var('commercial-use');
	$mod = get_query_var('modification_rights');
	$com_array = explode(",",$com);
	$mod_array = explode(",",$mod);
	$comDefault = get_option('mc_cc_default_com_setting_name');
	$modDefault = get_option('mc_cc_default_mod_setting_name');


	if (in_array( $comDefault , $com_array)){
		$com_array[] = "default";
	}
	if (in_array( $modDefault , $mod_array)){
		$mod_array[] = "default";
	}

	$cc_array = array();
	global $is_batch_page, $batch_dont_overwrite, $range_list;	

	if ($com && $mod) {
		$cc_string = "";
		foreach($com_array as $comValue){
			foreach($mod_array as $modValue){
				$cc_string .= "$comValue$modValue, ";
				$cc_array[] = "$comValue$modValue"; 
			}
		}
		$join .= " AND ( wp_postmeta.meta_key = '_mc_cc_full_setting_name')";



		$my_iterator = 0;
		$join .= " AND (";
		foreach($cc_array as $cc_thing){
			if($my_iterator > 0 ){
				$join .= " OR ";			
			}
			$join .= " wp_postmeta.meta_value = ('$cc_thing') ";
		$my_iterator++;
		}
		$join .= ")";

	} elseif(($is_batch_page === "range")) {
		$join .= $range_list;
	} elseif($is_batch_page) {
		$join .= " AND ( wp_postmeta.meta_key = '_mc_cc_full_setting_name')" .  $range_list;
	} 
	return $join;
}


function cc_queryvars( $qvars )
{
  $qvars[] = 'commercial-use';
  $qvars[] = 'modification_rights';
  return $qvars;
}

function add_cc_query_vars () {
	global $wp_query, $is_batch_page;
	if (
		(get_query_var('commercial-use') && get_query_var('modification_rights'))

		|| $is_batch_page

	) { 	
	    	$wp_query->query_vars['post_type'] = "attachment";
		$wp_query->query_vars['caller_get_posts']=1;
		$wp_query->query_vars['post_status']="inherit";
		$wp_query->is_search = true;
		$wp_query->is_home = false;

	} else {

	}
}

add_filter('query_vars', 'cc_queryvars' );
add_action('pre_get_posts', 'add_cc_query_vars');


function my_search_form1($form){
	$form = str_replace("</form>","",$form);
	$form = str_replace(__("Creative Commons Tagged Media"),"",$form);
	$form .="<br />";
	global $shareType;
	global $modType;
	$defaultShareArray = array(
	 "" => __("Don't search for Creative Commons tagged media"),
	 "com,-nc" => __("Either Commercial or Non-commercial")
	);
	$defaultModArray = array(
		"deriv,-sa,-nd" => __("Derivative allowed, No-Derivative, or Share-Alike"),
		"deriv,-sa" => __("Either Derivatives Allowed or Share-Alike"),
	);

	$shareType = array_merge($defaultShareArray, $shareType);
	$modType = array_merge($defaultModArray, $modType);
	$modType["deriv"] = __("Derivative Allowed ");
	$modType["-sa"] = __("Share Alike");
	$modType["-nd"] = __("No Derivatives allowed");
	unset($shareType['---']);
	$form .= "<label class='screen-reader-text'>" ."Search media for usage rights". "</label>";
	$form .= 	printOptions($shareType, "commercial-use", "forward");
	$form .="<br />";
	$form .= "<label class='screen-reader-text'>" ."search media for modification rights". "</label>";
	$form .= 	printOptions($modType, "modification_rights", "forward");
	$form .=  '</form>';
	return $form;

	}

if (trim(get_option("mc_cc_search_setting_name"))){
	add_filter( 'get_search_form', 'my_search_form1' );
}




function add_special_posts($posts) { 
	global $post, $parent_status;
	$parent_status = true;
	parent_recursive($post->ID);
	return $posts;
  }

function cc_get_search_query($var1=null){
	if (!$var1 ){
		$var1 = __("Creative Commons Tagged Media");
	}
	return $var1;
}





function cc_external_permalink($permalink) {
	global $post, $parent_status;
	if (!$parent_status){
			$permalink = "#";	
	}
	return $permalink;
}



function parent_recursive($recursive_post) {
    global $parent_status;

    $parent_post = get_post($recursive_post);

    // set flag to drop out of the recursion whenever we hit an unpublished post OR the top of the hierarchy

    if (
        ( $parent_post->post_status == "draft" ) ||
        ( $parent_post->post_status == "trash" ) ||
        ( $parent_post->post_status == "future" ) ||
        ( $parent_post->post_status == "pending" ) ||
                                                     // add more ORs here, if needed in the future
        ( ( $parent_post->post_status != "publish" ) && ( $parent_post->post_status != "inherit" ) && ( $parent_post->post_parent != 0 ) )
    ) {
        $parent_status = FALSE;
       //// print "Now will return FALSE\n";
    }

    // if we're still finding published posts AND are not at the top of the hierarchy, recurse into next post

    if ( ( $parent_status ) && (( $parent_post->post_status == "publish" ) || ($parent_post->post_status == "inherit")) && ( $parent_post->post_parent != 0 ) ) {
   
        // print something so we know what's happening


 ////       print "Parent id = " . $parent_post->post_parent . "\n";
 ////       print "Parent status = " . $parent_post->post_status . "\n";
 ////       print "   Currently will return " . $parent_status . "\n";
 ////       print "Recursing into record # " . $parent_post->post_parent . "...\n";

        // recurse using the parent as the next post

        parent_recursive($parent_post->post_parent);

    }
}


function cc_to_comments_popup_link(){
	global $post, $parent_status;
	$parent_status = true;
	parent_recursive($post->ID);
	if (!$parent_status){
		return  ' style="visibility:hidden" ';	
	}
	else {
		return;
	}
}

add_filter( 'comments_popup_link_attributes', 'cc_to_comments_popup_link' );

function printOptions($myArray=null, $my_field=null, $echo=null, $pageID=null) {
	global $post;
	if ($echo == "forward") {
		$my_string .= "<select style='width:100%' id='$my_field' name='$my_field'>";
		$echo = false;
	} elseif ($echo) {
		echo "<select id='$my_field' name='$my_field'>";
	} else {
		$my_string .= "<select id='$my_field' name='attachments[" . $pageID . "][$my_field]'>";
	}
	foreach ($myArray as $stateKey => $stateValue) {

		$is_selected = "";
		if ($echo) {
			$option_test = get_option($my_field);
		} else {
			$option_test = get_post_meta($pageID, ("_" . $my_field), true);
		}

		if ($pageID && (!$option_test || !get_post_meta($pageID, ("_" . $my_field. "_flag"), false))) {
			$option_test = "default";
		}


		if ($option_test== $stateKey) {
			$is_selected = "selected='selected'";
		}

		if ($echo) {
			echo "<option value='$stateKey' $is_selected>";
			echo $stateValue;	
			echo '</option>';	
		} else {
			$my_string .="<option value='$stateKey' $is_selected>";
			$my_string .= $stateValue;
			$my_string .="</option>";
		}
	}
	if ($echo) {
		echo "</select>";
	}
	$my_string .= "</select>";
	return $my_string;
}


require_once("admin_page.php");

?>
