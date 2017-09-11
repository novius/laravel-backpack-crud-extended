<?php

namespace Novius\Backpack\CRUD\PanelTraits;

trait Routes
{
    protected $indexRoute;
    protected $reorderRoute;

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
    public function indexRoute(): string
    {
        return $this->indexRoute ?? $this->route;
    }

    /**
     * Set a reorder route
     *
     * @param $routeName
     * @param array $parameters
     * @throws \Exception
     */
    public function setReorderRoute($routeName, $parameters = [])
    {
        if (!\Route::has($routeName)) {
            throw new \Exception('There are no routes for this route name.', 404);
        }

        $this->reorderRoute = route($routeName, $parameters);
    }

    /**
     * Get the reorder route
     *
     * @return string
     */
    public function reorderRoute(): string
    {
        return $this->reorderRoute ?? $this->route.'/reorder';
    }
}
