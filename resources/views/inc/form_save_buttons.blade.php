<div id="saveActions" class="form-group">

    <input type="hidden" name="save_action" value="{{ $saveAction['active']['value'] }}">

    <div class="btn-group">

        <button type="submit" class="btn btn-success">
            <span class="fa fa-save" role="presentation" aria-hidden="true"></span> &nbsp;
            <span data-value="{{ $saveAction['active']['value'] }}">{{ $saveAction['active']['label'] }}</span>
        </button>

        @if(!empty($saveAction['options']))
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aira-expanded="false">
                <span class="caret"></span>
                <span class="sr-only">Toggle Save Dropdown</span>
            </button>

            <ul class="dropdown-menu">
                @foreach( $saveAction['options'] as $value => $label)
                    <li><a href="javascript:void(0);" data-value="{{ $value }}">{{ $label }}</a></li>
                @endforeach
            </ul>
        @endif
    </div>

    <a href="{{ url($crud->indexRoute()) }}" class="btn btn-default"><span class="fa fa-ban"></span> &nbsp;{{ trans('backpack::crud.cancel') }}</a>
</div>
