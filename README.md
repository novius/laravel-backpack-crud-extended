# Laravel Backpack CRUD Extended
[![Travis](https://img.shields.io/travis/novius/laravel-backpack-crud-extended.svg?maxAge=1800&style=flat-square)](https://travis-ci.org/novius/laravel-backpack-crud-extended)
[![Packagist Release](https://img.shields.io/packagist/v/novius/laravel-backpack-crud-extended.svg?maxAge=1800&style=flat-square)](https://packagist.org/packages/novius/laravel-backpack-crud-extended)
[![Licence](https://img.shields.io/packagist/l/novius/laravel-backpack-crud-extended.svg?maxAge=1800&style=flat-square)](https://github.com/novius/laravel-backpack-crud-extended#licence)

This package extends [Backpack/CRUD](https://github.com/laravel-backpack/crud). See all features added bellow.

To do this without any modification on your controller or others package, this package:
- is able to override all Backpack/CRUD views;
- extends CrudPanel class.


## Table of Contents

- [Installation](#installation)
	- [Publish views](#publish-views)
    - [Configuration](#configuration)
- [Usage & Features](#usage--features)
    - [Permissions](#permissions)
	- [CRUD Boxes](#crud-boxes)
	- [Fields Drivers](#fields-drivers)
	- [Language / i18n](#language--i18n)
	- [Upload Field : UploadableFile Trait](#upload-field--uploadablefile-trait)
	- [Upload Field : file_upload_crud validation rule](#upload-field--file_upload_crud-validation-rule)
	- [Image Field : UploadableImage Trait](#image-field--uploadableimage-trait)
    - [CRUD : custom routes](#crud--custom-routes)
- [Testing](#testing)
- [Lint](#lint)
- [Contributing](#contributing)
- [Licence](#licence)


## Installation

In your terminal:

```bash
composer require novius/laravel-backpack-crud-extended
```

Then, if you are on Laravel 5.4 (no need for Laravel 5.5 and higher), register the service provider to your `config/app.php` file:

```php
'providers' => [
    ...
    Novius\Backpack\CRUD\CrudServiceProvider::class,
];
```


### Publish views

If you have already published backpack-crud views, this package will not work. 
You have to remove views into `resources/views/vendor/backpack/crud/`, or to override them with:

```sh
php artisan vendor:publish --provider="Novius\Backpack\CRUD\CrudServiceProvider" --force
```

### Configuration

Some options that you can override are available.

```sh
php artisan vendor:publish --provider="Novius\Backpack\CRUD\CrudServiceProvider" --tag="config"
```


## Usage & Features

### Permissions

#### Description
- Permissions can be applied automatically to CRUD Controllers : deny access if user hasn't the permission linked to the route => `apply_permissions` option
- Permissions can be automatically created for all Crud Controllers used in you application (4 permissions will be created : list, update, create, delete) => `create_permissions_while_browsing` option
- Permissions can be automatically given to the logged user (useful in local environment)  => `give_permissions_to_current_user_while_browsing` option

#### Requirements

- This feature require : [Laravel-Backpack/PermissionManager](https://github.com/Laravel-Backpack/PermissionManager)
- You have to publish config file :

```sh
php artisan permissions:generate // Insert permissions in database for each CRUD controllers.
```

- Set to true each options that you want activate

#### Usage

Your CrudController must extend `\Novius\Backpack\CRUD\Http\Controllers\CrudController`


### CRUD Boxes

You can now split your create/edit inputs into multiple boxes.

![backpack-crud-boxes](https://user-images.githubusercontent.com/1242207/29535541-7d14ca06-86ba-11e7-8ba6-303b2b99924b.png)

In order to use this feature, you just need to specify the box name for each of your fields.

```php
$this->crud->addField([
    'name' => 'title',
    'label' => "My Title",
    'type' => 'text',
    'box' => 'Box name here'
]);
```

You can also specify some options to each box:

```php
$this->crud->setBoxOptions('Details', [
    'side' => true,         // Place this box on the right side?
    'class' => "box-info",  // CSS class to add to the div. Eg, <div class="box box-info">
    'collapsed' => true,    // Collapse this box by default?
]);
```

If you forget to specify a tab name for a field, Backpack will place it in the last box.

You can specify manually a default box in the crud file.
If your field doesn't have the box attribute, this field will be displayed in this default box.

```php
$this->crud->setDefaultBox('YourBoxName');
```

### Fields Drivers

Fields type can now be a classname:

```php
$this->crud->addField([
    'name' => 'username',
    'label' => "My username",
    'type' => \My\Other\Package\Field\Foo::class,
]);
```

This allows you to propose new field types in external packages.
Your Field class must implement Field Contract.


### Language / i18n

Set a custom dictionary for a specific crud:

```php
$this->crud->setLangFile('backpack::crud/movie');
```

This dictionary will then be used in the CRUD views.

You can use it in your own views like this:

```php
{{ trans($crud->getLangFile().'.add') }}
```

### Upload Field : `UploadableFile` Trait

If you use Upload CRUD Field, you can implement this Trait on your Model to automatically upload / delete file(s) on server.

Example:
```php
// Article Model

class Article extends \Backpack\NewsCRUD\app\Models\Article
{
    use Sluggable, SluggableScopeHelpers;
    use HasTranslations;
    use UploadableFile;

    protected $fillable = ['slug', 'title', 'content', 'image', 'status', 'category_id', 'featured', 'date', 'document', 'document_2'];
    protected $translatable = ['slug', 'title', 'content'];

    public function uploadableFiles(): array
    {
        return [
            ['name' => 'document'],
            ['name' => 'document_2', 'slug' => 'title']
        ];
    }
}
```

```php
// ArticleCrudController

$this->crud->addField([ 
    'label' => 'Image',
    'name' => 'image',
    'type' => 'image',
    'upload' => true,
    'crop' => true, // set to true to allow cropping, false to disable
    'aspect_ratio' => 0, // ommit or set to 0 to allow any aspect ratio
    'prefix' => '/storage/',
]);

$this->crud->addField([
    'label' => 'Document',
    'name' => 'document',
    'type' => 'upload',
    'upload' => true,
    'prefix' => '/storage/',
]);

$this->crud->addField([
    'label' => 'Document 2',
    'name' => 'document_2',
    'type' => 'upload',
    'upload' => true,
    'prefix' => '/storage/',
]);
```

### Upload Field : `file_upload_crud` validation rule

A validation rule exists to easily validate CRUD request with "upload" field.

Example of usage in your requests files:
```php
public function rules()
{
    return [
        'name' => 'required|min:2|max:191',           
        'document' => 'file_upload_crud:pdf,docx', // the parameters must be valid mime types
    ];
}

public function messages()
{
    return [
        'file_upload_crud' => 'The :attribute must be a valid file.',
    ];
}
```

### Image Field : `UploadableImage` Trait

If you use Image CRUD Field, you can implement this Trait on your Model to automatically upload / delete image(s) on server.

Example:
```php
// Article Model

class Article extends \Backpack\NewsCRUD\app\Models\Article
{
    use Sluggable, SluggableScopeHelpers;
    use HasTranslations;
    use UploadableImage;

    protected $fillable = ['slug', 'title', 'content', 'image', 'status', 'category_id', 'featured', 'date', 'thumbnail'];
    protected $translatable = ['slug', 'title', 'content'];

    public function uploadableImages()
    {
        return [
            [
                'name' => 'image', // Attribute name where to stock image path
                'slug' => 'title', // Attribute name to generate image file name (optionnal)
            ],
            [
                'name' => 'thumbnail',
            ],
        ];
    }

    /**
     * You might like to perform some custom actions on your image after saving it.
     * For instance, create a thumbnail using the trait HasMediaTrait.
     * To get this:
     *  1. You must override this method.
     *  2. The configuration file medialibrary.php must defines an existing filesystem and image driver:
     *      'defaultFilesystem' => 'public',
     *      'image_driver' => 'imagick',
     */
    public function imagePathSaved(string $imagePath, string $imageAttributeName = null, string $diskName = null)
    {
        //perfoms some custom actions here
        $this->addMedia($imagePath)
            ->preservingOriginal()
            ->toMediaCollection();

        return true;
    }

    /**
     * You might like to perform some custom actions after deleting the image.
     */
    public function imagePathDeleted(string $imagePath, string $imageAttributeName = null, string $diskName = null)
    {
        return true;
    }
}

```

```php
// ArticleCrudController

$this->crud->addField([ 
    'label' => 'Image',
    'name' => 'image',
    'type' => 'image',
    'upload' => true,
    'crop' => true, // set to true to allow cropping, false to disable
    'aspect_ratio' => 0, // ommit or set to 0 to allow any aspect ratio
    'prefix' => '/storage/',
]);

$this->crud->addField([
    'label' => 'Image',
    'name' => 'thumbnail',
    'type' => 'image',
    'upload' => true,
    'crop' => true, // set to true to allow cropping, false to disable
    'aspect_ratio' => 0, // ommit or set to 0 to allow any aspect ratio
    'prefix' => '/storage/',
]);
```


### CRUD : custom routes

You can set a custom value to some routes.

- Index route : used with "back to all" button or breadcrumb's link. Available with `$crud->indexRoute()`.
- Reorder route : used with "Reorder button". Available with `$crud->reorderRoute()`.
 
Example of usage in your CrudController : 
```php
// Set a custom index route
$this->crud->setIndexRoute('crud.slide.index', ['slideshow' => (int) request('slideshow')]);

// Set a custom reorder route
$this->crud->setReorderRoute('crud.slide.reorder', ['slideshow' => (int) request('slideshow')]);
```

## Testing

Run the tests with:

```bash
./test.sh
```


## Lint

Run php-cs with:

```bash
./cs.sh
```

## Contributing

Contributions are welcome!
Leave an issue on Github, or create a Pull Request.


## Licence

This package is under [GNU Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html) or (at your option) any later version.

However, this package requires [Backpack\CRUD](http://github.com/laravel-backpack/crud), which is under YUMMY license: if you use it in a commercial project, you have to [buy a backpack license](https://backpackforlaravel.com/pricing).
