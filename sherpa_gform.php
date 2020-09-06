<?php
/*
Plugin Name: Sherpa Gravity Forms
Description: Send form data to the Sherpa CRM using Gravity Form's Add-on Framework
version: 0.7
Author: Husky Ninja
Author URI: https://www.husky.ninja
License: GPLv3 or later
Text Domain: sherpa_gform
Domain Path: /languages

---------------------------------------------------------------------

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

*/

define( 'SHERPA_GFORM_VERSION', '0.7' );
 
add_action( 'gform_loaded', array( 'Sherpa_Gform_Bootstrap', 'load' ), 5 );
 
class Sherpa_Gform_Bootstrap {
 
    public static function load() {
 
        if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
            return;
        }
 
        require_once( 'class-sherpa-gform.php' );
 
        GFAddOn::register( 'SherpaGform' );
    }
 
}
 
function sherpa_gform() {
    return SherpaGform::get_instance();
}
