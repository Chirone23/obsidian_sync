/* Bibliò — minimal interactions */
(function(){
  // Highlight current nav link if WP didn't mark it
  document.addEventListener('DOMContentLoaded', function(){
    var path = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(function(a){
      try {
        var u = new URL(a.href);
        if (u.pathname !== '/' && path.indexOf(u.pathname) === 0) a.classList.add('active');
      } catch(e){}
    });
  });
})();
