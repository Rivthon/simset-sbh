@props(['name'])

@switch($name)
    @case('dashboard')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h4A1.5 1.5 0 0 1 11 5.5v4A1.5 1.5 0 0 1 9.5 11h-4A1.5 1.5 0 0 1 4 9.5v-4ZM13 5.5A1.5 1.5 0 0 1 14.5 4h4A1.5 1.5 0 0 1 20 5.5v4a1.5 1.5 0 0 1-1.5 1.5h-4A1.5 1.5 0 0 1 13 9.5v-4ZM4 14.5A1.5 1.5 0 0 1 5.5 13h4a1.5 1.5 0 0 1 1.5 1.5v4A1.5 1.5 0 0 1 9.5 20h-4A1.5 1.5 0 0 1 4 18.5v-4ZM13 14.5a1.5 1.5 0 0 1 1.5-1.5h4a1.5 1.5 0 0 1 1.5 1.5v4a1.5 1.5 0 0 1-1.5 1.5h-4a1.5 1.5 0 0 1-1.5-1.5v-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
        </svg>
        @break

    @case('unit')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 20h16M6 20V7a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13M9 9h1.5M13.5 9H15M9 12.5h1.5M13.5 12.5H15M10 20v-3h4v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('category')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4.75 6.75h6.5v6.5h-6.5v-6.5ZM13 5.5h6.25v6.25H13V5.5ZM7.75 15h6.5v6h-6.5v-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
        </svg>
        @break

    @case('location')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 21s6-5.05 6-10A6 6 0 0 0 6 11c0 4.95 6 10 6 10Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="M12 13.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" stroke="currentColor" stroke-width="1.7"/>
        </svg>
        @break

    @case('container')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 8.5 12 4l8 4.5v7L12 20l-8-4.5v-7Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="m4.5 8.75 7.5 4.1 7.5-4.1M12 12.85V20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        </svg>
        @break

    @case('user')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM5 20a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        </svg>
        @break

    @case('asset')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M6.5 7.5h11A1.5 1.5 0 0 1 19 9v9a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 5 18V9a1.5 1.5 0 0 1 1.5-1.5Z" stroke="currentColor" stroke-width="1.7"/>
            <path d="M9 7.5V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1.5M8.5 12h7M8.5 15.5H13" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        </svg>
        @break

    @case('asset-plus')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M6.5 8h11A1.5 1.5 0 0 1 19 9.5V18a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 5 18V9.5A1.5 1.5 0 0 1 6.5 8Z" stroke="currentColor" stroke-width="1.7"/>
            <path d="M9 8V6.5A2.5 2.5 0 0 1 11.5 4h1A2.5 2.5 0 0 1 15 6.5V8M12 11.5v5M9.5 14h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        </svg>
        @break

    @case('inventory')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M8 5.5h8M9 4h6a1 1 0 0 1 1 1v2H8V5a1 1 0 0 1 1-1ZM6 7h12v13H6V7Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="m9 13 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('report')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M6 4.5h9.5L19 8v11.5H6v-15Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="M15.5 4.5V8H19M9 13h6M9 16h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        </svg>
        @break

    @case('view')
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M12 14.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" stroke="currentColor" stroke-width="1.8"/>
        </svg>
        @break

    @case('edit')
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="m14.5 6.5 3 3M5 19l4.25-1.1 8.4-8.4a2.12 2.12 0 0 0-3-3l-8.4 8.4L5 19Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('delete')
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M6 7h12M10 7V5.5h4V7M8 9l.75 10h6.5L16 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('qr')
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M5 5h5v5H5V5ZM14 5h5v5h-5V5ZM5 14h5v5H5v-5ZM14 14h2M19 14v2M14 19h5M17 17h2" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </svg>
        @break

    @default
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        </svg>
@endswitch
