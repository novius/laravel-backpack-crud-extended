<?php

namespace Novius\Backpack\CRUD;

use \Backpack\CRUD\CrudPanel as BackpackCrudPanel;

class CrudPanel extends BackpackCrudPanel
{
    protected $lang_file;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set the lang file to use
     * Used all over the CRUD interface (header, add button, reorder button, breadcrumbs).
     *
     * @param [string] Dictionary path
     */
    public function setLangFile($path)
    {
        $this->lang_file = $path;
    }

    /**
     * Get the lang file
     * Used all over the CRUD interface (header, add button, reorder button, breadcrumbs).
     *
     * @return [string]
     */
    public function getLangFile()
    {
        return $this->lang_file ?? 'backpack::crud';
    }
}
