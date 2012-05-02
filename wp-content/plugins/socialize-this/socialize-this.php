<?php
/*
Plugin Name: Socialize This
Plugin URI: http://www.fullondesign.co.uk/socialize-this
Description: Adds social widgets to your blog posts. It also can update your twitter status when you publish a post.
Version: 1.6.6
Author: Mike Rogers
Author URI: http://www.fullondesign.co.uk/
Text Domain: st_plugin

Some code and ideas from WordPress(http://wordpress.org/).

Released under the GPL v.2, http://www.gnu.org/copyleft/gpl.html

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

*/



define('ST_FILE', plugin_basename(__FILE__));
define('ST_VERSION', '1.6.5');
$st_folder = explode('socialize-this.php', __FILE__);
define('ST_FOLER', $st_folder[0]);
unset($st_folder);

load_theme_textdomain('st_plugin');

if(substr(phpversion(),0, 1) >= 5){
	include('socialize-this-php5-enviroment.php');
	$ql = new socialize_this(); 
} else {
	include('socialize-this-php4-enviroment.php');
	$ql = new socialize_this(); 
	$ql->__construct(); // __construct is not supported in PHP4
}

add_filter('plugin_row_meta', array($ql, 'extra_plugin_links'),10,2);

// The install/uninstall directorys.
register_activation_hook(ST_FILE, array($ql, 'st_install'));
register_deactivation_hook(ST_FILE, array($ql, 'st_uninstall')); 