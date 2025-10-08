// Simple client-side search across key pages' headings/links
// Collects searchable data and filters based on query; navigates to a results page

document.addEventListener('DOMContentLoaded', function(){
  // Attach to all search inputs in header
  document.querySelectorAll('.search-container input[type="text"]').forEach(inp => {
    // submit on Enter
    inp.addEventListener('keydown', function(e){
      if(e.key === 'Enter'){
        e.preventDefault();
        const q = inp.value.trim();
        if(!q) return;
        // Go to results page with query param
        window.location.href = `search.html?q=${encodeURIComponent(q)}`;
      }
    });
  });
});


