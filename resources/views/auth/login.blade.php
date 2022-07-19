@extends('layouts.preapp')
@section('title')
   {{ __('page-titles.login') }} | {{ 'Connects Staff' }}
@endsection
@section('content')
<div>
    <div class="account-pages my-5 pt-5">
      <div class="container">
        <login submit-url="{{ route('login') }}" auth-error="{{ $errors->first() }}">
            @csrf
        </login>
      </div>
    </div>
  </div>
@endsection
