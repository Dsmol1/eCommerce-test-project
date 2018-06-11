
<footer class="text-center" id="footer">Test project eCommerce</footer>
</div>



<script
  src="https://code.jquery.com/jquery-3.3.1.js"
  integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
  crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

<script type="text/javascript">
function updateSizes(){
  var sizeString = '';
  for (var i = 1; i <= 12; i++) {
    if ($('#size'+i).val() != '') {
      sizeString += $('#size'+i).val()+':'+$('#qty'+i).val()+',';
    }
  }
  sizeString = sizeString.replace(/,\s*$/, "");
  $('#sizes').val(sizeString);
}

function get_child_options(selected){
  if (typeof selected === 'object') {
    var selected = '';
  }
  var parentID = $('#parent').val();
  $.ajax({
    url: '/tutorial/admin/parsers/child_categories.php',
    type: 'POST',
    data: {parentID : parentID, selected : selected},
    success: function(data){
      $('#child').html(data);
    },
    error: function(){alert("Something went wrong with child options")},
  });
}
  $('select[name="parent"]').change(function(){
    get_child_options();
  });

  $('document').ready(function(){
    $("a.delete").click(function(e){
        if(!confirm('Are you sure you want to delete?')){
            e.preventDefault();
            return false;
        }
        return true;
    });
});
</script>
</body>
</html>
