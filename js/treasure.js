Handlebars.registerHelper('select_rarity', function(rarity, block) {
  var ret = "";
  $.each(rarity_values, function(key, value){
    var option = '<option value="' + key + '"';
    if (rarity == key) {
      option += ' selected="selected"';
    }
    option += '>' + value + '</option>';
    ret += option;
  });

  return new Handlebars.SafeString(ret);
});

$(function(){
  var templates = {
    "item": Handlebars.compile($("#item-template").html())
  };

  var genId = function() { 
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
        return v.toString(16);
    });
  };

  var getCurrentList = function() {
    var items = [];
    $("#items .item").each(function(){
      var elm = {};
      elm.item = $(this).find('input[data-type="name"]').val();
      elm.value = $(this).find('input[data-type="value"]').val();
      elm.rarity = $(this).find('select[data-type="rarity"]').val();
      elm.level = $(this).find('input[data-type="level"]').val();
      items.push(elm);
    });

    return items;
  };

  var translateRarity = function(compare) {
    var ret = null;
    compare = compare.toLowerCase();
    
    $.each(rarity_values, function(key, value){
      if (value.toLowerCase() == compare) {
        ret = key;
        return;
      }
    });

    return ret;
  };

  if (typeof elms !== "undefined") {
    $.each(elms, function(idx, value){
      value["id"] = genId();
      $("#items").append(templates["item"](value));
    });
  }

  $("#add-item").on("click", function(e){
    e.preventDefault();

    $("#items").append(templates["item"]({ "id": genId() }));
  });

  $(document).on("click", ".item .btn-danger", function(e) {
    e.preventDefault();
    $(this).closest(".item").remove();
  });

  $("#modify-list").on("click", function(e){
    e.preventDefault();

    var val = "";
    $.each(getCurrentList(), function(i, elm){
      val += elm.item + ", " + elm.level + ", " + rarity_values[parseInt(elm.rarity)] + ", " + elm.value + "\n";
    });

    $("#list-import").val(val);
    $(".continue-controls").hide();
    $("#add_list").show();
  });

  $("#update-list").on("click", function(e){
    e.preventDefault();

    var items = $("#list-import").val().split(/\n/);
    $("#items").empty();
    $.each(items, function(i, item){
      if ($.trim(item) != "") {
        var parts = item.split(",");

        $("#items").append(templates["item"]({
          "id": genId(),
          "name": $.trim(parts[0]),
          "level": $.trim(parts[1]),
          "rarity": translateRarity($.trim(parts[2])),
          "gold_value": $.trim(parts[3])
        }));
      }
    });

    $("#add_list").hide();
    $(".continue-controls").show();
  });

  $("#generate").on("click", function(e){
    e.preventDefault();

    var $button = $(this);
    var $form = $button.closest("form");

    $.ajax({
      url: $form.attr("action"),
      type: "post",
      dataType: 'json',
      data: $form.serialize(),
      beforeSend: function() {
        $button.attr("disabled", "disabled").find("i").removeClass("icon-ok-circle").addClass("icon-refresh");
      },
      success: function(response, status, xhr) {
        $("#selected").html('<div class="alert alert-success">' + response.item.display_value + '</div>');

        if (response.debug) {
          var debug = "";
          $.each(response.debug.odds_table, function(id, value){
            var val = (value * 100).toPrecision(5) + ": " + response.debug.items[id].display_value;
            if (id == response.debug.item_id) {
              val = "<strong>" + val + "</strong>";
            }
            debug += val + "<br>";
          });
          debug += "<br><b>Seed</b>: " + response.debug.random_seed;

          $("#debug").html('<div class="well well-small">' + debug + '</div>');
        }
      },
      error: function(xhr, status, e){
        alert(e);
      },
      complete: function(){
        $button.attr("disabled", null).find("i").removeClass("icon-refresh").addClass("icon-ok-circle");
      }
    });
  });
});