 // Dark mode toggle functionality
 const darkModeToggle = document.getElementById('darkModeToggle');

 if (
     localStorage.theme === 'dark' ||
     (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
 ) {
     document.documentElement.classList.add('dark');
 } else {
     document.documentElement.classList.remove('dark');
 }

 darkModeToggle.addEventListener('click', () => {
     document.documentElement.classList.toggle('dark');
     localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
     updateChartsTheme();
 });

 function isDarkMode() {
     return document.documentElement.classList.contains('dark');
 }

// funciones para lista desplegable de usuario 
const userMenuButton = document.getElementById('userMenuButton');
const userMenu = document.getElementById('userMenu');

// Alternar la visibilidad del menú
userMenuButton.addEventListener('click', () => {
    userMenu.classList.toggle('hidden');
});

// Cerrar el menú al hacer clic fuera de él
document.addEventListener('click', (event) => {
    if (!userMenu.contains(event.target) && !userMenuButton.contains(event.target)) {
        userMenu.classList.add('hidden');
    }
});
// termini funciones para lista desplegable de usuario

//  funciones para mostrar las graficas de ventas y productos 
 function getChartColors() {
     return {
         backgroundColor: isDarkMode()
             ? [
                   'rgba(96, 165, 250, 0.8)',
                   'rgba(52, 211, 153, 0.8)',
                   'rgba(251, 146, 60, 0.8)',
                   'rgba(167, 139, 250, 0.8)',
               ]
             : [
                   'rgba(59, 130, 246, 0.8)',
                   'rgba(16, 185, 129, 0.8)',
                   'rgba(249, 115, 22, 0.8)',
                   'rgba(139, 92, 246, 0.8)',
               ],
         textColor: isDarkMode() ? '#fff' : '#374151',
     };
 }

 function updateChartsTheme() {
     const colors = getChartColors();
     salesChart.data.datasets[0].backgroundColor = colors.backgroundColor;
     salesChart.options.plugins.legend.labels.color = colors.textColor;
     salesChart.update();

     productsChart.data.datasets[0].backgroundColor = colors.backgroundColor[0];
     productsChart.options.scales.x.ticks.color = colors.textColor;
     productsChart.options.scales.y.ticks.color = colors.textColor;
     productsChart.update();
 }

 const salesCtx = document.getElementById('salesChart').getContext('2d');
 const productsCtx = document.getElementById('productsChart').getContext('2d');

 const colors = getChartColors();

 // Configuración de la gráfica de ventas por categoría
 const salesChart = new Chart(salesCtx, {
     type: 'doughnut',
     data: {
         labels: ['Abarrotes', 'Bebidas', 'Snacks', 'Vegetales'],
         datasets: [
             {
                 data: [45, 25, 20, 10],
                 backgroundColor: colors.backgroundColor,
             },
         ],
     },
     options: {
         responsive: true,
         maintainAspectRatio: true,
         plugins: {
             legend: {
                 position: 'bottom',
                 labels: {
                     color: colors.textColor,
                 },
             },
         },
     },
 });

 // Configuración de la gráfica de productos más vendidos
 const productsChart = new Chart(productsCtx, {
     type: 'bar',
     data: {
         labels: ['Arroz', 'Aceite', 'Leche', 'Pan', 'Huevos'],
         datasets: [
             {
                 label: 'Unidades vendidas',
                 data: [150, 120, 100, 80, 60],
                 backgroundColor: colors.backgroundColor[0],
             },
         ],
     },
     options: {
         responsive: true,
         maintainAspectRatio: true,
         plugins: {
             legend: {
                 display: false,
             },
         },
         scales: {
             x: {
                 ticks: {
                     color: colors.textColor,
                 },
             },
             y: {
                 beginAtZero: true,
                 ticks: {
                     color: colors.textColor,
                 },
             },
         },
     },
 });