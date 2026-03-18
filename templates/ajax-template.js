function requestExample(data){

    $.ajax({

        url: 'index.php?route=test/example/ajax',

        type: 'POST',

        dataType: 'json',

        data: data,

        success: function(json){

            if(json.success){
                console.log(json);
            }

        }

    });

}