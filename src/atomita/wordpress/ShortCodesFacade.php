<?php

namespace atomita\wordpress;

/**
 * @author atomita
 */
class ShortCodesFacade extends \atomita\FacadeAEasy
{

	static public function activate()
	{
		$instance = self::facadeInstance();
		
		foreach (get_class_methods(get_class($instance)) as $method) {
			add_shortcode($method, array($instance, $method));
		}
	}

}
