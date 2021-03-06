@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="col-md-8 offset-md-2">
      <div class="card-header">
        <h4>
          <i class="glyphicon glyphicon-edit"></i> 编辑个人资料
        </h4>
      </div>

      <div class="card-body">
        <form action="{{ route('users.update', $user->id) }}" method="post" accept-charset="utf-8" enctype="multipart/form-data">
          {{ csrf_field() }}
          {{ method_field('put') }}

          @include('shared._errors')

          <div class="form-group">
            <label for="name-field">用户名</label>
            <input class="form-control" type="text" id="name-field" name="name" value="{{ old('name', $user->name) }}">
          </div>
          <div class="form-group">
            <label for="email-field">邮 箱</label>
            <input class="form-control" type="text" id="email-field" name="email" value="{{ old('email', $user->email) }}">
          </div>
          <div class="form-group">
            <label for="introduction-field">个人简介</label>
            <textarea class="form-control" type="text" id="introduction-field" name="introduction" rows="3">{{ old('introduction', $user->introduction) }} </textarea>
          </div>
          <div class="form-group mb-4">
            <label for="" class="avatar-lable">用户头像</label>
            <input class="form-control-file" type="file" id="avatar" name="avatar">
            @if ($user->avatar)
            <br>
            <img class="thunbnal img-responsive" src="{{ $user->avatar }}" width="200" />
            @endif
          </div>
          <div class="well well-sm">
            <button type="submit" class="btn btn-primary">保存</button>
          </div>

        </form>
      </div>
    </div>
  </div>
@stop
