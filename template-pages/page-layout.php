<?php
/**
 * Template Name: Page Layout Editor
 *
 * Template for displaying the layout editor for this page that uses the ACF layout.
 */

get_header();
?> 

<div class="wrapper" id="layout-page-wrapper">
	<?php
	   ChildThemeName::handleBlockLayout($post->ID); 
	?>
</div>

<?php get_footer(); ?>
