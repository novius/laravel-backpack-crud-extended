@extends('backpackcrud::reorder')
<?php
    if (isset($reorder_filter_callback) && is_callable($reorder_filter_callback)) {
        $entries = $entries->filter($reorder_filter_callback);
    }
?>

@section('header')
    <section class="content-header">
        <h1>
            <span class="text-capitalize">{{ $crud->entity_name_plural }}</span>
            <small>{{ trans('backpack::crud.all') }} <span>{{ $crud->entity_name_plural }}</span> {{ trans('backpack::crud.in_the_database') }}.</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
            <li><a href="{{ url($crud->indexRoute()) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>
            <li class="active">{{ trans('backpack::crud.reorder') }}</li>
        </ol>
    </section>
@endsection

@section('content')
    <?php
    $treeElementFunction = function ($entry, $key, $all_entries, $crud) use (&$treeElementFunction) {
        if (!isset($entry->tree_element_shown)) {
            // mark the element as shown
            $all_entries[$key]->tree_element_shown = true;
            $entry->tree_element_shown = true;

            // show the tree element
            echo '<li id="list_'.$entry->getKey().'">';
            echo '<div><span class="disclose"><span></span></span>'.object_get($entry, $crud->reorder_label).'</div>';

            // see if this element has any children
            $children = [];
            foreach ($all_entries as $key => $subentry) {
                if ($subentry->parent_id == $entry->getKey()) {
                    $children[] = $subentry;
                }
            }

            $children = collect($children)->sortBy('lft');

            // if it does have children, show them
            if (count($children)) {
                echo '<ol>';
                foreach ($children as $key => $child) {
                    $children[$key] = $treeElementFunction($child, $child->getKey(), $all_entries, $crud);
                }
                echo '</ol>';
            }
            echo '</li>';
        }

        return $entry;
    }

    ?>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @if ($crud->hasAccess('list'))
                <a href="{{ url($crud->indexRoute()) }}"><i class="fa fa-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a><br><br>
        @endif

        <!-- Default box -->
            <div class="box">

                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('backpack::crud.reorder').' '.$crud->entity_name_plural }}</h3>
                </div>

                <div class="box-body">

                    <p>{{ trans('backpack::crud.reorder_text') }}</p>

                    <ol class="sortable">
                        <?php
                        $all_entries = collect($entries->all())->sortBy('lft')->keyBy($crud->getModel()->getKeyName());
                        $root_entries = $all_entries->filter(function ($item) {
                            return $item->parent_id == 0;
                        });
                        foreach ($root_entries as $key => $entry) {
                            $root_entries[$key] = $treeElementFunction($entry, $key, $all_entries, $crud);
                        }
                        ?>
                    </ol>

                    <button id="toArray" class="btn btn-success ladda-button" data-style="zoom-in"><span class="ladda-label"><i class="fa fa-save"></i> {{ trans('backpack::crud.save') }}</span></button>

                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
    </div>
@endsection
