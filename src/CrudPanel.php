<?php

namespace Novius\Backpack\CRUD;

use Backpack\CRUD\CrudPanel as BackpackCrudPanel;
use Novius\Backpack\CRUD\PanelTraits\Boxes;
use Novius\Backpack\CRUD\PanelTraits\BoxTabs;
use Novius\Backpack\CRUD\PanelTraits\Routes;

class CrudPanel extends BackpackCrudPanel
{
    use Boxes;
    use BoxTabs;
    use Routes;

    protected $langFile;

    /**
     * Set the lang file to use
     * Used all over the CRUD interface (header, add button, reorder button, breadcrumbs).
     *
     * @param [string] Dictionary path
     */
    public function setLangFile($path)
    {
        $this->langFile = $path;
    }

    /**
     * Get the lang file
     * Used all over the CRUD interface (header, add button, reorder button, breadcrumbs).
     *
     * @return [string]
     */
    public function getLangFile()
    {
        return $this->langFile ?? 'backpack::crud';
    }
}
