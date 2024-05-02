<style>
  .target_balance_text {
    /*font-weight: bold;*/
    font-size:12px;
  }
</style>

<script>
  jQuery(document).ready(function ($) {

    // tree table

    $("#userParamsTeamtimeCareer").treeTable({
      indent: 15,
      initialState: "expanded",
      treeColumn: 1
    });

    // fix auto height
    $("#userParamsTeamtimeCareer").click(function () {
      var height = $("#userParamsTeamtimeCareer").css("height");

      $("#user_params_teamtimecareer").parent()
      .children("div.jpane-slider").css("height", height);
    });

    // collapse by default
    $("#userParamsTeamtimeCareer").find("tr.clpsd")
    .removeClass("clpsd").removeClass("expanded").addClass("collapsed").each(
    function () {
      $(this).collapse();
    });

    $('input[type!="checkbox"].target_balance').autoNumeric({mDec:2, aSep:''});

    // calculate target balance

    var updateChildrenTargets = function (nodeId, perc) {
      var result = 0;
      var targets = [];

      var children = $("#userParamsTeamtimeCareer tr.child-of-node-"
        + nodeId + " input.cb_target_value");
      if (children.length == 0) {
        return;
      }

      $(children).each(function (i, n) {
        var id = $(n).attr("id").replace("tmp_target_value", "");

        if ($(n).is(':checked')) {
          // calc for checked targets
          var balance = parseFloat($(n).val());
          targets.push([id, balance]);
          result += balance;
        }
        else {
          // reset target balance
          $("#target" + id).val("0");
          updateChildrenTargets(id, 0);
        }
      });

      var balancePerc = 0;
      for (var i = 0; i < targets.length; i++) {
        var balancePerc = (targets[i][1] / result) * perc;

        // update target balance
        $("#target" + targets[i][0]).val(balancePerc.toFixed(2));
        updateChildrenTargets(targets[i][0], balancePerc);
      }
    };

    var updateTargetsByInputData = function (nodeId, perc, inputData) {
      var result = 0;
      var targets = [];
      var children;

      if (typeof(perc) === 'undefined') {
        perc = 100;
      }
      // exсlude percent for changed target
      perc -= inputData[1];
      
      if (nodeId != "") {
        // get child nodes
        children = $("#userParamsTeamtimeCareer tr.child-of-node-"
          + nodeId + " input.cb_target_value");
      }
      else {
        // get top nodes
        children = $(".cb_target_value");
      }

      if (children.length == 0) {
        return;
      }

      $(children).each(function (i, n) {
        if (nodeId == "") {
          // filter for parent nodes
          if ($(n).closest("tr").attr("class").indexOf("child-of-node-") >= 0) {
            return;
          }
        }

        var id = $(n).attr("id").replace("tmp_target_value", "");

        if ($(n).is(':checked')) {
          // calc for checked targets
          var balance = parseFloat($(n).val());
          targets.push([id, balance]);

          // exlude balance from total sum for changed target
          if (id != inputData[0]) {
            result += balance;
          }
        }
      });

      var balancePerc = 0;
      for (var i = 0; i < targets.length; i++) {
        if (targets[i][0] != inputData[0]) {
          balancePerc = (targets[i][1] / result) * perc;
          // update target balance
          $("#target" + targets[i][0]).val(balancePerc.toFixed(2));
        }
        else {
          balancePerc = inputData[1];
        }

        updateChildrenTargets(targets[i][0], balancePerc);
      }
    };

    var updateBalance = function() {
      var result = 0;
      var targets = [];

      $(".cb_target_value").each(function (i, n) {
        // filter for parent nodes
        if ($(n).closest("tr").attr("class").indexOf("child-of-node-") >= 0) {
          return;
        }

        var id = $(n).attr("id").replace("tmp_target_value", "");

        if ($(n).is(':checked')) {
          // calc for checked targets
          var balance = parseFloat($(n).val());
          targets.push([id, balance]);
          result += balance;
        }
        else {
          // reset target balance
          $("#target" + id).val("0");
          updateChildrenTargets(id, 0);
        }
      });

      var perc = 100;
      var balancePerc = 0;
      var sum = 0;
      for (var i = 0; i < targets.length; i++) {
        var balancePerc = (targets[i][1] / result) * perc;
        sum += balancePerc;

        // update target balance
        $("#target" + targets[i][0]).val(balancePerc.toFixed(2));
        updateChildrenTargets(targets[i][0], balancePerc);
      }

      $("#total_target_balance").html(sum.toFixed(2) + "&nbsp;%");
    };

    var checkChildrenTargets = function (nodeId) {
      var children = $("#userParamsTeamtimeCareer tr.child-of-node-"
        + nodeId + " input.cb_target_value");
      if (children.length == 0) {
        return;
      }

      $(children).each(function (i, n) {
        var id = $(n).attr("id").replace("tmp_target_value", "");

        $(n).attr("checked", "checked");

        checkChildrenTargets(id);
      });
    };

    var uncheckChildrenTargets = function (nodeId) {
      var children = $("#userParamsTeamtimeCareer tr.child-of-node-"
        + nodeId + " input.cb_target_value");
      if (children.length == 0) {
        return;
      }

      $(children).each(function (i, n) {
        var id = $(n).attr("id").replace("tmp_target_value", "");

        $(n).removeAttr("checked");

        uncheckChildrenTargets(id);
      });
    };

    var checkParentTargets = function (nodeId) {
      var parent = $("#tmp_target_value" + nodeId);
      $(parent).attr("checked", "checked");

      var parentId = $(parent).closest("tr").attr("class");
      var isChild = parentId.indexOf("child-of-node-") >= 0;
      if (isChild) {
        checkParentTargets(getParentId(parentId));
      }
    };

    var getParentId = function (s) {
      var result = "";
      var names = s.split(" ");

      for (var i = 0; i < names.length; i++) {
        if (names[i].indexOf("child-of-node-") >= 0) {
          result = names[i].replace("child-of-node-", "");
          break;
        }
      }

      return result;
    }

    $(".cb_target_value").change(function () {
      var id = $(this).attr("id").replace("tmp_target_value", "");

      if ($(this).is(":checked")) {
        // check parent node
        var parentId = $(this).closest("tr").attr("class");
        var isChild = parentId.indexOf("child-of-node-") >= 0;
        if (isChild) {
          checkParentTargets(getParentId(parentId));
        }

        checkChildrenTargets(id);
      }
      else {
        uncheckChildrenTargets(id);
      }

      updateBalance();
    });

    $("input.target_balance").change(function () {
      var id = $(this).attr("id");
      id = id.replace("target", "");
      var newBalance = $(this).val();

      var parentId = $(this).closest("tr").attr("class");
      parentId = getParentId(parentId);

      var perc = $("#target" + parentId).val();
      updateTargetsByInputData(parentId, perc, [id, parseFloat(newBalance)]);
    });

    //updateBalance();

    /*
    var getBalanceValue = function (obj) {
      var v = $.trim($(obj).val());
      if (v == "") {
        v = "0";
      }

      // get value for checkbox
      if ($(obj).attr("type") == "checkbox") {
        if (!$(obj).is(':checked')) {
          v = "0";
        }
      }

      return parseFloat(v);
    };

    var updateBalanceField = function (obj, balanceVal, hasChildrenBalance, parentId) {
      $(obj).val(balanceVal);

      var parentText = $("#" + parentId + " span.target_balance_text");

      if (hasChildrenBalance) {
        $(obj).attr("style", "display:none;");
        $(parentText).html(balanceVal);
      }
      else {
        $(obj).removeAttr("style");
        $(parentText).html("");
      }
    };

    var getChildrenBalance = function (nodeId) {
      var result = -1;

      var children = $("#userParamsTeamtimeCareer tr.child-of-" + nodeId);
      if (children.length == 0) {
        return result;
      }

      result = 0;

      $("#userParamsTeamtimeCareer tr.child-of-" + nodeId + " input.target_balance")
      .each(function (i, n) {
        var id = $(n).closest("tr").attr("id");
        var balance = getBalanceValue(n);
        var childrenBalance = 0;

        if ($(n).attr("type") != "checkbox") {
          childrenBalance = getChildrenBalance(id);
          if (childrenBalance >= 0) {
            balance = childrenBalance;
          }

          updateBalanceField(n, balance, childrenBalance > 0, id);
        }

        result += balance;
      });

      return result;
    };

    var updateBalance = function() {
      var result = 0;

      $(".target_balance").each(function(i, n) {
        // filter for parent nodes
        if ($(n).attr("type") == "checkbox"
          || $(n).closest("tr").attr("class").indexOf("child-of-node-") >= 0) {
          return;
        }

        var id = $(n).closest("tr").attr("id");
        var balance = getBalanceValue(n);
        var childrenBalance = getChildrenBalance(id);
        if (childrenBalance >= 0) {
          balance = childrenBalance;
        }

        updateBalanceField(n, balance, childrenBalance > 0, id);
        result += balance;
      });

      $("#total_target_balance").html(result.toFixed(2) + "&nbsp;%");
    };

    $(".target_balance").change(updateBalance);
    updateBalance();

     */

  });
</script>