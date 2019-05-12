<div class="card">
    <h5 class="card-header">
        {{ $title ?? 'No Title' }}
    </h5>
    @if(isset($prefix))
        {!! $prefix !!}
    @endif
	<table class="table async_table" data-route="{{ $async_route }}">
		<thead>
			<tr>
				@foreach($structure as $col)
					@if($col->isVisible())
						<th data-name="{{ $col->getFieldName() }}" class="{{ $col->isNumber() ? 'numeric_cell' : '' }}">{{ $col->getDisplayName() }}</th>
					@endif
				@endforeach
			</tr>
		</thead>
		<tbody>

		</tbody>
	</table>
</div>