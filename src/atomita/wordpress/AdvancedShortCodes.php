<?php

namespace atomita\wordpress;

/**
 * @author atomita
 */
class AdvancedShortCodes
{

	/**
	 * Output Posts
	 */
	public function posts($atts, $template)
	{
		static $with;
		if (!isset($with)){
			$with = function ($v){ return $v; };
		}
		static $get;
		if (!isset($get)){
			$get = function ($post, $key /* , $args.. */)
				{
					switch ($key){
						case 'url':
							return get_permalink($post->ID);
							break;
						case 'title':
							return get_the_title($post->ID);
							break;
						case 'date':
							$args = array_slice(func_get_args(), 2);
							return mysql2date(isset($args[0]) ? $args[0] : get_option('date_format'), $post->post_date);
							break;
						case 'excerpt':
							return apply_filters('the_excerpt', get_the_excerpt());
							break;
						case 'content':
							return get_the_content();
							break;
					}
					return '';
				};
		}

		// get posts
		$attrs = shortcode_atts(array('', 'param' => ''), $atts);
		$param = empty($attrs[0]) ? (empty($attrs['param']) ? '{}' : $attrs['param']) : $attrs[0];
		$args  = json_decode($param, true);
		$posts = get_posts($args);

		// here document sign
		$here = 'EOTHTML';
		while (preg_match("/^{$here};/mu", $template)){
			$here .= rand(10000, 99999);
		}

		// use vars
		$use = array();
		foreach (array('url', 'title', 'date', 'excerpt', 'content') as $v){
			$use[$v] = (false !== strpos($template, "{\${$v}}"));
		}
		
		// apply template function
		$apply_template = function($post)use($template, $here, $with, $get, $use){
			foreach (array('url', 'title', 'date', 'excerpt', 'content') as $v){
				if ($use[$v]){
					$$v = $get($post, $v);
				}
			}
			return eval('return <<<' . $here . PHP_EOL . $template . PHP_EOL . $here . ';' . PHP_EOL);
		};

		// apply template
		global $post;
		$_post = $post;
		
		$contents = array();
		foreach ($posts as $__post){
			$post = $__post;
			setup_postdata($__post);
			$contents[] = $apply_template($__post);
		}

		$post = $_post;
		setup_postdata($post);
		return implode('', $contents);
	}
}
