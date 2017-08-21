# Laravel Backpack CRUD Extended

This package extends [Backpack/CRUD](https://github.com/laravel-backpack/crud). See all features added bellow.

To do this without any modification on your controller or others package, this package:
- is able to override all Backpack/CRUD views;
- extends CrudPanel class.


## Installation

In your terminal:

```
composer require novius/laravel-backpack-crud-extended
```


In `config/app.php`, replace

```php?start_inline=1
Backpack\CRUD\CrudServiceProvider::class,
```

by

```php?start_inline=1
Novius\Backpack\CRUD\CrudServiceProvider::class,
```


### Publish views

If you have already published backpack-crud views, this package will not work. 
You have to remove views into `resources/views/vendor/backpack/crud/`, or to override them with:

```
php artisan vendor:publish --provider="Novius\Backpack\CRUD\CrudServiceProvider" --force
```


## Usage & Features

### CRUD Boxes

You can now split your create/edit inputs into multiple boxes.

In order to use this feature, you just need to specify the box name for each of your fields.

```php?start_inline=1
$this->crud->addField([
    'name' => 'title',
    'label' => "My Title",
    'type' => 'text',
    'box' => 'Box name here'
]);
```

You can also specify some options to each box:

```php?start_inline=1
$this->crud->setBoxOptions('Details', [
    'side' => true,         // Place this box on the right side?
    'class' => "box-info",  // CSS class to add to the div. Eg, <div class="box box-info">
    'collapsed' => true,    // Collapse this box by default?
]);
```

If you forget to specify a tab name for a field, Backpack will place it in the last box.


### Fields Drivers

Fields type can now be a classname:

```php?start_inline=1
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

```php?start_inline=1
$this->crud->setLangFile('backpack::crud/movie');
```

This dictionary will then be used in the CRUD views.

You can use it in your own views like this:

```php?start_inline=1
{{ trans($crud->getLangFile().'.add') }}
```


## Testing

Run the tests with:

```
./test.sh
```


## Lint

Run php-cs with:

```
./cs.sh
```

## Contributing

Contributions are welcome!
Leave an issue on Github, or create a Pull Request.


## Licence

This package is under [GNU Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html) or (at your option) any later version.
