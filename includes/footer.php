</div>

<footer class="text-center" id="footer">copyright shauntas boutique</footer>



<script
  src="https://code.jquery.com/jquery-3.3.1.js"
  integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
  crossorigin="anonymous"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

<script>
$(window).scroll(function(){
  var vscroll = $(this).scrollTop();
  $('#logotext').css({
    "transform" : "translate(0px, "+vscroll/2+"px)"
  });

  var vscroll = $(this).scrollTop();
  $('#back-flower').css({
    "transform" : "translate("+vscroll/5+"px, -"+vscroll/8+"px)"
  });

  var vscroll = $(this).scrollTop();
  $('#fore-flower').css({
    "transform" : "translate(0px, -"+vscroll/2+"px)"
  });
});
</script>

<script>

// Modal
function detailsmodal(id){
  var data = {"id" : id};
  $.ajax({
    url : '/tutorial/includes/detailsmodal.php',
    method : "post",
    data : data,
    success: function(data){
      $('body').append(data);
      $('#details-modal').modal('toggle');
    },
    error: function(){
      alert("Something went wrong!");
    }
  });
}

// Update cart
function update_cart(mode,edit_id,edit_size){
  var data = {"mode" : mode, "edit_id" : edit_id, "edit_size" : edit_size};
  $.ajax({
    url : '/tutorial/admin/parsers/update_cart.php',
    method : "post",
    data : data,
    success : function(){
      location.reload();
    },
    error : function(){
      alert("Something went wrong");
    }
  })
}

// Add to cart
function add_to_cart(){
  $('#modal_errors').html("");
  var size = $('#size').val();
  var quantity = $('#quantity').val();
  var available = parseInt($('#available').val());
  var error = '';
  var data = $('#add_product_form').serialize();
  if(size == '' || quantity == '' || quantity <= 0){
    if (quantity < 0) {
      error += '<p class="text-danger text-center">You can\'t select negative amount.</p>';
      $('#modal_errors').html(error);
      return;
    }
    error += '<p class="text-danger text-center">You must choose a size and quantity.</p>';
    $('#modal_errors').html(error);
    return;
  } else if(quantity > available){
    error += '<p class="text-danger text-center">There are only '+available+' available.</p>';
    $('#modal_errors').html(error);
    return;
  } else {
    $.ajax({
      url : '/tutorial/admin/parsers/add_cart.php',
      method : 'post',
      data : data,
      success : function(){
        location.reload();
      },
      error : function(){alert("Something went wrong");}
    });
  }
}

</script>
</body>
</html>
