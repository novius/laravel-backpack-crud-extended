<?php

namespace Novius\Backpack\CRUD\PanelTraits;

trait Routes
{
    protected $indexRoute;

    /**
     * Set a custom index route
     *
     * @param $routeName
     * @param array $parameters
     * @throws \Exception
     */
    public function setIndexRoute($routeName, $parameters = [])
    {
        if (!\Route::has($routeName)) {
            throw new \Exception('There are no routes for this route name.', 404);
        }

        $this->indexRoute = route($routeName, $parameters);
    }

    /**
     * Get the index route
     *
     * @return string
     */
    public function indexRoute()
    {
        return empty($this->indexRoute) ? $this->route : $this->indexRoute;
    }
}
