<?php

namespace Novius\Backpack\CRUD\PanelTraits;

trait BoxTabs
{
    public function boxHasTabs(string $boxLabel)
    {
        return $this->tabsEnabled() && count($this->getBoxTabs($boxLabel)) > 0;
    }

    public function boxTabExists(string $boxLabel, $tabLabel)
    {
        $tabs = $this->getBoxTabs($boxLabel);
        return in_array($tabLabel, $tabs);
    }

    public function getLastBoxTab(string $boxLabel)
    {
        $tabs = $this->getBoxTabs($boxLabel);
        if (count($tabs)) {
            return last($tabs);
        }
        return false;
    }

    public function isLastBoxTab(string $boxLabel, string $tabLabel)
    {
        return $this->getLastBoxTab($boxLabel) === $tabLabel;
    }

    public function getBoxTabFields($boxLabel, $tabLabel)
    {
        if ($this->boxExists($boxLabel) && $this->boxTabExists($boxLabel, $tabLabel)) {
            $boxFields = $this->getBoxFields($boxLabel);
            $fields_for_current_tab = collect($boxFields)->filter(function ($value, $key) use ($tabLabel) {
                return isset($value['tab']) && $value['tab'] == $tabLabel;
            });
            if ($this->isLastBoxTab($boxLabel, $tabLabel)) {
                $fields_without_a_tab = collect($boxFields)->filter(function ($value, $key) {
                    return ! isset($value['tab']);
                });
                $fields_for_current_tab = $fields_for_current_tab->merge($fields_without_a_tab);
            }
            return $fields_for_current_tab;
        }
        return [];
    }

    public function getBoxTabs(string $boxLabel)
    {
        $tabs = [];
        $fields = $this->getBoxFields($boxLabel);
        collect($fields)
            ->filter(function ($value, $key) {
                return isset($value['tab']);
            })
            ->each(function ($value, $key) use (&$tabs) {
                if (! in_array($value['tab'], $tabs)) {
                    $tabs[] = $value['tab'];
                }
            });
        return $tabs;
    }
}
