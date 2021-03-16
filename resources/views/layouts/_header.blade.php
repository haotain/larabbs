<nav class="navbar navbar-expand-lg navbar-light bg-light navbar-static-top">
  <div class="container">
    <a class="navbar-brand" href="{{ url('/') }}">LaraBBS</a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <!-- Left Side of Navbar -->
      <ul class="navbar-nav mr-auto">

      </ul>

      <!-- Right Side of Navbar -->
      <ul class="navbar-nav navbar-right">
        <!-- Authentication Links -->
        @guest
          <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">登录</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">注册</a></li>
        @else
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img src="https://cdn.learnku.com/uploads/images/201709/20/1/PtDKbASVcz.png?imageView2/1/w/60/h/60" class="img-responsive img-circle" width="30px" height="30px">
              {{ Auth::user()->name }}
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="">个人中心</a>
              <a class="dropdown-item" href="">编辑资料</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" id="logout" href="#">
                <form method="post" action="{{ route('logout') }}">
                  @csrf
                  <button class="btn btn-block btn-danger" type="submit" name="button">退出</button>
                </form>
              </a>
            </div>
          </li>
        @endguest
>>>>>>> abad83c5dd66d92acef2f01f592a1b2e41580c2c
      </ul>
    </div>
  </div>
</nav>

