# Easy Wordpress CPT

An easy way to register and interact with Wordpress Custom Post Types.

## Features
* Register custom post types quickly and easily
* Extends the functionality of a WP_Post object
* Easily access WP_Post properties, post meta data and if installed ACF fields
* Useful methods such as getImage() to retrieve image url for a cleaner, more OOP way of building for Wordpress.


## Installation

1. Clone or download the repo into you WordPress theme folder.
2. Include the file in your functions.php

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
* You need to implement the 'setPostTypeArgs()' abstract method to set the arguments. For more information on the arguments needed for registering post types go to (https://codex.wordpress.org/Function_Reference/register_post_type)
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
 * Include your new class in your functions.php file
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
* To use the class in the loop instantiate the class without a post ID
 ```php
<?php if(have_posts()) : 
       while(have_posts()) : 
       the_post(); 
       $news_article = new NewsArticle(); ?>
       
       <h1><?= $news_article->post_title; ?></h1>
       
<?php endwhile;
       endif; ?>
 ```
* Alternatively you can pass a post ID when instantiating your class

## Retrieving post data
* All standard WP_Post properties are accessed as normal
* Meta data can be retrieved by using the meta_key like you would when accessing properties of an object 
 ```php
 $newsArticle = new NewsAticle();
 echo $newsArticle->{meta_key};
 ```
* If you have Advanced custom fields install you can also retrieve field data by using the field_name like you would when accessing properties of an object 
 ```php
 $news_article = new NewsAticle();
 
 $rows = $news_article->{field_name};

 foreach($rows as $row) {
    echo $row['{sub_field_name}']
 }
 ```
 
## Useful methods
#### PostType::get() 
Allows you to query posts like you would with get_posts and WP_Query but it will return an array of your PostType objects and will automatically know what post type you're trying to query.
 ```php
 $args = array(
     'posts_per_page' => 12,
 );
 
 $news_articles = NewsArticle::get($args);
 
 foreach($news_articles as $news_article) {
     echo $news_article->post_title;
 }
 ```
#### PostType::getImage()
This method returns a url of the featured image if the post has a featured image, otherwise it returns an empty string.
 ```php
 <?php $newsArticle = new NewsAticle(); ?>
 <img src="<?= $newsArticle->getImage(); ?>" />

 ```
#### PostType::permalink()
Returns the post permalink.
 ```php
 <?php $newsArticle = new NewsAticle(); ?>
 <a href="<?= $newsArticle->permalink(); ?>"<?= $newsArticle->post_title; ?></a>
 ```
 
## Issues
If you have any suggestions or problems add an issue to this repository on github