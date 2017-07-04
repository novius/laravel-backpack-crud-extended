# Backpack CRUD Extended

Ce package est juste un test.

Son but ? Surcharger Backpack\CRUD sans aucune modification dans les autres packages (donc sans surcharger le controller, notamment).

Pour le moment, il arrive à :
* surcharger toutes les vues
  * Si elle existe, une vue placée dans `ressources/views/` sera appelée en pririté
  * Pour l'exemple, la vue `show_fields.blade.php` est surchargé pour permettre de mettre un nom de classe dans en "type" de champs.
* surcharger `CrudPanel`
  * Le controller appelle cette classe systématiquement, donc étendre cette classe permet de faire beaucoup de choses.


## Installation

In `config/app.php`, replaces

```php?start_inline=1
Backpack\CRUD\CrudServiceProvider::class,
```

by

```php?start_inline=1
Novius\Backpack\CRUD\CrudServiceProvider::class,
```


## Usage

Fields type can now be a classname:

```php?start_inline=1
$this->crud->addField([
    'name' => 'username',
    'label' => "My username",
    'type' => \Novius\Backpack\CRUD\Field\Truc::class,
]);```
