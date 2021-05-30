<a class="navbar-brand" href="{{ url('/') }}">
@if(!empty($homepage_logo->logo_path))
    <img src="{{ $homepage_logo->homepageLogoURL() }}" alt="{{ $homepage_logo->altText() }}" style="height: 1em; width: 2em;">
@else
    {{ config('app.name') }}
@endif
</a>
