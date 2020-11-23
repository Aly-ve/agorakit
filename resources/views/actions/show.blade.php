@extends('app')

@section('content')

@include('groups.tabs')


<div class="content">

    <div class="flex justify-between">

        <h1>
            {{ $action->name }}
        </h1>

        <div class="ml-4 dropdown">
            <a class="text-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="fas fa-ellipsis-h"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

                @can('update', $action)
                <a class="dropdown-item" href="{{ route('groups.actions.edit', [$group, $action]) }}">
                    <i class="fa fa-pencil"></i>
                    {{trans('messages.edit')}}
                </a>
                @endcan

                @can('delete', $action)
                <a class="dropdown-item" href="{{ route('groups.actions.deleteconfirm', [$group, $action]) }}">
                    <i class="fa fa-trash"></i>
                    {{trans('messages.delete')}}
                </a>
                @endcan

                @if ($action->revisionHistory->count() > 0)
                <a class="dropdown-item" href="{{route('groups.actions.history', [$group, $action])}}"><i
                        class="fa fa-history"></i> {{trans('messages.show_history')}}</a>
                @endif
            </div>
        </div>
    </div>


    <div class="meta mb-3">
        {{trans('messages.started_by')}}
        <span class="user">
            @if ($action->user)
            <a up-follow href="{{ route('users.show', [$action->user]) }}">{{ $action->user->name}}</a>
            @endif
        </span>
        {{trans('messages.in')}}
        <strong>
            <a up-follow href="{{ route('groups.show', [$action->group]) }}">{{ $action->group->name}}</a>
        </strong>
        {{ $action->created_at->diffForHumans()}}
    </div>


    <div class="tags mb-3">
        @if ($action->getSelectedTags()->count() > 0)
        @foreach ($action->getSelectedTags() as $tag)
        @include('tags.tag')
        @endforeach
        @endif
    </div>


    <h3>{{trans('messages.begins')}} : {{$action->start->format('d/m/Y H:i')}}</h3>

    <h3>{{trans('messages.ends')}} : {{$action->stop->format('d/m/Y H:i')}}</h3>

    @if (!empty($action->location))
    <h3>{{trans('messages.location')}} : {{$action->location}}</h3>
    @endif



    <div>
        {!! filter($action->body) !!}
    </div>

    @if ($action->attending->count() > 0)
    <div class="d-flex justify-content-between mt-5 mb-4">
        <h2>{{trans('messages.user_attending')}} ({{$action->attending->count()}})</h2>
        <div>
            @if (Auth::user() && Auth::user()->isAttending($action))
            <a class="btn btn-primary btn-sm" up-modal=".dialog"
                href="{{route('groups.actions.participation', [$group, $action])}}">{{trans('messages.edit')}}</a>
            @endif
        </div>
    </div>
    <div class="d-flex flex-wrap users mt-2 mb-2">
        @foreach($action->attending as $user)
        @include('users.user-card')
        @endforeach
    </div>

    @endif


    @if ($action->notAttending->count() > 0)
    <div class="d-flex justify-content-between mt-5 mb-4">
        <h2>{{trans('messages.user_not_attending')}} ({{$action->notAttending->count()}})</h2>
        <div>
            @if (Auth::user() && Auth::user()->isAttending($action))
            <a class="btn btn-primary btn-sm" up-modal=".dialog"
                href="{{route('groups.actions.participation', [$group, $action])}}">{{trans('messages.edit')}}</a>
            @endif
        </div>
    </div>
    <div class="d-flex flex-wrap users mt-2 mb-2">
        @foreach($action->notAttending as $user)
        @include('users.user-card')
        @endforeach
    </div>

    @endif


    <div class="mt-4">
        @if (Auth::user())
        <a class="btn btn-primary" up-modal=".dialog"
            href="{{route('groups.actions.participation', [$group, $action])}}">{{trans('Edit my participation')}}</a>
        @endif
    </div>
</div>





</div>

@endsection