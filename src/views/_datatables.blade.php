$(document).ready(function() {
    $('#table_div').dataTable( {
        "processing": true,
        "serverSide": true,
        "bFilter": false,
        "ajax": "{{$datatables_ajax_route}}",
        "columnDefs": [ {
            "targets": "_all",
            "defaultContent": ""
        } ],
        "columns": [
            <?php echo $datatables_columns; ?>
        ]
    } );
} );
