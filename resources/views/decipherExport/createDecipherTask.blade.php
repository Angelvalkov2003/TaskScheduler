@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="text-center">Decipher Auto Export Create Page</h2>

    <div class="card">
        <div class="card-body">
            @livewire('decipher-export-form')
        </div> <!-- Card body -->
    </div> <!-- Card -->
</div>
@endsection
