<?php

namespace Novius\Backpack\CRUD\Contracts;

interface Field
{
    /**
     * What view should this field use?
     * eg: 'crud::fields.text'
     *
     * @return string
     */
    public function view();
}
