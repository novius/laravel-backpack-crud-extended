@extends('backpackcrud::create')

@section('header')
    <section class="content-header">
        <h1>
            {{ trans('backpack::crud.add') }} <span>{{ $crud->entity_name }}</span>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
            <li><a href="{{ url($crud->indexRoute()) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>
            <li class="active">{{ trans('backpack::crud.add') }}</li>
        </ol>
    </section>
@endsection

@section('content')
	{!! Form::open(array('url' => $crud->route, 'method' => 'post', 'files'=>$crud->hasUploadFields('create'))) !!}
	  @include('crud::form_content', [ 'fields' => $crud->getFields('create'), 'action' => 'create' ])
	{!! Form::close() !!}
@endsection
