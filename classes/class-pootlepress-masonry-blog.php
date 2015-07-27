<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Pootlepress_Masonry_Blog Class
 *
 * Base class for the Pootlepress Masonry Blog.
 *
 * @package WordPress
 * @subpackage Pootlepress_Masonry_Blog
 * @category Core
 * @author Pootlepress
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * public $token
 * public $version
 * 
 * - __construct()
 * - add_theme_options()
 * - get_menu_styles()
 * - load_stylesheet()
 * - load_script()
 * - load_localisation()
 * - check_plugin()
 * - load_plugin_textdomain()
 * - activation()
 * - register_plugin_version()
 * - get_header()
 * - woo_nav_custom()
 */
class Pootlepress_Masonry_Blog {
	public $token = 'pootlepress-masonry-blog';
	public $version;
	private $file;

    private $masonryBlogEnabled;
    private $columnCount;
    private $hidePostContent;
    private $hidePostImage;
    private $hidePostMeta;
    private $infiniteScrollEnabled;
    private $hidePostTitle;
    private $hideContinueReadingLink;


	/**
	 * Constructor.
	 * @param string $file The base file of the plugin.
	 * @access public
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct ( $file ) {
		$this->file = $file;
		$this->load_plugin_textdomain();
		add_action( 'init', array( &$this, 'load_localisation' ), 0 );

		// Run this on activation.
		register_activation_hook( $file, array( &$this, 'activation' ) );

		// Add the custom theme options.
		$this->add_theme_options();

		// Load for a method/function for the selected style and load it.

		// Load for a stylesheet for the selected style and load it.
        add_action( 'wp_enqueue_scripts', array( &$this, 'load_script' ) );

        // the script is only to redirect link when WooTheme,
        // only for old WooFramework
        if (!class_exists('WF')) {
            add_action('admin_print_scripts', array(&$this, 'load_admin_script'));
        }

        add_action('wp_head', array(&$this, 'option_css'));

        add_action( 'init', array(&$this, 'init_infinite_scroll') , 10);

        add_filter('infinite_scroll_archive_supported', array(&$this, 'filter_infinite_scroll_support'), 20);

        add_filter( 'infinite_scroll_query_object', array(&$this, 'filter_infinite_scroll_query'));

        add_filter('infinite_scroll_credit', '__return_empty_string');

        $this->masonryBlogEnabled = get_option('pootlepress-masonry-blog-enable', 'true') == 'true';
        $this->columnCount = (int)get_option('pootlepress-masonry-blog-column-count', '2');

        $this->hidePostContent = get_option('pootlepress-masonry-blog-hide-content', 'false') == 'true';
        $this->hidePostImage = get_option('pootlepress-masonry-blog-hide-image', 'false') == 'true';
        $this->hidePostMeta = get_option('pootlepress-masonry-blog-hide-meta', 'false') == 'true';
        $this->infiniteScrollEnabled = get_option('pootlepress-masonry-blog-infinite-scroll-enable', 'false') == 'true';
        $this->hidePostTitle = get_option('pootlepress-masonry-blog-hide-title', 'false') == 'true';
		$this->hideContinueReadingLink = get_option('pootlepress-masonry-blog-hide-continue-reading', 'false') == 'true';
		$this->hideCommentBalloon = get_option('pootlepress-masonry-blog-hide-comment-baloon', 'false') == 'true';

	} // End __construct()

    public function load_script() {
        if ($this->masonryBlogEnabled && is_page_template('template-magazine.php')) {
            $pluginFile = dirname(dirname(__FILE__)) . '/pootlepress-masonry-blog.php';
            wp_enqueue_script('pootlepress-masonry', plugin_dir_url($pluginFile) . 'scripts/masonry.pkgd.min.js', array('jquery'));
            wp_enqueue_script('pootlepress-images-loaded', plugin_dir_url($pluginFile) . 'scripts/imagesloaded.pkgd.min.js', array('jquery'));
            wp_enqueue_script('pootlepress-masonry-blog', plugin_dir_url($pluginFile) . 'scripts/masonry-blog.js', array('jquery'));
        }
    }

    public function load_admin_script() {
        if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'woothemes') {
            $pluginFile = dirname(dirname(__FILE__)) . '/pootlepress-masonry-blog.php';
            wp_enqueue_script('pootlepress-masonry-blog-admin', plugin_dir_url($pluginFile) . 'scripts/masonry-blog-admin.js', array('jquery'));
        }
    }

    public function init_infinite_scroll() {
        if ($this->masonryBlogEnabled && $this->infiniteScrollEnabled) {
            remove_theme_support('infinite-scroll');
            add_theme_support( 'infinite-scroll', array(
                'container' => 'masonry',
                'wrapper' => false,
                'footer' => false,
                'render' => 'pootlepress_masonry_blog_render'
            ) );
        }
    }

    public function filter_infinite_scroll_support($supported) {
        if ($this->masonryBlogEnabled && $this->infiniteScrollEnabled &&
            is_page_template('template-magazine.php')) {
            return true;
        } else {
            return $supported;
        }
    }

    public function filter_infinite_scroll_query($query) {
        if ( $this->masonryBlogEnabled && $this->infiniteScrollEnabled &&
            is_page_template('template-magazine.php')) {
            $args = woo_get_magazine_query_args();
            $query = new WP_Query( $args );
            return $query;
        } else {
            return $query;
        }
    }

	/**
	 * Add theme options to the WooFramework.
	 * @access public
	 * @since  1.0.0
	 * @param array $o The array of options, as stored in the database.
	 */
	public function add_theme_options () {

        $o = array();

        $o[] = array(
            'name' => __( 'Masonry Blog', 'pootlepress-masonry-blog' ),
            'type' => 'heading'
        );

		$o[] = array(
				'name' => __( 'Masonry Blog Settings', 'pootlepress-masonry-blog' ),
				'type' => 'subheading'
				);

        if (class_exists('WF')) {
            $o[] = array(
                'name' => 'Masonry Blog',
                'desc' => '',
                'id' => 'pootlepress-masonry-blog-notice',
                'std' => 'Masonry Blog works with the Canvas Magazine template. Set options for the posts grid <a id="masonry-blog-link" href="?page=woothemes&tab=magazine-template">here</a>.',
                'type' => 'info'
            );
        } else {
            $o[] = array(
                'name' => 'Masonry Blog',
                'desc' => '',
                'id' => 'pootlepress-masonry-blog-notice',
                'std' => 'Masonry Blog works with the Canvas Magazine template. Set options for the posts grid <a id="masonry-blog-link" href="javascript:void(0)">here</a>.',
                'type' => 'info'
            );
        }

        $o[] = array(
            'id' => 'pootlepress-masonry-blog-enable',
            'name' => __( 'Enable Masonry Blog', 'pootlepress-masonry-blog' ),
            'desc' => __( 'Enable Masonry Blog', 'pootlepress-masonry-blog' ),
            'std' => 'true',
            'type' => 'checkbox'
        );
        $o[] = array(
            'id' => 'pootlepress-masonry-blog-column-count',
            'name' => __( 'Number of columns for posts', 'pootlepress-masonry-blog' ),
            'desc' => __( 'Number of columns for posts', 'pootlepress-masonry-blog' ),
            'type' => 'select',
            'options' => array('2', '3', '4')
        );
        $o[] = array(
            'id' => 'pootlepress-masonry-blog-hide-content',
            'name' => __( 'Do not display Post Content for "Grid” Posts (Full Content or Exceprt)', 'pootlepress-masonry-blog' ),
            'desc' => __( 'Do not display Post Content for "Grid” Posts (Full Content or Exceprt)', 'pootlepress-masonry-blog' ),
            'std' => 'false',
            'type' => 'checkbox'
        );
        $o[] = array(
            'id' => 'pootlepress-masonry-blog-hide-image',
            'name' => __( 'Do not display Post Images', 'pootlepress-masonry-blog' ),
            'desc' => __( 'Do not display Post Images', 'pootlepress-masonry-blog' ),
            'std' => 'false',
            'type' => 'checkbox'
        );
        $o[] = array(
            'id' => 'pootlepress-masonry-blog-hide-meta',
            'name' => __( 'Do not display Post Meta (e.g. author, date and categories)', 'pootlepress-masonry-blog' ),
            'desc' => __( 'Do not display Post Meta (e.g. author, date and categories)', 'pootlepress-masonry-blog' ),
            'std' => 'false',
            'type' => 'checkbox'
        );
        $o[] = array(
            'id' => 'pootlepress-masonry-blog-infinite-scroll-enable',
            'name' => __( 'Enable Infinite scroll', 'pootlepress-masonry-blog' ),
            'desc' => __( 'Enable Infinite scroll', 'pootlepress-masonry-blog' ),
            'std' => 'false',
            'type' => 'checkbox'
        );
        $o[] = array(
            'id' => 'pootlepress-masonry-blog-hide-title',
            'name' => __( 'Do not display Post Titles', 'pootlepress-masonry-blog' ),
            'desc' => __( 'Do not display Post Titles', 'pootlepress-masonry-blog' ),
            'std' => 'false',
            'type' => 'checkbox'
        );
		$o[] = array(
			'id' => 'pootlepress-masonry-blog-hide-continue-reading',
			'name' => __( 'Do not display Continue Reading link', 'pootlepress-masonry-blog' ),
			'desc' => __( 'Do not display Continue Reading link', 'pootlepress-masonry-blog' ),
			'std' => 'false',
			'type' => 'checkbox'
		);
		$o[] = array(
			'id' => 'pootlepress-masonry-blog-hide-comment-baloon',
			'name' => __( 'Do not display comment baloon', 'pootlepress-masonry-blog' ),
			'desc' => __( 'Do not display comment baloon', 'pootlepress-masonry-blog' ),
			'std' => 'false',
			'type' => 'checkbox'
		);


        $afterName = 'Map Callout Text';
        $afterType = 'textarea';

        global $PCO;
        $PCO->add_options($afterName, $afterType, $o);

	} // End add_theme_options()



    public function option_css() {
        if (is_page_template('template-magazine.php')) {
            $css = '';
            if ($this->masonryBlogEnabled) {

//                $css .= "#masonry > article { display: none; }\n";
                $css .= "#masonry > .pagination { bottom: -10px; position: absolute; }\n";
                $css .= "#masonry > .block { margin-bottom: 10px; }\n";
                if ($this->columnCount == 2) {
                    $css .= "#masonry > .column-width { width: 45%; }\n";
                    $css .= "#masonry > .gutter-sizer { width: 10%; }\n";
                    $css .= "#masonry > .block { width: 45%; }\n";
                } else if ($this->columnCount == 3) {
                    $css .= "#masonry > .column-width { width: 30%; }\n";
                    $css .= "#masonry > .gutter-sizer { width: 5%; }\n";
                    $css .= "#masonry > .block { width: 30%; }\n";
                } else if ($this->columnCount == 4) {
                    $css .= "#masonry > .column-width { width: 22.5%; }\n";
                    $css .= "#masonry > .gutter-sizer { width: 3.33333333%; }\n";
                    $css .= "#masonry > .block { width: 22.5%; }\n";
                }

                if ($this->hidePostContent) {
                    $css .= "#masonry > .block > article > .entry { display: none; }\n";
                }

                if ($this->hidePostImage) {
                    $css .= "#masonry > .block > article > a > .woo-image { display: none; }\n";
                }

                if ($this->hidePostMeta) {
                    $css .= "#masonry > .block > article > .post-meta { display: none; }\n";
                }

                if ($this->infiniteScrollEnabled) {
                    $css .= "#masonry > .pagination { display: none; }\n";
                    $css .= "#masonry > .infinite-loader {\n";
                    $css .= "\t" . 'text-indent: 0 !important; position: absolute !important;' . "\n";
                    $css .= "\t" . 'left: 50% !important; bottom: 0 !important;' . "\n";
                    $css .= "}\n";
                }

                if ($this->hidePostTitle) {
                    $css .= "#masonry > .block > article > header > .entry-title { display: none; }\n";
                }

	            if ($this->hideContinueReadingLink) {
		            $css .= "#masonry > .block > article > .post-more > .read-more { display: none; }\n";
	            }

	            if ($this->hideCommentBalloon) {
		            $css .= "#masonry > .block > article > .post-more > .comments { display: none; }\n";
	            }
            }

            echo "<style>".$css."</style>";
        }

    }

	/**
	 * Load stylesheet required for the style, if has any.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function load_stylesheet () {

	} // End load_stylesheet()

	/**
	 * Load the plugin's localisation file.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( $this->token, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation()

	/**
	 * Load the plugin textdomain from the main WordPress "languages" folder.
	 * @access public
	 * @since  1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = $this->token;
	    // The "plugin_locale" filter is also used in load_plugin_textdomain()
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	 
	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain()

	/**
	 * Run on activation.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
	} // End activation()

	/**
	 * Register the plugin's version.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	private function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( $this->token . '-version', $this->version );
		}
	} // End register_plugin_version()


} // End Class


