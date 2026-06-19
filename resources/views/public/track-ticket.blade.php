<!DOCTYPE html>
<html lang="en">
<head>
    @include('public.partials.head')
    <title>Track Ticket - Aleto Clan Portal</title>
</head>
<body class="bg-background font-sans text-foreground">
@include('public.partials.nav')

<main class="flex-1 flex flex-col items-center py-16 px-4">
  <div class="max-w-3xl w-full">
    <div class="text-center mb-12">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-2xl text-primary mb-6">
        <iconify-icon icon="lucide:search" class="text-3xl"></iconify-icon>
      </div>
      <h1 class="text-3xl font-bold text-secondary mb-3">Track Your Application</h1>
      <p class="text-muted-foreground">Enter your unique ticket ID to check the real-time status of your enquiry, complaint, or grant application.</p>
    </div>

    <!-- Search Box -->
    <div class="bg-card border border-border rounded-3xl p-8 shadow-sm mb-12">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
          <iconify-icon icon="lucide:tag" class="absolute left-4 top-1/2 -translate-y-1/2 text-muted-foreground text-xl"></iconify-icon>
          <input type="text" id="ticketInput" placeholder="Enter Ticket ID (e.g., TKT-XXXXXXXX)" class="w-full pl-12 pr-4 py-4 bg-muted/30 border border-border rounded-2xl text-lg font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
        </div>
        <button onclick="trackTicket()" class="px-8 py-4 bg-primary text-primary-foreground rounded-2xl font-bold text-lg hover:opacity-90 transition-opacity flex items-center justify-center gap-2">
          Track <iconify-icon icon="lucide:arrow-right"></iconify-icon>
        </button>
      </div>
    </div>

    <!-- Result -->
    <div id="trackResult" style="display:none;"></div>
  </div>
</main>

<footer class="bg-secondary text-white py-12 mt-auto">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
    <div class="flex items-center gap-3"><iconify-icon icon="lucide:shield" class="text-tertiary text-2xl"></iconify-icon><span class="text-sm font-bold">Aleto Clan Tracking System</span></div>
    <div class="flex gap-6 text-xs text-secondary-foreground/60"><a href="/about" class="hover:text-white">Terms of Use</a><a href="/contact" class="hover:text-white">Contact Support</a></div>
  </div>
</footer>

<script>
function trackTicket(){
  const tid=document.getElementById('ticketInput').value.trim();
  if(!tid)return alert('Please enter a Ticket ID');
  fetch('/api/enquiries/track',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({ticket_id:tid})})
  .then(r=>r.json()).then(d=>{
    const el=document.getElementById('trackResult');el.style.display='block';
    if(d.data){const t=d.data;
      const statusColors={pending:'bg-amber-100 text-amber-800',in_progress:'bg-blue-100 text-blue-800',resolved:'bg-green-100 text-green-800',closed:'bg-gray-100 text-gray-800'};
      el.innerHTML=`
        <div class="bg-card border border-border rounded-3xl overflow-hidden shadow-lg border-t-4 border-t-primary">
          <div class="p-8 border-b border-border bg-muted/10">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
              <div><p class="text-xs font-bold text-primary uppercase tracking-widest mb-1">Ticket Found</p><h3 class="text-xl font-bold text-secondary">${t.subject}</h3><p class="text-sm text-muted-foreground">Reference: ${t.ticket_id}</p></div>
              <div class="px-4 py-2 ${statusColors[t.status]||'bg-gray-100 text-gray-800'} rounded-full text-sm font-bold flex items-center gap-2"><span class="w-2 h-2 bg-current rounded-full"></span>${t.status.replace('_',' ').toUpperCase()}</div>
            </div>
          </div>
          <div class="p-8">
            <div class="space-y-4">
              <div class="flex justify-between text-sm"><span class="text-muted-foreground">Type:</span><span class="font-semibold capitalize">${t.type}</span></div>
              <div class="flex justify-between text-sm"><span class="text-muted-foreground">Submitted:</span><span class="font-semibold">${t.submitted_at}</span></div>
              ${t.responded_at?`<div class="flex justify-between text-sm"><span class="text-muted-foreground">Responded:</span><span class="font-semibold">${t.responded_at}</span></div>`:''}
            </div>
            ${t.response?`<div class="mt-6 p-6 bg-primary/5 border border-primary/10 rounded-2xl"><div class="flex gap-3"><div class="w-10 h-10 bg-primary text-primary-foreground rounded-xl flex items-center justify-center shrink-0"><iconify-icon icon="lucide:message-circle" class="text-xl"></iconify-icon></div><div><h4 class="font-bold text-secondary mb-1">Admin Response</h4><p class="text-sm text-muted-foreground">${t.response}</p></div></div></div>`:`<div class="mt-6 p-4 bg-amber-50 border border-amber-100 rounded-xl text-amber-800 text-sm"><iconify-icon icon="lucide:clock" class="mr-1"></iconify-icon>No response yet. Please check back later.</div>`}
          </div>
        </div>`;
    } else {
      el.innerHTML=`<div class="bg-card border border-border rounded-3xl p-8 text-center"><iconify-icon icon="lucide:search-x" class="text-5xl text-muted-foreground mb-4"></iconify-icon><h3 class="text-xl font-bold text-secondary mb-2">Ticket Not Found</h3><p class="text-muted-foreground">Please double-check your ticket ID and try again.</p></div>`;
    }
  }).catch(()=>{document.getElementById('trackResult').style.display='block';document.getElementById('trackResult').innerHTML=`<div class="p-6 bg-red-50 border border-red-200 rounded-xl text-red-800 text-center">Error looking up ticket. Please try again.</div>`;});
}
document.getElementById('ticketInput').addEventListener('keypress',function(e){if(e.key==='Enter')trackTicket();});
</script>
</body>
</html>
