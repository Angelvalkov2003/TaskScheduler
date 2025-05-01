@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="text-center">Edit Decipher Auto Export</h2>

    <div class="card">
        <div class="card-body">
            @livewire('decipher-export-edit-form', ['task' => $task])
        </div> <!-- Card body -->
    </div> <!-- Card -->
</div>
@endsection 