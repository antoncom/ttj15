Raphael.fn.pieChart = function (cx, cy, r, values, labels, stroke, colors) {
  var paper = this;
  var rad = Math.PI / 180;
  var chart = this.set();

  function sector(cx, cy, r, startAngle, endAngle, params) {
    var x1 = cx + r * Math.cos(-startAngle * rad),
    x2 = cx + r * Math.cos(-endAngle * rad),
    y1 = cy + r * Math.sin(-startAngle * rad),
    y2 = cy + r * Math.sin(-endAngle * rad);
    return paper.path([
      "M", cx, cy, "L", x1, y1, "A", r, r, 0, +(endAngle - startAngle > 180), 0, x2, y2, "z"])
    .attr(params);
  }

  var angle = 0;
  var total = 0;
  var start = 0;

  var process = function (j) {
    var value = values[j],
    angleplus = 360 * value / total,
    popangle = angle + (angleplus / 2),
    color = "hsb(" + start + ", 1, .5)",
    ms = 500,
    delta = 20,
    bcolor = "hsb(" + start + ", 1, 1)";

    if (value == 100) {
      var p = paper.circle(cx, cy, r).attr({
        //gradient: "90-" + bcolor + "-" + color,
        fill: colors[j],
        stroke: stroke,
        "stroke-width": 2
      });
    }
    else {
      var p = sector(cx, cy, r, angle, angle + angleplus, {
        //gradient: "90-" + bcolor + "-" + color,
        fill: colors[j],
        stroke: stroke,
        "stroke-width": 2
      });
    }

    var txt = paper.text(
      cx + (r + delta + 55) * Math.cos(-popangle * rad), cy + (r + delta + 25)
      * Math.sin(-popangle * rad), labels[j])
    .attr({
      //fill: bcolor,
      fill: "#000000",
      stroke: "none",
      opacity: 1,
      "font-family": 'Fontin-Sans, Arial',
      "font-size": "12px"
    });

    /*p.mouseover(function () {
      p.animate({
        scale: [1.05, 1.05, cx, cy]
      }, ms, "elastic");
    txt.animate({
        opacity: 1
      }, ms, "elastic");
    }).mouseout(function () {
      p.animate({
        scale: [1, 1, cx, cy]
      }, ms, "elastic");
    txt.animate({
        opacity: 0
      }, ms);
    });*/

    angle += angleplus;
    chart.push(p);
    chart.push(txt);
    start += .1;
  };

  for (var i = 0, ii = values.length; i < ii; i++) {
    total += values[i];
  }

  for (var i = 0; i < ii; i++) {
    process(i);
  }

  return chart;
};

var Raphael_PieChart = function (elementId, cx, cy, r, color) {
  color = typeof(color) != 'undefined' ? color : "#fff";

  var raphael = Raphael;
  var $ = jQuery;

  var values = [];
  var labels = [];
  var colors = [];
  var tableId = "table#" + elementId + ".raphael-piechart";

  $(tableId + " tr").each(function () {
    var td = $("td", this);

    values.push(parseInt($(td[0]).text(), 10));
    labels.push($("th", this).text());
    colors.push($(td[2]).text());
  });

  $(tableId).hide();

  if (values.length > 0) {
    raphael("holder-" + elementId, Math.round(cx * 2), Math.round(cy * 2)).pieChart(
      cx, cy, r, values, labels, color, colors);
  }
  else {
    $("#holder-" + elementId).hide();
  }
};
