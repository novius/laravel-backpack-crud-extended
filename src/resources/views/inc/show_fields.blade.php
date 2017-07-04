{{-- Show the inputs --}}
@foreach ($fields as $field)
    <!-- load the view from the application if it exists, otherwise load the one in the package -->
    @if(strpos($field['type'], '\\') !== false)
        @include((new $field['type'])->view(), array('field' => $field))
    @elseif(view()->exists('vendor.backpack.crud.fields.'.$field['type']))
        @include('vendor.backpack.crud.fields.'.$field['type'], array('field' => $field))
    @else
        @include('crud::fields.'.$field['type'], array('field' => $field))
    @endif
@endforeach
