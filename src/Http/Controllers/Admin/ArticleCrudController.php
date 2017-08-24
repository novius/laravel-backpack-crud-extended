<?php

namespace Novius\Backpack\NewsCRUD\Http\Controllers\Admin;

use Novius\Backpack\NewsCRUD\Http\Models\Article;
use Novius\Backpack\NewsCRUD\Http\Models\Category;

class ArticleCrudController extends \Backpack\NewsCRUD\app\Http\Controllers\Admin\ArticleCrudController
{
    public function __construct()
    {
        parent::__construct();
        $this->crud->setModel(Article::class);

        unset($this->crud->columns['category_id']);
        if (isset($this->crud->create_fields['category_id'])) {
            unset($this->crud->create_fields['category_id']);
        }
        if (isset($this->crud->update_fields['category_id'])) {
            unset($this->crud->update_fields['category_id']);
        }

        if (isset($this->crud->create_fields['image'])) {
            unset($this->crud->create_fields['image']);
        }
        if (isset($this->crud->update_fields['image'])) {
            unset($this->crud->update_fields['image']);
        }

        $this->crud->addColumn([
            'label' => 'Category',
            'type' => 'select',
            'name' => 'category_id',
            'entity' => 'category',
            'attribute' => 'name',
            'model' => Category::class,
        ]);

        $this->crud->addField([    // SELECT
            'label' => 'Category',
            'type' => 'select2',
            'name' => 'category_id',
            'entity' => 'category',
            'attribute' => 'name',
            'model' => Category::class,
        ]);

        $this->crud->addField([ // image
            'label' => 'Image',
            'name' => 'image',
            'type' => 'image',
            'upload' => true,
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 0, // ommit or set to 0 to allow any aspect ratio
            'prefix' => '/storage/', // in case you only store the filename in the database, this text will be prepended to the database value
        ]);

        $this->crud->addField([ // image
            'label' => 'Image',
            'name' => 'thumbnail',
            'type' => 'image',
            'upload' => true,
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 0, // ommit or set to 0 to allow any aspect ratio
            'prefix' => '/storage/', // in case you only store the filename in the database, this text will be prepended to the database value
        ]);
    }
}
