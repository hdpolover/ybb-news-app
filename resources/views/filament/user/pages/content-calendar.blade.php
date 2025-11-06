<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div id="calendar"></div>
        </div>
    </div>

    @push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                events: {!! json_encode($events ?? []) !!},
                editable: true,
                droppable: true,
                eventClick: function(info) {
                    // Navigate to post edit page
                    window.location.href = info.event.url;
                    info.jsEvent.preventDefault();
                },
                eventDrop: function(info) {
                    // Update post date when dragged
                    // @this is a Livewire directive to call component methods
                    @this.call('updateEventDate', 
                        parseInt(info.event.id), 
                        info.event.start.toISOString()
                    );
                },
                eventDidMount: function(info) {
                    // Add tooltip with additional info
                    var tooltip = document.createElement('div');
                    tooltip.style.cssText = 'position: absolute; z-index: 10000; background: #333; color: #fff; padding: 8px 12px; border-radius: 4px; font-size: 12px; display: none;';
                    tooltip.innerHTML = `
                        <strong>${info.event.title}</strong><br>
                        Status: ${info.event.extendedProps.status}<br>
                        Author: ${info.event.extendedProps.author}
                    `;
                    document.body.appendChild(tooltip);
                    
                    info.el.addEventListener('mouseenter', function(e) {
                        tooltip.style.display = 'block';
                        tooltip.style.left = e.pageX + 'px';
                        tooltip.style.top = (e.pageY - tooltip.offsetHeight - 10) + 'px';
                    });
                    
                    info.el.addEventListener('mouseleave', function() {
                        tooltip.style.display = 'none';
                    });
                },
                height: 'auto',
                themeSystem: 'standard',
            });
            
            calendar.render();
            
            // Refresh calendar when posts are updated
            window.addEventListener('post-updated', function() {
                calendar.refetchEvents();
            });
        });
    </script>
    
    <style>
        #calendar {
            max-width: 100%;
        }
        
        .fc {
            font-family: inherit;
        }
        
        .fc-event {
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .fc-event:hover {
            transform: scale(1.05);
        }
        
        .fc-toolbar-title {
            font-size: 1.5rem !important;
            font-weight: 600;
        }
        
        .fc-button {
            text-transform: capitalize !important;
            padding: 0.5rem 1rem !important;
        }
        
        .fc-button-primary {
            background-color: rgb(251, 191, 36) !important;
            border-color: rgb(251, 191, 36) !important;
        }
        
        .fc-button-primary:hover {
            background-color: rgb(245, 158, 11) !important;
            border-color: rgb(245, 158, 11) !important;
        }
        
        .fc-button-primary:disabled {
            opacity: 0.5;
        }
        
        .dark .fc {
            color: #fff;
        }
        
        .dark .fc-theme-standard td,
        .dark .fc-theme-standard th {
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        .dark .fc-scrollgrid {
            border-color: rgba(255, 255, 255, 0.1);
        }
    </style>
    @endpush
</x-filament-panels::page>
