<div class="white-area-content">

    <div class="db-header clearfix">
        <div class="page-header-title"> <span class="glyphicon glyphicon-education"></span> <?php echo lang("ctn_481") ?></div>
        <div class="db-header-extra form-inline"> 

            <div class="form-group has-feedback no-margin">
                <div class="input-group">
                    <input type="text" class="form-control input-sm" placeholder="<?php echo lang("ctn_336") ?>" id="form-search-input" />
                    <div class="input-group-btn">
                        <input type="hidden" id="search_type" value="0">
                        <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                        <ul class="dropdown-menu small-text" style="min-width: 90px !important; left: -90px;">
                            <li><a href="#" onclick="change_search(0)"><span class="glyphicon glyphicon-ok" id="search-like"></span> <?php echo lang("ctn_337") ?></a></li>
                            <li><a href="#" onclick="change_search(1)"><span class="glyphicon glyphicon-ok no-display" id="search-exact"></span> <?php echo lang("ctn_338") ?></a></li>
                            <li><a href="#" onclick="change_search(2)"><span class="glyphicon glyphicon-ok no-display" id="username-exact"></span> <?php echo lang("ctn_25") ?></a></li>
                            <li><a href="#" onclick="change_search(3)"><span class="glyphicon glyphicon-ok no-display" id="fn-exact"></span> <?php echo lang("ctn_29") ?></a></li>
                            <li><a href="#" onclick="change_search(4)"><span class="glyphicon glyphicon-ok no-display" id="ln-exact"></span> <?php echo lang("ctn_30") ?></a></li>
                            <li><a href="#" onclick="change_search(5)"><span class="glyphicon glyphicon-ok no-display" id="email-exact"></span> <?php echo lang("ctn_78") ?></a></li>
                        </ul>
                    </div><!-- /btn-group -->
                </div>
            </div>

            <?php if ($this->common->has_permissions(array("admin", "student_manager", "reception_manager"), $this->user)) : ?>
                <a href="<?php echo site_url("students/add_student") ?>" class="btn btn-primary btn-sm"><?php echo lang("ctn_765") ?></a>
            <?php endif; ?>

        </div>
    </div>


    <div class="table-responsive">
        <table id="student-table" class="table table-striped table-hover table-bordered">
            <thead>
                <tr class="table-header">
                    <td><?php echo lang("ctn_339") ?></td>
                    <td><?php echo lang("ctn_1021") ?></td>
                    <td><?php echo lang("ctn_78") ?></td>
                    <td><?php echo lang("ctn_52") ?></td>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>


</div>

<?php if ($this->common->has_permissions(array("admin", "class_manager", "reception_manager"), $this->user)) : ?>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-send"></span> <?php echo lang("ctn_504") ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo form_open(site_url("classes/add_student_to_class"), array("class" => "form-horizontal")) ?>
                    <input type="hidden" name="student_id" id="student_id" value="" />
                    <div class="form-group">
                        <label for="inputClass" class="col-sm-2 control-label"><?php echo lang("ctn_471") ?></label>
                        <div class="col-sm-8">
                            <select name="class_id" class="form-control">
                                <option value="" class="text-center"><?php echo lang('ctn_1029'); ?></option>
                                <?php foreach ($classes->result() as $r) : ?>
                                    <?php if($r->class_days === 'odd'){ 
                                        $day = '1,3,5'; 
                                    }elseif ($r->class_days === 'even') {
                                        $day = '2,4,6'; 
                                    }else{
                                        $day = '1-6';
                                    } ?>
                                <option value="<?php echo $r->ID ?>"><?php echo $r->cat_name . " / " . $r->branch_name . " (Room: " . $r->room_code . ") / " . $r->name . " / " . $day . " / " . substr($r->start_hour, 0, 5) . " - " . substr($r->end_hour, 0, 5)?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>
                    <input type="submit" class="btn btn-primary" value="<?php echo lang("ctn_504") ?>">
                    <?php echo form_close() ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<script type="text/javascript">
    $(document).on("click", ".addStudentDialog", function () {
     var studentId = $(this).data('id');
     $(".modal-body #student_id").val( studentId );
});
</script>
<script type="text/javascript">
    $(document).ready(function () {

        var st = $('#search_type').val();
        var table = $('#student-table').DataTable({
            "dom": "B<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "processing": false,
            "pagingType": "full_numbers",
            "pageLength": 15,
            buttons: [
                {"extend": 'copy', "text": '<?php echo lang("ctn_911") ?>', "className": 'btn btn-default btn-sm'},
                {"extend": 'csv', "text": '<?php echo lang("ctn_912") ?>', "className": 'btn btn-default btn-sm'},
                {"extend": 'excel', "text": '<?php echo lang("ctn_913") ?>', "className": 'btn btn-default btn-sm'},
                {"extend": 'pdf', "text": '<?php echo lang("ctn_914") ?>', "className": 'btn btn-default btn-sm'},
                {"extend": 'print', "text": '<?php echo lang("ctn_915") ?>', "className": 'btn btn-default btn-sm'}
            ],
            "serverSide": true,
            "orderMulti": false,
            "order": [
            ],
            "columns": [
                null,
                {"orderable": false},
                null,
                {"orderable": false}
            ],
            "ajax": {
                url: "<?php echo site_url("students/student_page") ?>",
                type: 'GET',
                data: function (d) {
                    d.search_type = $('#search_type').val();
                }
            },
            "drawCallback": function (settings, json) {
                $('[data-toggle="tooltip"]').tooltip();
            },
            "createdRow": function( row, data, dataIndex){
                if( data[4] ==  `1`){
                    $(row).addClass('black_list');
                }
            }
        });
        $('#form-search-input').on('keyup change', function () {
            table.search(this.value).draw();
        });

    });
    function change_search(search)
    {
        var options = [
            "search-like",
            "search-exact",
            "username-exact",
            "fn-exact",
            "ln-exact",
            "email-exact"
        ];
        set_search_icon(options[search], options);
        $('#search_type').val(search);
        $("#form-search-input").trigger("change");
    }

    function set_search_icon(icon, options)
    {
        for (var i = 0; i < options.length; i++) {
            if (options[i] == icon) {
                $('#' + icon).fadeIn(10);
            } else {
                $('#' + options[i]).fadeOut(10);
            }
        }
    }
</script>