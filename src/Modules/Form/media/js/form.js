replaceAttr = function(data, form){
    
    alert(data);
    
//    var response; // = jQuery.parseJSON(data);
//    var element;
    
    try {
        data = JSON.parse(data);
        for(var name in data){
            $('#'+name, form).replaceWith(data[name]);
        }
    } catch(e) {
        form.replaceWith(data);
    }
    
    
//    for(var name in response){
//        $('#'+name, form).replaceWith(response[name]);
//    }

//    }
    
//    var wrap = $('[name = ]')
    return false;
};

$.fn.wnFormSubmit = function(selector, success){
    
    

            selector = selector || '';
            success = success || replaceAttr;
//                    function(data, elem){
//                
//                alert(data);
//                
//              location.reload();
//              return false;
//            };
                  
            $(this).on('submit', 'form'+selector, function(){
                
//                alert('www');

                var form = $(this);            
                var action = $(this).data('action');
                var form_data = form.serializeArray();
                
//                alert(action);

                $.post(
                    action,
                    {form_data:form_data},
                    function(data){    
//                        alert(data);
                        success(data, form);
                    }
                );
                return false;
              });
        };
        
        $.fn.wnButtonClick = function(){
            $(this).on('click', 'button[data-action]', function(){
                
                var button = $(this);
                var action = button.data('action');
                var success = button.data('success');

                //              alert(success);

                $.get(action, function(data){
                  eval(success)(button, data);
                });
                return false;
          });
        };
        
        
//$('body').wnFormSubmit('.wn-form');        
