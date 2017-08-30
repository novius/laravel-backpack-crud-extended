<!-- text input -->
<div @include('crud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
    @include('crud::inc.field_translatable_icon')

    {{-- Show the file name and a "Clear" button on EDIT form. --}}
    @if (isset($field['value']) && $field['value']!=null)
        <div class="well well-sm">
            @if (isset($field['disk']))
                <a target="_blank" href="{{ (asset(\Storage::disk($field['disk'])->url((!empty($field['prefix']) ? $field['prefix'] : '').$field['value']))) }}">
            @else
                <a target="_blank" href="{{ (asset((!empty($field['prefix']) ? $field['prefix'] : '').$field['value'])) }}">
            @endif
                    {{ $field['value'] }}
                </a>
                <a id="{{ $field['name'] }}_file_clear_button" href="#" class="btn btn-default btn-xs pull-right" title="Clear file"><i class="fa fa-remove"></i></a>
                <div class="clearfix"></div>
        </div>
    @endif

    @if (isset($field['value']) && $field['value'] !== null)
        {{-- File already exists : put the path value to hidden file  --}}
        <input
                type="hidden"
                id="{{ $field['name'] }}_file_input"
                name="{{ $field['name'] }}"
                value="{{ $field['value'] }}"
                @include('crud::inc.field_attributes', ['default_class' =>  'form-control']) />
    @else
        {{-- File not exists : display an input file --}}
        <input
                type="file"
                id="{{ $field['name'] }}_file_input"
                name="{{ $field['name'] }}"
                value="{{ isset($field['default']) ? $field['default'] : '' }}"
                @include('crud::inc.field_attributes', ['default_class' => 'form-control'])
        />
    @endif

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>

{{-- FIELD EXTRA JS --}}
{{-- push things in the after_scripts section --}}

@push('crud_fields_scripts')
    <!-- no scripts -->
    <script>
        $("#{{ $field['name'] }}_file_clear_button").click(function(e) {
            e.preventDefault();
            $(this).parent().addClass('hidden');
            // Replace input hidden by an input file and show it
            var $input = $("#{{ $field['name'] }}_file_input");
            $input.replaceWith($input.clone().attr('type', 'file').val('').removeClass('hidden'));
        });
    </script>
@endpush
