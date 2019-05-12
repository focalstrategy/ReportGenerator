$(function() {
    $('.async_table').each(function() {
        var $self = $(this);

        var columns = [];
        var init_sort = 0;
        $self.find('th').each(function(i) {
            var $th = $(this);

            var col = {name:'',title:'',orderable:true,searchable:true};
            col.title = $th.text();
            col.name = $th.data('name');
            col.orderable = $th.data('orderable');
            col.searchable = $th.data('searchable');

            if($th.data('init-sort')) {
                init_sort = i;
            }

            columns.push(col);
        });

        var dom = $('.table_search').length > 0 ? 'rtip' : 'lfrtip';

        $self.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                'url':$self.data('route'),
                'data':function ( d ) {
                    var data = $self.data('filters');
                    $.each(data,function(i,itm) {
                        d[i] = itm;
                    });
                }
            },
            pageLength: 25,
            lengthChange: false,
            orderClasses: false,
            scrollX: true,
            stateSave: true,
            stateDuration: -1,
            order: [[init_sort, 'desc']],
            "searching": true,
            columns: columns,
            dom:dom
        });
    });

    $(document).on('filter_manager:change',function(e, filters, url) {
        $('.async_table').each(function() {
            $(this).data('filters',filters);

            if($.fn.DataTable.isDataTable( $(this) ) ) {
                var dt = $(this).DataTable();

                if(filters.search_term
                    && $('.dataTables_filter input',this).val() != filters.search_term) {
                    dt.search(filters.search_term);
                }

                dt.ajax.reload();
            }
        });
    });
});