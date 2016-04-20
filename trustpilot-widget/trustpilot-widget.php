<?php
	/*
	Plugin Name: Widget to show a trustpilot score
	Description: Small plugin to display a widget like the TrustBox mini
	Version: 1.0
	Author: Folkmann
	*/
	/* Start Adding Functions Below this Line */

	// Creating the widget
	class wp_trustpilot_widget extends WP_Widget {

		function __construct() {
			parent::__construct(
				// Base ID of your widget
				'wp_trustpilot_widget',

				// Widget name will appear in UI
				__('Show a trustpilot score', 'wp_trustpilot_widget_domain'),

				// Widget description
				array('description' => __('Plugin to display trustpilot rating', 'wp_trustpilot_widget_domain'),)
			);
		}
		 
		// Creating widget front-end
		// This is where the action happens
		public function widget($args, $instance){
			$title = apply_filters('widget_title', $instance['title']);
			$site = apply_filters('widget_site', $instance['site']);
			$url = 'https://dk.trustpilot.com/review/'.$site;
			// before and after widget arguments are defined by themes
			echo $args['before_widget'];
			if(!empty( $title ) )
			echo $args['before_title'].'<a target="_BLANK" href="'.$url.'">'.$title.'</a>'.$args['after_title'];

			// Get data from trustpilot
			$template = file_get_contents($url);
			$cut = explode('ratingValue', $template);
			$cut = explode('span>', $cut[1]);
			$rating = floatval(str_replace("\">", "", str_replace("</", "", $cut[0])));
			$stars = round($rating/2);
			if($stars == 1)
				$starClass = "one";
			elseif($stars == 2)
				$starClass = "two";
			elseif($stars == 3)
				$starClass = "three";
			elseif($stars == 4)
				$starClass = "four";
			elseif($stars == 5)
				$starClass = "five";
			$cut = explode('ratingCount', $template);
			$cut = explode('span>', $cut[1]);
			$reviews = intval(str_replace("'>", "", str_replace("</", "", $cut[0])));

			// This is where you run the code and display the output
			$out .= "<style>\n";
			$out .= "	.widget_wp_trustpilot_widget .stars{ height: 40px; }\n";
			$out .= "	.widget_wp_trustpilot_widget .one{ background-color: #e22027 !important; }\n";
			$out .= "	.widget_wp_trustpilot_widget .two{ background-color: #f47324 !important; }\n";
			$out .= "	.widget_wp_trustpilot_widget .three{ background-color: #f8cc18 !important; }\n";
			$out .= "	.widget_wp_trustpilot_widget .four{ background-color: #73b143 !important; }\n";
			$out .= "	.widget_wp_trustpilot_widget .five{ background-color: #007f4e !important; }\n";
			$out .= "	.widget_wp_trustpilot_widget .trustpilot-star{ padding: 4px; background-color: #c8c8c8; width: 38px; float: left; margin: 0 4px 0 0; border-radius: 4px; }\n";
			$out .= "	.widget_wp_trustpilot_widget .trustpilot-star img{ height: 30px; width: 30px; }\n";
			$out .= "</style>\n";
			$out .= "<div class=\"stars\">\n";
			for($i = 1; $i <= 5; $i++){
				if($i <= $stars)
					$out .= "	<div class=\"trustpilot-star ".$starClass."\"><img src=\"".WP_PLUGIN_URL."/trust-pilot-widget/star.png\" class=\"\"></div>\n";
				else
					$out .= "	<div class=\"trustpilot-star\"><img src=\"".WP_PLUGIN_URL."/trust-pilot-widget/star.png\" class=\"\"></div>\n";
			}
			$out .= "</div>\n";
			echo __($out, 'wp_trustpilot_widget_domain');
			echo __("Trustscore: ".$rating." / 10<br/>\n", 'wp_trustpilot_widget_domain');
			echo __("Reviews: ".$reviews, 'wp_trustpilot_widget_domain');
			echo $args['after_widget'];
		}

		// Widget Backend
		public function form($instance){
			if(isset($instance['title']))
				$title = $instance['title'];
			else
				$title = __('TrustPilot', 'wp_trustpilot_widget_domain');

			if(isset($instance['site']))
				$site = $instance['site'];
			else
				$site = __('www.trustpilot.com', 'wp_trustpilot_widget_domain');

			// Widget admin form
?>
<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
	<br/>
	<br/>
	<label for="<?php echo $this->get_field_id('site'); ?>"><?php _e('Site:'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('site'); ?>" name="<?php echo $this->get_field_name('site'); ?>" type="text" value="<?php echo esc_attr($site); ?>"/>
</p>
<?php
		}

		// Updating widget replacing old instances with new
		public function update($new_instance, $old_instance){
			$instance = array();
			$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
			$instance['site'] = (!empty($new_instance['site'])) ? strip_tags($new_instance['site']) : '';
			return $instance;
		}
	} // Class wp_trustpilot_widget ends here
	 
	// Register and load the widget
	function wpb_load_widget(){
		register_widget('wp_trustpilot_widget');
	}
	add_action('widgets_init', 'wpb_load_widget');
	/* Stop Adding Functions Below this Line */
?>