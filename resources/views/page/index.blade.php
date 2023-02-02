@extends('layouts.main')

@section('content')

    <div id="app">
        <index-component :key_data="{{json_encode($key)}}"></index-component>
    </div>

@endsection