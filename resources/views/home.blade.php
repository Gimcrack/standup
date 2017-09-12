@extends('layouts.app')

@section('content')
<home :user="{{ Auth::user()->toJson() }}"></home>
@endsection
