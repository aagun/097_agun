const optionsMonthlyIncome = {
  annotations: {
    position: "back",
  },
  dataLabels: {
    enabled: false,
  },
  chart: {
    type: "bar",
    height: 300,
  },
  fill: {
    opacity: 1,
  },
  plotOptions: {},
  series: [
    {
      name: "Income",
      data: [5, 10, 15, 20, 25, 30, 35, 40, 45, 50],
    },
  ],
  colors: "#435ebe",
  yaxis: {
    min: 0,
    max: 100,
    stepSize: 10,
    title: {
      text: 'Rp (Juta)'
    }
  },
  xaxis: {
    categories: [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "May",
      "Jun",
      "Jul",
      "Aug",
      "Sep",
      "Oct",
      "Nov",
      "Dec",
    ],
  },
}

const chartMonthlyIncome = new ApexCharts(
  document.querySelector("#chartMonthlyIncome"),
  optionsMonthlyIncome
)

chartMonthlyIncome.render()
