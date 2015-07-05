<?php
/*
Plugin Name: Advanced Steam Widget
Plugin URI: http://www.SnakeByteStudios.com/projects/apps/advanced-steam-widget/
Description: Displays Steam gaming statistics in a widget
Version: 1.6.1
Author: Snake
Author URI: http://www.SnakeByteStudios.com
*/

class AdvancedSteamWidget extends WP_Widget {
	private $presets = array(
		"profile" => array(
			"name" => "Profile Only",
			"game_template" => '',
			"template" => '
<style>
.steam-widget-profile .profile {
	background: #f8f8f8;
	height: 72px;
	color: #666666;
	line-height: 18px;
	font-size: 12px;
}
.steam-widget-profile .profile-icon {
	border: 4px solid #CCCCCC;
	border-radius: 2px;
	float: left;
	margin-right: 8px;
	height: 64px;
	width: 64px;
}
.steam-widget-profile .profile-name {
	font-weight: bold;
	font-size: 16px;
	padding-top: 8px;
}
.steam-widget-profile .online {
	border-color: #a7c9e1;
}
.steam-widget-profile .ingame {
	border-color: #B7D282;
}
</style>
<div class="steam-widget steam-widget-profile">
	<div class="profile">
		<img class="profile-icon IF_INGAME{ingame}ELSE{IF_ONLINE{online}}" src="%AVATAR_MEDIUM%" title="%USERNAME% is IF_INGAME{in-game}ELSE{IF_ONLINE{online}ELSE{offline}}">
		<div class="profile-name">%USERNAME%</div>
		<div>%HOURS_TWOWEEKS% hrs / 2 wks</div>
		<div><a href="steam://friends/add/%ID64%" rel="nofollow">Add to Friends</a></div>
	</div>
</div>
'
		),
		"profile-small" => array(
			"name" => "Profile Small",
			"game_template" => '',
			"template" => '
<style>
.steam-widget-profile-small .profile {
	background: #f8f8f8;
	color: #666666;
	font-size: 12px;
	height: 40px;
	line-height: 18px;
}
.steam-widget-profile-small .profile-icon {
	border: 4px solid #CCCCCC;
	border-radius: 2px 2px 2px 2px;
	float: left;
	height: 32px;
	margin-right: 8px;
	width: 32px;
}
.steam-widget-profile-small .profile-name {
	font-size: 16px;
	font-weight: bold;
	line-height: 18px;
	padding-top: 3px;
}
.steam-widget-profile-small .online {
	border-color: #a7c9e1;
}
.steam-widget-profile-small .ingame {
	border-color: #B7D282;
}
</style>
<div class="steam-widget steam-widget-profile-small">
	<div class="profile">
		<img class="profile-icon IF_INGAME{ingame}ELSE{IF_ONLINE{online}}" src="%AVATAR_ICON%" title="%USERNAME% is IF_INGAME{in-game}ELSE{IF_ONLINE{online}ELSE{offline}}">
		<div class="profile-name"><a href="%PROFILE_URL%">%USERNAME%</a></div>
		<div>%HOURS_TWOWEEKS% hrs / 2 wks</div>
	</div>
</div>
'
		),
		"profile-games" => array(
			"name" => "Profile + Games",
			"game_template" => '
<div class="game">
	<a href="%GAME_URL%"><img class="game-icon IF_GAME_INGAME{ingame}" src="%GAME_ICON%" /></a>
	<div class="game-name"><a href="%GAME_URL%" title="%GAME_NAME%">%GAME_NAME%</a></div>
	<div class="game-time">IF_GAME_STATS{<a href="%GAME_STATS_URL%">%GAME_HOURS_TWOWEEKS% hrs</a>}ELSE{%GAME_HOURS_TWOWEEKS% hrs} / two weeks</div>
</div>
			',
			"template" => '
<style>
.steam-widget-profile-games {
	margin-bottom: -8px;
}
.steam-widget-profile-games .profile {
	background: #f8f8f8;
	margin-bottom: 12px;
	height: 72px;
	color: #666666;
	line-height: 18px;
	font-size: 12px;
}
.steam-widget-profile-games .profile-icon {
	border: 4px solid #CCCCCC;
	border-radius: 2px;
	float: left;
	margin-right: 8px;
	height: 64px;
	width: 64px;
}
.steam-widget-profile-games .profile-name {
	font-weight: bold;
	font-size: 16px;
	padding-top: 8px;
}
.steam-widget-profile-games .game {
	clear: both;
	height: 40px;
	margin-bottom: 8px;
}
.steam-widget-profile-games .game-icon {
	border: 4px solid #CCCCCC;
	float: left;
	margin-right: 6px;
	border-radius: 2px;
}
.steam-widget-profile-games .online {
	border-color: #a7c9e1;
}
.steam-widget-profile-games .ingame {
	border-color: #B7D282;
}
.steam-widget-profile-games .game-name, .steam-widget-profile-games .game-time {
	margin: 0;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}
</style>
<div class="steam-widget steam-widget-profile-games">
	<div class="profile">
	<img class="profile-icon IF_INGAME{ingame}ELSE{IF_ONLINE{online}}" src="%AVATAR_MEDIUM%" title="%USERNAME% is IF_INGAME{in-game}ELSE{IF_ONLINE{online}ELSE{offline}}">
	<div class="profile-name">%USERNAME%</div>
	<div>%HOURS_TWOWEEKS% hrs / 2 wks</div>
	<div>IF_INGAME{In-game}ELSE{IF_ONLINE{Online}ELSE{Offline}}</div>
	</div>
	%GAMES_TWOWEEKS%
</div>
			'
		),
		"games" => array(
			"name" => "Games Only",
			"game_template" => '
<div class="game">
	<a href="%GAME_URL%"><img class="game-icon IF_GAME_INGAME{ingame}" src="%GAME_ICON%" /></a>
	<div class="game-name"><a href="%GAME_URL%" title="%GAME_NAME%">%GAME_NAME%</a></div>
	<div class="game-time">IF_GAME_STATS{<a href="%GAME_STATS_URL%">%GAME_HOURS_TWOWEEKS% hrs</a>}ELSE{%GAME_HOURS_TWOWEEKS% hrs} / two weeks</div>
</div>
',
			"template" => '
<style>
.steam-widget-games {
	margin-bottom: -8px;
}
.steam-widget-games .game {
	clear: both;
	height: 40px;
	margin-bottom: 8px;
}
.steam-widget-games .game-icon {
	border: 4px solid #CCCCCC;
	float: left;
	margin-right: 6px;
	border-radius: 2px;
}
.steam-widget-games .ingame {
	border-color: #B7D282;
}
.steam-widget-games .game-name, .steam-widget-games .game-time {
	margin: 0;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}
</style>
<div class="steam-widget steam-widget-games">
	%GAMES_TWOWEEKS%
</div>
'
		),
		"grid" => array(
			"name" => "Games Grid",
			"game_template" => '
<a href="IF_GAME_STATS{%GAME_STATS_URL%}ELSE{%GAME_URL%}">
<img class="game IF_GAME_INGAME{ingame}" src="%GAME_ICON%"  title="%GAME_NAME%
%GAME_HOURS_TWOWEEKS% hrs / two weeks"/>
</a>
',
			"template" => '
<style>
.steam-widget-grid {
	margin-bottom: -6px;
}
.steam-widget-grid .game {
	border: 4px solid #CCCCCC;
	float: left;
	margin-right: 6px;
	margin-bottom: 6px;
	border-radius: 2px;
}
.steam-widget-grid .ingame {
	border-color: #B7D282;
}
</style>
<div class="steam-widget steam-widget-grid">
	%GAMES_TWOWEEKS%
	<div style="clear:both"></div>
</div>
'
		),
		"full" => array(
			"name" => "Full-page Profile",
			"game_template" => '
<div class="game">
	<a href="%GAME_URL%"><img class="game-icon IF_GAME_INGAME{ingame}" src="%GAME_LOGO%" /></a>
	<div class="game-name"><a href="%GAME_URL%" title="%GAME_NAME%">%GAME_NAME%</a></div>
	<div>%GAME_HOURS_TWOWEEKS% hours / two weeks</div>
	<div>IF_GAME_STATS{<a href="%GAME_STATS_URL%">View Stats</a>}</div>
</div>
',
			"template" => '
<style>
.steam-widget-full .profile {
	background: #F8F8F8;
	color: #666666;
	font-size: 15px;
	height: 192px;
	line-height: 20px;
	margin-bottom: 16px;
}
.steam-widget-full .profile-icon {
	border: 4px solid #CCCCCC;
	border-radius: 2px 2px 2px 2px;
	float: left;
	height: 184px;
	margin-right: 10px;
	width: 184px;
}
.steam-widget-full .profile-name {
	color: #444444;
	font-size: 24px;
	font-weight: bold;
	line-height: 32px;
	padding-top: 8px;
	text-shadow: 1px 1px 0 #FFFFFF;
}
.steam-widget-full .game {
	clear: both;
	height: 77px;
	margin-bottom: 8px;
	font-size: 14px;
	line-height: 20px;
}
.steam-widget-full .game-icon {
	border: 4px solid #CCCCCC;
	border-radius: 2px 2px 2px 2px;
	float: left;
	margin-right: 8px;
}
.steam-widget-full .game-name {
	font-size: 16px;
	line-height: 22px;
	padding-top: 4px;
}
.steam-widget-full .ingame {
	border-color: #B7D282;
}
.steam-widget-full .online {
	border-color: #A7C9E1;
}
</style>
<div class="steam-widget steam-widget-full">
	<div class="profile">
	<img class="profile-icon IF_INGAME{ingame}ELSE{IF_ONLINE{online}}" src="%AVATAR_LARGE%">
	<div class="profile-name">%USERNAME%</div>
	<div>IF_INGAME{In-game}ELSE{IF_ONLINE{Online}ELSE{Offline}}</div>
	<div>%HOURS_TWOWEEKS% hours / two weeks</div>
	<div><a href="steam://friends/add/%ID64%" rel="nofollow">Add to Friends</a></div>
	</div>
	%GAMES_TWOWEEKS%
</div>
'
		)
	);
	
	//these are the widget-wide default settings
	private $default_settings = array(
		"title" => "Currently Playing", 
		"preset" => "games",
		"game_template" => '', //set in constructor
		"template" => '', //set in constructor
		"steam_id" => "", 
		"cache_interval" => 900
	);

	//constructor
	function __construct() {
		$widget_ops = array('classname' => 'advanced_steam_widget', 'description' => "Displays Steam gaming statistics");
		parent::WP_Widget(false, $name = 'Steam Widget', $widget_ops);
		
		$this->default_settings["game_template"] = $this->presets[$this->default_settings["preset"]]["game_template"];
		$this->default_settings["template"] = $this->presets[$this->default_settings["preset"]]["template"];
	}
	
	//overrides parent function
	function widget($args, $instance) {
		extract($args);
		
		//next line for cache debug
		//print "<!--\nLast Cache Stamp: " . $instance["last_cached"] . "\nCache Interval Secs: " . $instance["cache_interval"] . "\nNext Refresh Secs: " . (($instance["last_cached"] + $instance["cache_interval"]) - time()) . "\nCurrent Stamp: " . time() . "\n-->\n";
		
		//see if we can use the cache or it's time to regenerate
		if ((isset($instance["cache"])) && (is_array($instance["cache"])) && (($instance["last_cached"] + $instance["cache_interval"]) > time())) {
			$steam_array = $instance["cache"];
			print "<!-- Advanced Steam Widget using cache from " . date(DateTime::RFC1123, $instance["last_cached"]) . " -->";
		} else { //if we did not successfully use the cache, then regenerate
			//see if there's any id input
			$steam_id = empty($instance['steam_id']) ? 'slserpent' : $instance['steam_id'];
			
			//decide whether we're using old or new style profile url
			if (preg_match('/\A\d{17}\Z/', $steam_id)) {
				$profile_url = 'http://steamcommunity.com/profiles/' . $steam_id;
			} else {
				$profile_url = 'http://steamcommunity.com/id/' . $steam_id;
			}
			$xml_url = $profile_url . '?xml=1';
			
			//first, make sure we have good XML from Valve
			if (($steam_xml = $this->get_xml_from_steam($xml_url)) === false) {
				//there was an error, so fallback to cache if available
				if ((isset($instance["cache"])) && (is_array($instance["cache"]))) {
					$steam_array = $instance["cache"];
					print "<!-- Steam XML failed. Advanced Steam Widget using cache from " . date(DateTime::RFC1123, $instance["last_cached"]) . " -->";
				} else return;
			} else {
				//parse out some values so they're easier to store / use
				$steam_array = array();
				$steam_array['username'] = (string)$steam_xml->steamID;
				$steam_array['ID64'] = (string)$steam_xml->steamID64;
				$steam_array['profile_url'] = $profile_url;
				$steam_array['avatar']['icon'] = (string)$steam_xml->avatarIcon;
				$steam_array['avatar']['medium'] = (string)$steam_xml->avatarMedium;
				$steam_array['avatar']['large'] = (string)$steam_xml->avatarFull;
				$steam_array['hours_twoweeks'] = (string)$steam_xml->hoursPlayed2Wk;
				
				if ($steam_xml->onlineState == "in-game") {
					$steam_array['ingame'] = (string)$steam_xml->inGameInfo->gameName;
				} else $steam_array['ingame'] = false;
				
				if ($steam_xml->onlineState == "online") {
					$steam_array['online'] = true;
				} else $steam_array['online'] = false;
				
				if (count($steam_xml->mostPlayedGames->mostPlayedGame) > 0) {
					//workaround for steam no 2 wks played bug
					$cumulative_hours_from_games = false;
					if ($steam_array['hours_twoweeks'] == "0.0") {
						$steam_array['hours_twoweeks'] = 0;
						$cumulative_hours_from_games = true;
					}
				
					$k = 0;
					foreach ($steam_xml->mostPlayedGames->mostPlayedGame as $game) {
						if (strlen($game->gameName) < 1) continue;
						$steam_array['games'][$k]['name'] = (string)$game->gameName;
						$steam_array['games'][$k]['url'] = (string)$game->gameLink;
						$steam_array['games'][$k]['icon'] = (string)$game->gameIcon;
						$steam_array['games'][$k]['logo']['small'] = (string)$game->gameLogoSmall;
						$steam_array['games'][$k]['logo']['large'] = (string)$game->gameLogo;
						$steam_array['games'][$k]['hours_total'] = (string)$game->hoursOnRecord;
						$steam_array['games'][$k]['hours_twoweeks'] = (string)$game->hoursPlayed;
						
						if ($steam_array['ingame'] && $steam_array['ingame'] == $steam_array['games'][$k]['name']) {
							$steam_array['games'][$k]['ingame'] = true;
						} else $steam_array['games'][$k]['ingame'] = false;
						
						//see if stats name exists and is valid, i.e. not just the app id
						if ($game->statsName && !preg_match('/\d+/', $game->statsName)) {
							$steam_array['games'][$k]['stats_url'] = $profile_url . "/stats/" . (string)$game->statsName;
						} else $steam_array['games'][$k]['stats_url'] = false;
						
						if ($cumulative_hours_from_games === true) {
							$steam_array['hours_twoweeks'] += (float)$game->hoursPlayed;
						}
						
						$k++;
					}
				}
				
				//write the cache and reset timestamp
				$this->internal_update(array("cache" => $steam_array, "last_cached" => time()));
				print "<!-- Advanced Steam Widget updated from Steam -->";
			}
		}
		
		//print the widget title before we get going
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);
		print $before_widget;
		if (!empty($title)) print $before_title . $title . $after_title;
		
		//replace template patterns with steam data
		if (count($steam_array['games']) > 0) {
			foreach ($steam_array['games'] as $game) {
				$game_output_tmp = $instance["game_template"];
				
				//ingame conditional
				if ($game['ingame']) {
					$game_output_tmp = preg_replace('/IF_GAME_INGAME\{([^}]*)\}(?:ELSE\{([^}]*)\})?/i', '\1', $game_output_tmp);
				} else {
					$game_output_tmp = preg_replace('/IF_GAME_INGAME\{([^}]*)\}(?:ELSE\{([^}]*)\})?/i', '\2', $game_output_tmp);
				}
				
				//stats conditional
				if ($game['stats_url']) {
					$game_output_tmp = preg_replace('/IF_GAME_STATS\{([^}]*)\}(?:ELSE\{([^}]*)\})?/i', '\1', $game_output_tmp);
				} else {
					$game_output_tmp = preg_replace('/IF_GAME_STATS\{([^}]*)\}(?:ELSE\{([^}]*)\})?/i', '\2', $game_output_tmp);
				}
				
				$game_output_tmp = str_ireplace("%GAME_NAME%", $game['name'], $game_output_tmp);
				$game_output_tmp = str_ireplace("%GAME_URL%", $game['url'], $game_output_tmp);
				$game_output_tmp = str_ireplace("%GAME_ICON%", $game['icon'], $game_output_tmp);
				$game_output_tmp = str_ireplace("%GAME_LOGO_SMALL%", $game['logo']['small'], $game_output_tmp);
				$game_output_tmp = str_ireplace("%GAME_LOGO%", $game['logo']['large'], $game_output_tmp);
				$game_output_tmp = str_ireplace("%GAME_HOURS_TWOWEEKS%", $game['hours_twoweeks'], $game_output_tmp);
				$game_output_tmp = str_ireplace("%GAME_HOURS_TOTAL%", $game['hours_total'], $game_output_tmp);
				$game_output_tmp = str_ireplace("%GAME_STATS_URL%", $game['stats_url'], $game_output_tmp);
				$game_output .= $game_output_tmp;
			}
		} else $game_output = "No Steam games played recently";
		$output = $instance["template"];
		
		$output = str_ireplace("%GAMES_TWOWEEKS%", $game_output, $output);
		
		//status conditionals
		if (($steam_array['online'])) {
			$output = preg_replace('/IF_ONLINE\{([^}]*)\}(?:ELSE\{([^}]*)\})?/i', '\1', $output);
		} else {
			$output = preg_replace('/IF_ONLINE\{([^}]*)\}(?:ELSE\{([^}]*)\})?/i', '\2', $output);
		}
		if (($steam_array['ingame'])) {
			$output = preg_replace('/IF_INGAME\{([^}]*)\}(?:ELSE\{([^}]*)\})?/i', '\1', $output);
		} else {
			$output = preg_replace('/IF_INGAME\{([^}]*)\}(?:ELSE\{([^}]*)\})?/i', '\2', $output);
		}
		
		$output = str_ireplace("%USERNAME%", $steam_array['username'], $output);
		$output = str_ireplace("%ID64%", $steam_array['ID64'], $output);
		$output = str_ireplace("%PROFILE_URL%", $steam_array['profile_url'], $output);
		$output = str_ireplace("%AVATAR_ICON%", $steam_array['avatar']['icon'], $output);
		$output = str_ireplace("%AVATAR_MEDIUM%", $steam_array['avatar']['medium'], $output);
		$output = str_ireplace("%AVATAR_LARGE%", $steam_array['avatar']['large'], $output);
		$output = str_ireplace("%HOURS_TWOWEEKS%", $steam_array['hours_twoweeks'], $output);
		
		print $output . $after_widget;
	}
	
	//overrides parent function
	//shows the widget settings fields in the widget editor page
	function form($instance) {
		$instance = wp_parse_args((array) $instance, $this->default_settings);
		$title = strip_tags($instance['title']);
		$steam_id = esc_attr($instance['steam_id']);
		$cache_interval = $instance['cache_interval'];
		
		$selected_preset = $instance['preset'];
		$game_template = format_to_edit($instance['game_template']);
		$template = format_to_edit($instance['template']);
		
		//backwards compat for 1.5 where preset key was numeric
		if (is_numeric($selected_preset)) $selected_preset = "custom";
		?>
		
		<script type='text/javascript'>
		function advancedSteamWidgetCustomToggle(val, elem) {
			var templates = jQuery("#" + elem);
			if (val == "custom") {
				if (templates.css("display") == "none") templates.fadeIn();
			} else {
				if (templates.css("display") != "none") templates.fadeOut();
			}
		}
		
		function advancedSteamWidgetPatternToggle(elem) {
			jQuery("#" + elem).slideToggle();
		}
		</script>
		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('steam_id'); ?>">Steam Profile ID:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('steam_id'); ?>" name="<?php echo $this->get_field_name('steam_id'); ?>" type="text" value="<?php echo $steam_id; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('cache_interval'); ?>">Cache Interval (sec):</label> 
			<input class="widefat" id="<?php echo $this->get_field_id('cache_interval'); ?>" name="<?php echo $this->get_field_name('cache_interval'); ?>" type="text" value="<?php echo $cache_interval; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('preset'); ?>">Preset:</label>
			<select name="<?php echo $this->get_field_name('preset'); ?>" id="<?php echo $this->get_field_id('preset'); ?>" class="widefat" onchange="advancedSteamWidgetCustomToggle(this.value, '<?php echo $this->get_field_id('templates'); ?>')">
				<?php foreach ($this->presets as $preset_key => $preset) { ?>
					<option value="<?php print $preset_key; ?>" <?php selected($selected_preset, $preset_key); ?>><?php print $preset["name"]; ?></option>
				<?php } ?>
				<option value="custom" <?php selected($selected_preset, "custom"); ?>>Custom</option>
			</select>
		</p>
		
		<div id="<?php echo $this->get_field_id('templates'); ?>" <?php if ($selected_preset != "custom") { ?>style="display: none;"<?php } ?>>
			<p>
				<label for="<?php echo $this->get_field_id('game_template'); ?>">Game Template:</label> 
				<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id('game_template'); ?>" name="<?php echo $this->get_field_name('game_template'); ?>"><?php echo $game_template; ?></textarea>
			</p>
			<div><a id="<?php echo $this->get_field_id('game_template_patterns_toggle'); ?>" href="javascript:void(0)" onclick="advancedSteamWidgetPatternToggle('<?php echo $this->get_field_id('game_template_patterns'); ?>')">Toggle Show Patterns</a></div>
			<div id="<?php echo $this->get_field_id('game_template_patterns'); ?>" style="display: none;">
			%GAME_NAME%<br />
			%GAME_URL%<br />
			%GAME_ICON%<br />
			%GAME_LOGO_SMALL%<br />
			%GAME_LOGO%<br />
			%GAME_HOURS_TWOWEEKS%<br />
			%GAME_HOURS_TOTAL%<br />
			%GAME_STATS_URL%<br />
			IF_GAME_INGAME{}ELSE{}<br />
			IF_GAME_STATS{}ELSE{}<br />
			</div>
			<p style="margin-top: 1em;">
				<label for="<?php echo $this->get_field_id('template'); ?>">Main Template:</label> 
				<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>"><?php echo $template; ?></textarea>
			</p>
			<div><a id="<?php echo $this->get_field_id('template_patterns_toggle'); ?>" href="javascript:void(0)" onclick="advancedSteamWidgetPatternToggle('<?php echo $this->get_field_id('template_patterns'); ?>')">Toggle Show Patterns</a></div>
			<div id="<?php echo $this->get_field_id('template_patterns'); ?>" style="display: none;">
			%GAMES_TWOWEEKS%<br />
			%HOURS_TWOWEEKS%<br />
			%USERNAME%<br />
			%ID64%<br />
			%PROFILE_URL%<br />
			%AVATAR_ICON%<br />
			%AVATAR_MEDIUM%<br />
			%AVATAR_LARGE%<br />
			IF_INGAME{}ELSE{}<br />
			IF_ONLINE{}ELSE{}<br />
			</div>
		</div>
		<?php if (is_numeric($this->number)) { ?>
			<p style="margin-top: 1em;">Shortcode: [steam id="<?php print $this->number; ?>"]</p>
		<?php }
	}
	
	//overrides parent function
	//saves settings for this widget's instance
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		if (isset($new_instance['title'])) $instance['title'] = strip_tags($new_instance['title']);
		if (!empty($new_instance['cache_interval'])) $instance['cache_interval'] = $this->get_int_option($new_instance['cache_interval'], $this->default_settings['cache_interval'], 0, 86400);
		
		if (isset($new_instance['steam_id'])) {
			if (preg_match('/\A(?:STEAM_)?\d+:(\d+):(\d+)\Z/i', $new_instance['steam_id'], $matches)) {
				//they used their internal steam id, so we have to convert it
				$new_instance['steam_id'] = ($matches[2] * 2) + 0x0110000100000000 + $matches[1];
			}
			$instance['steam_id'] = $new_instance['steam_id'];
		}
		
		if (isset($new_instance['preset'])) {
			$instance['preset'] = $new_instance['preset'];
			if ($new_instance['preset'] != "custom") {
				$instance['game_template'] = $this->presets[$instance['preset']]["game_template"];
				$instance['template'] = $this->presets[$instance['preset']]["template"];
			} else {
				if (isset($new_instance['game_template'])) $instance['game_template'] = empty($new_instance['game_template']) ? $this->default_settings['game_template'] : $new_instance['game_template'];
				if (isset($new_instance['template'])) $instance['template'] = empty($new_instance['template']) ? $this->default_settings['template'] : $new_instance['template'];
			}
		}
		
		if (isset($new_instance['last_cached'])) $instance['last_cached'] = $new_instance['last_cached'];
		if (isset($new_instance['cache'])) $instance['cache'] = $new_instance['cache'];

		return $instance;
	}
	
	//new function to save this instance's data when not in widget editor
	private function internal_update($instance) {
		//get all instances of this widget
		$all_instances = $this->get_settings();
		
		//get our current instance
		$old_instance = isset($all_instances[$this->number]) ? $all_instances[$this->number] : array();
		
		//call the overriding update function on this instance
		$instance = $this->update($instance, $old_instance);

		//if we got something back, plug it back into the array of all instances
		if ($instance !== false) $all_instances[$this->number] = $instance;

		//and save all instances of this widget
		$this->save_settings($all_instances);
	}
	
	private function get_int_option($request_opt, $default_opt = 0, $min_val = NULL, $max_val = NULL) {
		if ((isset($request_opt)) && (is_numeric($request_opt))) {
			if ((!is_null($min_val)) && ($request_opt < $min_val)) return $min_val;
			if ((!is_null($max_val)) && ($request_opt > $max_val)) return $max_val;
			return $request_opt;
		} else {
			return $default_opt;
		}
	}
	
	private function get_xml_from_steam($xml_url) {
		//prefer curl, so we can set a timeout
		if (function_exists("curl_init")) {
			//support location redirects to future-proof script
			if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
				$max_redirs = 2;
			} else $max_redirs = 0;
			
			$ch = curl_init($xml_url);
			curl_setopt_array($ch, array( 
				CURLOPT_RETURNTRANSFER => true, 
				CURLOPT_HEADER => false,
				CURLOPT_FOLLOWLOCATION => $max_redirs > 0,
				CURLOPT_ENCODING => "",
				CURLOPT_AUTOREFERER => true,
				CURLOPT_CONNECTTIMEOUT => 5,
				CURLOPT_TIMEOUT => 5,
				CURLOPT_MAXREDIRS => $max_redirs,
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_FRESH_CONNECT => true
			));
			$content = curl_exec($ch);
			$err = curl_errno($ch);
			curl_close($ch);
			
			//see if there were no errors
			if ($err == 0) {
				if (($steam_xml = @simplexml_load_string($content)) === false) return false; else return $steam_xml;
			}
		}
		
		//fallback to simple xml remote open
		if (($steam_xml = @simplexml_load_file($xml_url)) === false) return false; else return $steam_xml;
	}
}

function AdvancedSteamWidget_register() {
	register_widget('AdvancedSteamWidget');
}
add_action( 'widgets_init', 'AdvancedSteamWidget_register' );

function AdvancedSteamWidget_admin_scripts($hook) {
	if( $hook != 'widget.php') return;
	wp_enqueue_script('jquery');
}
add_action('admin_enqueue_scripts', 'AdvancedSteamWidget_admin_scripts');

function AdvancedSteamWidget_shortcode($attribs) {
	$widget = new AdvancedSteamWidget();
	
	if (!isset($attribs["id"])) return '';
	
	$id = $attribs["id"];
	$widget->_set($id);
	$settings = $widget->get_settings();
	$instance = $settings[$id];
	if (!is_array($instance)) return "Invalid Steam Widget ID!";
	
	$args = array('before_widget' => '<div class="advanced_steam_widget">', 'after_widget' => "</div>", 'before_title' => '<h3>', 'after_title' => '</h3>');
	
	ob_start();
	$widget->widget($args, $instance);
	return ob_get_clean();
}
add_shortcode('steam', 'AdvancedSteamWidget_shortcode');