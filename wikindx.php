<?php
/* 
Plugin Name: WIKINDX macro plug-in for WordPress
Plugin URL: http://stargrads.net/blogs/davinci/2009/09/wikindx-macro-plug-in-for-wordpress
Description: Adds a macro to WordPress to insert bibliography information from Wikindx.
Version: 1.0-$Rev$
Author: D. L. Yonge-Mallo
Author URI: http://stargrads.net/blogs/davinci/

Copyright (c) 2009, D. L. Yonge-Mallo (davinci at stargrads dot net)

This program is free software: you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the Free
Software Foundation, either version 3 of the License, or (at your option)
any later version.

This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
more details.

You should have received a copy of the GNU General Public License along
with this program (in a file named COPYING).  If not, see
<http://www.gnu.org/licenses/>.

*/

if (!CUSTOM_TAGS) {
    /* This is a kses hack, so that the wikindx tag is not stripped. */
	$allowedposttags['wikindx'] = array(
		'resource' => array()
	);
}

function parse_wikindx($content) {
    $WikindxPath="http://path.to/wikindx/";

    preg_match_all('#<wikindx( resource="([ \w]+)")?(>(.*)</wikindx>| ?/>)#Usi', $content, $wikindxs, PREG_SET_ORDER);

    if ( count($wikindxs) > 0 ) {
        foreach ( (array) $wikindxs as $wikindx ) {
            if( empty($wikindx[2]) ) {
                $output = "ERROR: WIKINDX MISSING RESOURCE ARGUMENT";
            } else {
                $resourceId = $wikindx[2];
                $string = file_get_contents("{$WikindxPath}cmsprint.php?action=getResource&id={$resourceId}");
                if($string) {
                    $array = unserialize(base64_decode($string));
                    $output = $array[$resourceId] . " ([{$WikindxPath}index.php?action=resourceView&id={$resourceId} details])";

                } else {
                    $output = "ERROR: WIKINDX RESOURCE ".$resourceId." NOT FOUND";
                }
            }
            $content = str_replace($wikindx[0], $output, $content);
        }
    }

    return $content;
}

add_filter('the_content', 'parse_wikindx', 2);

?>
