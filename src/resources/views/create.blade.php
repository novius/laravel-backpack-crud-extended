@extends('backpackcrud::create')

@section('content')
	{!! Form::open(array('url' => $crud->route, 'method' => 'post', 'files'=>$crud->hasUploadFields('create'))) !!}
	  @include('crud::form_content', [ 'fields' => $crud->getFields('create'), 'action' => 'create' ])
	{!! Form::close() !!}
@endsection
