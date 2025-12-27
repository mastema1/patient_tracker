(function(){
  document.addEventListener('DOMContentLoaded', function(){
    // Fade-in after CSS loads
    document.body.classList.add('loaded');

    // Initialize theme from storage or system
    (function initTheme(){
      var saved = null;
      try{ saved = localStorage.getItem('theme'); }catch(e){}
      var prefersDark = false;
      try{ prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches; }catch(e){}
      var shouldDark = saved ? saved === 'dark' : prefersDark;
      document.documentElement.classList.toggle('dark-mode', !!shouldDark);
      applyChartTheme();
    })();

    // Theme toggle button
    var toggleBtn = document.getElementById('theme-toggle');
    if (toggleBtn){
      toggleBtn.addEventListener('click', function(){
        var isDark = document.documentElement.classList.toggle('dark-mode');
        try{ localStorage.setItem('theme', isDark ? 'dark' : 'light'); }catch(e){}
        applyChartTheme();
      });
    }

    // Search suggestions
    var input = document.getElementById('global-search');
    var box = document.getElementById('search-suggest');
    var form = document.getElementById('global-search-form');
    if (!input || !box || !form) return;

    var controller = null;
    var cache = {};
    var loadingEl = document.createElement('div');
    loadingEl.className = 'p-2 text-center';
    loadingEl.innerHTML = '<span class="loading-spinner"></span>';

    function hide(){ box.style.display = 'none'; box.innerHTML = ''; }
    function show(){ box.style.display = 'block'; }

    function render(items){
      box.innerHTML = '';
      if (!items || !items.length){ hide(); return; }
      items.forEach(function(it){
        var div = document.createElement('div');
        div.className = 'item';
        div.innerHTML = '<i class="fa-solid '+(it.type === 'Doctor' ? 'fa-user-doctor' : 'fa-hospital')+'"></i>'+
                        '<div><div class="fw-semibold">'+escapeHtml(it.label)+'</div><div class="type">'+it.type+'</div></div>';
        div.addEventListener('click', function(){
          if (it.url){ window.location.href = it.url; return; }
          input.value = it.label; form.submit();
        });
        box.appendChild(div);
      });
      show();
    }

    function escapeHtml(str){
      return String(str).replace(/[&<>"']/g, function(s){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[s]) });
    }

    var debounce;
    input.addEventListener('input', function(){
      var term = input.value.trim();
      if (term.length < 2){ hide(); return; }
      if (cache[term]){ render(cache[term]); return; }
      if (debounce) clearTimeout(debounce);
      debounce = setTimeout(function(){ fetchSuggest(term); }, 180);
    });

    input.addEventListener('focus', function(){
      var term = input.value.trim();
      if (term.length >= 2 && cache[term]){ render(cache[term]); }
    });

    document.addEventListener('click', function(e){
      if (!box.contains(e.target) && e.target !== input){ hide(); }
    });

    function fetchSuggest(term){
      try{
        if (controller) controller.abort();
      }catch(e){}
      controller = new AbortController();
      box.innerHTML = '';
      box.appendChild(loadingEl);
      show();
      fetch('/search?suggest=1&q='+encodeURIComponent(term), {
        headers: {'Accept':'application/json'},
        signal: controller.signal
      }).then(function(r){ return r.ok ? r.json() : []; })
        .then(function(data){ cache[term] = Array.isArray(data) ? data : []; render(cache[term]); })
        .catch(function(){ hide(); });
    }

    function applyChartTheme(){
      if (!window.Chart) return;
      var isDark = document.documentElement.classList.contains('dark-mode');
      try{
        Chart.defaults.color = isDark ? '#E2E8F0' : '#1A365D';
        Chart.defaults.borderColor = isDark ? '#334155' : '#E2E8F0';
        Chart.defaults.font.family = 'Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, Apple Color Emoji, Segoe UI Emoji';
        if (Chart.defaults.scales){
          // Grid line colors are picked from borderColor.
        }
        if (Chart.defaults.plugins && Chart.defaults.plugins.legend && Chart.defaults.plugins.legend.labels){
          Chart.defaults.plugins.legend.labels.usePointStyle = true;
          Chart.defaults.plugins.legend.labels.boxWidth = 10;
        }
      }catch(e){}
    }
  });
})();
