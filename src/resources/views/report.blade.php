@if (isset($view_object_class_name) && config('app.debug'))
<!-- Report Generator: VO = {{ $view_object_class_name }} -->
@endif

<div class="card">
    <h5 class="card-header">
        {{ $title ?? 'No Title' }}
    </h5>
    <div class="table-responsive">
        <table data-pageLength="{{ isset($page_length) ? $page_length : 25 }}" class="table table-bordered table-hover datatable {{ isset($checkbox_select) ? 'checkbox_select' : '' }}">
            <thead>
                <tr>
                    @if (isset($checkbox_select))
                    <th class='checkbox_select'></th>
                    @endif
                    @foreach($structure as $col)
                        @if($col->isVisible())
                            <th class="{{ $col->isNumber() ? 'numeric_cell' : '' }}">{{ $col->getDisplayName() }}</th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                <tr class="{{ $row->row_class or '' }}" {{ $row->row_data_attribute}}>
                        @if (isset($checkbox_select))
                        <td></td>
                        @endif
                        @foreach($structure as $col)
                            @if($col->isVisible())
                                @if($row->{$col->getFieldName()} != null)
                                    {!! $row->{$col->getFieldName()} !!}
                                @endif
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
            @if (!empty($aggregate) && count($aggregate) > 0)
            <tfoot>
                @foreach($aggregate as $ag)
                <tr>
                    @if (isset($checkbox_select))
                    <th></th>
                    @endif
                    <th class="numeric_cell">
                        <strong>
                            {!! isset($ag['title']) ? str_replace(' ','&nbsp;', $ag['title']) : '' !!}
                        </strong>
                    </th>
                    @foreach($structure as $col)
                        @if($ag['skip'] != $col->getFieldName())
                            @if($col->isVisible())
                                {!! $ag['data'][$col->getFieldName()] ?? '<th />' !!}
                            @endif
                        @endif
                    @endforeach
                </tr>
                @endforeach
            </tfoot>
            @endif
        </table>
    </div>
</div>
