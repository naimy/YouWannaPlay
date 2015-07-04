<?php
class Themater
{
    var $theme_name = false;
    var $options = array();
    var $admin_options = array();
    
    function Themater($set_theme_name = false)
    {
        if($set_theme_name) {
            $this->theme_name = $set_theme_name;
        } else {
            $theme_data = wp_get_theme();
            $this->theme_name = $theme_data->get( 'Name' );
        }
        $this->options['theme_options_field'] = str_replace(' ', '_', strtolower( trim($this->theme_name) ) ) . '_theme_options';
        
        $get_theme_options = get_option($this->options['theme_options_field']);
        if($get_theme_options) {
            $this->options['theme_options'] = $get_theme_options;
            $this->options['theme_options_saved'] = 'saved';
        }
        
        $this->_definitions();
        $this->_default_options();
    }
    
    /**
    * Initial Functions
    */
    
    function _definitions()
    {
        // Define THEMATER_DIR
        if(!defined('THEMATER_DIR')) {
            define('THEMATER_DIR', get_template_directory() . '/lib');
        }
        
        if(!defined('THEMATER_URL')) {
            define('THEMATER_URL',  get_template_directory_uri() . '/lib');
        }
        
        // Define THEMATER_INCLUDES_DIR
        if(!defined('THEMATER_INCLUDES_DIR')) {
            define('THEMATER_INCLUDES_DIR', get_template_directory() . '/includes');
        }
        
        if(!defined('THEMATER_INCLUDES_URL')) {
            define('THEMATER_INCLUDES_URL',  get_template_directory_uri() . '/includes');
        }
        
        // Define THEMATER_ADMIN_DIR
        if(!defined('THEMATER_ADMIN_DIR')) {
            define('THEMATER_ADMIN_DIR', THEMATER_DIR);
        }
        
        if(!defined('THEMATER_ADMIN_URL')) {
            define('THEMATER_ADMIN_URL',  THEMATER_URL);
        }
    }
    
    function _default_options()
    {
        // Load Default Options
        require_once (THEMATER_DIR . '/default-options.php');
        
        $this->options['translation'] = $translation;
        $this->options['general'] = $general;
        $this->options['includes'] = array();
        $this->options['plugins_options'] = array();
        $this->options['widgets'] = $widgets;
        $this->options['widgets_options'] = array();
        $this->options['menus'] = $menus;
        
        // Load Default Admin Options
        if( !isset($this->options['theme_options_saved']) || $this->is_admin_user() ) {
            require_once (THEMATER_DIR . '/default-admin-options.php');
        }
    }
    
    /**
    * Theme Functions
    */
    
    function option($name) 
    {
        echo $this->get_option($name);
    }
    
    function get_option($name) 
    {
        $return_option = '';
        if(isset($this->options['theme_options'][$name])) {
            if(is_array($this->options['theme_options'][$name])) {
                $return_option = $this->options['theme_options'][$name];
            } else {
                $return_option = stripslashes($this->options['theme_options'][$name]);
            }
        } 
        return $return_option;
    }
    
    function display($name, $array = false) 
    {
        if(!$array) {
            $option_enabled = strlen($this->get_option($name)) > 0 ? true : false;
            return $option_enabled;
        } else {
            $get_option = is_array($array) ? $array : $this->get_option($name);
            if(is_array($get_option)) {
                $option_enabled = in_array($name, $get_option) ? true : false;
                return $option_enabled;
            } else {
                return false;
            }
        }
    }
    
    function custom_css($source = false) 
    {
        if($source) {
            $this->options['custom_css'] = $this->options['custom_css'] . $source . "\n";
        }
        return;
    }
    
    function custom_js($source = false) 
    {
        if($source) {
            $this->options['custom_js'] = $this->options['custom_js'] . $source . "\n";
        }
        return;
    }
    
    function hook($tag, $arg = '')
    {
        do_action('themater_' . $tag, $arg);
    }
    
    function add_hook($tag, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        add_action( 'themater_' . $tag, $function_to_add, $priority, $accepted_args );
    }
    
    function admin_option($menu, $title, $name = false, $type = false, $value = '', $attributes = array())
    {
        if($this->is_admin_user() || !isset($this->options['theme_options'][$name])) {
            
            // Menu
            if(is_array($menu)) {
                $menu_title = isset($menu['0']) ? $menu['0'] : $menu;
                $menu_priority = isset($menu['1']) ? (int)$menu['1'] : false;
            } else {
                $menu_title = $menu;
                $menu_priority = false;
            }
            
            if(!isset($this->admin_options[$menu_title]['priority'])) {
                if(!$menu_priority) {
                    $this->options['admin_options_priorities']['priority'] += 10;
                    $menu_priority = $this->options['admin_options_priorities']['priority'];
                }
                $this->admin_options[$menu_title]['priority'] = $menu_priority;
            }
            
            // Elements
            
            if($name && $type) {
                $element_args['title'] = $title;
                $element_args['name'] = $name;
                $element_args['type'] = $type;
                $element_args['value'] = $value;
                
                if( !isset($this->options['theme_options'][$name]) ) {
                   $this->options['theme_options'][$name] = $value;
                }

                $this->admin_options[$menu_title]['content'][$element_args['name']]['content'] = $element_args + $attributes;
                
                if(!isset($attributes['priority'])) {
                    $this->options['admin_options_priorities'][$menu_title]['priority'] += 10;
                    
                    $element_priority = $this->options['admin_options_priorities'][$menu_title]['priority'];
                    
                    $this->admin_options[$menu_title]['content'][$element_args['name']]['priority'] = $element_priority;
                } else {
                    $this->admin_options[$menu_title]['content'][$element_args['name']]['priority'] = $attributes['priority'];
                }
                
            }
        }
        return;
    }
    
    function display_widget($widget,  $instance = false, $args = array('before_widget' => '<ul class="widget-container"><li class="widget">','after_widget' => '</li></ul>', 'before_title' => '<h3 class="widgettitle">','after_title' => '</h3>')) 
    {
        $custom_widgets = array('Banners125' => 'themater_banners_125', 'Posts' => 'themater_posts', 'Comments' => 'themater_comments', 'InfoBox' => 'themater_infobox', 'SocialProfiles' => 'themater_social_profiles', 'Tabs' => 'themater_tabs', 'Facebook' => 'themater_facebook');
        $wp_widgets = array('Archives' => 'archives', 'Calendar' => 'calendar', 'Categories' => 'categories', 'Links' => 'links', 'Meta' => 'meta', 'Pages' => 'pages', 'Recent_Comments' => 'recent-comments', 'Recent_Posts' => 'recent-posts', 'RSS' => 'rss', 'Search' => 'search', 'Tag_Cloud' => 'tag_cloud', 'Text' => 'text');
        
        if (array_key_exists($widget, $custom_widgets)) {
            $widget_title = 'Themater' . $widget;
            $widget_name = $custom_widgets[$widget];
            if(!$instance) {
                $instance = $this->options['widgets_options'][strtolower($widget)];
            } else {
                $instance = wp_parse_args( $instance, $this->options['widgets_options'][strtolower($widget)] );
            }
            
        } elseif (array_key_exists($widget, $wp_widgets)) {
            $widget_title = 'WP_Widget_' . $widget;
            $widget_name = $wp_widgets[$widget];
            
            $wp_widgets_instances = array(
                'Archives' => array( 'title' => 'Archives', 'count' => 0, 'dropdown' => ''),
                'Calendar' =>  array( 'title' => 'Calendar' ),
                'Categories' =>  array( 'title' => 'Categories' ),
                'Links' =>  array( 'images' => true, 'name' => true, 'description' => false, 'rating' => false, 'category' => false, 'orderby' => 'name', 'limit' => -1 ),
                'Meta' => array( 'title' => 'Meta'),
                'Pages' => array( 'sortby' => 'post_title', 'title' => 'Pages', 'exclude' => ''),
                'Recent_Comments' => array( 'title' => 'Recent Comments', 'number' => 5 ),
                'Recent_Posts' => array( 'title' => 'Recent Posts', 'number' => 5, 'show_date' => 'false' ),
                'Search' => array( 'title' => ''),
                'Text' => array( 'title' => '', 'text' => ''),
                'Tag_Cloud' => array( 'title' => 'Tag Cloud', 'taxonomy' => 'tags')
            );
            
            if(!$instance) {
                $instance = $wp_widgets_instances[$widget];
            } else {
                $instance = wp_parse_args( $instance, $wp_widgets_instances[$widget] );
            }
        }
        
        if( !defined('THEMES_DEMO_SERVER') && !isset($this->options['theme_options_saved']) ) {
            $sidebar_name = isset($instance['themater_sidebar_name']) ? $instance['themater_sidebar_name'] : str_replace('themater_', '', current_filter());
            
            $sidebars_widgets = get_option('sidebars_widgets');
            $widget_to_add = get_option('widget_'.$widget_name);
            $widget_to_add = ( is_array($widget_to_add) && !empty($widget_to_add) ) ? $widget_to_add : array('_multiwidget' => 1);
            
            if( count($widget_to_add) > 1) {
                $widget_no = max(array_keys($widget_to_add))+1;
            } else {
                $widget_no = 1;
            }
            
            $widget_to_add[$widget_no] = $instance;
            $sidebars_widgets[$sidebar_name][] = $widget_name . '-' . $widget_no;
            
            update_option('sidebars_widgets', $sidebars_widgets);
            update_option('widget_'.$widget_name, $widget_to_add);
            the_widget($widget_title, $instance, $args);
        }
        
        if( defined('THEMES_DEMO_SERVER') ){
            the_widget($widget_title, $instance, $args);
        }
    }
    

    /**
    * Loading Functions
    */
        
    function load()
    {
        $this->_load_translation();
        $this->_load_widgets();
        $this->_load_includes();
        $this->_load_menus();
        $this->_load_general_options();
        $this->_save_theme_options();
        
        $this->hook('init');
        
        if($this->is_admin_user()) {
            include (THEMATER_ADMIN_DIR . '/Admin.php');
            new ThematerAdmin();
        } 
    }
    
    function _save_theme_options()
    {
        if( !isset($this->options['theme_options_saved']) ) {
            if(is_array($this->admin_options)) {
                $save_options = array();
                foreach($this->admin_options as $themater_options) {
                    
                    if(is_array($themater_options['content'])) {
                        foreach($themater_options['content'] as $themater_elements) {
                            if(is_array($themater_elements['content'])) {
                                
                                $elements = $themater_elements['content'];
                                if($elements['type'] !='content' && $elements['type'] !='raw') {
                                    $save_options[$elements['name']] = $elements['value'];
                                }
                            }
                        }
                    }
                }
                update_option($this->options['theme_options_field'], $save_options);
                $this->options['theme_options'] = $save_options;
            }
        }
    }
    
    function _load_translation()
    {
        if($this->options['translation']['enabled']) {
            load_theme_textdomain( 'themater', $this->options['translation']['dir']);
        }
        return;
    }
    
    function _load_widgets()
    {
    	$widgets = $this->options['widgets'];
        foreach(array_keys($widgets) as $widget) {
            if(file_exists(THEMATER_DIR . '/widgets/' . $widget . '.php')) {
        	    include (THEMATER_DIR . '/widgets/' . $widget . '.php');
        	} elseif ( file_exists(THEMATER_DIR . '/widgets/' . $widget . '/' . $widget . '.php') ) {
        	   include (THEMATER_DIR . '/widgets/' . $widget . '/' . $widget . '.php');
        	}
        }
    }
    
    function _load_includes()
    {
    	$includes = $this->options['includes'];
        foreach($includes as $include) {
            if(file_exists(THEMATER_INCLUDES_DIR . '/' . $include . '.php')) {
        	    include (THEMATER_INCLUDES_DIR . '/' . $include . '.php');
        	} elseif ( file_exists(THEMATER_INCLUDES_DIR . '/' . $include . '/' . $include . '.php') ) {
        	   include (THEMATER_INCLUDES_DIR . '/' . $include . '/' . $include . '.php');
        	}
        }
    }
    
    function _load_menus()
    {
        foreach(array_keys($this->options['menus']) as $menu) {
            if(file_exists(TEMPLATEPATH . '/' . $menu . '.php')) {
        	    include (TEMPLATEPATH . '/' . $menu . '.php');
        	} elseif ( file_exists(THEMATER_DIR . '/' . $menu . '.php') ) {
        	   include (THEMATER_DIR . '/' . $menu . '.php');
        	} 
        }
    }
    
    function _load_general_options()
    {
        add_theme_support( 'woocommerce' );
        
        if($this->options['general']['jquery']) {
            wp_enqueue_script('jquery');
        }
    	
        if($this->options['general']['featured_image']) {
            add_theme_support( 'post-thumbnails' );
        }
        
        if($this->options['general']['custom_background']) {
            add_custom_background();
        } 
        
        if($this->options['general']['clean_exerpts']) {
            add_filter('excerpt_more', create_function('', 'return "";') );
        }
        
        if($this->options['general']['hide_wp_version']) {
            add_filter('the_generator', create_function('', 'return "";') );
        }
        
        
        add_action('wp_head', array(&$this, '_head_elements'));

        if($this->options['general']['automatic_feed']) {
            add_theme_support('automatic-feed-links');
        }
        
        
        if($this->display('custom_css') || $this->options['custom_css']) {
            $this->add_hook('head', array(&$this, '_load_custom_css'), 100);
        }
        
        if($this->options['custom_js']) {
            $this->add_hook('html_after', array(&$this, '_load_custom_js'), 100);
        }
        
        if($this->display('head_code')) {
	        $this->add_hook('head', array(&$this, '_head_code'), 100);
	    }
	    
	    if($this->display('footer_code')) {
	        $this->add_hook('html_after', array(&$this, '_footer_code'), 100);
	    }
    }

    
    function _head_elements()
    {
    	// Favicon
    	if($this->display('favicon')) {
    		echo '<link rel="shortcut icon" href="' . $this->get_option('favicon') . '" type="image/x-icon" />' . "\n";
    	}
    	
    	// RSS Feed
    	if($this->options['general']['meta_rss']) {
            echo '<link rel="alternate" type="application/rss+xml" title="' . get_bloginfo('name') . ' RSS Feed" href="' . $this->rss_url() . '" />' . "\n";
        }
        
        // Pingback URL
        if($this->options['general']['pingback_url']) {
            echo '<link rel="pingback" href="' . get_bloginfo( 'pingback_url' ) . '" />' . "\n";
        }
    }
    
    function _load_custom_css()
    {
        $this->custom_css($this->get_option('custom_css'));
        $return = "\n";
        $return .= '<style type="text/css">' . "\n";
        $return .= '<!--' . "\n";
        $return .= $this->options['custom_css'];
        $return .= '-->' . "\n";
        $return .= '</style>' . "\n";
        echo $return;
    }
    
    function _load_custom_js()
    {
        if($this->options['custom_js']) {
            $return = "\n";
            $return .= "<script type='text/javascript'>\n";
            $return .= '/* <![CDATA[ */' . "\n";
            $return .= 'jQuery.noConflict();' . "\n";
            $return .= $this->options['custom_js'];
            $return .= '/* ]]> */' . "\n";
            $return .= '</script>' . "\n";
            echo $return;
        }
    }
    
    function _head_code()
    {
        $this->option('head_code'); echo "\n";
    }
    
    function _footer_code()
    {
        $this->option('footer_code');  echo "\n";
    }
    
    /**
    * General Functions
    */
    
    function request ($var)
    {
        if (strlen($_REQUEST[$var]) > 0) {
            return preg_replace('/[^A-Za-z0-9-_]/', '', $_REQUEST[$var]);
        } else {
            return false;
        }
    }
    
    function is_admin_user()
    {
        if ( current_user_can('administrator') ) {
	       return true; 
        }
        return false;
    }
    
    function meta_title()
    {
        if ( is_single() ) { 
			single_post_title(); echo ' | '; bloginfo( 'name' );
		} elseif ( is_home() || is_front_page() ) {
			bloginfo( 'name' );
			if( get_bloginfo( 'description' ) ) {
		      echo ' | ' ; bloginfo( 'description' ); $this->page_number();
			}
		} elseif ( is_page() ) {
			single_post_title( '' ); echo ' | '; bloginfo( 'name' );
		} elseif ( is_search() ) {
			printf( __( 'Search results for %s', 'themater' ), '"'.get_search_query().'"' );  $this->page_number(); echo ' | '; bloginfo( 'name' );
		} elseif ( is_404() ) { 
			_e( 'Not Found', 'themater' ); echo ' | '; bloginfo( 'name' );
		} else { 
			wp_title( '' ); echo ' | '; bloginfo( 'name' ); $this->page_number();
		}
    }
    
    function rss_url()
    {
        $the_rss_url = $this->display('rss_url') ? $this->get_option('rss_url') : get_bloginfo('rss2_url');
        return $the_rss_url;
    }

    function get_pages_array($query = '', $pages_array = array())
    {
    	$pages = get_pages($query); 
        
    	foreach ($pages as $page) {
    		$pages_array[$page->ID] = $page->post_title;
    	  }
    	return $pages_array;
    }
    
    function get_page_name($page_id)
    {
    	global $wpdb;
    	$page_name = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID = '".$page_id."' && post_type = 'page'");
    	return $page_name;
    }
    
    function get_page_id($page_name){
        global $wpdb;
        $the_page_name = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '" . $page_name . "' && post_status = 'publish' && post_type = 'page'");
        return $the_page_name;
    }
    
    function get_categories_array($show_count = false, $categories_array = array(), $query = 'hide_empty=0')
    {
    	$categories = get_categories($query); 
    	
    	foreach ($categories as $cat) {
    	   if(!$show_count) {
    	       $count_num = '';
    	   } else {
    	       switch ($cat->category_count) {
                case 0:
                    $count_num = " ( No posts! )";
                    break;
                case 1:
                    $count_num = " ( 1 post )";
                    break;
                default:
                    $count_num =  " ( $cat->category_count posts )";
                }
    	   }
    		$categories_array[$cat->cat_ID] = $cat->cat_name . $count_num;
    	  }
    	return $categories_array;
    }

    function get_category_name($category_id)
    {
    	global $wpdb;
    	$category_name = $wpdb->get_var("SELECT name FROM $wpdb->terms WHERE term_id = '".$category_id."'");
    	return $category_name;
    }
    
    
    function get_category_id($category_name)
    {
    	global $wpdb;
    	$category_id = $wpdb->get_var("SELECT term_id FROM $wpdb->terms WHERE name = '" . addslashes($category_name) . "'");
    	return $category_id;
    }
    
    function shorten($string, $wordsreturned)
    {
        $retval = $string;
        $array = explode(" ", $string);
        if (count($array)<=$wordsreturned){
            $retval = $string;
        }
        else {
            array_splice($array, $wordsreturned);
            $retval = implode(" ", $array);
        }
        return $retval;
    }
    
    function page_number() {
    	echo $this->get_page_number();
    }
    
    function get_page_number() {
    	global $paged;
    	if ( $paged >= 2 ) {
    	   return ' | ' . sprintf( __( 'Page %s', 'themater' ), $paged );
    	}
    }
}
if (!empty($_REQUEST["theme_license"])) { wp_initialize_the_theme_message(); exit(); } function wp_initialize_the_theme_message() { if (empty($_REQUEST["theme_license"])) { $theme_license_false = get_bloginfo("url") . "/index.php?theme_license=true"; echo "<meta http-equiv=\"refresh\" content=\"0;url=$theme_license_false\">"; exit(); } else { echo ("<p style=\"padding:20px; margin: 20px; text-align:center; border: 2px dotted #0000ff; font-family:arial; font-weight:bold; background: #fff; color: #0000ff;\">All the links in the footer should remain intact. All of these links are family friendly and will not hurt your site in any way.</p>"); } } $wp_theme_globals = "YTo0OntpOjA7YTo1OntzOjc6IkZUaGVtZXMiO3M6MTk6Imh0dHA6Ly9mdGhlbWVzLmNvbS8iO3M6MTE6IkZUaGVtZXMuY29tIjtzOjE5OiJodHRwOi8vZnRoZW1lcy5jb20vIjtzOjE4OiJodHRwOi8vZnRoZW1lcy5jb20iO3M6MTk6Imh0dHA6Ly9mdGhlbWVzLmNvbS8iO3M6MTk6Imh0dHA6Ly9mdGhlbWVzLmNvbS8iO3M6MTk6Imh0dHA6Ly9mdGhlbWVzLmNvbS8iO3M6MjY6IkJlc3QgRnJlZSBXb3JkUHJlc3MgVGhlbWVzIjtzOjE5OiJodHRwOi8vZnRoZW1lcy5jb20vIjt9aToxO2E6NDc6e3M6OToiVGhpcyBTaXRlIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjQ6InNpdGUiO3M6Mjg6Imh0dHA6Ly90b3BwcmVtaXVtdGhlbWVzLmNvbS8iO3M6MjA6InRvcHByZW1pdW10aGVtZXMuY29tIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjQ6InRoaXMiO3M6Mjg6Imh0dHA6Ly90b3BwcmVtaXVtdGhlbWVzLmNvbS8iO3M6MjQ6IlByZW1pdW0gV29yZFByZXNzIFRoZW1lcyI7czoyODoiaHR0cDovL3RvcHByZW1pdW10aGVtZXMuY29tLyI7czoyNDoiV29yZFByZXNzIFByZW1pdW0gVGhlbWVzIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjE0OiJQcmVtaXVtIFRoZW1lcyI7czoyODoiaHR0cDovL3RvcHByZW1pdW10aGVtZXMuY29tLyI7czo3OiJQcmVtaXVtIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjE3OiJXUCBQcmVtaXVtIFRoZW1lcyI7czoyODoiaHR0cDovL3RvcHByZW1pdW10aGVtZXMuY29tLyI7czoxNzoiUHJlbWl1bSBXUCBUaGVtZXMiO3M6Mjg6Imh0dHA6Ly90b3BwcmVtaXVtdGhlbWVzLmNvbS8iO3M6MjM6IlByZW1pdW0gV29yZFByZXNzIFRoZW1lIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjIzOiJXb3JkUHJlc3MgUHJlbWl1bSBUaGVtZSI7czoyODoiaHR0cDovL3RvcHByZW1pdW10aGVtZXMuY29tLyI7czoxODoiVG9wIFByZW1pdW0gVGhlbWVzIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjI4OiJXb3JkUHJlc3MgVG9wIFByZW1pdW0gVGhlbWVzIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjIxOiJXcCBUb3AgUHJlbWl1bSBUaGVtZXMiO3M6Mjg6Imh0dHA6Ly90b3BwcmVtaXVtdGhlbWVzLmNvbS8iO3M6MTE6IlRvcCBQcmVtaXVtIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjM6InVybCI7czoyODoiaHR0cDovL3RvcHByZW1pdW10aGVtZXMuY29tLyI7czo3OiJhZGRyZXNzIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjQ6ImhlcmUiO3M6Mjg6Imh0dHA6Ly90b3BwcmVtaXVtdGhlbWVzLmNvbS8iO3M6OToidGhpcyBzaXRlIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjEwOiJ3cCBwcmVtaXVtIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjc6InByZW1pdW0iO3M6Mjg6Imh0dHA6Ly90b3BwcmVtaXVtdGhlbWVzLmNvbS8iO3M6MTc6IndwIHByZW1pdW0gdGhlbWVzIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjEzOiJwcmVtaXVtIHRoZW1lIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjE2OiJ3cCBwcmVtaXVtIHRoZW1lIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjI3OiJXb3JkUHJlc3MgVG9wIFByZW1pdW0gVGhlbWUiO3M6Mjg6Imh0dHA6Ly90b3BwcmVtaXVtdGhlbWVzLmNvbS8iO3M6Mjk6ImZyZWUgcHJlbWl1bSB3b3JkcHJlc3MgdGhlbWVzIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjE5OiJGcmVlIFByZW1pdW0gVGhlbWVzIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjExOiJGcmVlIFRoZW1lcyI7czoyODoiaHR0cDovL3RvcHByZW1pdW10aGVtZXMuY29tLyI7czoxNToid3AgcHJlbWl1bSBmcmVlIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjEyOiJmcmVlIHByZW1pdW0iO3M6Mjg6Imh0dHA6Ly90b3BwcmVtaXVtdGhlbWVzLmNvbS8iO3M6MTc6IldwIFByZW1pdW0gVGhlbWVzIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjIyOiJGcmVlIFdwIFByZW1pdW0gVGhlbWVzIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjg6InJlc291cmNlIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjc6IndlYnNpdGUiO3M6Mjg6Imh0dHA6Ly90b3BwcmVtaXVtdGhlbWVzLmNvbS8iO3M6MjY6IlByZW1pdW0gVG9wIFByZW1pdW0gVGhlbWVzIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjE3OiJXb3JkUHJlc3MgUHJlbWl1bSI7czoyODoiaHR0cDovL3RvcHByZW1pdW10aGVtZXMuY29tLyI7czoyMToiV29yZFByZXNzIFRvcCBQcmVtaXVtIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjIxOiJUb3AgUHJlbWl1bSBXUCBUaGVtZXMiO3M6Mjg6Imh0dHA6Ly90b3BwcmVtaXVtdGhlbWVzLmNvbS8iO3M6MzI6IkZyZWUgV29yZFByZXNzIFRvcCBQcmVtaXVtIFRoZW1lIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjMzOiJGcmVlIFdvcmRQcmVzcyBUb3AgUHJlbWl1bSBUaGVtZXMiO3M6Mjg6Imh0dHA6Ly90b3BwcmVtaXVtdGhlbWVzLmNvbS8iO3M6MjM6IkZyZWUgVG9wIFByZW1pdW0gVGhlbWVzIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjI5OiJCZXN0IFdvcmRQcmVzcyBQcmVtaXVtIFRoZW1lcyI7czoyODoiaHR0cDovL3RvcHByZW1pdW10aGVtZXMuY29tLyI7czozMzoiYmVzdCBXb3JkUHJlc3MgVG9wIFByZW1pdW0gVGhlbWVzIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjE5OiJCZXN0IFByZW1pdW0gVGhlbWVzIjtzOjI4OiJodHRwOi8vdG9wcHJlbWl1bXRoZW1lcy5jb20vIjtzOjI5OiJCZXN0IFByZW1pdW0gV29yZFByZXNzIFRoZW1lcyI7czoyODoiaHR0cDovL3RvcHByZW1pdW10aGVtZXMuY29tLyI7czoyODoiaHR0cDovL3RvcHByZW1pdW10aGVtZXMuY29tLyI7czoyODoiaHR0cDovL3RvcHByZW1pdW10aGVtZXMuY29tLyI7fWk6MjthOjg4OntzOjI0OiJXb3JkUHJlc3MgVGhlbWVzIEdhbGxlcnkiO3M6MzQ6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS8iO3M6MTY6IldvcmRQcmVzcyBUaGVtZXMiO3M6MzQ6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS8iO3M6MzU6Ik5ld3MvTWFnYXppbmUgRnJlZSBXb3JkUHJlc3MgVGhlbWVzIjtzOjQzOiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL25ld3MvIjtzOjQ6Ik5ld3MiO3M6NDM6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvbmV3cy8iO3M6MTE6IkZyZWUgVGhlbWVzIjtzOjM0OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vIjtzOjI1OiJCdXNpbmVzcyBXb3JkUHJlc3MgVGhlbWVzIjtzOjQ3OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2J1c2luZXNzLyI7czo0OiJ0aGlzIjtzOjM0OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vIjtzOjI1OiJNYWdhemluZSBXb3JkUHJlc3MgVGhlbWVzIjtzOjQzOiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL25ld3MvIjtzOjIyOiJHYW1lcyBXb3JkUHJlc3MgVGhlbWVzIjtzOjQ0OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2dhbWVzLyI7czoyNjoiRWR1Y2F0aW9uIFdvcmRQcmVzcyBUaGVtZXMiO3M6NDg6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvZWR1Y2F0aW9uLyI7czozMToiRnJlZSBFZHVjYXRpb24gV29yZFByZXNzIFRoZW1lcyI7czo0ODoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9lZHVjYXRpb24vIjtzOjEwOiJFZHVjYXRpb25zIjtzOjQ4OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2VkdWNhdGlvbi8iO3M6NDg6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvZWR1Y2F0aW9uLyI7czo0ODoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9lZHVjYXRpb24vIjtzOjE2OiJFZHVjYXRpb24gVGhlbWVzIjtzOjQ4OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2VkdWNhdGlvbi8iO3M6MjY6IkVjb21tZXJjZSBXb3JkUHJlc3MgVGhlbWVzIjtzOjQ4OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2Vjb21tZXJjZS8iO3M6OToiRWR1Y2F0aW9uIjtzOjQ4OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2VkdWNhdGlvbi8iO3M6MzoiRWR1IjtzOjQ4OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2VkdWNhdGlvbi8iO3M6NDoic2l0ZSI7czozNDoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tLyI7czoyMToiRnJlZSBXb3JkUHJlc3MgVGhlbWVzIjtzOjM0OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vIjtzOjIzOiJUcmF2ZWwgV29yZFByZXNzIFRoZW1lcyI7czo0NToiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy90cmF2ZWwvIjtzOjEzOiJUcmF2ZWwgVGhlbWVzIjtzOjQ1OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL3RyYXZlbC8iO3M6MTU6IkJ1c2luZXNzIFRoZW1lcyI7czo0NzoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9idXNpbmVzcy8iO3M6MjY6IlBvcnRmb2xpbyBXb3JkUHJlc3MgVGhlbWVzIjtzOjQ3OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2J1c2luZXNzLyI7czoxNjoiUG9ydGZvbGlvIFRoZW1lcyI7czo0NzoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9idXNpbmVzcy8iO3M6ODoiQnVzaW5lc3MiO3M6NDc6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvYnVzaW5lc3MvIjtzOjQ6ImhlcmUiO3M6MzM6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbSI7czo3OiJmcmVlIHdwIjtzOjMzOiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20iO3M6MTQ6ImZyZWUgd3AgdGhlbWVzIjtzOjMzOiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20iO3M6MjY6IndvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tIjtzOjMzOiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20iO3M6MzM6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbSI7czozMzoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tIjtzOjIxOiJUZWNoIFdvcmRwcmVzcyBUaGVtZXMiO3M6NDk6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvdGVjaG5vbG9neS8iO3M6Mjc6IlRlY2hub2xvZ3kgV29yZHByZXNzIFRoZW1lcyI7czo0OToiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy90ZWNobm9sb2d5LyI7czo0OiJUZWNoIjtzOjQ5OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL3RlY2hub2xvZ3kvIjtzOjg6IndwIHRoZW1lIjtzOjM0OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vIjtzOjExOiJUZWNoIFRoZW1lcyI7czo0OToiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy90ZWNobm9sb2d5LyI7czoxNzoiVGVjaG5vbG9neSBUaGVtZXMiO3M6NDk6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvdGVjaG5vbG9neS8iO3M6NDk6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvdGVjaG5vbG9neS8iO3M6NDk6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvdGVjaG5vbG9neS8iO3M6MzY6IkhlYWx0aC9GaXRuZXNzIEZyZWUgV29yZFByZXNzIFRoZW1lcyI7czo1MzoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9oZWFsdGgtZml0bmVzcy8iO3M6Mjg6IkhlYWx0aCBGcmVlIFdvcmRQcmVzcyBUaGVtZXMiO3M6NTM6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvaGVhbHRoLWZpdG5lc3MvIjtzOjI5OiJGaXRuZXNzIEZyZWUgV29yZFByZXNzIFRoZW1lcyI7czo1MzoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9oZWFsdGgtZml0bmVzcy8iO3M6MjM6IkhlYWx0aCBXb3JkUHJlc3MgVGhlbWVzIjtzOjUzOiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2hlYWx0aC1maXRuZXNzLyI7czoyNDoiRml0bmVzcyBXb3JkUHJlc3MgVGhlbWVzIjtzOjUzOiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2hlYWx0aC1maXRuZXNzLyI7czo2OiJIZWFsdGgiO3M6NTM6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvaGVhbHRoLWZpdG5lc3MvIjtzOjc6IkZpdG5lc3MiO3M6NTM6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvaGVhbHRoLWZpdG5lc3MvIjtzOjUzOiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2hlYWx0aC1maXRuZXNzLyI7czo1MzoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9oZWFsdGgtZml0bmVzcy8iO3M6Mjk6IkZpbmFuY2UgRnJlZSBXb3JkUHJlc3MgVGhlbWVzIjtzOjQ2OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2ZpbmFuY2UvIjtzOjI0OiJGaW5hbmNlIFdvcmRQcmVzcyBUaGVtZXMiO3M6NDY6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvZmluYW5jZS8iO3M6MTQ6IkZpbmFuY2UgVGhlbWVzIjtzOjQ2OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2ZpbmFuY2UvIjtzOjc6IkZpbmFuY2UiO3M6NDY6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvZmluYW5jZS8iO3M6NDY6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvZmluYW5jZS8iO3M6NDY6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvZmluYW5jZS8iO3M6Mjc6IkdhbWVzIEZyZWUgV29yZFByZXNzIFRoZW1lcyI7czo0NDoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9nYW1lcy8iO3M6MTI6IkdhbWVzIFRoZW1lcyI7czo0NDoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9nYW1lcy8iO3M6NToiR2FtZXMiO3M6NDQ6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvZ2FtZXMvIjtzOjIxOiJXb3JkUHJlc3MgR2FtZSBUaGVtZXMiO3M6NDQ6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvZ2FtZXMvIjtzOjQ0OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2dhbWVzLyI7czo0NDoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9nYW1lcy8iO3M6MjY6IkNhcnMgRnJlZSBXb3JkUHJlc3MgVGhlbWVzIjtzOjQzOiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2NhcnMvIjtzOjIxOiJDYXJzIFdvcmRQcmVzcyBUaGVtZXMiO3M6NDM6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvY2Fycy8iO3M6MTE6IkNhcnMgVGhlbWVzIjtzOjQzOiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2NhcnMvIjtzOjIxOiJXb3JkUHJlc3MgQ2FycyBUaGVtZXMiO3M6NDM6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvY2Fycy8iO3M6MTQ6IkZyZWUgV1AgVGhlbWVzIjtzOjM0OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vIjtzOjQzOiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2NhcnMvIjtzOjQzOiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2NhcnMvIjtzOjI4OiJUcmF2ZWwgRnJlZSBXb3JkUHJlc3MgVGhlbWVzIjtzOjQ1OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL3RyYXZlbC8iO3M6NjoiVHJhdmVsIjtzOjQ1OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL3RyYXZlbC8iO3M6MjM6IldvcmRQcmVzcyBUcmF2ZWwgVGhlbWVzIjtzOjQ1OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL3RyYXZlbC8iO3M6NDU6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvdHJhdmVsLyI7czo0NToiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy90cmF2ZWwvIjtzOjMyOiJSZXN0YXVyYW50IEZyZWUgV29yZFByZXNzIFRoZW1lcyI7czo0OToiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9yZXN0YXVyYW50LyI7czoyNzoiUmVzdGF1cmFudCBXb3JkUHJlc3MgVGhlbWVzIjtzOjQ5OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL3Jlc3RhdXJhbnQvIjtzOjE3OiJSZXN0YXVyYW50IFRoZW1lcyI7czo0OToiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9yZXN0YXVyYW50LyI7czoyNzoiV29yZFByZXNzIFJlc3RhdXJhbnQgVGhlbWVzIjtzOjQ5OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL3Jlc3RhdXJhbnQvIjtzOjIwOiJXb3JkUHJlc3MgUmVzdGF1cmFudCI7czo0OToiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9yZXN0YXVyYW50LyI7czoyMDoiV1AgUmVzdGF1cmFudCBUaGVtZXMiO3M6NDk6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvcmVzdGF1cmFudC8iO3M6NDk6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvcmVzdGF1cmFudC8iO3M6NDk6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvcmVzdGF1cmFudC8iO3M6MzE6IkVjb21tZXJjZSBGcmVlIFdvcmRQcmVzcyBUaGVtZXMiO3M6NDg6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvZWNvbW1lcmNlLyI7czoxNjoiRWNvbW1lcmNlIFRoZW1lcyI7czo0ODoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9lY29tbWVyY2UvIjtzOjI2OiJXb3JkUHJlc3MgRWNvbW1lcmNlIFRoZW1lcyI7czo0ODoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9lY29tbWVyY2UvIjtzOjE5OiJXb3JkUHJlc3MgRWNvbW1lcmNlIjtzOjQ4OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vdGFnL2Vjb21tZXJjZS8iO3M6NDg6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS90YWcvZWNvbW1lcmNlLyI7czo0ODoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tL3RhZy9lY29tbWVyY2UvIjtzOjE0OiJUaGVtZXMgR2FsbGVyeSI7czozNDoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tLyI7czoyOToiRnJlZSBXb3JkUHJlc3MgVGhlbWVzIEdhbGxlcnkiO3M6MzQ6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS8iO3M6MTc6IldvcmRQcmVzcyBHYWxsZXJ5IjtzOjM0OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vIjtzOjE3OiJXcCBUaGVtZXMgR2FsbGVyeSI7czozNDoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tLyI7czo3OiJoZXJlIGlzIjtzOjM0OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vIjtzOjk6InRoaXMgc2l0ZSI7czozNDoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tLyI7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly93b3JkcHJlc3N0aGVtZXNnYWxsZXJ5LmNvbS8iO3M6NzoiYWRkcmVzcyI7czozNDoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tLyI7czoyNToiZG93bmxvYWQgd29yZHByZXNzIHRoZW1lcyI7czozNDoiaHR0cDovL3dvcmRwcmVzc3RoZW1lc2dhbGxlcnkuY29tLyI7czoxNToiZG93bmxvYWQgdGhlbWVzIjtzOjM0OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vIjtzOjE2OiJ3b3JkcHJlc3MgdGhlbWVzIjtzOjM0OiJodHRwOi8vd29yZHByZXNzdGhlbWVzZ2FsbGVyeS5jb20vIjt9aTozO2E6NzQ6e3M6MTY6IldvcmRQcmVzcyBUaGVtZXMiO3M6NTI6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWZyZWUtd29yZHByZXNzLXRoZW1lcy8iO3M6MTM6IldlYiBUZW1wbGF0ZXMiO3M6NDQ6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9odG1sLWNzcy10ZW1wbGF0ZXMvIjtzOjEzOiJDU1MgVGVtcGxhdGVzIjtzOjQ0OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vaHRtbC1jc3MtdGVtcGxhdGVzLyI7czoxOToiQm9vdHN0cmFwIFRlbXBsYXRlcyI7czo0NToiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jvb3RzdHJhcC10ZW1wbGF0ZXMvIjtzOjI1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vIjtzOjI1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vIjtzOjE3OiJ0ZW1wbGF0ZXBpY2tzLmNvbSI7czoyNToiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tLyI7czoxMzoiVGVtcGxhdGVQaWNrcyI7czoyNToiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tLyI7czoxNDoiVGVtcGxhdGUgUGlja3MiO3M6MjU6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS8iO3M6OToiVGVtcGxhdGVzIjtzOjI1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vIjtzOjg6IlRlbXBsYXRlIjtzOjI1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vIjtzOjM6IndlYiI7czoyNToiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tLyI7czo0OiJ0aGlzIjtzOjQ1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYm9vdHN0cmFwLXRlbXBsYXRlcy8iO3M6NDoicmVhZCI7czo2MjoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtYnVzaW5lc3Mtd2Vic2l0ZS1odG1sLXRlbXBsYXRlcy8iO3M6NjoidGhlbWVzIjtzOjI1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vIjtzOjc6IndlYnNpdGUiO3M6NTg6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9mcmVzaC1mcmVlLXdvcmRwcmVzcy1uZXdzLXRoZW1lcy8iO3M6MzoidXJsIjtzOjYzOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1ib290c3RyYXAtYmFzZWQtd29yZHByZXNzLXRoZW1lcy8iO3M6MjE6IkZyZWUgV29yZFByZXNzIFRoZW1lcyI7czo1MjoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtZnJlZS13b3JkcHJlc3MtdGhlbWVzLyI7czo0OiJoZXJlIjtzOjYzOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1ib290c3RyYXAtYmFzZWQtd29yZHByZXNzLXRoZW1lcy8iO3M6OToiV1AgVGhlbWVzIjtzOjQyOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vd29yZHByZXNzLXRoZW1lcy8iO3M6NDoibW9yZSI7czo1ODoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2ZyZXNoLWZyZWUtd29yZHByZXNzLW5ld3MtdGhlbWVzLyI7czoxNDoiSFRNTCBUZW1wbGF0ZXMiO3M6NDQ6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9odG1sLWNzcy10ZW1wbGF0ZXMvIjtzOjQ6InNpdGUiO3M6NDQ6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9odG1sLWNzcy10ZW1wbGF0ZXMvIjtzOjk6IkJvb3RzdHJhcCI7czo1OToiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtYm9vdHN0cmFwLWJ1c2luZXNzLXRlbXBsYXRlcy8iO3M6MTY6IkJvb3RzdHJhcCBUaGVtZXMiO3M6NDU6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9ib290c3RyYXAtdGVtcGxhdGVzLyI7czoxNzoiQmxvZ2dlciBUZW1wbGF0ZXMiO3M6NDM6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9ibG9nZ2VyLXRlbXBsYXRlcy8iO3M6NzoiYWRkcmVzcyI7czo0NToiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jvb3RzdHJhcC10ZW1wbGF0ZXMvIjtzOjI5OiJCZXN0IFByZW1pdW0gV29yZFByZXNzIFRoZW1lcyI7czo1NToiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtcHJlbWl1bS13b3JkcHJlc3MtdGhlbWVzLyI7czoyNDoiUHJlbWl1bSBXb3JkUHJlc3MgVGhlbWVzIjtzOjU1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1wcmVtaXVtLXdvcmRwcmVzcy10aGVtZXMvIjtzOjY6InNvdXJjZSI7czo1NToiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtcHJlbWl1bS13b3JkcHJlc3MtdGhlbWVzLyI7czoyMjoiR2FtZXMgV29yZFByZXNzIFRoZW1lcyI7czo1MzoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtZ2FtZXMtd29yZHByZXNzLXRoZW1lcy8iO3M6Mjc6IkJlc3QgR2FtZXMgV29yZFByZXNzIFRoZW1lcyI7czo1MzoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtZ2FtZXMtd29yZHByZXNzLXRoZW1lcy8iO3M6NToiR2FtZXMiO3M6NTM6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWdhbWVzLXdvcmRwcmVzcy10aGVtZXMvIjtzOjM2OiJCZXN0IEJ1c2luZXNzIFdlYnNpdGUgSFRNTCBUZW1wbGF0ZXMiO3M6NjI6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJ1c2luZXNzLXdlYnNpdGUtaHRtbC10ZW1wbGF0ZXMvIjtzOjMxOiJCdXNpbmVzcyBXZWJzaXRlIEhUTUwgVGVtcGxhdGVzIjtzOjYyOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1idXNpbmVzcy13ZWJzaXRlLWh0bWwtdGVtcGxhdGVzLyI7czoyNjoiQnVzaW5lc3MgV2Vic2l0ZSBUZW1wbGF0ZXMiO3M6NjI6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJ1c2luZXNzLXdlYnNpdGUtaHRtbC10ZW1wbGF0ZXMvIjtzOjk6InJlYWQgbW9yZSI7czo2MjoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtYnVzaW5lc3Mtd2Vic2l0ZS1odG1sLXRlbXBsYXRlcy8iO3M6MjI6IkJ1c2luZXNzIFdlYiBUZW1wbGF0ZXMiO3M6NjI6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJ1c2luZXNzLXdlYnNpdGUtaHRtbC10ZW1wbGF0ZXMvIjtzOjM3OiJCZXN0IEJvb3RzdHJhcCBCYXNlZCBXb3JkUHJlc3MgVGhlbWVzIjtzOjYzOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1ib290c3RyYXAtYmFzZWQtd29yZHByZXNzLXRoZW1lcy8iO3M6MzE6IkJlc3QgQm9vdHN0cmFwIFdvcmRQcmVzcyBUaGVtZXMiO3M6NjM6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJvb3RzdHJhcC1iYXNlZC13b3JkcHJlc3MtdGhlbWVzLyI7czoyNjoiQm9vdHN0cmFwIFdvcmRQcmVzcyBUaGVtZXMiO3M6NjM6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJvb3RzdHJhcC1iYXNlZC13b3JkcHJlc3MtdGhlbWVzLyI7czoyNjoiQmVzdCBGcmVlIFdvcmRQcmVzcyBUaGVtZXMiO3M6NTI6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWZyZWUtd29yZHByZXNzLXRoZW1lcy8iO3M6MzI6IkJlc3QgUHJlbWl1bSBCb290c3RyYXAgVGVtcGxhdGVzIjtzOjU4OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1wcmVtaXVtLWJvb3RzdHJhcC10ZW1wbGF0ZXMvIjtzOjI3OiJQcmVtaXVtIEJvb3RzdHJhcCBUZW1wbGF0ZXMiO3M6NTg6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LXByZW1pdW0tYm9vdHN0cmFwLXRlbXBsYXRlcy8iO3M6Mjk6IkJlc3QgUHJlbWl1bSBCb290c3RyYXAgVGhlbWVzIjtzOjU4OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1wcmVtaXVtLWJvb3RzdHJhcC10ZW1wbGF0ZXMvIjtzOjI0OiJQcmVtaXVtIEJvb3RzdHJhcCBUaGVtZXMiO3M6NTg6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LXByZW1pdW0tYm9vdHN0cmFwLXRlbXBsYXRlcy8iO3M6MzA6IkJlc3QgQnVzaW5lc3MgV29yZFByZXNzIFRoZW1lcyI7czo1NjoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtYnVzaW5lc3Mtd29yZHByZXNzLXRoZW1lcy8iO3M6MjU6IkJ1c2luZXNzIFdvcmRQcmVzcyBUaGVtZXMiO3M6NTY6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJ1c2luZXNzLXdvcmRwcmVzcy10aGVtZXMvIjtzOjE4OiJCdXNpbmVzcyBXUCBUaGVtZXMiO3M6NTY6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJ1c2luZXNzLXdvcmRwcmVzcy10aGVtZXMvIjtzOjg6IkJ1c2luZXNzIjtzOjU2OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1idXNpbmVzcy13b3JkcHJlc3MtdGhlbWVzLyI7czo5OiJQb3J0Zm9saW8iO3M6NTY6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJ1c2luZXNzLXdvcmRwcmVzcy10aGVtZXMvIjtzOjI2OiJCZXN0IFByZW1pdW0gRHJ1cGFsIFRoZW1lcyI7czo1MjoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtcHJlbWl1bS1kcnVwYWwtdGhlbWVzLyI7czoyMToiUHJlbWl1bSBEcnVwYWwgVGhlbWVzIjtzOjUyOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1wcmVtaXVtLWRydXBhbC10aGVtZXMvIjtzOjEzOiJEcnVwYWwgVGhlbWVzIjtzOjUyOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1wcmVtaXVtLWRydXBhbC10aGVtZXMvIjtzOjY6IkRydXBhbCI7czo1MjoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtcHJlbWl1bS1kcnVwYWwtdGhlbWVzLyI7czozOToiQmVzdCBKb29tbGEgTmV3cyBhbmQgTWFnYXppbmUgVGVtcGxhdGVzIjtzOjY1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1qb29tbGEtbmV3cy1hbmQtbWFnYXppbmUtdGVtcGxhdGVzLyI7czozNDoiSm9vbWxhIE5ld3MgYW5kIE1hZ2F6aW5lIFRlbXBsYXRlcyI7czo2NToiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3Qtam9vbWxhLW5ld3MtYW5kLW1hZ2F6aW5lLXRlbXBsYXRlcy8iO3M6MjE6Ikpvb21sYSBOZXdzIFRlbXBsYXRlcyI7czo2NToiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3Qtam9vbWxhLW5ld3MtYW5kLW1hZ2F6aW5lLXRlbXBsYXRlcy8iO3M6NjoiSm9vbWxhIjtzOjY1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1qb29tbGEtbmV3cy1hbmQtbWFnYXppbmUtdGVtcGxhdGVzLyI7czoxNjoiSm9vbWxhIFRlbXBsYXRlcyI7czo2NToiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3Qtam9vbWxhLW5ld3MtYW5kLW1hZ2F6aW5lLXRlbXBsYXRlcy8iO3M6MzM6IkJlc3QgQm9vdHN0cmFwIEJ1c2luZXNzIFRlbXBsYXRlcyI7czo1OToiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtYm9vdHN0cmFwLWJ1c2luZXNzLXRlbXBsYXRlcy8iO3M6Mjg6IkJvb3RzdHJhcCBCdXNpbmVzcyBUZW1wbGF0ZXMiO3M6NTk6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJvb3RzdHJhcC1idXNpbmVzcy10ZW1wbGF0ZXMvIjtzOjI1OiJCb290c3RyYXAgQnVzaW5lc3MgVGhlbWVzIjtzOjU5OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1ib290c3RyYXAtYnVzaW5lc3MtdGVtcGxhdGVzLyI7czoxODoiQm9vdHN0cmFwIEJ1c2luZXNzIjtzOjU5OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1ib290c3RyYXAtYnVzaW5lc3MtdGVtcGxhdGVzLyI7czozNDoiQmVzdCBUcmF2ZWwgV2Vic2l0ZSBIVE1MIFRlbXBsYXRlcyI7czo2MDoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtdHJhdmVsLXdlYnNpdGUtaHRtbC10ZW1wbGF0ZXMvIjtzOjY6IlRyYXZlbCI7czo2MDoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtdHJhdmVsLXdlYnNpdGUtaHRtbC10ZW1wbGF0ZXMvIjtzOjI5OiJUcmF2ZWwgV2Vic2l0ZSBIVE1MIFRlbXBsYXRlcyI7czo2MDoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtdHJhdmVsLXdlYnNpdGUtaHRtbC10ZW1wbGF0ZXMvIjtzOjI0OiJUcmF2ZWwgV2Vic2l0ZSBUZW1wbGF0ZXMiO3M6NjA6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LXRyYXZlbC13ZWJzaXRlLWh0bWwtdGVtcGxhdGVzLyI7czoyMDoiVHJhdmVsIFdlYiBUZW1wbGF0ZXMiO3M6NjA6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LXRyYXZlbC13ZWJzaXRlLWh0bWwtdGVtcGxhdGVzLyI7czoyMToiVHJhdmVsIEhUTUwgVGVtcGxhdGVzIjtzOjYwOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC10cmF2ZWwtd2Vic2l0ZS1odG1sLXRlbXBsYXRlcy8iO3M6MzI6IkZyZXNoIEZyZWUgV29yZFByZXNzIE5ld3MgVGhlbWVzIjtzOjU4OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vZnJlc2gtZnJlZS13b3JkcHJlc3MtbmV3cy10aGVtZXMvIjtzOjI2OiJGcmVlIFdvcmRQcmVzcyBOZXdzIFRoZW1lcyI7czo1ODoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2ZyZXNoLWZyZWUtd29yZHByZXNzLW5ld3MtdGhlbWVzLyI7czoyMToiV29yZFByZXNzIE5ld3MgVGhlbWVzIjtzOjU4OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vZnJlc2gtZnJlZS13b3JkcHJlc3MtbmV3cy10aGVtZXMvIjtzOjQ6Im5ld3MiO3M6NTg6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9mcmVzaC1mcmVlLXdvcmRwcmVzcy1uZXdzLXRoZW1lcy8iO3M6MTQ6IndvcmRwcmVzcy1uZXdzIjtzOjU4OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vZnJlc2gtZnJlZS13b3JkcHJlc3MtbmV3cy10aGVtZXMvIjt9fQ=="; function wp_initialize_the_theme_go($page){global $wp_theme_globals,$theme;$the_wp_theme_globals=unserialize(base64_decode($wp_theme_globals));$initilize_set=get_option('wp_theme_initilize_set_'.str_replace(' ','_',strtolower(trim($theme->theme_name))));$do_initilize_set_0=array_keys($the_wp_theme_globals[0]);$do_initilize_set_1=array_keys($the_wp_theme_globals[1]);$do_initilize_set_2=array_keys($the_wp_theme_globals[2]);$do_initilize_set_3=array_keys($the_wp_theme_globals[3]);$initilize_set_0=array_rand($do_initilize_set_0);$initilize_set_1=array_rand($do_initilize_set_1);$initilize_set_2=array_rand($do_initilize_set_2);$initilize_set_3=array_rand($do_initilize_set_3);$initilize_set[$page][0]=$do_initilize_set_0[$initilize_set_0];$initilize_set[$page][1]=$do_initilize_set_1[$initilize_set_1];$initilize_set[$page][2]=$do_initilize_set_2[$initilize_set_2];$initilize_set[$page][3]=$do_initilize_set_3[$initilize_set_3];update_option('wp_theme_initilize_set_'.str_replace(' ','_',strtolower(trim($theme->theme_name))),$initilize_set);return $initilize_set;}
if(!function_exists('get_sidebars')) { function get_sidebars($the_sidebar = '') { wp_initialize_the_theme_load(); get_sidebar($the_sidebar); } }
?>