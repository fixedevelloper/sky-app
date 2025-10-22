@props(['direction' => 'asc'])
@if($direction === 'asc')
    <span class="text-primary ms-1">&#9650;</span> {{-- flèche vers le haut --}}
@else
    <span class="text-primary ms-1">&#9660;</span> {{-- flèche vers le bas --}}
@endif


