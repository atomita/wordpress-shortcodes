<?php

namespace \Atomita\Wordpress;

/**
 * 
 */
class ShortCodes
{

	public static function activate(){
		foreach (get_class_methods(__CLASS__) as $method) {
			if('activate' !== $method){
				add_shortcode($tag, array(__CLASS__, $method));
			}
		}
	}

	/**
	 * Archive page URL of the term
	 * example: [term_url taxonomy term]
	 */
	static public function term_url($atts)
	{
		$attrs	  = shortcode_atts(array('', '', 'taxonomy' => '', 'term' => ''), $atts);
		$taxonomy = empty($attrs[0]) ? (empty($attrs['taxonomy']) ? '' : $attrs['taxonomy']) : $attrs[0];
		$term	  = empty($attrs[1]) ? (empty($attrs['term']) ? '' : $attrs['term']) : $attrs[1];
		$url	  = get_term_link($term, $taxonomy);
		if (is_wp_error($url)){
			return '';
		}
		return $url;
	}

	/**
	 * Archive page URL of the post type
	 * example: [post_type_url post-type]
	 */
	static public function post_type_url($atts)
	{
		$attrs = shortcode_atts(array('', '', 'slug' => ''), $atts);
		$slug  = empty($attrs[0]) ? (empty($attrs['slug']) ? '' : $attrs['slug']) : $attrs[0];
		$url   = get_post_type_archive_link($slug);
		if (is_wp_error($url)){
			return '';
		}
		return $url;
	}

	/**
	 * Page URL
	 * example: [page_url slug]
	 */
	static public function page_url($atts)
	{
		$attrs = shortcode_atts(array('', '', 'slug' => ''), $atts);
		$slug  = empty($attrs[0]) ? (empty($attrs['slug']) ? '' : $attrs['slug']) : $attrs[0];

		global $wpdb;
		$query = $wpdb->prepare("select ID from {$wpdb->posts} where post_name = %s", $slug);
		$pages = $wpdb->get_results($query);
		if (empty($pages)){
			return '';
		}
		return get_permalink($pages[0]->ID);
	}


	/**
	 * Home URL
	 */
	static public function home_url($atts)
	{
		$attrs	= shortcode_atts(array('', '', 'path' => '', 'scheme' => ''), $atts);
		$path	= empty($attrs[0]) ? (empty($attrs['path']) ? '' : $attrs['path']) : $attrs[0];
		$scheme = empty($attrs[1]) ? (empty($attrs['scheme']) ? null : $attrs['scheme']) : $attrs[1];
		return home_url($path, $scheme);
	}
	
	/**
	 * Output to in the case of a given theme
	 */
	static public function theme_is($attr, $content = '')
	{
		$attrs = shortcode_atts(array('', 'theme' => ''), $atts);
		$theme = empty($attrs[0]) ? (empty($attrs['theme']) ? '' : $attrs['theme']) : $attrs[0];
		
		if (basename(STYLESHEETPATH) == $theme){
			return do_shortcode($content);
		}
		return '';
	}

}
