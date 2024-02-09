var app = new Vue({
  el: '#app',
  data: {
    rate: 0,
    alldata: [],
    showSpinner: false,
  },
  created: function () {
    console.log('Привет!')
  },
  mounted: function () {
    this.getRate()
    this.getIData()
  },
  computed: {
 
  },
  methods: {
    getRate: function(){
      axios.get('https://tools-api.robolatoriya.com/cbrfkeyrate')
      .then(function (response) {
            console.log(response.data);
            app.rate = response.data.rate;
      })
      .catch(function (error) {
        console.log(error);
      });
    },
    getIData: function(){
      axios.get('https://tools-api.robolatoriya.com/cbrfkeyrate/all')
      .then(function (response) {
            console.log(response.data);
            app.alldata=response.data['Dataset'];
            chart(app.alldata);
      })
      .catch(function (error) {
        console.log(error);
      });
    },
    getData: function(startdate, enddate){
      axios.get('https://tools-api.robolatoriya.com/cbrfkeyrate/all?start='+startdate+'&end='+enddate)
      .then(function (response) {
            console.log(response.data);
            app.alldata=response.data['Dataset'];
            chart(app.alldata);
      })
      .catch(function (error) {
        console.log(error);
      });
    },
  }
});


function chart(items) {
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Дата');
  data.addColumn('number', "Значение");

  data.addRows(items.map(function(item){return [item.DateStamp,parseFloat(item.Rate)]})); 
	
  var options = {
    legend: {position: 'none'},
    chartArea: { "left": "auto", "right": "auto", "top": 50, "bottom": "10", "width": "80%", "height": "60%"},
    width: "100%",
    pointSize: 7,
    hAxis: {
      textPosition: 'none',
    },
  };
 
  var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

  chart.draw(data, options)
      
}

mydiv = document.getElementById("graph");

function showhide(panels) {
    panels.style.display = (panels.style.display !== "none") ? "none" : "block";
}


function redraw() {
  chart.draw(data, options);
}

google.charts.load('current', {'packages':['corechart']});
