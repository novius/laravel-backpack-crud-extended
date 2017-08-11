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
     * @param [string] Entity name, in singular. Ex: article
     * @param [string] Entity name, in plural. Ex: articles
     */
    public function setLangFile($path)
    {
        $this->lang_file = $path;
    }

    /**
     * Get the entity strings
     * Used all over the CRUD interface (header, add button, reorder button, breadcrumbs).
     *
     * @param [string] Entity name, in singular. Ex: article
     * @param [string] Entity name, in plural. Ex: articles
     * @return [array]
     */
    public function getLangFile()
    {
        return $this->lang_file ?? 'backpack::crud';
    }
}
