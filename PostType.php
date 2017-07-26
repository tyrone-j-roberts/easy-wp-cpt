<?php
/**
 * Created by Tyrone Roberts
 */

abstract class PostType
{
    protected $ID, $args;

    /**
     * PostType constructor.
     * Registers post type when instantiated if it hasn't already been registered
     * Can be instantiated without an $id as long as it's in the loop
     * @param int $id
     */
    public function __construct($id = 0)
    {
        $ptype = self::getPostType();
        if (!post_type_exists($ptype)) {
            $this->setPostTypeArgs();
            $this->setupPostType();
        } else if ($id) {
            $this->ID = $id;
            $this->init();
        } else {
            global $post;
            $this->ID = $post->ID;
            $this->init();
        }
    }

    /**
     * @param $var
     * @return mixed
     * @throws Exception
     * Retrieves properties from Post Object, post_meta or ACF fields if ACF is enabled
     */
    public function __get($var)
    {
        $post = $this->getPostObject();

        $r = null;
        if (function_exists('get_field')) {
            $r = get_field($var, $this->ID);
        }

        if(empty($r)) {
            $r = get_post_meta($this->ID, $var, true);
        }

        if (empty($r)) {
            if (isset($post->$var)) {
                return $post->$var;
            }
        }

        $class_name = get_called_class();
        $err = "Cannot get {$var} in {$class_name}.";
        $err .= "Property must be a WP_Post property, a valid meta_key or an ACF_Field name if ACF is installed";
        throw new Exception($err);
    }

    /**
     * Automatically get post type from class name.
     * Used for static methods as post type var isn't set up.
     * @return string
     */
    protected static function getPostType()
    {
        $class_name = str_replace(__NAMESPACE__ . '\\', '', get_called_class());
        $class_name_parts = preg_split('/(?=[A-Z])/',$class_name);
        $post_type_parts = array();
        foreach($class_name_parts as $part) {
            if (empty($part)) {
                continue;
            }
            $post_type_parts[] = strtolower($part);
        }
        $post_type = implode('-', $post_type_parts);
        return $post_type;
    }

    /**
     * @throws Exception
     */
    protected function init()
    {
        if (!$this->ID) {
            return;
        }
        $post_type = self::getPostType();
        if (get_post_type($this->ID) !== $post_type) {
            throw new Exception("Warning: $post_type ID: " . esc_attr($this->ID) . " does not exist!");
        }
    }

    /**
     * Returns the WP_Post object
     * @return array|null|WP_Post
     */
    protected function getPostObject()
    {
        $post = get_post($this->ID);
        return $post;
    }

    /**
     * Sets up post type hook
     */
    function setupPostType()
    {
        add_action('init', array($this, 'registerPostType'));
    }

    /**
     *  Registers post type
     */
    function registerPostType()
    {
        $post_type = self::getPostType();
        register_post_type($post_type, $this->args);
    }

    /**
     * Same arguments as wp_query/get_posts but returns an array of PostType objects and automatically knows which post type to query
     * @param $args
     * @return array
     */
    public static function get($args = array())
    {
        $args['post_type'] = self::getPostType();
        if (!isset($args['orderby'])) {
            $args['orderby'] = 'menu_order';
        }
        $posts_array = get_posts($args);
        $objects = array();
        foreach ($posts_array as $p) {
            $class = get_called_class();
            $objects[] = new $class($p->ID);
        }
        return $objects;
    }

    /**
     * Gets the featured image url
     */
    public function getImage()
    {
        if (has_post_thumbnail($this->ID)) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id($this->ID), 'single-post-thumbnail');
            return $image[0];
        }
        return '';
    }

    /** 
    * Gets the post permalink
    */
    public function permalink()
    {
        return get_permalink($this->ID);
    }

    protected abstract function setPostTypeArgs();


}
