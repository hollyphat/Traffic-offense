var routes=[ // Index page
  {
      path: '/', url: './index.html', name: 'home',
  }
  ,
  {
      path: '/about/', url: './pages/about.html', name: 'about',
  }
  ,
  {
      path: '/login/', componentUrl: './pages/login.html', name: 'login',
  }
  ,
  {
      path: '/register/', componentUrl: './pages/register.html', name: 'login',
  }
  ,
  {
      path: '/home/', componentUrl: './pages/home.html', name: 'user-home',
  }

  ,
  {
      path: '/offense/', componentUrl: './pages/offense.html', name: 'offense',
  }
  ,
  {
      path: '/profile/', componentUrl: './pages/profile.html', name: 'profile',
  }
  ,
  {
      path: '/offenders/', componentUrl: './pages/edit-offenders.html', name: 'edit-offenders',
  }
  ,
  {
      path: '/new/', componentUrl: './pages/new.html', name: 'new-offenders',
  },
  ,
  {
      path: '/search/', componentUrl: './pages/search.html', name: 'search',
  }
  , // Default route (404 page). MUST BE THE LAST
  {
      path: '(.*)', url: './pages/404.html',
  }
  
  ];