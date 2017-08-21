@extends('backpackcrud::edit')

@section('content')
	{!! Form::open(array('url' => $crud->route.'/'.$entry->getKey(), 'method' => 'put', 'files'=>$crud->hasUploadFields('update', $entry->getKey()))) !!}
	  @include('crud::form_content', ['fields' => $fields, 'action' => 'edit', 'entry' => $entry])
	{!! Form::close() !!}
@endsection
