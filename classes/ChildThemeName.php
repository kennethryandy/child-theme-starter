<?php


/**
 * Class that wraps the core theme functionality.
 */
class ChildThemeName
{
	/**
	 * The theme version, as used by sricpts, etc.
	 * @var string
	 */
	protected $themeVersion;

	public static $connection;


	public function __construct($themeVersion)
	{
		$this->themeVersion  =  $themeVersion;

		// Set up initialisation hooks
		add_action('init', [$this, 	'init']);

		// ACF initialisations
		$this->init_ACF();
	}

	/**
	 * Core init called by WordPress on init().
	 */
	public function init()
	{
		
		// Menu Related
		$this->initMenus();

		// Custom Post Types
		$this->init_createCustomPostTypes();
		$this->init_createCustomTaxonomies();
		
		// Actions

		// filters
		add_filter('generate_logo', [$this, 'header_returnLogoImage']);
		add_filter('generate_copyright', [$this, 'footer_renderFooterMenu']);
		
		// Load scripts and styles
		add_action('wp_enqueue_scripts', [$this, 'init_theme_scriptsAndStyles']);
		
		// Script Injection
		add_action('wp_head', array($this, 'scripts_header_top'), 1);
		add_action('wp_head', array($this, 'scripts_header_bottom'), 1000);
		add_action('wp_body_open', array($this, 'scripts_body_top'));
		add_action('wp_footer', array($this, 'scripts_body_bottom'));


		// GeneratePress Customisations
		add_filter('generate_page_class', [$this, 'gp_content_adjust_page_classes'], 20);
	}


	/**
	 * Adds the styles and scripts to the page.
	 */
	public function init_theme_scriptsAndStyles()
	{
		$path  =  self::getAssetsPath('');

		// Styles
		wp_enqueue_style('child-theme-name', $path . 'css/main.min.css', [], filemtime(get_stylesheet_directory() . '/assets/css/main.min.css'), 'all');
		

		// Scripts
		wp_enqueue_script('child-theme-name', $path . 'js/app.min.js',  ['jquery'],  filemtime(get_stylesheet_directory() . '/assets/js/app.min.js'), true);
		
		wp_dequeue_script('generate-menu');
	}


	/**
	 * Initalise menu settings.
	 */
	protected function initMenus()
	{
		register_nav_menu('main-menu', 'Main Menu');
	}

	/**
	 * Register Custom Post Types.
	 */
	public function init_createCustomPostTypes()
	{
	}

	/**
	 * Register Custom Taxonomies.
	 */
	public function init_createCustomTaxonomies()
	{
	}

	/**
	 * Functions for setting up ACF.
	 */
	public function init_ACF()
	{
		// Create a settings page.
		if (function_exists('acf_add_options_page'))
		{
			$menuSlug  =  'website-settings';

			acf_add_options_page(array(
				'page_title'  => __('Rooche - Website Settings'),
				'menu_title'  => __('Website Settings'),
				'menu_slug'   => $menuSlug,
				'redirect'    => false
			));
		}
	}


	/**
	 * Returns the image needed for a logo.
	 * @return string
	 */
	public static function header_returnLogoImage()
	{
		// return self::images_getImageURL('logo.png');
	}


	/**
	 * Changes the URL of the login page for the website.
	 * @return string
	 */
	public function images_changeLogin_URLforSite()
	{
	}


	/**
	 * Return a branded favicon for this website.
	 * @return string
	 */
	public function images_returnFavicon()
	{
	}

	/**
	 * Generate the copyright for the website at the footer of the page using settings from ACF.
	 * @return string The HTML to use for the copyright.
	 */
	public function footer_renderCopyright()
	{
		$html = false;

		// Add footer social media icon links
		// $html .= $this->generate_footer_socialMedia();
				
		// Add footer menu terms
		// $html .= $this->footer_renderFooterMenuTerms();

		$html .= '<div class="footer_copyright">';
		
		// Copyright bit
		$footer = get_field('footer_copyright', 'options');
		$html .= str_replace('{YEAR}', date('Y'), $footer);
		
		$html .= '</div>';

		return $html;
	}


	/**
	 * Method that handles rendering the page content using the layout blocks.
	 * @param integer $pageID The ID of the page being rendered.
	 */
	public static function handleBlockLayout($pageID)
	{
		if (have_rows('layout_blocks', $pageID))
		{
			while (have_rows('layout_blocks', $pageID))
			{
				the_row();
				$layout = get_row_layout();
				// Convert the name of the layout to the file format. The structure is:
				// block__video_welcome, so this needs to become video-welcome
				$blockName = false;
				if (preg_match('%^block__([\_a-zA-Z0-9]+)$%', $layout, $matches))
				{
					$blockName = str_replace('_', '-', $matches[1]);
				}

				// See if the template file exists? All templates should be in template-blocks. 
				$templateFile = 'template-block/' . $blockName . '.php';
				if (!empty($blockName) && !empty($blockTFile = locate_template($templateFile)))
				{
					require($blockTFile);
				}

				else
				{
					printf('<div class="container"><b>%s</b> (<code>%s</code>)</div>', __('Template not found.'), esc_html($templateFile));
				}
			}
		}
	}


	/**
	 * Adjust the page classes before the page class attributes are rendered so that we can
	 * change what page classes the page gets.
	 * 
	 * @param string[] $classes
	 * @return string[]
	 */
	public function gp_content_adjust_page_classes($classes)
	{
		// Full width for certain page templates.
		$classes = array(
			'site',
			'hfeed',
			'page_fullwidth__container'
		);

		// Otherwise pages use grid-container to make sure the width isn't too wide.
		return $classes;
	}



	/**
	 * Renders scripts in the top of the header immediately after <head>. This is a custom hook in the theme.
	 */
	public function scripts_header_top()
	{
		the_field('script_header_top', 'options');
	}


	/**
	 * Renders scripts in the top of the header immediately before </head>. This is a custom hook in the theme.
	 */
	public function scripts_header_bottom()
	{
		the_field('script_header_bottom', 'options');
	}


	/**
	 * Renders scripts in the top of the body immediately after <body>. This is a custom hook in the theme.
	 */
	public function scripts_body_top()
	{
		the_field('script_body_top', 'options');
	}


	/**
	 * Renders scripts in the top of the footer immediately before </body>. This is a custom hook in the theme.
	 */
	public function scripts_body_bottom()
	{
		the_field('script_body_bottom', 'options');
	}

	/**
	 * Get the assets path for this theme.
	 *
	 * @param string $subPath If provided, add this to the URL that's returned.
	 * @return string The path to the assets (including the subpath if included).
	 */
	public static function getAssetsPath($subPath = false)
	{
		return get_stylesheet_directory_uri() . '/assets/' . $subPath;
	}


	/**
	 * Returns the URL of the image folder for images used in the theme.
	 * 
	 * @param string $filename If specified, append this filename to the URL for the image folder.
	 * @return string The URL to the image path
	 */
	public static function images_getImageURL($filename = false)
	{
		return get_stylesheet_directory_uri() . '/assets/img/' . $filename;
	}

	public function __redirect_home() { wp_safe_redirect(get_home_url()); }


	/**
	 * Static factory method to initialise this theme and return an instantiated
	 * object.
	 *
	 * @param string $themeVersion The current theme version.
	 *
	 * @return ChildThemeName The instantiated theme object.
	 */
	public static function createTheme($themeVersion = "")
	{
		if(!isset(self::$connection)) {
			self::$connection = new ChildThemeName($themeVersion);
		}
		return self::$connection;
	}


	/**
	 * Debug function - shows an array on the screen, and in the PHP error log for debug purposes.
	 * @param array $data The array of data to show and display.
	 * @param string $label If specified, show this as a label for the data that's been shown to make it clearer what the data applies to.
	 * @param bool $errorlogOnly If true, do not echo the data to the screen, error log only.
	 */
	public static function __debug_showArray($data, $label = false, $errorLogOnly = false)
	{
		// Break out a preformatted array
		$debugData = print_r($data, true);

		// Add a label prefix if there is one
		if ($label)
		{
			$debugData = $label . ":\n" . $debugData;
		}

		// Show to the screen if available.
		if (!$errorLogOnly)
		{
			printf('<div><pre style="padding: 20px; background: #efefef; border: 2px solid #ddd; margin: 20px;">%s</pre></div>', htmlentities($debugData));
		}

		// Show in the PHP error log
		error_log($debugData);
	}
}
