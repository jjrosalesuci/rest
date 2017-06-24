$(document).ready(function(){

   var map = null;
   var current_step = 1;
   var validator1 = null;
   var validator2 = null;
   var validator3 = null;

   function initMap(lat,lng) {
       
        var centerLatLng = new google.maps.LatLng(
            parseFloat(lat),
            parseFloat(lng)
        );

    	var mapOptions = {
            zoom: 15,
            center: centerLatLng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById('map'), mapOptions);

        var marker = new google.maps.Marker({
            position: centerLatLng,
            map: map,
            draggable: true            
        });

        google.maps.event.addListener(marker, 'dragend', function (event) {
            restaurant_lat   = this.getPosition().lat();
            restaurant_long  = this.getPosition().lng();
        });
    }


   function FireRezize(){
       google.maps.event.trigger(map, 'resize');
       map.setCenter({lat:restaurant_lat, lng:restaurant_long});
   }

   $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
       if(current_step ==1){
          $('#input_location_name').focus();
        }
        if(current_step ==2){
          $('#input_first_name').focus();
        }
        if(current_step ==3){
          FireRezize();
          $('#input_email_code').focus();
        }     
   });


   $('#myModal').on('hide.bs.modal', function(e){
      
         var r = confirm("Are you sure you want to cancel?");
            if (r == true) {
                current_step = 1;
                map          = null;
                $('#basic_link').click();
             
                $("#label_emailexists_error").hide();
                $("#label_address_error").hide();
                $('#label_terms_and_conditions_error').hide();

                $('#step1').trigger("reset");
                $('#step2').trigger("reset");
                $('#step3').trigger("reset");

                validator1.resetForm(); 
                validator2.resetForm();
                validator3.resetForm();

                $('#step1 .form-group').removeClass('has-error');
                $('#step2 .form-group').removeClass('has-error');
                $('#step3 .form-group').removeClass('has-error');
                     
                $("#confirm_li").addClass("disabled");
                $('#account_li').addClass('disabled');
                $('#baslic_li').removeClass('disabled');

                return true;
            } else {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false; 
           }
        });
 

   $('#basic_link').click(function(e) {     
       current_step = 1;
       $("#confirm_andstart").hide(); 
       $('#next').show();  

       
       $("#confirm_li").addClass("disabled");
       $('#account_li').addClass('disabled');
       $('#baslic_li').removeClass('disabled');



   });
  
   $('#account_link').click(function(e) {
       if(current_step ==1 ){
          e.stopPropagation();
          return;
       }
       current_step = 2;

       $('#baslic_li').removeClass('disabled');
       $('#account_li').removeClass('disabled');
       $("#confirm_li").addClass("disabled");
     
       $("#confirm_andstart").hide(); 
       $('#next').show(); 
   });
  
   $('#confirm_link').click(function(e) {
       if(current_step==1 ||current_step==2 ){
          e.stopPropagation();
          return;
       }
       $("#confirm_li").removeClass("disabled");
   });
 
   //Step1
   var restaurant_lat           = null;
   var restaurant_long          = null;
   var restaurant_location_name = null;
   var restaurant_email         = null;
   var restaurant_telephone     = null;

   var restaurant_address_1     = null;
   var restaurant_address_2     = null;                       
   var restaurant_city          = null;
   var restaurant_state          = null;
   var restaurant_postcode      = null;
   var restaurant_country       = null;

   //Step2
   var admin_first_name         = null;
   var admin_last_name          = null;
   var admin_email              = null;
   var admin_mobilephone        = null;
   var admin_password           = null;

  

   
   /* Validations rules */    
   validator1 =  $('#step1').validate({
        rules: {
            location_name: { minlength: 3, maxlength: 40, required: true },
            email: { email: true, required: true },
            address_1:{ required: true },
            address_city:{ required: true }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
            if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
     });

    validator2 =  $('#step2').validate({
        rules: {
            first_name: {minlength: 3,maxlength: 15, required: true },
            last_name:{ minlength: 3,maxlength: 15, required: true },
            admin_email: {email: true, required: true },
            admin_mobilephone:{ required: true },
            password:{minlength: 6, required: true},
            password_confirm:{  minlength: 6, equalTo:'#input_password' }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
            if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

       validator3 =  $('#step3').validate({
          rules: {
            email_code : {required: true,            
            remote:  {
                 url:"checkemailcode",
                 data: { email: function() { return admin_email; }},    
                 async:false                
            }
          },
          mobile_code:{  
            required   : true ,
            remote:  {
                 url:"checkphonecode",
                 data: { mobile: function() { return admin_mobilephone; }},                
                 async:false
            }
          }          
        },
        messages:{
            email_code: {
                remote: "Please enter a valid code.."
            },
            mobile_code: {
                remote: "Please enter a valid code.."
            }
        },

        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        success: function() {
           
                //$("#confirm_andstart").removeAttr('disabled');  //Enable input 
                       
        },
        errorPlacement: function(error, element) {
           //$("#confirm_andstart").attr('disabled','disabled');   //Disable input
            if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });
    
    $('#next').click(function() {
           switch(current_step) {
            case 1:
                   if($("#step1").valid()){
                       $("#label_address_error").hide();
                       $.ajax({
                        type: "POST",
                        url:  "cheklocation", 
                        data: {
                            address_1     : $("#address_1").val(),
                            address_2     : $("#address_2").val(),                         
                            city          : $("#address_city").val(),
                            state         : $("#address_state").val(),
                            postcode      : $("#address_postcode").val(), 
                            country       : $("#address_country").val()                           
                        },
                        dataType: "json",  
                        cache:false,
                            success: function(data){
                                if(data.succes == false) {                                    
                                    $("#label_address_error").show();
                                } else {
                                    // change step
                                     current_step    = 2;
                                    // set data
                                    restaurant_lat           = data.lat;
                                    restaurant_long          = data.lng;

                                    initMap(restaurant_lat,restaurant_long);

                                    restaurant_location_name = $("#input_location_name").val();
                                    restaurant_email         = $("#input_email").val();
                                    restaurant_telephone     = $("#input_telephone").val();

                                    restaurant_address_1     = $("#address_1").val(),
                                    restaurant_address_2     = $("#address_2").val(),                         
                                    restaurant_city          = $("#address_city").val(),
                                    restaurant_state         = $("#address_state").val(),
                                    restaurant_postcode      = $("#address_postcode").val(), 
                                    restaurant_country       = $("#address_country").val()  

                                    $("#account_link").click();
                                    $("#input_first_name").focus();
                                }            
                            }
                         });
                     }

                break;
            case 2:
                 if($("#step2").valid()){
                    $.ajax({
                        type: "POST",
                        url:  "sendcodes", 
                        data: {
                            admin_email        : $("#input_admin_email").val(),
                            admin_mobilephone  : $("#input_admin_mobilephone").val()
                        },
                        dataType: "json",  
                        cache:false,
                            success: function(data){
                                if(data.success == false) {                                  
                                    $("#label_emailexists_error").show();
                                } else {
                                    // change step
                                    current_step    = 3;
                                    // set data
                                    admin_first_name         = $("#input_first_name").val();
                                    admin_last_name          = $("#input_last_name").val();
                                    admin_email              = $("#input_admin_email").val();
                                    admin_mobilephone        = $("#input_admin_mobilephone").val();
                                    admin_password           = $("#input_password").val();
                                    //Show values
                                    $("#confirm_email_info").html(admin_email);
                                    $("#confirm_phone_info").html(admin_mobilephone);
                                    $("#info_restaurant_title").html(restaurant_location_name);

                                    var address = "";
                                    if(restaurant_address_1!=""){
                                      address = restaurant_address_1;
                                    }
                                    if(restaurant_address_2!=""){
                                      address = address + ' , ' + restaurant_address_2;
                                    }
                                    if(restaurant_city!=""){
                                      address = address + '<br>' + restaurant_city;
                                    }
                                    if(restaurant_state!=""){
                                      address = address + ' , ' + restaurant_state;
                                    }
                                    if(restaurant_country!=""){
                                      address = address + '<br>'+ restaurant_country ;
                                    }
                                    $("#info_restaurant_address").html(address);       

                                    // TODO Center Map                            

                                    $("#confirm_link").click();   
                                    $("#confirm_andstart").show(); 
                                    $('#next').hide();
                                }            
                            }
                         });
                 }
                break;
            default:
               
            }       
    }); 

    /* Terms and conditions */
    $('#accept_terms').click(function(){
             if(!$('#accept_terms').is(":checked")){
                 $('#label_terms_and_conditions_error').show();
             }else{
                 $('#label_terms_and_conditions_error').hide(); 
             }
    });

    $('#confirm_andstart').click(function(){

        $('#label_terms_and_conditions_error').hide();

        if($("#step3").valid()){

             if(!$('#accept_terms').is(":checked")){
                 $('#label_terms_and_conditions_error').show();
                 return;
             }
            
             $.ajax({
                        type: "POST",
                        url:  "register", 
                        data: {
                              restaurant_lat           : restaurant_lat,
                              restaurant_long          : restaurant_long,
                              restaurant_location_name : restaurant_location_name,
                              restaurant_email         : restaurant_email,
                              restaurant_telephone     : restaurant_telephone,
                              restaurant_address_1     : restaurant_address_1,
                              restaurant_address_2     : restaurant_address_2,                      
                              restaurant_city          : restaurant_city,
                              restaurant_state         : restaurant_state,
                              restaurant_postcode      : restaurant_postcode,
                              restaurant_country       : restaurant_country,
                              admin_first_name         : admin_first_name,
                              admin_last_name          : admin_last_name,
                              admin_email              : admin_email,
                              admin_mobilephone        : admin_mobilephone,
                              admin_password           : admin_password,
                              mobile_code              : $("#input_mobile_code").val(), 
                              email_code               : $("#input_email_code").val()
                        },
                        dataType: "json",
                        success: function(data){
                          if(data.success == true) {
                             window.location.replace("../admin");
                          }else{
                              if(data.error == 'EMAIL_CODE_INVALID' ||data.error == 'SMS_CODE_INVALID' ){
                                 $("#input_mobile_code").val(''); 
                                 $("#input_email_code").val('');
                                 $("#step3").valid();
                              }                   
                          }
                        } 
             });


        }
    });

});