'use strict';

$(document).ready(function(){

    $('#mybutton').click(function() {
        OrderGrid();
    });

    function OrderGrid(){
        $('#jqGrid').jqGrid('setGridParam', {sortname: 'status_priority', sortorder: 'desc'}).trigger('reloadGrid', [{page: 1}]);
    }

    var CURRENT_VERSION   = 1;   
    /* ORDER STATUS */
    var ORDER_PREPARATION = 13;
    var ORDER_DELIVERY    = 14;
    var ORDER_COMPLETED   = 15;
    var ORDER_CANCELED    = 19;
    var current_edit_row  = null;
    var GRID_DATA         = [];

    
    function loadInitialLoad(){                   
          $.ajax({
                type: "POST",
                url:  "monitor/currentorders", 
                dataType: "jsonp",
                async: false,
                global: true,
                success: function(result,status,xhr){
                  CURRENT_VERSION  = result.version;        
                  GRID_DATA        = result.rows;     
                  console.info(GRID_DATA);    
                  $('#jqGrid').jqGrid('setGridParam', { datatype: 'local', data: GRID_DATA }).trigger("reloadGrid");
                }
            });
    }

    function searchNewOrders(){
         $.ajax({
                type: "POST",
                url:  "monitor/currentorders", 
                data: {
                    version : CURRENT_VERSION,
                    isfromtimer : true
                },
                dataType: "json",
                success: function(data){
                   console.info(data);
                   if(CURRENT_VERSION==data.version){
                     console.info('sin cambios');
                   }else{
                     console.info('Con cambios');
                     CURRENT_VERSION=data.version;
                     //insert fisrt
                     for(var i = 0; i< data.rows.length;i++){
                        if( data.rows[i].actionv =='insert'){
                            $("#jqGrid").addRowData(data.rows[i].order_id, {
                                order_id     : data.rows[i].order_id,
                                first_name   : data.rows[i].first_name,
                                order_status : data.rows[i].order_status,
                                order_type   : data.rows[i].order_type,
                                payment      : data.rows[i].payment,  
                                order_total  : data.rows[i].order_total,
                                order_time   : data.rows[i].order_time,
                                order_date   : data.rows[i].order_date,
                                status_color : data.rows[i].status_color,
                                order_status_read : data.rows[i].order_status_read,
                                status_priority   : data.rows[i].status_priority
                            }, "first");
                            insertActionsButtos(data.rows[i].order_id);
                        }
                     }

                     for(var i = 0; i< data.rows.length;i++){
                        if( data.rows[i].actionv =='update'){
                            // Si se termino
                            if(data.rows[i].status_code == ORDER_CANCELED || data.rows[i].status_code == ORDER_COMPLETED){
                                  $("#jqGrid").delRowData(data.rows[i].order_id);
                            }else{
                                // update                                 
                                  $("#jqGrid").setRowData(data.rows[i].order_id, {
                                        first_name        : data.rows[i].first_name,
                                        order_status      : data.rows[i].order_status,
                                        order_type        : data.rows[i].order_type,
                                        payment           : data.rows[i].payment,  
                                        order_total       : data.rows[i].order_total,
                                        order_time        : data.rows[i].order_time,
                                        order_date        : data.rows[i].order_date,
                                        status_color      : data.rows[i].status_color,
                                        order_status_read : data.rows[i].order_status_read,
                                        status_priority   : data.rows[i].status_priority
                                 }, "first");

                                 if(current_edit_row==data.rows[i].order_id){
                                     current_edit_row = null;
                                     console.log('Quitar el modo edicion',current_edit_row);
                                 }
                            }
                        }
                     }

                     if(current_edit_row == null){
                        OrderGrid();
                     }
                       
                   }
                } 
             });
        
         updateOrderLeaftTimes();
    }

    setInterval(searchNewOrders,10000); 


    function updateOrderLeaftTimes(){   
        var rows = $("#jqGrid").getDataIDs();
        for(var a=0;a<rows.length;a++)
        {
              if(current_edit_row == rows[a] ){
                 break;
              }
              var row=$("#jqGrid").getRowData(rows[a]);              
              $("#jqGrid").setRowData(row.order_id, {
                 deadline          : row.deadline,
                 order_id          : row.order_id,
                 order_time        : row.order_time,
                 order_date        : row.order_date,  
                 order_status_read : row.order_status_read,
                 status_priority   : row.status_priority                               
              });             
        }
    }

    $('#SelectMarkAs').on('change', function(ev) {
        var status =  this.value;
        var myGrid = $("#jqGrid");
        var selRowIds = myGrid.jqGrid ('getGridParam', 'selarrrow');       
        if(selRowIds.length>0){
            chageOrderStatus(selRowIds,status);
            $('#SelectMarkAs').val(-1);
        }else{
            ev.preventDefault();
            $('#SelectMarkAs').val(-1);
            return false;
        }

    })
    
    function chageOrderStatus(orderArray,status){
       var orders_array_str = "";
       for(var i = 0;i<orderArray.length;i++){
          if(i+1==orderArray.length){
               orders_array_str= orders_array_str +orderArray[i]; 
          }else{
              orders_array_str= orders_array_str +orderArray[i]+","; 
          }         
       }
       $.ajax({
                url: "monitor/changeOrderStatus",
                type: "POST",
                dataType: "json",
                data:{
                     orders : orders_array_str,                     
                     status : status
                },
                success: function (html) {   
                  searchNewOrders();
                }
         });
    }
    
    function redirectToEdit(OrderID){
         window.location.replace("../admin/orders/edit?id="+OrderID+"&from=monitor");
    }

    function insertActionsButtos(OrderID){        
          var grid = $("#jqGrid");
          var iCol = getColumnIndexByName(grid,'actions');                          
          var el   = $(grid).find(">tbody>tr.jqgrow>td:nth-child(" + (iCol + 1) + ")");
          
          /*Delete*/
          $("<div>",{title: "Your order has been canceled",
          mouseover: function() { $(this).addClass('ui-state-hover'); },
          mouseout: function() {$(this).removeClass('ui-state-hover'); },
          click: function(e) {
              var orderArray = [];
              orderArray.push(OrderID);
              chageOrderStatus(orderArray,ORDER_CANCELED);
          }}
          ).css({"margin-right": "5px", float: "left", cursor: "pointer"})
          .addClass("ui-pg-div ui-inline-edit")  //custom
          .append('<span class="glyphicon glyphicon-trash actions-incons"></span>')
          .prependTo($(el[0]).children("div"));


          /* Ready */ 
          $("<div>",{title: "Your order wil be with you shortly",
          mouseover: function() { $(this).addClass('ui-state-hover'); },
          mouseout: function() {$(this).removeClass('ui-state-hover'); },
          click: function(e) {
              var orderArray = [];
              orderArray.push(OrderID);
              chageOrderStatus(orderArray,ORDER_DELIVERY);
          }}
          ).css({"margin-right": "5px", float: "left", cursor: "pointer"})
          .addClass("ui-pg-div ui-inline-edit")  //custom
          .append('<span class="glyphicon glyphicon-ok-circle actions-incons"></span>')
          .prependTo($(el[0]).children("div"));

            /*Send*/
          $("<div>",{title: "Your order has been completed",
          mouseover: function() { $(this).addClass('ui-state-hover'); },
          mouseout: function() {$(this).removeClass('ui-state-hover'); },
          click: function(e) {
              var orderArray = [];
              orderArray.push(OrderID);
              chageOrderStatus(orderArray,ORDER_COMPLETED);
              $('#'+OrderID).removeClass("danger");            

          }}
          ).css({"margin-right": "5px", float: "left", cursor: "pointer"})
          .addClass("ui-pg-div ui-inline-edit")  //custom
          .append('<span class="glyphicon glyphicon-send actions-incons"></span>')
          .prependTo($(el[0]).children("div"));

            /*Fire*/
          $("<div>",{title: "Your order is being prepared",
          mouseover: function() { $(this).addClass('ui-state-hover'); },
          mouseout: function() {$(this).removeClass('ui-state-hover'); },
          click: function(e) {
              var orderArray = [];
              orderArray.push(OrderID);
              chageOrderStatus(orderArray,ORDER_PREPARATION);
          }}
          ).css({"margin-right": "5px", float: "left", cursor: "pointer"})
          .addClass("ui-pg-div ui-inline-edit")  //custom
          .append('<span class="glyphicon glyphicon-fire actions-incons"></span>')
          .prependTo($(el[0]).children("div"));

      

          /*Edit*/
          $("<div>",{title: "Edit you order",
          mouseover: function() { $(this).addClass('ui-state-hover'); },
          mouseout: function() {$(this).removeClass('ui-state-hover'); },
          click: function(e) {
             redirectToEdit(OrderID);
          }}
          ).css({"margin-right": "5px", float: "left", cursor: "pointer"})
          .addClass("ui-pg-div ui-inline-edit")  //custom
          .append('<span class="glyphicon glyphicon-pencil actions-incons"></span>')
          .prependTo($(el[0]).children("div"));
    }

    var getColumnIndexByName = function(grid,columnName) {
        var cm = grid.jqGrid('getGridParam','colModel'), i=0,l=cm.length;
        for (; i<l; i+=1) {
            if (cm[i].name===columnName) {
                return i; // return the index
            }
        }
        return -1;
    };

    function editRow(id) {
          if(current_edit_row!=null){
              return;
          }
          setTimeout(function(){
                 var grid = $("#jqGrid");
                    current_edit_row = id;
                    var editParameters = {
                            keys: true,
                            successfunc: editSuccessful,
                            errorfunc: editFailed,
                            restoreAfterError : false
                    };
                    grid.jqGrid('editRow',id, editParameters);          
          }, 0);
    }
      
    function editSuccessful( data, stat) {
		var response =  JSON.parse (data.responseText);              
		if (response.hasOwnProperty("error")) {
			if(response.error.length) {
				return [false,response.error ];
			}
		}
        current_edit_row = null; 

        setTimeout(function(){
           updateOrderLeaftTimes();
        }, 0);

     	return [true,"",""];
    }

    function editFailed(rowID, response) {
         var response =  JSON.parse (response.responseText);
		 $.jgrid.info_dialog(
		 $.jgrid.regional["en"].errors.errcap,
		 '<div class="ui-state-error">Order '+rowID+ ' :  '+ response.error +'</div>', 
		 $.jgrid.regional["en"].edit.bClose,
		 {buttonalign:'right', styleUI : 'Bootstrap'}
		)             
    }

    $("#jqGrid").jqGrid({
                //  url: 'monitor/currentorders',
                mtype: "GET",
				styleUI : 'Bootstrap',
                datatype: "jsonp",
                hoverrows:false,
                loadonce:true,              
                sortable:true,
                datatype:"local",
                localReader: {repeatitems: true},
                data: GRID_DATA,
                colModel: [
                     {
						label: "Quick Actions",
                        name: "actions",
                        width: 230,
                        formatter: "actions",
                        formatoptions: {                            
                            keys: true,
                            editbutton: false,
                            delbutton:false                                                                   
                        }       
                    },
                    { label: 'ID', name: 'order_id', key: true, width: 30 },
                    { label: 'Customer Name', name: 'first_name', width: 150 },
                    { label: 'hhiddenstatus', name: 'order_status_read', hidden:true, width: 150 },
                    { label: 'Status', name: 'order_status', width: 80 , formatter: function(cellValue, opts, rowObject){
                       console.log('Info Status Render',rowObject.status_priority);
                       var template = ' <span class="label label-default" style="background-color:';
                           template = template + rowObject.status_color ; 
                           template = template + ';"> '+rowObject.order_status+'</span>';
                       return template;
                    }},
                    { label: 'Type', name: 'order_type', width: 70 },
                    { label: 'Payment', name: 'payment', width: 150 },
                    { label: 'Total', name: 'order_total', width: 80 },
                    { label: 'Date', name: 'order_date', width: 80 },
                    { label: 'Time', name: 'order_time', width: 80,
                        editable: true,
                        edittype:"text",
                        formatter: function(cellValue, opts, rowObject){
                          //var template = cellValue + '<span class="glyphicon glyphicon-pencil actions-incons"></span>' ;                                 
                          return cellValue;
                        }
                     },                  
                     {label: 'Remaining time',name: 'deadline',width: 80,
                            formatter: function(cellValue, opts, rowObject){
                               var current_time        = moment();  
                               var order_time          = moment(rowObject.order_date + ' '+rowObject.order_time);            
                               var diff                = order_time.diff(current_time, 'm');                             
                               var template = diff + ' Min' ; 
                             
                               if(diff<15 && rowObject.order_status_read!="Delivery"){
                                 setTimeout(function(){
                                     $('#'+rowObject.order_id).addClass("danger");
                                 }, 0);
                               }else{
                                 setTimeout(function(){
                                      $('#'+rowObject.order_id).removeClass("danger");
                                 }, 0);
                               }                              
                               if(Math.abs(diff) > 60){
                                   diff = diff / 60;
                                   template = Math.round(diff*100)/100 + ' Hs' ; 
                               }
                               return template; 
                            },
                     },
                     {label: 'status_color'    ,name: 'status_color'    , hidden:true},
                     {label: 'status_priority' ,name: 'status_priority' , hidden:true, firstsortorder:'asc', sorttype:'int'}
                ],
				viewrecords: true,
                multiselect: true,
                loadonce: true,
                ondblClickRow: editRow,
                subGrid: true, // set the subGrid property to true to show expand buttons for each row
                subGridRowExpanded: showChildGrid, // javascript function that will take care of showing the child grid
                height: 350,
                rowNum: 20,
                editurl: 'monitor/orderEdit',
                loadComplete: function (data) {
                  // CURRENT_VERSION = data.version;
                     console.log('loadComplete',data);
                     var grid = $(this);
                     var iCol = getColumnIndexByName(grid,'actions'); // 'act' - name of the actions column                   
                          $(this).find(">tbody>tr.jqgrow>td:nth-child(" + (iCol + 1) + ")")
                            .each(function() {
                           // ORDER_CANCELED
                           $("<div>",{ title: "Your order has been canceled",
                                    mouseover: function() {$(this).addClass('ui-state-hover'); },
                                    mouseout: function() { $(this).removeClass('ui-state-hover'); },
                                    click: function(e) {
                                        var OrderID = $(e.target).closest("tr.jqgrow").attr("id");
                                        var orderArray = [];
                                        orderArray.push(OrderID);
                                        chageOrderStatus(orderArray,ORDER_CANCELED);
                                    }
                            }
                            ).css({"margin-right": "5px", float: "left", cursor: "pointer"})
                            .addClass("ui-pg-div ui-inline-edit")  //custom
                            .append('<span class="glyphicon glyphicon-trash actions-incons"></span>')
                            .prependTo($(this).children("div"));


                             /* Ready */ 
                             $("<div>",{title: "Your order has been completed",
                                    mouseover: function() { $(this).addClass('ui-state-hover'); },
                                    mouseout: function() { $(this).removeClass('ui-state-hover');},
                                    click: function(e) {
                                       var OrderID = $(e.target).closest("tr.jqgrow").attr("id");
                                       var orderArray = [];
                                       orderArray.push(OrderID);
                                       chageOrderStatus(orderArray,ORDER_COMPLETED);
                                    }
                                }
                            ).css({"margin-right": "5px", float: "left", cursor: "pointer"})
                            .addClass("ui-pg-div ui-inline-edit")
                            .append('<span class="glyphicon glyphicon-ok-circle actions-incons"></span>')
                            .prependTo($(this).children("div"));
                            /*Send*/
                            $("<div>",{title: "Your order wil be with you shortly",
                                    mouseover: function() {  $(this).addClass('ui-state-hover'); },
                                    mouseout: function() { $(this).removeClass('ui-state-hover'); },
                                    click: function(e) {                                   
                                        var OrderID = $(e.target).closest("tr.jqgrow").attr("id");
                                        var orderArray = [];
                                        orderArray.push(OrderID);
                                        chageOrderStatus(orderArray,ORDER_DELIVERY);
                                        $('#'+OrderID).removeClass("danger");
                                    }
                                }
                            ).css({"margin-right": "5px", float: "left", cursor: "pointer"})
                            .addClass("ui-pg-div ui-inline-edit")  //custom
                            .append('<span class="glyphicon glyphicon-send actions-incons"></span>')
                            .prependTo($(this).children("div"));
                            /* Fire */
                            $("<div>", {title: "Your order is being prepared",
                                    mouseover: function() {$(this).addClass('ui-state-hover');  },
                                    mouseout: function() { $(this).removeClass('ui-state-hover'); },
                                    click: function(e) {
                                        var OrderID = $(e.target).closest("tr.jqgrow").attr("id");
                                        var orderArray = [];
                                        orderArray.push(OrderID);
                                        chageOrderStatus(orderArray,ORDER_PREPARATION);
                                    }
                                }
                            ).css({"margin-right": "5px", float: "left", cursor: "pointer"})
                            .addClass("ui-pg-div ui-inline-edit")  //custom
                            .append('<span class="glyphicon glyphicon-fire actions-incons"></span>')
                            .prependTo($(this).children("div"));
                          
                            // Edit
                            $("<div>",{ title: "Edit",
                                    mouseover: function() { $(this).addClass('ui-state-hover'); },
                                    mouseout: function() { $(this).removeClass('ui-state-hover');},
                                    click: function(e) {
                                        var OrderID = $(e.target).closest("tr.jqgrow").attr("id");
                                        redirectToEdit(OrderID);
                                    }
                                }
                            ).css({"margin-right": "5px", float: "left", cursor: "pointer"})
                            .addClass("ui-pg-div ui-inline-edit")  //custom
                            .append('<span class="glyphicon glyphicon-pencil actions-incons"></span>')
                            .prependTo($(this).children("div"));
                        });
                }           
        });

        function showChildGrid(parentRowID, parentRowKey) {
            $.ajax({
                url: "monitor/options",
                type: "GET",
                data: {
                     id_order : parentRowKey,
                },
                success: function (html) {                   
                   $("#" + parentRowID).append(html);
                }
            });
        }
  
       loadInitialLoad();
});