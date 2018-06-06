<div class="white-area-content">

    <div class="db-header clearfix">
        <div class="page-header-title"> <span class="glyphicon glyphicon-list-alt"></span> <?php echo lang("ctn_732") ?></div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <h4 class="home-label"><?php echo lang("ctn_979") ?></h4>
            <div class="demo-container">
                <div id="pivotgrid-demo">
                    <div id="pivotgrid-chart"></div>
                    <div id="pivotgrid"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var total = [<?php echo $results; ?>];
    $(function () {
        var pivotGridChart = $("#pivotgrid-chart").dxChart({
            commonSeriesSettings: {
                type: "bar"
            },
            tooltip: {
                enabled: true,
                //format: "currency",
                customizeTooltip: function (args) {
                    return {
                        html:  args.seriesName + " - years old | Total<div /*class='currency'*/>" + args.valueText + "</div>"
                    };
                }
            },
            size: {
                height: 200
            },
            adaptiveLayout: {
                width: 450
            }
        }).dxChart("instance");

        var pivotGrid = $("#pivotgrid").dxPivotGrid({
            allowSortingBySummary: true,
            allowFiltering: true,
            showBorders: true,
            showColumnGrandTotals: false,
            showRowGrandTotals: false,
            showRowTotals: false,
            showColumnTotals: false,
            fieldChooser: {
                enabled: true,
                height: 400
            },
            dataSource: {
                fields: [
                    {
                        caption: "City",
                        width: 120,
                        dataField: "city",
                        area: "row",
                        sortBySummaryField: "Total"
                    }, 
                    {
                        caption: "Region",
                        width: 120,
                        dataField: "region",
                        area: "row"
                    },{
                        caption: "Sex",
                        width: 60,
                        dataField: "sex",
                        area: "row"
                    }, {
                        caption: "Title",
                        width: 120,
                        dataField: "social",
                        area: "row"
                    },{
                        caption: "Age",
                        dataField: "age",
                        width: 50,
                        area: "row"
                    },{
                        caption: "Survey",
                        dataField: "survey",
                        width: 60,
                        area: "row"
                    }, {
                        caption: "Year",
                        dataField: "year",
                        //dataType: "date",
                        area: "column"
                    }, {
                        dataField: "category",
                        //dataType: "date",
                        area: "column"
                    }, /*{
                        groupName: "class",
                        groupInterval: "class",
                        visible: false
                    },*/
                    {
                        caption: "Class",
                        dataField: "class",
                        //dataType: "date",
                        area: "column"
                    },{
                        caption: "Total",
                        dataField: "city",
                        //dataType: "number",
                        //summaryType: "sum",
                        postProcess: "groupByCity",
                        //format: "currency",
                        area: "data"
                    }],
                store: total
            }
        }).dxPivotGrid("instance");

        pivotGrid.bindChart(pivotGridChart, {
            dataFieldsDisplayMode: "splitPanes",
            alternateDataFields: false
        });

        function expand() {
            var dataSource = pivotGrid.getDataSource();
            dataSource.expandHeaderItem("row", [""]);
            //dataSource.expandHeaderItem("column", ["Default Category"]);
        }

        setTimeout(expand, 0);
    });
</script>