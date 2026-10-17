<div class="alert alert-info" role="status">
    <p>Some front sheet records have not been initialized. Viewing this page does not create them. Existing saved values are retained.</p>
    @can('comparison.update')
        <form action="{{ route('comparison.update', ['comparison' => $quotation->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="initialize_front_sheet" value="1">
            <button type="submit" class="btn btn-primary">Initialize front sheet</button>
        </form>
    @else
        <p class="mb-0">A user with permission to update comparisons can initialize this front sheet.</p>
    @endcan
</div>
