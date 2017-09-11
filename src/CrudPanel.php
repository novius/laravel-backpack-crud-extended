<?php

namespace Novius\Backpack\CRUD;

use Backpack\CRUD\CrudPanel as BackpackCrudPanel;
use Novius\Backpack\CRUD\PanelTraits\Boxes;
use Novius\Backpack\CRUD\PanelTraits\BoxTabs;

class CrudPanel extends BackpackCrudPanel
{
    use Boxes;
    use BoxTabs;

    protected $langFile;

    protected $backToAllRoute;

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

    /**
     * Set a route for back to all button
     *
     * @param $routeName
     * @param array $parameters
     * @throws \Exception
     */
    public function setBackToAllRouteRouteName($routeName, $parameters = [])
    {
        if (!\Route::has($routeName)) {
            throw new \Exception('There are no routes for this route name.', 404);
        }

        $this->backToAllRoute = route($routeName, $parameters);
    }

    /**
     * Get back to all URL
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function backtoAllUrl()
    {
        return empty($this->backToAllRoute) ? url($this->route) : url($this->backToAllRoute);
    }
}
