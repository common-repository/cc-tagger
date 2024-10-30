<?php

	/*
		get_option('mc_cc_area_setting_name');
		get_option('mc_cc_image_setting_name');
		$global_com;
		$global_deriv;
	*/


	// attach our function to the correct hook
	add_filter( "the_content", "mc_cc_content" );
	add_shortcode('cc-tag', 'mc_cc_shortcode');

	add_filter( "prepend_attachment", "mc_cc_prepend_attachment" );

	function mc_cc_prepend_attachment($p=null){
		global $post, $parent_status;
		$pos = strpos("x".$post->post_mime_type, "image/");

		if (is_search() && !$parent_status){
////			echo "unpublished page";
			return $p;
		} elseif(is_search() && $pos) {
////			echo "published page";
			return $p;
		} elseif(!$pos && is_search()) {
////			echo "NOT Normal case";
			$replace = "";
			if (!trim($post->post_content)){
				$replace = "<a href='" . get_permalink( ) . "'>" . __("Visit the media page for the media and license information.") . "</a>";
			}
			$p = str_replace (wp_get_attachment_link(0, 'medium', false), $replace, $p);
			return $p;
		} else {
////			echo "Normal case";
			return $p;
		}


		return ;
	}





	function mc_closing_slash ($content) {
		if($content){
			return "/";
		}
	}

	function mc_cc_content ($content) {
		global $post, $parent_status;
		$front_div_start = "";
		$do_image1 = "";
		$do_image2 = "";
		$front_div_end = "";

		///// http://googlewebmastercentral.blogspot.com/2009/08/specifying-images-license-using-rdfa.html
		if (is_attachment() || trim($post->post_mime_type) && !$parent_status){
			global $stateFullArray;			
			global $versionArray;	
			$area = "";
			$area = get_option('mc_cc_area_setting_name');	


			$keys = array_flip(array_keys($stateFullArray));
			$currentKey = $keys[$area];
			$ccalue = "";

			foreach($versionArray as $version => $value){				
				if ( $keys[$value] > $currentKey  ){
					$ccvalue = $value;
					break;
				}				
			}	


			$front_div_start = "<div xmlns:dc='http://creativecommons.org/ns#' about='" . $post->guid . "'>";
			$front_div_end = "</div>";
	

			$deriv = get_post_meta($post->ID, "_mc_cc_default_mod_setting_name", true);
			$com =   get_post_meta($post->ID, "_mc_cc_default_com_setting_name", true);

			if ($com == "NULL" || $com == "COM" || $com == "com" ) {
				$com = null;
			}
			if ($deriv == "NULL" || $deriv == "deriv") {
				$deriv = null;
			}


			$global_com = get_option('mc_cc_default_com_setting_name');

			if ($global_com=="NULL" || $global_com=="COM" || $global_com=="com") {
				$global_com = null;
			}

			$global_deriv = get_option('mc_cc_default_mod_setting_name');

			if ($global_deriv=="NULL" || $global_deriv=="deriv") {
				$global_deriv = null;
			}




			$liscense_string1a = "<a rel='license' class='license image licenseimage' href='";
			$liscense_string1b = "<a rel='license' class='license link licenselink' href='";
			$liscense_string1 .= "http://creativecommons.org/licenses/by";

			$img_string1 = "<img src='";
			$img_string1 .= "http://i.creativecommons.org/l/by";


			if ((!$com && get_post_meta($post->ID, "_mc_cc_default_com_setting_name_flag", true) == "COM") || $com == "default") {
				$liscense_string1 .= $global_com;
				$img_string1 .= $global_com;
				$com = $global_com;
			} else {
				$liscense_string1 .= $com;
				$img_string1 .= $com;
			}

			if ($com == "---"){
				return $content;
			}


			if (($deriv && !get_post_meta($post->ID, "_mc_cc_default_mod_setting_name_flag", true) == "deriv") || $deriv == "default") {
				$liscense_string1 .= $global_deriv;
				$img_string1 .= $global_deriv;
				$deriv .= $global_deriv;
			} else {
				$liscense_string1 .= $deriv;
				$img_string1 .= $deriv;
			}

			
			$liscense_string1 .="/$ccvalue/";
			$img_string1 .="/$ccvalue/";
			if ($area) {
				$liscense_string1 .= $area . "/";
				$img_string1 .= $area . "/";
			}

			$liscense_string1 .= "'>";
			$do_image = get_option('mc_cc_image_setting_name');
			$img_string1 .= $do_image . ".png";
			$img_string1 .= "' />";


			$liscense_stringContent .= "Attribution";

			switch ($com) {
			    case "-nc":
			        $liscense_stringContent .= "-NonCommercial";
			        break;
			}


			switch ($deriv) {
			    case "-sa":

			        $liscense_stringContent .= "-ShareAlike";
			        break;
			    case "-nd":
			        $liscense_stringContent .= "-NoDerivs";
			        break;

			}

			$liscense_stringContent .=" $ccvalue ";

			if($area){
				$liscense_stringContent .= $stateFullArray[$area];
			}


			$liscense_string3 .= "</a>";
			$img_string3 .= "</a>";

			if ($do_image){
				$do_image1 = $liscense_string1a . $liscense_string1 . $img_string1  . $liscense_string3;
			} 

			if (!$do_image || get_option('mc_cc_link_setting_name')) {
				if ($do_image){
					$do_image2 = "<br /><br />";
				}
				$do_image2 .= __("This work is licensed under a  "). $liscense_string1b . $liscense_string1 . "Creative Commons " . $liscense_stringContent . " License" . $liscense_string3;
			}
			return $content . $front_div_start . $do_image1 . $do_image2 . $front_div_end;

		}
		else return $content;
	}


?>