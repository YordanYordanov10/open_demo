# OpenCart AJAX Patterns

## Basic AJAX Request (jQuery)

OpenCart frontend usually uses jQuery.

Example:

```javascript
$.ajax({

    url: 'index.php?route=test/example',

    type: 'POST',

    dataType: 'json',

    data: {
        name: $('#name').val()
    },

    success: function(json){

        if(json.success){
            console.log(json.success);
        }

        if(json.error){
            console.log(json.error);
        }

    }

});