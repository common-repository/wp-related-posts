<?php
/*
Plugin Name: WP Related Posts
Plugin URI: http://adminofsystem.net/
Description: Plugin for wordpress related posts.
Version: 1.3
Author: Yakhin Ruslan
Author URI: http://adminofsystem.net
*/

/*  Copyright 2013  Yakhin Ruslan (email : nessus@adminofsystem.netL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$token = md5(uniqid(mt_rand() . microtime()));
$_SESSION['tokenpost'] = $tokenpost;

function wp_relatedposts_options()
{
	$options=array();

	$options['type']  = get_option('wp_relatedposts_type');
	$options['count'] = get_option('wp_relatedposts_num');
	$options['title'] = get_option('wp_relatedposts_title');
	$options['css'] = get_option('wp_relatedposts_css');
	
	return $options;
}
function wp_relatedposts_content( $content )
{
	$options = wp_relatedposts_options();

	if(is_single())
 	{
		if( $options['type'] == "Tags" )
		{
			return $content.'<style type="text/css">'.$options['css'].'</style>'.wp_relatedposts_ontags(&$options);
		}
		if( $options['type'] == "Category" )
		{
			return $content.'<style type="text/css">'.$options['css'].'</style>'.wp_relatedposts_oncategory(&$options);
		}
	}
	return $content;
}
function wp_relatedposts_oncategory( $options )
{
	global $post;
	$posts = array();
	$category=get_the_category($post->ID);

	if( $category )
	{
		foreach($category as $cat)
		{
			$args = array
        		(
				'category' => $cat->cat_ID,
                		'numberposts' => 10,
                		'orderby' => 'rand'
			);
			foreach(get_posts($args) as $relpost)
        		{
				if( $post->post_title != $relpost->post_title ) 
				{
                        		$posts[$relpost->post_title] = $relpost->ID;
                		}
        		}
		}
	}
	if( count($posts) > 0 )
        {
		$n = 0;
                $relatedpost.= '<br><br><br><h3 class="wp_related_title">' . $options['title'] . '</h3><ul>';
		foreach($posts as $post_title=>$post_id)
                {
                        if( $n < $options['count'] ) 
			{
                                $relatedpost.= '<li><a class="wp_related_url" href="' . get_permalink($post_id) . '">' . $post_title . '</a></li>';
                                $n++;
                        }
                        else 
			{
                                return $relatedpost . '</ul><font size="1">coded by <a href="http://adminofsystem.net">nessus</a></font>';
                        }
                }
                return $relatedpost . '</ul><font size="1">coded by <a href="http://adminofsystem.net">nessus</a></font>';		
	}
}
function wp_relatedposts_ontags( $options )
{
   	global $post;
        $tags = get_the_tags();
        $posts = array();

	if( $tags )
	{
        	foreach($tags as $tag) 
		{
            		$args = array
	    		(
	        		'tag' => $tag->name,
				'numberposts' => 10,
                		'orderby' => 'rand'
	    		); 
            		foreach(get_posts($args) as $relpost)
	    		{
            			if( $post->post_title != $relpost->post_title ) 
				{
                			$posts[$relpost->post_title] = $relpost->ID;
				}
	    		}
		}
        }
   	if( count($posts) > 0 )
   	{
        	$n = 0;
   		$relatedpost.= '<br><br><br><h3 class="wp_related_title">' . $options['title'] . '</h3><ul>';
   		foreach($posts as $post_title=>$post_id)
        	{
			if( $n < $options['count'] ) 
			{
				$relatedpost.= '<li><a class="wp_related_url" href="' . get_permalink($post_id) . '">' . $post_title . '</a></li>';
        			$n++;
			}
	        	else
			{
				return $relatedpost . '</ul><font size="1">coded by <a href="http://adminofsystem.net">nessus</a></font>';
			}
		}
		return $relatedpost . '</ul><font size="1">coded by <a href="http://adminofsystem.net">nessus</a></font>';
   	}
}
function wp_relatedposts_admin_menu()
{
   if(isset($_REQUEST['submit']) && is_admin()) 
   {
	$tokenpost = $_POST['tokenpost'];
	if($_SESSION['tokenpost'] == $tokenpost) 
	{
		$_SESSION['tokenpost'] = '';
   		if(!empty($_REQUEST['wp_relatedposts_title'])) 
		{
			update_option('wp_relatedposts_title', $_REQUEST['wp_relatedposts_title']);
		}
   		if(!empty($_REQUEST['wp_relatedposts_num'])) 
		{
        		update_option('wp_relatedposts_num', $_REQUEST['wp_relatedposts_num']);
		}
		if(!empty($_REQUEST['wp_relatedposts_type'])) 
		{
                	update_option('wp_relatedposts_type', $_REQUEST['wp_relatedposts_type']);
        	}
		if(!empty($_REQUEST['wp_relatedposts_css']))
                {
                        update_option('wp_relatedposts_css', $_REQUEST['wp_relatedposts_css']);
                }
        	echo "<div class='updated'><p><strong>WP Related Posts options updated</strong></p></div>";
   	}
	else
	{
		header("HTTP/1.1 404 Not Found"); 
		die();
	}
   }
   $options=wp_relatedposts_options();
?>
   <form method="POST" action="<?=$_SERVER['PHP_SELF']?>?page=wp-relatedposts.php">
   <input name="tokenpost" type="hidden" value="<?=$tokenpost?>" />
   <div class="wrap"><h2>Display options</h2>
   <table class="form-table">
   <tr valign="top">
         <th scope="row">Title:</th>
         <td><input type="text" name="wp_relatedposts_title" value="<?=$options['title']?>"></td>
   </tr>
   <tr valign="top">
	 <th scope="row">Number posts:</th>
         <td><input type="text" name="wp_relatedposts_num" value="<?=$options['count']?>"></td>
   </tr>
   <tr valign="top">
         <th scope="row">Related on:</th>
         <td>
		<select name="wp_relatedposts_type">
			<option selected value="<?=$options['type']?>"><?=$options['type']?></option>
         		<option value="Tags">Tags</option>
			<option value="Category">Category</option>
		</select>
	</td>
   </tr>
   <tr valign="top">
         <th scope="row">CSS:</th>
         <td>
		<textarea cols=30 rows=5 name="wp_relatedposts_css"><?=$options['css']?></textarea>
	</td>
   </tr>
   </table>
   <p class="submit">
	<input type="submit" value="Update Options &raquo;" name="submit">
   </p>
   </div>
   </form>
<?php
}
function wp_relatedposts_admin_init()
{
   add_options_page('WP Related Posts', 'WP Related Posts', 8, basename(__FILE__), 'wp_relatedposts_admin_menu'); 
}
function wp_relatedposts_activation()
{
   add_option('wp_relatedposts_title','Related posts');
   add_option('wp_relatedposts_type','Tags');
   add_option('wp_relatedposts_num',10);
   add_option('wp_relatedposts_css','.wp_related_title {color: #2a526b;} .wp_related_url {color: #2a526b;}');
}
function wp_relatedposts_deactivation()
{
   delete_option('wp_relatedposts_title');
   delete_option('wp_relatedposts_type');
   delete_option('wp_relatedposts_num');
   delete_option('wp_relatedposts_css');
}
if (function_exists('add_action')) 
{
	add_action('the_content', 'wp_relatedposts_content');
	add_action('admin_menu', 'wp_relatedposts_admin_init');

	register_activation_hook(__FILE__, 'wp_relatedposts_activation');
	register_deactivation_hook(__FILE__, 'wp_relatedposts_deactivation');
}
else {
	header("HTTP/1.1 404 Not Found");
	die();
}
?>
