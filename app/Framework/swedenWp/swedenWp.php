<?php
require_once dirname(__FILE__) . '/swedenWpFunctions.php';
require_once dirname(__FILE__) . '/swedenWpMacros.php';
require_once dirname(__FILE__) . '/swedenWpCommentWalker.php';
require_once dirname(__FILE__) . '/swedenWpNavigationWalker.php';
if(is_admin()) {
	require_once dirname(__FILE__) . '/swedenWpImport.php';
	require_once dirname(__FILE__) . '/swedenWpNetworkSync.php';
	require_once dirname(__FILE__) . '/swedenWpMediaLibrary.php';
}

require_once dirname(__FILE__) . '/Entities/swedenWpBaseEntity.php';
require_once dirname(__FILE__) . '/Entities/swedenWpSiteEntity.php';
require_once dirname(__FILE__) . '/Entities/swedenWpCategoryEntity.php';
require_once dirname(__FILE__) . '/Entities/swedenWpTagEntity.php';
require_once dirname(__FILE__) . '/Entities/swedenWpArchiveEntity.php';
require_once dirname(__FILE__) . '/Entities/swedenWpPostAuthorEntity.php';
require_once dirname(__FILE__) . '/Entities/swedenWpCommentAuthorEntity.php';
require_once dirname(__FILE__) . '/Entities/swedenWpCommentEntity.php';
require_once dirname(__FILE__) . '/Entities/swedenWpPostEntity.php';

/** 
 * ver. of template cache.
 * need to update this for quick apply. otherwise will take 30sec(or whatever time you set) to get proper http request.
 **/
define('CACHE_VERSION', 11);

/**
 * Creating swedenWp templates and renders them.
 *
 * @author juhonglee
 *
 */
class swedenWp extends NFileTemplate {

    /**
     * Absolut path to cache directory
     * @var string
     */
    public static $cacheDir;

    /**
     * Absolut path to templates directory
     * @var string
     */
    public static $templatesDir;

    /**
     * @param string $file Absolut path to template file
     * @param array $params Variables for template
     */
    public function __construct($file, $params, $absolutePath = false) {
        if (!$absolutePath) {
            $prefix = "main-";

            $file = basename($file, '.html');

            $file = self::$templatesDir . "/%s{$file}.html";

            if (is_file(sprintf($file, $prefix))) {
                $file = sprintf($file, $prefix);
            } else {
                $prefix = "";
                $file = sprintf($file, $prefix);
            }
        }

        parent::__construct($file);

        $params['site'] = swedenWpSiteEntity::getInstance();

        if (file_exists(self::$templatesDir . '/main-layout.html')) $params['layout'] = 'main-layout.html';
        elseif (file_exists(self::$templatesDir . '/layout.html')) $params['layout'] = 'layout.html';
        else $params['layout'] = '@layout.html';

        if (file_exists(self::$templatesDir . '/@layout-fullscreen.html')) $params['layoutFullscreen'] = '@layout-fullscreen.html';

        $params['headerNotCalled'] = did_action('get_header') == 0;
        $params['footerNotCalled'] = did_action('get_footer') == 0;
        $params['sidebarNotCalled'] = did_action('get_sidebar') == 0;

        $this->setParams($params);

        $this->registerHelperLoader('NTemplateHelpers::loader');
        $this->registerHelper("printf", "sprintf");

        $this->setCacheStorage(new NPhpFileStorage(realpath(self::$cacheDir)));

        $this->onPrepareFilters[] = create_function('$template',
            '
            $engine = new NLatteFilter();
            swedenWpMacros::install($engine->parser);
            $template->registerFilter($engine);
            '
        );
    }

    /**
     * Rendering template from controller
     *
     * @see NFileTemplate::render()
     */
    function render() {
        if ($this->getFile() == NULL) {
            throw new InvalidStateException("Template file name was not specified.");
        }

        $cache = new NCache($storage = $this->getCacheStorage(), 'swedenWp');
        if ($storage instanceof NPhpFileStorage) {
            $storage->hint = str_replace(dirname(dirname($this->getFile())), '', $this->getFile());
        }
        $cached = $compiled = $cache->load($this->getFile());

        if ($compiled === NULL) {
            try {
                $compiled = "<?php\n\n// source file: {$this->getFile()}\n\n?>" . $this->compile();

            } catch (NTemplateException $e) {
                $e->setSourceFile($this->getFile());
                throw $e;
            }

            $cache->save($this->getFile(), $compiled, array(
                NCache::FILES => $this->getFile(),
                NCache::CONSTS => array('NFramework::REVISION', 'CACHE_VERSION'),
            ));
            $cache->release();
            $cached = $cache->load($this->getFile());
        }

        if ($cached !== NULL && $storage instanceof NPhpFileStorage) {
            NLimitedScope::load($cached['file'], $this->getParams());
        } else {
            NLimitedScope::evaluate($compiled, $this->getParams());
        }
    }

    /**
     * Creates new template
     * @param string $file
     * @param array $params
     * @return swedenWp
     */
    public static function createTemplate($file, $params, $absolutePath = false) {
        return new self($file, $params, $absolutePath);
    }

    /**
     * Little factory for swedenWpPostEntity
     *
     * @param WP_Query|stdClass|array $input
     * @param array $meta
     * @return array|swedenWpPostEntity // Array of post entities or single post entities
     */
    public static function createPostEntity($input, $meta = array()) {
        if($input instanceof WP_Query)
            $posts = $input->posts;
        elseif($input instanceof stdClass or $input instanceof WP_Post)
            return new swedenWpPostEntity($input, $meta);
        else
            $posts = $input;

        $return = array();

        foreach ($posts as $post) {
            $return[] = new swedenWpPostEntity($post, $meta);
        }

        return $return;
    }

    /**
     * Alias for swedenWpSiteEntity::createCusomPostEntity()
     *
     * @param string $what Name of custom type prefix
     * @param string $category Selecting custom type by specific category
     * @param int $limit Limit of returned items
     * @return array|swedenWpPostEntity
     */
    public static function createCustomPostEntity($what, $category = 0, $limit = null, $order = 'menu_order', $suppress_filters = false) {
        return swedenWpSiteEntity::createCustomPostEntity($what, $category, $limit, $order, $suppress_filters);
    }

}

/**
// implement twig using framework templating so don't need to worry about parser and node
class swedenTwig implements IFileTemplate {
    private $file;
    private $variables;
    public function setFile() { $this->file = $file; }
    public function getFile() { return $this->file; }
    public function render() {
        $loader = new Twig_Loader_Filesystem(dirname($this->file));
        $twig = new Twig_Environment($loader, array(
                'cache' => THEME_DIR . '/cache',
        ));
        $thig->render(basename($this->file), $this->variables);
    }
    public function __set($var, $val) { $this->variables[$var] = $val; }
    public function &__get($var) { return $this->variables[$var]; }
    public function __isset() {}

}
class BasePresenter extends Nette\Application\UI\Presenter
{

    public function createTemplate() {
        $template = new swedenTwig();
        $template->presenter = $template->control = $this;

        return $template;
    }

}
class BaseControl extends Nette\Application\UI\Control {
    public function createTemplate() {
        $template = new NetteTemplate();
        $template->presenter = $this->getPresenter(FALSE);
        $template->control = $this;

        return $template;
    }
}
*/