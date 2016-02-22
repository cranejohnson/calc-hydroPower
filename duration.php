<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">



<html>
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Hydropower Flow Duration Curve Method</title>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
    <script type="text/javascript" src="http://data.whitewinter.net/hydro/jsLib/jeditable.js"></script>
  <script type="text/javascript" src="http://data.whitewinter.net/hydro/jsLib/Highcharts/js/highcharts.js"></script>
  <script type="text/javascript" src="http://data.whitewinter.net/hydro/jsLib/Highcharts/js/modules/data.js"></script>
    <script type ="text/javascript" src="http://data.whitewinter.net/hydro/jsLib/Highcharts/js/themes/grid.js"></script>
  <script src="http://code.highcharts.com/modules/exporting.js"></script>

  <script>
    var turbineDefaults = [{
    name : 'Pelton',
    data : [[0,70],[10,80],[20,85],[30,88],[40,89],[50,89],[60,89],[70,89],[80,89],[90,89],[100,88]]},
               { name : 'Turgo',
                 data : [[0,0],[5,15],[10,44],[15,63],[20,73],[25,79],[30,85],[35,84],[40,85],[45,85],[50,85],[55,85],[60,85],[65,85],[70,85],[75,85],[80,85],[85,85],[90,85],[95,85],[100,84]]
                },
               { name : 'CrossFlow',
                 data : [[0,40],[10,82],[20,88],[30,88],[40,88],[50,88],[60,88],[70,88],[80,88],[90,88],[100,88]]
                },
               { name : 'Francis',
                 data : [[0,0],[10,0],[20,0],[30,45],[40,60],[50,72.5],[60,82],[70,89],[80,93],[90,93],[100,92]]
                },
               {name : 'Kaplan',
                data : [[0,65],[10,73],[20,80],[30,88],[40,91],[50,92],[60,92],[70,92],[80,92],[90,91.5],[100,91]]
                },
               {name : 'Constant',
                data : [[0,88],[100,88]]
               }
              ];

    var hydroData = {

    "turbine" : {
      xAxis : {
      title: {
        enabled: true,
        text: '% Design Discharge'
      },
      units: '%'},
      yAxis : {
      title :{
        enabled:true,
        text: 'Efficiency'
      },
      units: ''},
      "capacity" : {
      flow : 3000,
      percent : 20,
      data : null
      },
      type: 0,
      data: turbineDefaults[0].data,
      minimum : 500,
    },
    "flowLoss": 0,
    "generator":{
      efficiency: 97
      },
    "headData" : {
      "forebayElev" : 500,
      "tailwaterElev" :100,
      "lossesFt" : 20},
    "flowDuration" : {
      "netFlow" : null,
      "envFlow" : 300,
      xAxis : {
      title: {
        enabled: true,
        text: 'Duration (%)'
      },
      units: '%'},
      yAxis : {
      title :{
        enabled:true,
        text: 'Discharge (cfs)'
      },
      units: 'cfs'},
      data : [
      [0,5000],
      [10,4000],
      [20,3000],
      [30,2800],
      [40,2500],
      [50,2300],
      [60,2000],
      [70,1100],
      [80,800],
      [90,300],
      [100,100]]

    }
    };

    ///////////////////////////////////
    //  Calculate net flow (gross - environmental flows)
    ///////////////////////////////////

    function calcNetFlow() {
    var netFlow = new Array();
    var gross = hydroData.flowDuration.data;
    var envFlow = hydroData.flowDuration.envFlow;

    for (i = 0; i < gross.length; i++){
      x = gross[i][0];
      y = gross[i][1] - envFlow;

      if (y < 0) {
      y = 0;
      var point = lineInterpolate(gross[i-1],gross[i],'y',envFlow);

      x = point[0];
      i = gross.length;
      }
      netFlow.push([x,y]);
    }

    return netFlow;
    }



  </script>



  <style>
    body { font-family:'lucida grande', tahoma, verdana, arial, sans-serif; font-size:11px; }
    h1 { font-size: 15px; }
    a { color: #548dc4; text-decoration: none; }
    a:hover { text-decoration: underline; }
    table.testgrid { border-collapse: collapse; border: 1px solid #CCB; width: 200px; }
    table.testgrid td, table.testgrid th { padding: 5px; border: 1px solid #E0E0E0; }
    table.testgrid th { background: #E5E5E5; text-align: left; }
    input.invalid { background: red; color: #FDFDFD; }
    .editclass {
    background-color:#99ff99;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    width: 100%;
    }

    .envedit,.turbineEdit,.headedit,.editable {
      color:#CC3333;
      }
    .chart {
    width: 600px;
    float: left;
    height: 400px;
    margin: 0 1em 1em 0;
    }
    #main {
    width : 1200px;
    }
    .results {
      background-color:#C0C0C0;
      font-weight:bold;
      font-size:20px;
    }

  </style>



  </head>

  <body>
  <div id ='main'>
    <div id='usgsform'>
      <div>USGS Site:<input type="text" id="usgsid" value="15514000" size="10"></div>
      <div>Seasonal Start:<input type="text" id="usgsstart" value="Jan 1" size="6"></div>
      <div>Seasonal End:<input type="text" id="usgsend" value="Dec 31" size="6"></div>
      <div>Multiplier:<input type="text" id="usgsmult" value="1" size="4"></div>
      <input type="button" value="Load USGS Data" onclick="getUSGSData(usgsid.value,usgsstart.value,usgsend.value,usgsmult.value);" />
      <div id='usgsname'></div>
    </div>
    <div id='flowDurationTableDiv' style= "width:200px;float:left;"></div>

    <div id="flowDuration" class = "chart"></div>
    <table style = "width:300px; margin-bottom: 20px;" class = 'testgrid'><tbody>
    <tr><td>Average Flow:</td><td style="width:125px; height:25px;"> <div id="avgFlow"></div></td></tr>
    <tr><td>Environmental Flow:</td><td style="width:125px; height:25px;"><div id="envFlow" class = "envedit"></div></td><td style="width:125px; height:25px;"><div id="envFlowPercent" class = "envedit">%</div></td></tr>
    <tr><td>Turbine Capacity (cfs):</td><td style="width:125px; height:25px;"><div id="turbineFlow" class = "turbineEdit"></div></td><td style="width:125px; height:25px;"><div id="turbinePercent" class = "turbineEdit">%</div></td></tr>
    <tr><td>Turbine Minimum (cfs):</td><td style="width:125px; height:25px;"><div id="turbineMin" class = "turbineEdit"></div></td></tr>
    <tr><td>Gross Head (ft):</td><td style="width:125px; height:25px;"><div id="grossHead" class = "headedit"></div></td></tr>
    <tr><td>Head Loss (ft):</td><td style="width:125px; height:25px;"><div id="headLossFt" class = "headedit"><td style="width:125px; height:25px;"><div id="headLossPer" class = "headedit"></div></td></tr>
    <tr><td>Net Head (ft):</td><td style="width:125px; height:25px;"><div id="netHead"></div></td></tr>
    <tr></tr>
    <tr><td>Capacity (Mw):</td><td style="width:125px; height:25px;"><div class = "results" id="capacity"></div></td></tr>
    <tr><td>Annual Power(KwH x 10<sup>6</sup>):</td><td style="width:125px; height:25px;"><div class= "results"  id="annPower"></div></td></tr>
    <tr><td>Capacity Factor:</td><td style="width:125px; height:25px;"><div class= "results" id="capFactor"></div></td></tr>
    </tbody>
    </table><br>
    <form>
    <select onChange="updateTurbine(value);">
      <option value="" selected>Turbine Type:</option>
      <option value="0" selected>Pelton</option>

      <option value="2">CrossFlow</option>
      <option value="3">Francis</option>
      <option value="4">Kaplan</option>
      <option value="5">Constant</option>
    </select>
    </form>

    <div id='turbineTableDiv' style= "width:200px;float:left;"></div>
    <div id="turbine" class = "chart"></div>

  </div>
  </body>
  <script>


  ///////////////////////////////////
  //  Day of year prototype
  ///////////////////////////////////

  Date.prototype.getDOY = function() {
    var onejan = new Date(this.getFullYear(),0,1);
    return Math.ceil((this - onejan) / 86400000);
    }



  ///////////////////////////////////
  //  Main Update Page Function
  ///////////////////////////////////

  function calcPage(){
        //Calculate several variables
    setCapacityArray('y',hydroData.turbine.capacity.flow,hydroData.turbine.minimum,calcNetFlow());
    hydroData.headData.lossesPer = Math.round(hydroData.headData.lossesFt/(hydroData.headData.forebayElev - hydroData.headData.tailwaterElev)*100);

    //Update Page Elements
    document.getElementById('turbineTableDiv').innerHTML = createTable(hydroData.turbine, 'turbine.table','testgrid', 'Download');
    document.getElementById('flowDurationTableDiv').innerHTML = createTable(hydroData.flowDuration, 'flowDuration.table','testgrid', 'Download');
    document.getElementById('avgFlow').innerHTML = curveArea(hydroData.flowDuration.data);
    document.getElementById('envFlow').innerHTML = hydroData.flowDuration.envFlow;
    document.getElementById('envFlowPercent').innerHTML = '%'+Math.round(hydroData.flowDuration.envFlow/curveArea(hydroData.flowDuration.data)*100);
    document.getElementById('turbineMin').innerHTML = hydroData.turbine.minimum;
    document.getElementById('grossHead').innerHTML = hydroData.headData.forebayElev - hydroData.headData.tailwaterElev;
    document.getElementById('headLossFt').innerHTML =  hydroData.headData.lossesFt;
    document.getElementById('headLossPer').innerHTML = '%'+ hydroData.headData.lossesPer;
    document.getElementById('netHead').innerHTML = hydroData.headData.forebayElev - hydroData.headData.tailwaterElev - hydroData.headData.lossesFt;

    //Update Charts
    $('#flowDuration').highcharts().series[1].setData(hydroData.turbine.capacity.data);
    $('#flowDuration').highcharts().series[2].setData(calcNetFlow());
    //$('#turbine').highcharts().series[0].setData(hydroData.turbine.data);
    var startdate = getDate(usgsstart.value);
    var enddate = getDate(usgsend.value);
    var powerSeason = enddate.getDOY() - startdate.getDOY()+1;
    calcPower(powerSeason);
    calcCap();
    //Make editable tables usable after adding new DOM table elements
      $('.editable').on('click', function(){
          $(this).editable(function(value,settings) {
          return(value);
          }, {
          width : 5,
          cols : 5,
        callback : function(value,settings) {
            var tableId = $(this).closest('table').attr('id') ;
            var parts = tableId.split(".");
            hydroData[parts[0]].data = tableToJSONData(tableId);
            updateChart(parts[0]);
        }
        });
    });

  }



  ///////////////////////////////////
  //  Get Date
  ///////////////////////////////////



  function getDate(input){
      var parts=input.split(" ");
        var months=['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
      var month = months.indexOf(parts[0].toLowerCase());
        var day = Math.round(parts[1]);
        var year = 2000;
        var parsedate = new Date(year,month,day);

        return parsedate;

  }


  ///////////////////////////////////
  //  Get USGS data
  ///////////////////////////////////

  function getUSGSData(site,start,end,mult){
      document.getElementById('flowDurationTableDiv').innerHTML = 'Loading USGS Data....';
       $.getJSON('http://www.data.chenabasin.org/hydro/power/usgs_flowduration.php?site='+site+'&start='+start+'&end='+end+'&mult='+mult, function(data) {
      hydroData.flowDuration.data = data['dur_data'];
      document.getElementById('flowDurationTableDiv').innerHTML = createTable(hydroData.flowDuration, 'flowDuration.table','testgrid', 'Download');
      document.getElementById('usgsname').innerHTML = data['name'];
        updateChart('flowDuration');
        document.getElementById('envFlowPercent').innerHTML = '%'+Math.round(hydroData.flowDuration.envFlow/curveArea(hydroData.flowDuration.data)*100);
      setCapacityArray('y',hydroData.turbine.capacity.flow,hydroData.turbine.minimum,calcNetFlow());
            calcPage();

  });
   }


    ///////////////////////////////////
  //  Calculate area under the curve
  ///////////////////////////////////

  function curveArea(array){
    var area = 0;
    for($i=0;$i<array.length-1;$i++){
      var width = array[$i+1][0]-array[$i][0];
      var height = (array[$i+1][1]+array[$i][1])/2;
      area = area + width*height;
      }
     return Math.round(area/100);
  }

   ///////////////////////////////////
  //  Array Lookup
  ///////////////////////////////////

  function arrayLookup(XorY,value,array){
    var point = new Array();
    for (i = 0; i < array.length-1; i++){
      if(XorY == 'y'){
          if(array[i+1][1] <= value) {
          point = lineInterpolate(array[i],array[i+1],'y',value);
          break;
          }
      }
      if(XorY == 'x'){
          if(array[i+1][0] >= value){
          point = lineInterpolate(array[i],array[i+1],'x',value);
          break;
          }
      }
      }
      return point;
  }

  ///////////////////////////////////
  //  Calculate Capacity
  ///////////////////////////////////

  function calcCap(){
    var nethead = hydroData.headData.forebayElev - hydroData.headData.tailwaterElev - hydroData.headData.lossesFt;
      var maxflow = 0;
    for(i=0;i<hydroData.turbine.capacity.data.length;i++){
      if(hydroData.turbine.capacity.data[i][1]>maxflow){
        maxflow = hydroData.turbine.capacity.data[i][1];
        flowPer = hydroData.turbine.capacity.data[i][1];
        }
      }

    var turEff = arrayLookup('x',(maxflow/hydroData.turbine.capacity.flow)*100,hydroData.turbine.data)[1]/100;
    var power = Math.round(maxflow*nethead*turEff*hydroData.generator.efficiency/100/11.8/1000);
    document.getElementById('capacity').innerHTML = power;

  }




   ///////////////////////////////////
  //  Calculate Power
  ///////////////////////////////////

  function calcPower(daysOfPower){
    var array = hydroData.turbine.capacity.data;

    var effArray = hydroData.turbine.data;
    var power = 0;
    var nethead = hydroData.headData.forebayElev - hydroData.headData.tailwaterElev - hydroData.headData.lossesFt;

    for($i=0;$i<array.length-1;$i++){

         var width = (array[$i+1][0]-array[$i][0])/100;
      var height = (array[$i][1]+array[$i+1][1])/2;

      var turbEff = arrayLookup('x',height/hydroData.turbine.capacity.flow*100,hydroData.turbine.data)[1];
      var efficiency = (turbEff/100)*(hydroData.generator.efficiency/100)
      //alert('w='+width+' height='+height+'nethead='+nethead+'eff='+efficiency);
      //alert((width*height*nethead*efficiency)/11.81);
      power = power + (width*height*nethead*efficiency)/11.81;
    }
    power = power*(daysOfPower*24)/1000000;

    maxPower = hydroData.turbine.capacity.flow*arrayLookup('x',100,hydroData.turbine.data)[1]/100*(hydroData.generator.efficiency/100)*nethead*8760/11.81/1000000;
    document.getElementById('annPower').innerHTML = power.toFixed(2);
    document.getElementById('capFactor').innerHTML = Math.round((power/maxPower)*100);

  }

  ///////////////////////////////////
  //  Update JSON data from HTML table data
  ///////////////////////////////////

  function tableToJSONData(tableId){
    var newData = new Array();
    var table = document.getElementById(tableId);
    for (var i = 1, row; row = table.rows[i]; i++) {
    //iterate through rows
    var dataPoint = new Array();
    for (var j = 0, col; col = row.cells[j]; j++) {
      //iterate through columns
      dataPoint.push(parseFloat(col.innerHTML));
    }
    newData.push(dataPoint);
    }

    return newData;
  }


  ///////////////////////////////////
  //  Update JSON data from Chart data
  ///////////////////////////////////


  function chartToJSONData(chartId){
    var newData = new Array();
    var chart= $('#'+chartId).highcharts();
    series = chart.series[0].data;
    $.each(series, function(){
    var dataPoint = new Array();
    dataPoint.push(this.x);
    dataPoint.push(this.y);
    newData.push(dataPoint);
    });
    return newData;
  }

  ///////////////////////////////////
  //  Calculate Annual Volume
  ///////////////////////////////////

  function annualVolume(dataArray){
    for (i; i < dataArray.length; i++){
    }}

  ///////////////////////////////////
  //  Linear Interpolation
  ///////////////////////////////////

  function lineInterpolate( point1, point2,XorY,value)
  {
    var result = new Array();
    if(XorY == 'y'){
    result[1] = value;
    ratio = (value-point1[1])/(point2[1]-point1[1]);
    result[0] = Math.round(point1[0]+ratio*(point2[0]-point1[0]));
    }
    if(XorY == 'x'){
    result[0] = value;
    ratio = (value-point1[0])/(point2[0]-point1[0]);
    result[1] = Math.round(point1[1]+ratio*(point2[1]-point1[1]));
    }
    return result;
  }


  ///////////////////////////////////
  //  Set xy array to plot turbine capacity on flow duration graph
  ///////////////////////////////////


  function setCapacityArray(XorY,value,min,dataArray){
    var ratio = 0;
    var capacityArray = new Array();
    var setPoint = new Array();
    var endPoint = new Array();
    var i = 0;
    setPoint = arrayLookup(XorY,value,dataArray);

    capacityArray[0] = [0,parseFloat(setPoint[1])];
    capacityArray[1] = [parseFloat(setPoint[0]),parseFloat(setPoint[1])];
    for (i=0; i < dataArray.length; i++){
    if((dataArray[i][0]>capacityArray[1][0]) && (dataArray[i][1]>min)){
      capacityArray.push(dataArray[i]);
    }
    if((dataArray[i][1]<min)|| (i == dataArray.length-1)){
      endPoint = lineInterpolate(dataArray[i-1],dataArray[i],'y',min);
      break;
    }
    }

    //Push last point on net flow curve
    capacityArray.push(endPoint);
    //Push the turbine capacity curve intersect with the x axis
    capacityArray.push([endPoint[0],0]);

    hydroData.turbine.capacity.percent = setPoint[0];
    hydroData.turbine.capacity.flow = setPoint[1];
    document.getElementById('turbineFlow').innerHTML = setPoint[1];
    document.getElementById('turbinePercent').innerHTML = '%'+setPoint[0];
    hydroData.turbine.capacity.data = capacityArray;

  }


  ///////////////////////////////////
  //  Creates HTML table from XY data in hydroData format
  ///////////////////////////////////

  function createTable(dataObject,tableId,tableClass){
    //Pattern for table

    var idMarkup = tableId ? ' id="' + tableId + '"' : '';
      var classMarkup = tableClass ? ' class="' + tableClass + '"' :'';
    var tbl = '<table ' + idMarkup + classMarkup + '>{0}{1}</table>';
    //Patterns for table content
    var th = '<thead>{0}</thead>';
    var tb = '<tbody>{0}</tbody>';
    var tr = '<tr>{0}</tr>';
    var thRow = '<th>{0}</th>';
    var tdRow = '<td class="editable">{0}</td>';
    var thCon = '';
    var tbCon = '';
    var trCon = '';
    String.prototype.format = function()
      {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function(match, number)
              {
                return typeof args[number] != 'undefined' ? args[number] :
                '{' + number + '}';
              });
      };
      // Create Header rows

      thCon += thRow.format(dataObject.xAxis.title.text);
      thCon += thRow.format(dataObject.yAxis.title.text);
      th = th.format(tr.format(thCon));

      // Create table rows from data

      for (i = 0; i < dataObject.data.length; i++)
      {
      tbCon += tdRow.format(dataObject.data[i][0]);
      tbCon += tdRow.format(dataObject.data[i][1]);
      trCon += tr.format(tbCon);
      tbCon = '';
      }

      tb = tb.format(trCon);
      tbl = tbl.format(th, tb);

      return tbl;
  }




  function getXYdata(table){
    var xydata = new Array();
    $('tr', $(table)).each(function(i) {
    var tr = this;
    var datapoint = new Array();
    if(i>0){
      $('th, td', tr).each(function(j) {
      datapoint.push(parseFloat(this.innerHTML));
      });

      xydata.push(datapoint);
    }
    });
    return xydata;
  }

  /**
  * Experimental Draggable points plugin
  * Revised 2013-06-13 (get latest version from http://jsfiddle.net/highcharts/AyUbx/)
  * Author: Torstein Honsi
  * License: MIT License
  *
  */
  (function (Highcharts) {
    var addEvent = Highcharts.addEvent,
      each = Highcharts.each;

    /**
    * Filter by dragMin and dragMax
    */
    function filterRange(newY, series, XOrY) {
    var options = series.options,
      dragMin = options['dragMin' + XOrY],
      dragMax = options['dragMax' + XOrY];

    if (newY < dragMin) {
      newY = dragMin;
    } else if (newY > dragMax) {
      newY = dragMax;
    }
    return Math.round(newY);
    }

    Highcharts.Chart.prototype.callbacks.push(function (chart) {

    var container = chart.container,
      dragPoint,
      dragX,
      dragY,
      dragPlotX,
      dragPlotY;

    chart.redraw(); // kill animation (why was this again?)

    addEvent(container, 'mousedown', function (e) {
      var hoverPoint = chart.hoverPoint,
        options;

      if (hoverPoint) {
      options = hoverPoint.series.options;
      if (options.draggableX) {
        dragPoint = hoverPoint;

        dragX = e.pageX;
        dragPlotX = dragPoint.plotX;
      }

      if (options.draggableY) {
        dragPoint = hoverPoint;

        dragY = e.pageY;
        dragPlotY = dragPoint.plotY + (chart.plotHeight - (dragPoint.yBottom || chart.plotHeight));
      }

      // Disable zooming when dragging
      if (dragPoint) {
        chart.mouseIsDown = false;
      }
      }


    });

    addEvent(container, 'mousemove', function (e) {
      if (dragPoint) {
      var deltaY = dragY - e.pageY,
        deltaX = dragX - e.pageX,
        newPlotX = dragPlotX - deltaX - dragPoint.series.xAxis.minPixelPadding,
        newPlotY = chart.plotHeight - dragPlotY + deltaY,
        newX = dragX === undefined ? dragPoint.x : dragPoint.series.xAxis.translate(newPlotX, true),
        newY = dragY === undefined ? dragPoint.y : dragPoint.series.yAxis.translate(newPlotY, true),
        series = dragPoint.series,
        proceed;

      newX = filterRange(newX, series, 'X');
      newY = filterRange(newY, series, 'Y');

      //Get the container of this chart and update the associated html table
      var hydroType = chart.options.hydroType;

      hydroData[hydroType].data = chartToJSONData(hydroType);
         calcPage();

      // Fire the 'drag' event with a default action to move the point.
      dragPoint.firePointEvent(
        'drag', {
        newX: newX,
        newY: newY
        },

        function () {
        proceed = true;
        dragPoint.update([newX, newY], false);
        //chart.tooltip.refresh(chart.tooltip.shared ? [dragPoint] : dragPoint);
        if (series.stackKey) {
          chart.redraw();
        } else {
          series.redraw();
        }
        });

      // The default handler has not run because of prevented default
      if (!proceed) {
        drop();
      }
      }
    });

    function drop(e) {
      if (dragPoint) {
      if (e) {
        var deltaX = dragX - e.pageX,
          deltaY = dragY - e.pageY,
          newPlotX = dragPlotX - deltaX - dragPoint.series.xAxis.minPixelPadding,
          newPlotY = chart.plotHeight - dragPlotY + deltaY,
          series = dragPoint.series,
          newX = dragX === undefined ? dragPoint.x : dragPoint.series.xAxis.translate(newPlotX, true),
          newY = dragY === undefined ? dragPoint.y : dragPoint.series.yAxis.translate(newPlotY, true);

        newX = filterRange(newX, series, 'X');
        newY = filterRange(newY, series, 'Y');
        dragPoint.update([newX, newY]);
      }
      dragPoint.firePointEvent('drop');
      }
      dragPoint = dragX = dragY = undefined;


    }
    addEvent(document, 'mouseup', drop);
    addEvent(container, 'mouseleave', drop);
    });

    /**
    * Extend the column chart tracker by visualizing the tracker object for small points
    */


  })(Highcharts);
  // End plugin



  ///////////////////////////HighCharts Config/////////////////////////
  Highcharts.setOptions({
    tooltip: {
      positioner: function () {
        return { x: 80, y: 50 };
      }
    },
      credits : false,
    chart: {
    backgroundColor: '#F0F0F8',
    shadow: true,
    animation: false,
    spacingRight: 30,

    events: {
      click: function(e) {
      // find the clicked values and the series
      var x = e.xAxis[0].value,
        newY = Math.round(e.yAxis[0].value),
        series = this.series[0].data;
      var closest = series[0].x.value;
      var i = 0;
      $.each(series, function(){
        if (closest == null || Math.abs(this.x - x) < Math.abs(closest - x)) {
        closest = this.x;
        i++;
        }
      });
      this.series[0].data[i-1].update(y = newY);
      var hydroType = this.options.hydroType;
      hydroData[hydroType].data = chartToJSONData(hydroType);
      calcPage();


      }
    }
    },
    plotOptions: {
    series: {
      stickyTracking: false
    }
    }
  });


  /////////////Configure flowDuration Chart
  var param = 'flowDuration';
  $('#'+param).highcharts({
    hydroType : param,
    series: [{
    data: hydroData.flowDuration.data,
    name : 'Available Flow',
    draggableY: true,
    zIndex : 2
    },
         {data: hydroData.turbine.capacity.data,
        type : 'area',
        fillOpacity: 0.3,
        name : 'Turbine Capacity',
        lineWidth : 4,
        color: '#FF0000',
        zIndex: 1,
        marker :{
          enabled: false
        },
        states: {
          hover: {
          enabled: false
          }
        }

         },{
         data: calcNetFlow(),
         name : 'Net Flow',
         marker:{
         symbol: 'circle'
         },
         color : 'green',
         zIndex : 10}],
    xAxis: {
    title: {
      enabled : true,
      text : 'Duration (%)'
    },
    min : 0,
    max : 100,
    tickInterval : 10
    },
    yAxis: {

    min: 0,
    title: {
      enabled: true,
      text: 'Discharge (cfs)'

    }

    },

    title: {
    text: 'Flow Duration Data'
    }

  });


  var param = 'turbine';
  $('#'+param).highcharts({
    hydroType : param,
    series: [{
    data: hydroData.turbine.data,
    name : 'e',
    draggableY: true,
    zIndex : 2
    }],
    xAxis: {
    title: {
      enabled : true,
      text : '% Design Discharge'
    },
    min : 0,
    max : 100,
    tickInterval : 10
    },
    yAxis: {
    min: 0,
    max:100,
    title: {
      enabled: true,
      text: 'Efficency'
    }
    },
    title: {
    text: 'Turbine Efficiency'
    }

  });


  calcPage();

  ///////////////////////////////////
  //  MISC FUNCTIONS
  ///////////////////////////////////

  ///////////////////////////////////
  //  Update turbine and set new data in turbine chart
  ///////////////////////////////////

  function updateTurbine(type){
    turbineDefaults[hydroData.turbine.type].data = hydroData.turbine.data;
    hydroData.turbine.data = turbineDefaults[type].data;
    hydroData.turbine.type = type;
    updateChart('turbine');
//     document.getElementById('turbineTableDiv').innerHTML = createTable(hydroData.turbine, 'turbine.table','testgrid', 'Download');
    calcPage();
    $('.editable').on('click', function(){
          $(this).editable(function(value,settings) {
          return(value);
          }, {
          width : 5,
          cols : 5,
        callback : function(value,settings) {
            var tableId = $(this).closest('table').attr('id') ;
            var parts = tableId.split(".");
            hydroData[parts[0]].data = tableToJSONData(tableId);
            updateChart(parts[0]);
        }
        });
    });
  }

  function updateChart(hydroType){
    var chart= $('#'+hydroType).highcharts();
    if(hydroType == "flowDuration"){
    setCapacityArray('y',hydroData.turbine.capacity.flow,hydroData.turbine.minimum,calcNetFlow());
    chart.series[1].setData(hydroData.turbine.capacity.data);
    chart.series[2].setData(calcNetFlow());
    }
    chart.series[0].setData(hydroData[hydroType].data);
    chart.redraw();
    document.getElementById('avgFlow').innerHTML = curveArea(hydroData.flowDuration.data);
  }

  function updateTable(htmltable,indVal,depVal){
    var table = document.getElementById(htmltable);
    $('tr', $(table)).each(function(i) {
    var tr = this;
    if(indVal == tr.cells[0].innerHTML){
      tr.cells[1].innerHTML = depVal;
    }
    });
  }




  ///////////////////////////////////
  //  EDITABLE FUNCTIONS
  ///////////////////////////////////



  $(function() {
    $('.headedit').editable(function(value) {
    return(value);
    }, {
    callback : function(value, settings) {
    if($(this).attr("id") == "grossHead"){
      hydroData.headData.tailwaterElev = hydroData.headData.forebayElev - value;
      hydroData.headData.lossesFt = hydroData.headData.lossesPer/100* (hydroData.headData.forebayElev - hydroData.headData.tailwaterElev);
       }
    if($(this).attr("id") == "headLossFt"){
          hydroData.headData.lossesFt = value;
        }
    if($(this).attr("id") == "headLossPer"){
          hydroData.headData.lossesFt = (value.replace("%","")/100)*(hydroData.headData.forebayElev - hydroData.headData.tailwaterElev);
        }
     calcPage();
    }
    });
  });

  $(function() {
    $('.editable').editable(function(value,settings) {
    return(value);
    }, {
      width : 'none',
      cols : 10,
    callback : function(value,settings) {
      var tableId = $(this).closest('table').attr('id') ;
      var parts = tableId.split(".");
      hydroData[parts[0]].data = tableToJSONData(tableId);
      updateChart(parts[0]);
      calcPage();
    }
    });
  });

  $(function() {
    $('.envedit').editable(function(value) {
    return(value);
    }, {
    callback : function(value, settings) {
    if($(this).attr("id") == "envFlow"){
      hydroData.flowDuration.envFlow = value;
      }
    if($(this).attr("id") == "envFlowPercent"){
     hydroData.flowDuration.envFlow = Math.round(value.replace("%","")/100*curveArea(hydroData.flowDuration.data));
       }
      calcPage();

    }
    });
  });

  $(function() {
    $('.turbineEdit').editable(function(value) {
    return(value);
    }, {
    callback : function(value, settings) {
      var chart = $('#flowDuration').highcharts();
      if($(this).attr("id") == "turbineFlow"){
      hydroData.turbine.capacity.flow = Math.round(value);
      if(hydroData.turbine.minimum > hydroData.turbine.capacity.flow) hydroData.turbine.minimum = hydroData.turbine.capacity.flow;
      setCapacityArray('y',hydroData.turbine.capacity.flow,hydroData.turbine.minimum,calcNetFlow());
      }
      else if($(this).attr("id") == "turbineMin"){
      hydroData.turbine.minimum = Math.round(value);
      if(hydroData.turbine.minimum > hydroData.turbine.capacity.flow) hydroData.turbine.minimum = hydroData.turbine.capacity.flow;
      setCapacityArray('y',hydroData.turbine.capacity.flow,hydroData.turbine.minimum,calcNetFlow());
      }

      else{
      hydroData.turbine.capacity.percent = Math.round(value.replace("%",""));
      setCapacityArray('x',hydroData.turbine.capacity.percent,hydroData.turbine.minimum,calcNetFlow());
      }

    calcPage();

    }
    });
  });


  </script>

</html>
