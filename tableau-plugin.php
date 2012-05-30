<?php 
/*
Plugin Name: Tableau Plugin
Project URI: https://github.com/maid0marion/Tableau-Wordpress-Plugin
Description: The following code defines and registers a shortcode to embed a Tableau visualization via an iFrame. 
Version: 1.0
Author: Julie Repass
License: GPL2
*/
/*
Copyright (c) 2012 Julie Repass

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

*/

// [tabviztag server="server_name" workbook="workbook_name" view="view_name" width="width" height="height" tabs="tabs" toolbar="toolbar" revert="revert" refresh="refresh"]


function tableau_func( $atts, $content = null ) {
		extract( shortcode_atts( array(
    				'server' => 'public.tableausoftware.com',
    				'workbook' => 'workbook_name',
    				'view' => 'view_name',
						'tabs' => 'yes',
						'toolbar' => 'yes',
						'revert' => '',
						'refresh' => '',
						'width' => '800px',
    				'height' => '600px'   				
    				), $atts));

		$output = "<iframe src='http://{$server}/views/{$workbook}/{$view}?:embed=yes&:tabs={$tabs}&:toolbar={$toolbar}?:revert={$revert}?:refresh={$refresh}' width='{$width}' height='{$height}'></iframe>";
    	return $output;
	}
	add_shortcode( 'tableau', 'tableau_func');

function tableau_addbuttons() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
	    
   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter('mce_external_plugins', 'add_tableau_mce_plugin');
     add_filter('mce_buttons_2', 'register_tableau_button');
   }
}

add_shortcode( 'tableau-button', 'tableau_func');
add_action('admin_footer','tableau_quicktag');

function tableau_quicktag() {
?>
<script type="text/javascript" charset="utf-8">
/* Adding Quicktag buttons to the editor Wordpress ver. 3.3 and above
* - Button HTML ID (required)
* - Button display, value="" attribute (required)
* - Opening Tag (required)
* - Closing Tag (required)
* - Access key, accesskey="" attribute for the button (optional)
* - Title, title="" attribute (optional)
* - Priority/position on bar, 1-9 = first, 11-19 = second, 21-29 = third, etc. (optional)
*/

QTags.addButton( 'tableau-plugin', 'tableau', '\n[tableau server="" workbook="" view="" tabs="yes" revert="" refresh="" width="800px" height="600px"]', '[/tableau]\n' );
</script>
<?php 
}

function register_tableau_button( $buttons ) {
   array_push($buttons, "|", "tableau" );
   return $buttons;
}
 
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_tableau_mce_plugin($plugin_array) {
   $plugin_array['tableau'] = plugin_dir_url(__FILE__) . 'tinymce/tableau/editor_plugin.js';
   return $plugin_array;
}
 
// init process for button control

function tableau_refresh_mce($ver) {
  $ver += 3;
  return $ver;
}

add_filter( 'tiny_mce_version', 'tableau_refresh_mce');

add_action('init', 'tableau_addbuttons');

?>