@extends("layout")
@section('siederbar')

@endsection

@section('title', 'Historique des callbacks')

@section('content')
    <h1>Historique des callbacks MoMo</h1>
    <table class="table" border="1" cellpadding="5">
        <thead>
        <tr>
            <th>ID</th>
            <th>Référence</th>
            <th>Status</th>
            <th>Montant</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach($callbacks as $cb)
            <tr>
                <td>{{ $cb->id }}</td>
                <td>{{ $cb->reference_id }}</td>
                <td>{{ $cb->status }}</td>
                <td>{{ $cb->amount }}</td>
                <td>{{ $cb->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $callbacks->links() }}
@endsection
