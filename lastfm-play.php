<?php
/*
Plugin Name: Last.fm Play
Plugin URI: http://www.arjentienkamp.com
Description: A simple Wordpress-plugin showing your most recent last.fm plays (scrobbles).
Version: 1.0
Author: Arjen Tienkamp
Author URI: http://www.arjentienkamp.com
License: GPL
*/


function lastfmplay()
{
$username = get_option('lastfmplay_data');
$scrobbler_url = "http://ws.audioscrobbler.com/2.0/user/" . $username . "/recenttracks";


	function ShortenText($text, $chars)
	{
		$chars = $chars;$text = $text." ";
		$countchars = strlen($text);
		if($countchars > $chars)
		{
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text."...";
		}
		return $text;
	}
	
		$scrobbler_xml = file_get_contents($scrobbler_url);
        $scrobbler_data = simplexml_load_string($scrobbler_xml);        
	
		$output='
		
		<style>
		
		div#lastfm {
		width:300px;
		}
		
		div#lastfm ul {
		display:block;
		width:300px;
		}
		
		li.play {
		width:300px;
		height:80px;
		}
		
		div.cover {
		float:left;
		width:60px;	
		}
		
		div.cover img {
		padding-bottom:40px;
		display:block;
		width:40px;
		height:40px;
		}
		
		p.info {
		float:left;
		margin:0px;
		display:block;
		width:200px;
		}
		
		p.played {
		float:left;
		font-size:10px;
		width:200px;
		}
		
		</style>
		
		';
        
		
		
		
		$output.='';
		$output.='<div id="lastfm"><ul>';
        foreach ($scrobbler_data->track as $track) {
                $output.= '<li class="play">';
                $output.=  '<div class="cover"><img width="40" height="40" class="cover" src="' . $track->image[0] . '" /></div>';
                $output.=  '<p class="info"><span class="title">' . $track->artist . '</span><br /> '. ShortenText($track->name, 30) .'</p>';
                $output.=  '<p class="played">Played: ' . $track->date . '</p>';
                $output.=  '</li>';
        }
        $output.='</ul></div>';
		return $output; 

}


add_shortcode( 'lastfm', 'lastfmplay' );


/* Runs when plugin is activated */
register_activation_hook(__FILE__,'lastfmplay_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'lastfmplay_remove' );

function lastfmplay_install() {
/* Creates new database field */
add_option("lastfmplay_data", 'Default', '', 'yes');
}

function lastfmplay_remove() {
/* Deletes the database field */
delete_option('lastfmplay_data');
}


if ( is_admin() ){

/* Call the html code */
add_action('admin_menu', 'lastfmplay_admin_menu');

function lastfmplay_admin_menu() {
add_options_page('Last.fm Play', 'Last.fm Play', 'administrator',
'lastfmplay', 'lastfmplay_html_page');
}
}



function lastfmplay_html_page() {
?>
<div>
<h2>Last.fm Play Options</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table width="600">
<tr valign="top">
<th width="200" scope="row">Enter Last.fm username:</th>
<td width="400">
<input name="lastfmplay_data" type="text" id="lastfmplay_data"
value="<?php echo get_option('lastfmplay_data'); ?>" />
</td>
</tr>
</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="lastfmplay_data" />

<p>
<input type="submit" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>
<?php
}
?>