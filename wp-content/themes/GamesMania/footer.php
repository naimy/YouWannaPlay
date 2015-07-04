  </div>
		<div id="footer">Copyright &copy; <a href="<?php bloginfo('home'); ?>"><strong><?php bloginfo('name'); ?></strong></a>  - <?php bloginfo('description'); ?></div>
        <?php /* 
                    All links in the footer should remain intact. 
                    These links are all family friendly and will not hurt your site in any way. 
                    Warning! Your site may stop working if these links are edited or deleted 
                    
                    You can buy this theme without footer links online at http://freewpthemes.com/buy/?theme=gamesmania 
                */ ?>
        <div id="credits">Powered by <a href="http://wordpress.org/"><strong>WordPress</strong></a> | Designed by: <a href="http://freewpthemes.co">Free WordPress Themes</a> | Thanks to <a href="http://freewpthemes.co">wordpress themes free</a>, Download <a href="http://toppremiumthemes.com">Premium WordPress Themes</a> and <?php if(is_home() || is_front_page()) { ?><a href="http://freewpthemes.com/">WordPress Themes</a><?php } ?></div>
  

</div>
<?php
	 wp_footer();
	echo get_theme_option("footer")  . "\n";
?>
</body>
</html>