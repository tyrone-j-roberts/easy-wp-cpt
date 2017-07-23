# Easy Wordpress CPT

An easy way to register and interact with Wordpress Custom Post Types.

## Features
* Register custom post types quickly and easily
* Extends the functionality of a WP_Post object
* Easily access WP_Post properties, post meta data and if installed ACF fields
* Useful methods such as getImage() to retrieve image url for a cleaner, more OOP way of building for Wordpress.


## Installation

1. Clone or download the repo into you WordPress theme folder.
2. Include the file in your function.php

```php
include get_template_directory() . '/{class-directory}/PostType.php';
```

## Usage
* Create a new class with a camel case name such as 'NewsArticle' or 'Testimonial'
* Extend PostType in the new class you just made
 ```php
 <?php 
 class NewsArticle extends PostType { 
     
     
 }
 ```
* You need to implement the 'setPostTypeArgs()' abstract method to set the argument. For more information on the arguments needed for registering post types go to (https://codex.wordpress.org/Function_Reference/register_post_type)
 ```php
 <?php 
 class NewsArticle extends PostType { 
     
       protected function setPostTypeArgs()
         {
              $labels = array(
                  'name' => 'News Articles',
                  'singular_name' => 'News Article',
                  'menu_name' => 'News Articles',
                  'name_admin_bar' => 'News Article',
                  'add_new' => 'Add New',
                  'add_new_item' => 'Add News Article',
                  'new_item' => 'New Article',
                  'edit_item' => 'Edit Article',
                  'view_item' => 'View Article',
                  'all_items' => 'All News Articles',
                  'search_items' => 'Search Articles',
                  'parent_item_colon' => 'Parent News Articles:',
                  'not_found' => 'No articles found.',
                  'not_found_in_trash' => 'No articles found in Trash.',
              );
              $this->args = array(
                 'labels' => $labels,
                 'public' => true,
                 'show_ui' => true,
                 'show_in_menu' => true,
                 'hierarchical' => true,
                 'supports' => array('title', 'editor', 'excerpt', 'thumbnail'),
              );
         }
         
 }
 ```
 * Include your new class in your function.php file
 ```php
 include get_template_directory() . '/{class-directory}/NewsArticle.php';
 ```
 * Instantiate your new class to register your post type, the resulting post type name in this example will be news-article
 ```php
new NewsArticle();
 ```
 * To add custom taxonomies or meta boxes just override the setup_post_type() method in your class
 ```php
public function setup_post_type()
    {
        parent::setup_post_type();
        add_action('init', array($this, 'registerCategoryTaxonomy'));
        
        public function registerCategoryTaxonomy()
            {
                $labels = array(
                    'name'              => 'Categories',
                    'singular_name'     => 'Category',
                    'search_items'      => 'Search Categories',
                    'all_items'         => 'All Category',
                    'parent_item'       => 'Parent Category',
                    'parent_item_colon' => 'Parent Category:',
                    'edit_item'         => 'Edit Category',
                    'update_item'       => 'Update Category',
                    'add_new_item'      => 'Add New Category',
                    'new_item_name'     => 'New Category Name',
                    'menu_name'         => 'Category'
                );
        
                $args = array(
                    'hierarchical'      => true,
                    'labels'            => $labels,
                    'show_ui'           => true,
                    'show_admin_column' => true,
                    'query_var'         => true,
                    'rewrite'           => array( 'slug' => 'category' ),
                );
                register_taxonomy( 'news-category', self::getPostType(), $args);
            }
    }
 ```

## Issues
If you have any suggestions or problems add an issue to this repository on github