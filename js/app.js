// Dom7
var $$ = Dom7;

// Theme
var theme = 'md';

// Init App
var app = new Framework7({  
  id: 'ng.com.onlinemedia.tods',
  name: app_name,
  view: {
    pushState: true
  },
  root: '#app',
  theme: theme,
  routes: routes,
  template7Pages: true, // enable Template7 rendering for Ajax and Dynamic pages
  precompileTemplates: true,
});
var mainView = app.views.create('.view-main');