@if ($crud->model->translationEnabled())
    <input type="hidden" name="locale" value={{ $crud->request->input('locale')?$crud->request->input('locale'):App::getLocale() }}>
@endif

<div class="row">
    <div class="{{ $crud->sideBoxesEnabled() ? 'col-md-12' : 'col-md-8 col-md-offset-2' }}">
        <!-- Default box -->
        @if ($crud->hasAccess('list'))
            <a href="{{ url($crud->indexRoute()) }}"><i class="fa fa-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a><br><br>
        @endif

        @if ($crud->model->translationEnabled())
        <!-- Single button -->
            <div class="btn-group pull-right">
                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{trans('backpack::crud.language')}}: {{ $crud->model->getAvailableLocales()[$crud->request->input('locale')?$crud->request->input('locale'):App::getLocale()] }} <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    @foreach ($crud->model->getAvailableLocales() as $key => $locale)
                        @if ($action === 'edit')
                            <li><a href="{{ url($crud->route.'/'.$entry->getKey().'/edit') }}?locale={{ $key }}">{{ $locale }}</a></li>
                        @else
                            <li><a href="{{ url($crud->route.'/create') }}?locale={{ $key }}">{{ $locale }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif


        @include('crud::inc.grouped_errors')
    </div>
</div>

<div class="row">
    <div class="col-md-8 {{ $crud->sideBoxesEnabled() ? '' : 'col-md-offset-2' }}">
        @foreach ($crud->getBoxes('content') as $k => $box)
            <div class="box {{ $crud->getBoxOptions($box)['class'] }}">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        {{ $box }}
                    </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-{{ $crud->getBoxOptions($box)['collapsed'] ? 'plus' : 'minus' }}"></i></button>
                    </div>
                </div>
                <div class="box-body row">
                    {{-- See if we're using tabs --}}
                    @if ($crud->boxHasTabs($box))
                        @include('crud::inc.show_tabbed_fields', ['fields' => $fields, 'box' => $box])
                    @else
                        @include('crud::inc.show_fields', ['fields' => $fields, 'boxFields' => $crud->getBoxFields($box)])
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @if ($crud->sideBoxesEnabled())
        <div class="col-md-4">
            @foreach ($crud->getBoxes('side') as $k => $box)
                <div class="box {{ $crud->getBoxOptions($box)['class'] }}">
                    <div class="box-header">
                        <h3 class="box-title">{{ $box }}</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-{{ $crud->getBoxOptions($box)['collapsed'] ? 'plus' : 'minus' }}"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        @include('crud::inc.show_fields', ['fields' => $fields, 'boxFields' => $crud->getBoxFields($box)])
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="row">
    <div class="{{ $crud->sideBoxesEnabled() ? 'col-md-12' : 'col-md-8 col-md-offset-2' }}">
        @include('crud::inc.form_save_buttons')
    </div>
</div>

{{-- Define blade stacks so css and js can be pushed from the fields to these sections. --}}

@section('after_styles')
    <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/crud.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/'.$action.'.css') }}">

    <!-- CRUD FORM CONTENT - crud_fields_styles stack -->
    @stack('crud_fields_styles')
@endsection

@section('after_scripts')
    <script src="{{ asset('vendor/backpack/crud/js/crud.js') }}"></script>
    <script src="{{ asset('vendor/backpack/crud/js/form.js') }}"></script>
    <script src="{{ asset('vendor/backpack/crud/js/'.$action.'.js') }}"></script>

    <!-- CRUD FORM CONTENT - crud_fields_scripts stack -->
    @stack('crud_fields_scripts')

    <script>
        jQuery('document').ready(function($){

            // Save button has multiple actions: save and exit, save and edit, save and new
            var saveActions = $('#saveActions'),
                crudForm        = saveActions.parents('form'),
                saveActionField = $('[name="save_action"]');

            saveActions.on('click', '.dropdown-menu a', function(){
                var saveAction = $(this).data('value');
                saveActionField.val( saveAction );
                crudForm.submit();
            });

            // Ctrl+S and Cmd+S trigger Save button click
            $(document).keydown(function(e) {
                if ((e.which == '115' || e.which == '83' ) && (e.ctrlKey || e.metaKey))
                {
                    e.preventDefault();
                    // alert("Ctrl-s pressed");
                    $("button[type=submit]").trigger('click');
                    return false;
                }
                return true;
            });

            // Place the focus on the first element in the form
            @if( $crud->autoFocusOnFirstField )
                    @php
                        $focusField = array_first($fields, function($field) {
                            return isset($field['auto_focus']) && $field['auto_focus'] == true;
                        });
                    @endphp

                    @if ($focusField)
                window.focusField = $('[name="{{ $focusField['name'] }}"]').eq(0),
                    @else
            var focusField = $('form').find('input, textarea, select').not('[type="hidden"]').eq(0),
                    @endif

                    fieldOffset = focusField.offset().top,
                scrollTolerance = $(window).height() / 2;

            focusField.trigger('focus');

            if( fieldOffset > scrollTolerance ){
                $('html, body').animate({scrollTop: (fieldOffset - 30)});
            }
            @endif

            // Add inline errors to the DOM
            @if ($crud->inlineErrorsEnabled() && $errors->any())

                window.errors = {!! json_encode($errors->messages()) !!};
            // console.error(window.errors);

            $.each(errors, function(property, messages){

                var field = $('[name="' + property + '[]"]').length ?
                        $('[name="' + property + '[]"]') :
                        $('[name="' + property + '"]'),
                    container = field.parents('.form-group');

                console.log(field);

                container.addClass('has-error');

                $.each(messages, function(key, msg){
                    // highlight the input that errored
                    var row = $('<div class="help-block">' + msg + '</div>');
                    row.appendTo(container);

                    // highlight its parent tab
                            @if ($crud->tabsEnabled())
                    var tab_id = $(container).parent().attr('id');
                    $("#form_tabs [aria-controls="+tab_id+"]").addClass('text-red');
                    @endif
                });
            });

            @endif

        });
    </script>
@endsection
