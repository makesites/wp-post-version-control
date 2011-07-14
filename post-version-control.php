<?php
/*
Plugin Name: Post Version Control
Plugin URI: http://www.makesites.cc/projects/pvc
Description: Version control for your posts. Define a keyword for each version control group and use it as a prefix in the name of each post of the group. The plugin will sort out the latest post automatically and label the rest as outdated. 
Version: 1.0
Author: MAKE SITES
Author URI: http://www.makesites.cc/
*/

/*  Copyright 2008 MAKE SITES  (email : makis@makesites.cc)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// creates a new option; does nothing if option already exists.
add_option('pvc-list', $value = 'test, etc');
add_option('pvc-path', $value = '/current');

// hook for adding admin menus
add_action('admin_menu', 'pvc_adminPages');

// call each function on the selected filters
add_filter('the_content', 'pvc_showOutdated');
add_filter('content_edit_pre', 'pvc_editPost');

add_action('init','pvc_latestRedirect');
add_action('save_post','pvc_versionControl');

// the keywords we have selected in an array
$pvc_list =  get_option('pvc-list');
$pvc_list = explode(', ', $pvc_list);


function pvc_adminPages() {
  // Add a newpage under Options:
  add_options_page('Post Version Control', 'Post Version Control', 8, 'post-version-control', 'pvc_optionsPage');
}

// displays the options page for the plugin in the Settings menu
function pvc_optionsPage() {

    echo '<div class="wrap">';
    echo '<h2>Post Version Control</h2>';
	echo '<p>Here you define all the keywords of the groups you want to track for version control. To link a post to a version control group you will have to enter the keyword of the group as the first part of the name of your post followed by a dash, ex "test-...". Insert all your keywords in the following textfield seperating each other with a ", " (a comma followed by a space).</p>';
	echo '<form method="post" action="options.php">';
	wp_nonce_field('update-options');

	echo '<input type="text" name="pvc-list" value="' . get_option('pvc-list') . '" />';
	echo '<p>This is the URL you will use to access the latest version of your posts. Please leave the trailing slash from the start and remove any trailing slash from the end. The <strong>%keyword%</strong> variable can be replace by any of the keywords mentioned above.</p>';
	echo get_bloginfo('wpurl') . '<input type="text" name="pvc-path" value="' . get_option('pvc-path') . '" /> /%keyword%';
	echo '<input type="hidden" name="action" value="update" />';
	echo '<input type="hidden" name="page_options" value="pvc-list, pvc-path" />';
	echo '<p class="submit">';
	echo '<input type="submit" name="Submit" value="Update Options" />';
	echo '</p>';
	echo '</form>';
	echo '</div>';
	
}


function pvc_versionControl( $id ){
  global $wpdb;

  $post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = $id");
  
  // check for the keyword in the name (if any)
  $keyword = pvc_checkName( $post->post_name );

  if( $keyword ){
    pvc_gatherPosts( $keyword );
  }
}


function pvc_checkName( $name ){
  global $pvc_list;

  $name_parts = explode('-', $name);
  
  if( in_array( $name_parts[0], $pvc_list) ){
    return $name_parts[0];
  } else {
    return false;
  }

}


function pvc_gatherPosts( $keyword ){
  global $wpdb;

  $post_group = $wpdb->get_results("SELECT ID, post_content FROM $wpdb->posts WHERE post_name LIKE '$keyword-%' ORDER BY post_date DESC");

  if($post_group){

    foreach( $post_group as $post ){
      if( !$latest ){ $latest = $post->ID; }
      $new_content = '';
      
      if( !preg_match("/<!-- outdated: (\w+) -->/i", $post->post_content) && $latest != $post->ID ){
        $new_content = pvc_writeOutdated( $keyword, $post->post_content );
      } elseif( preg_match("/<!-- outdated: (\w+) -->/i", $post->post_content) && $latest == $post->ID  ){
        $new_content = pvc_removeOutdated( $keyword, $post->post_content );
	  }
	  if( $new_content != '' ){
	    pvc_updatePost( $post->ID, $new_content );
	  }
    }
    
  }

}


function pvc_writeOutdated( $keyword, $content ){
  $content = '<!-- outdated: ' . $keyword . ' -->' . $content;
  return $content;
}

function pvc_removeOutdated( $keyword, $content ){
  $content = str_replace('<!-- outdated: ' . $keyword . ' -->', '', $content);
  return $content;
}

function pvc_updatePost( $id, $content ){
  global $wpdb;

  //$content = $wpdb->escape( $content );
  $content = str_replace( "'", "&apos;", $content );
  $wpdb->query("UPDATE $wpdb->posts SET post_content='$content' WHERE ID=$id");

}

function pvc_editPost( $content ){
  $content = preg_replace("/<!-- outdated: (\w+) -->/i", "", $content);
  return $content;
}

	
function pvc_showOutdated( $content ){
  preg_match("/<!-- outdated: (\w+) -->/i", $content, $key);
  $keyword = $key[1];
  if( $keyword ){
    $outdated = file_get_contents( dirname(__FILE__) . '/outdated.html');
    $content = preg_replace("/<!-- outdated: (\w+) -->/i", $outdated, $content);
  
    str_replace('<!-- outdated: testing -->', $outdated, $content);
    $link = get_bloginfo('wpurl') . get_option('pvc-path') . '/' . $keyword;
    $content = str_replace('{{url}}', $link, $content);
  }
  return $content;
}

function pvc_latestRedirect() {
  global $wpdb, $pvc_list;
  
  $pvc_dir = dirname( str_replace( get_bloginfo('wpurl'), '', 'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] ) );
  
  $pvc_path = get_option('pvc-path');
  // first check if we are accessing the vrsion control from the right path
  if( $pvc_dir == $pvc_path ){ 
    $keyword = basename($_SERVER['REQUEST_URI']);
    // then check if there is a keyword for this version control redirect
	if( in_array($keyword, $pvc_list) ){ 
	  $latest_post = $wpdb->get_var("SELECT guid FROM $wpdb->posts WHERE post_name LIKE '$keyword-%' ORDER BY post_date DESC");
	  if( $latest_post ){
	    header("Location:" . $latest_post);
	    //header("HTTP/1.1 307 Temporary Redirect");
	    exit();
	  }
    }
  }
}


// PHP5 functions...
if (!function_exists('file_get_contents')) {
	/**
	 * Load file_get_contents() if not available..
	 */
	function file_get_contents($filename) {
		$fh = fopen($filename, 'r');
		if ($fsize = @filesize($filename)) {
			$data = fread($fh, $fsize);
		} else {
			$data = '';
			while (!feof($fh)) {
				$data .= fread($fh, 8192);
			}
		}
		
		fclose($fh);
		return $data;
	}	
}

?>