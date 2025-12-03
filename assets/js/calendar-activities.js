document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      locale: 'es',
      initialView: 'dayGridMonth',
      height: 650,
      headerToolbar:{
        left: 'prev,next,today',
        center: 'title',
        right: 'dayGridMonth,listDay',
      }
    });
    calendar.render();
});