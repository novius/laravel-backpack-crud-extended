@extends('backpackcrud::edit')

@section('header')
    <section class="content-header">
        <h1>
            {{ trans('backpack::crud.edit') }} <span>{{ $crud->entity_name }}</span>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('backpack.base.route_prefix'),'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
            <li><a href="{{ url($crud->indexRoute()) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>
            <li class="active">{{ trans('backpack::crud.edit') }}</li>
        </ol>
    </section>
@endsection

@section('content')
	{!! Form::open(array('url' => $crud->route.'/'.$entry->getKey(), 'method' => 'put', 'files'=>$crud->hasUploadFields('update', $entry->getKey()))) !!}
	  @include('crud::form_content', ['fields' => $fields, 'action' => 'edit', 'entry' => $entry])
	{!! Form::close() !!}
@endsection
