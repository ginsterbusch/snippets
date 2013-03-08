<?php
/**
 * Embed external content via iframe - eg. the shopping cart of your shop site.
 */

class iFrameContentWidget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'description' => 'Embed external content via iframe.' );
		$control_ops = array('width' => 450, 'height' => 500);
		parent::__construct( 'iframe_content_widget', __('iFrame Content Widget'), $widget_ops, $control_ops );
	}



	function widget($args, $instance) {
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		
		$url = $instance['url'];
		
		
		if( !empty($instance['width']) ) {
			
			$custom_css = '[widget_class]-iframe { width:' . $instance['width'];
			
			if( !empty( $instance['height'] ) ) {
				$custom_css .= '; height: ' . $instance['height']; 
			
			}
			
			$custom_css .= '; }' . "\n\n";
			
			
		}
		
		// may overwrite already existing rules
		$custom_css .= $instance['custom_css'];
		
		$template = new StdClass;
		$template->title = $title;
		$template->widget_id = $this->id;
		$template->id = $this->id;
		$template->widget_class = $this->widget_options['classname'];
		$template->url = $url;
		
		
		
	// widget_output_main.start
	
		if( !empty( $url ) ) {
		
			echo $args['before_widget'];
			
			if ( !empty($title) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			
			?>
			
			<iframe src="<?php echo $url; ?>" class="<?php echo $template->widget_class . '-iframe'; ?>" border="0" frameborder="0" scrolling="no" noscroll="noscroll"><noframes><a href="<?php echo $url; ?>">Content: <?php echo $url; ?></a></noframes></iframe>
			
			<?php

			echo $args['after_widget'];	
			
			// widget_output_main.end

			// custom css.start

			

			if( !empty( $custom_css ) ) { ?>
			<style type="text/css">
			<?php if(!empty($custom_css) ) { /** A duplicate one might ask? No, actually NOT. Snippet out of a much more advanced widget. So I left this in here just in case I need to add back a few other functions ;) */ ?>
				/* custom css for .<?php echo $this->classname; ?>, #<?php echo $this->id; ?> */
				<?php echo str_replace( array('[widget_id]', '[widget_class]', '[url]'), array('#'.$this->id, '.'.$this->widget_options['classname'], $url), $custom_css ); ?>
			<?php } ?>
			
			</style>
			<?php
			}
			// custom css.end
		} // no effort if there's NO url given
	
	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		
		$instance['url'] = $new_instance['url'];
		$instance['width'] = $new_instance['width'];
		$instance['height'] = $new_instance['height'];
		
		$instance['custom_css'] = $new_instance['custom_css'];
		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
	
		$url = $instance['url'];
		$width = $instance['width'];
		$height = $instance['height'];
	
		$custom_css = $instance['custom_css'];
		
	
	
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('URL:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" value="<?php echo $url; ?>" /><br />
			<small>URL that shall be displayed inside the iframe.</small>
		</p>
		
		
		<p>
			<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('width:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $width; ?>" /><br />
			<small>Width of the iframe (eg. "100 px" = 100 pixel wide, or "100%" = completely fill the available space inside its respective sidebar or widget container.</small>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('height:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $height; ?>" /><br />
			<small>Height of the iframe (eg. "100 px" = 100 pixel high, or "100%" = completely fill the available space from bottom to top of its respective sidebar or widget container.</small>
		</p>
		
		
		<p>
			<label for="<?php echo $this->get_field_id('custom_css'); ?>"><?php _e( 'Custom CSS:' ); ?></label> <textarea class="widefat" name="<?php echo $this->get_field_name('custom_css'); ?>" id="<?php echo $this->get_field_id('custom_css'); ?>"><?php echo $custom_css; ?></textarea>
			<br />
			<small><?php _e( 'Set custom CSS for this widget. [widget_id] and [widget_class] will be replaced accordingly.' ); ?></small>
		</p>
		<?php
	}
}

/**
 * Fetches remotely located data, ie. HTML snippets and outputs them (optionally strips stuff like javascript, etc.)
 */



class remoteContentWidget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'description' => 'Fetch externally located content (HTML).' );
		$control_ops = array('width' => 450, 'height' => 500);
		parent::__construct( 'remote_content_widget', __('Remote Content Widget'), $widget_ops, $control_ops );
	}



	function widget($args, $instance) {
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		
		$url = $instance['url'];
		$charset = $instance['charset'];
		
		
		
		$strip['js'] = !empty( $instance['strip_js'] ) ? true : false;
		$strip['csss'] = !empty( $instance['strip_css'] ) ? true : false;
		$strip['comments'] = !empty( $instance['strip_comments'] ) ? true : false;
		$strip['tags'] = !empty( $instance['strip_tags'] ) ? true : false;
		
		
		if( !empty($instance['width']) ) {
			
			$custom_css = '[widget_class]-iframe { width:' . $instance['width'];
			
			if( !empty( $instance['height'] ) ) {
				$custom_css .= '; height: ' . $instance['height']; 
			
			}
			
			$custom_css .= '; }' . "\n\n";
			
			
		}
		
		// may overwrite already existing rules
		$custom_css .= $instance['custom_css'];
		
		$template = new StdClass;
		$template->title = $title;
		$template->widget_id = $this->id;
		$template->id = $this->id;
		$template->widget_class = $this->widget_options['classname'];
		$template->url = $url;
		
		
		
	// widget_output_main.start
	
		if( !empty( $url ) ) {
		
			echo $args['before_widget'];
			
			if ( !empty($title) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			
			flush();
			$output = _slf_fetch_url( $url, $charset );
			
			/**
			 * Strip given types of data
			 * @see http://www.php.net/manual/en/function.strip-tags.php#68757
			 */
			
			// strip javascript
			if( $strip['js'] != false ) { // strip javascript
				$output = preg_replace('@<script[^>]*?>.*?</script>@si', '', $output);
			}
			
			if( $strip['css'] != false ) {
				$output = preg_replace('@<style[^>]*?>.*?</style>@siU', '', $output);
			}
			
			if( $strip['comments'] != false) {
				$output = preg_replace('@<![\s\S]*?--[ \t\n\r]*>@', '', $output);
			}
			
			if( $strip['tags'] != false) {
				/** 
				 * NOTE: possible better version (yet to test): '@<[\/\!]*?[^<>]*?>@si'
				 */
				$output = strip_tags( $output );
			}
			
			// output content
			
			echo $output;
			flush();
			
			echo $args['after_widget'];	
		
			// widget_output_main.end

			// custom css.start

			

			if( !empty( $custom_css ) ) { ?>
			<style type="text/css">
			<?php if(!empty($custom_css) ) { /** A duplicate one might ask? No, actually NOT. Snippet out of a much more advanced widget. So I left this in here just in case I need to add back a few other functions ;) */ ?>
				/* custom css for .<?php echo $this->classname; ?>, #<?php echo $this->id; ?> */
				<?php echo str_replace( array('[widget_id]', '[widget_class]', '[url]'), array('#'.$this->id, '.'.$this->widget_options['classname'], $url), $custom_css ); ?>
			<?php } ?>
			
			</style>
			<?php
			flush();
			
			}
			// custom css.end
		} // no effort if there's NO url given
	
	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		
		$instance['url'] = $new_instance['url'];
		$instance['width'] = $new_instance['width'];
		$instance['height'] = $new_instance['height'];
		$instance['charset'] = ( !empty($new_instance['charset']) ? $new_instance['charset'] : 'utf-8' );
		
		$instance['strip_js'] = !empty($new_instance['strip_js']) ? 1 : 0;
		
		
		$instance['custom_css'] = $new_instance['custom_css'];
		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
	
		$url = $instance['url'];
		$width = $instance['width'];
		$height = $instance['height'];
		$charset = $instance['charset'];
	
		// builds a switch for the checkbox
		$strip_js = isset($instance['strip_js']) ? (bool) $instance['strip_js'] : false;
	
		$custom_css = $instance['custom_css'];
		
		
	
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('URL:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" value="<?php echo $url; ?>" /><br />
			<small>URL from which the content shall be retrieved.</small>
		</p>
		
		
		<p>
			<label for="<?php echo $this->get_field_id('charset'); ?>"><?php _e('Charset:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('charset'); ?>" name="<?php echo $this->get_field_name('charset'); ?>" value="<?php echo $charset; ?>" /><br />
			<small>Optional character setings for the content; defaults to UTF-8. European usually is ISO-8859-15 (extended) or ISO-8859-1 (western europe).</small>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $width; ?>" /><br />
			<small>Width of the content (eg. "100 px" = 100 pixel wide, or "100%" = completely fill the available space inside its respective sidebar or widget container.</small>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $height; ?>" /><br />
			<small>Height of the content (eg. "100 px" = 100 pixel high, or "100%" = completely fill the available space from bottom to top of its respective sidebar or widget container.</small>
		</p>
		
		
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('strip_js'); ?>" name="<?php echo $this->get_field_name('strip_js'); ?>" value="1" <?php checked( $strip_js ); ?> /> <label for="<?php echo $this->get_field_id('strip_js'); ?>"><?php _e('Strip Javascript') ?></label> 
			<small>&mdash; Strip <a href="http://en.wikipedia.org/wiki/JavaScript">Javascript</a> from the retrieved content (eg. to avoid JS errors because of missing libraries ect). </small>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('custom_css'); ?>"><?php _e( 'Custom CSS:' ); ?></label> <textarea class="widefat" name="<?php echo $this->get_field_name('custom_css'); ?>" id="<?php echo $this->get_field_id('custom_css'); ?>"><?php echo $custom_css; ?></textarea>
			<br />
			<small><?php _e( 'Set custom CSS for this widget. [widget_id] and [widget_class] will be replaced accordingly.' ); ?></small>
		</p>
		<?php
	}
}
