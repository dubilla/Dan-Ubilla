<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" title="style"  media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />


    <!--[if lte IE 6]>
         <style type="text/css">
            img, div, a, input, span{ behavior: url('<?php bloginfo('template_directory'); ?>/img/iepngfix.htc') }
        </style>
       <style type="text/css">@import url('<?php bloginfo('template_directory'); ?>/ie.css');</style>
	<![endif]-->

	<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery-1.2.3.min.js"></script>
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.easing.min.js"></script>
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.lavalamp.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/smoothscroll.js"></script>
<script type="text/javascript">
        $(function() {
            $("#lmenu").lavaLamp({
                fx: "backout", 
                click: function(event, menuItem) {
                    return true;
                }
            });
        });
</script>
<script type="text/javascript">
	  // When the document loads do everything inside here ...
	  $(document).ready(function(){	
		// When a link is clicked
		$("a.tab").click(function () {
			// switch all tabs off
			$(".active").removeClass("active");	
			// switch this tab on
			$(this).addClass("active");
			// slide all content up
			$(".contentlist").slideUp();
			// slide this content up
			var content_show = $(this).attr("title");
			$("#"+content_show).slideDown();
		});
	  });
</script>	
	
<?php wp_head(); ?>
</head>
<body>
<div id="page">


<div id="header">
		<h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
		<div class="description"><?php bloginfo('description'); ?></div>

</div>
<div id="menu">
		<ul class="lavalamp" id="lmenu">
				<li <?php if(is_home()){echo 'class="current_page_item"';}?>><a href="<?php echo get_option('home'); ?>/" title="Home">Home</a></li>
	   	 		<?php wp_list_pages('title_li=&depth=1&sort_column=menu_order');?>
		</ul>
		<div class="clear"></div>
</div>

	<div class="clear"></div>


<hr />
