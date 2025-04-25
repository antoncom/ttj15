
$("#is_dotu_price").change(function() {

  if ($("#is_dotu_price").attr("checked")) {
    $("#rate").attr("disabled", "disabled");    
  }
  else {
    $("#rate").removeAttr("disabled");    
  }
  
});

$("#is_dotu_price").change();
