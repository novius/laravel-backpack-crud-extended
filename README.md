# Laravel Backpack CRUD Extended
[![Travis](https://img.shields.io/travis/novius/laravel-backpack-crud-extended.svg?maxAge=1800&style=flat-square)](https://travis-ci.org/novius/laravel-backpack-crud-extended)
[![Packagist Release](https://img.shields.io/packagist/v/novius/laravel-backpack-crud-extended.svg?maxAge=1800&style=flat-square)](https://packagist.org/packages/novius/laravel-backpack-crud-extended)
[![Licence](https://img.shields.io/packagist/l/novius/laravel-backpack-crud-extended.svg?maxAge=1800&style=flat-square)](https://github.com/novius/laravel-backpack-crud-extended#licence)

This package extends [Backpack/CRUD](https://github.com/laravel-backpack/crud). See all features added bellow.

To do this without any modification on your controller or others package, this package:
- is able to override all Backpack/CRUD views;
- extends CrudPanel class.


## Installation

In your terminal:

```bash
composer require novius/laravel-backpack-crud-extended
```


In `config/app.php`, replace

```php
Backpack\CRUD\CrudServiceProvider::class,
```

by

```php
Novius\Backpack\CRUD\CrudServiceProvider::class,
```


### Publish views

If you have already published backpack-crud views, this package will not work. 
You have to remove views into `resources/views/vendor/backpack/crud/`, or to override them with:

```sh
php artisan vendor:publish --provider="Novius\Backpack\CRUD\CrudServiceProvider" --force
```


## Usage & Features

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
