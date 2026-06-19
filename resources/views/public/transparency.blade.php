<!DOCTYPE html>
<html lang="en">
<head>
    @include('public.partials.head')
    <title>Transparency Dashboard - Aleto Clan Portal</title>
</head>
<body class="bg-background font-sans text-foreground">
@include('public.partials.nav')

<main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
  <div class="mb-8">
    <h1 class="text-3xl font-bold text-secondary">Transparency Dashboard</h1>
    <p class="text-muted-foreground">Real-time oversight of community funds and development projects.</p>
  </div>

  <!-- Stats Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="p-6 bg-card border border-border rounded-2xl shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary"><iconify-icon icon="lucide:users" class="text-xl"></iconify-icon></div>
      </div>
      <p class="text-sm font-medium text-muted-foreground mb-1">Total Members</p>
      <p class="text-2xl font-bold text-secondary" id="tTotal">-</p>
    </div>
    <div class="p-6 bg-card border border-border rounded-2xl shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <div class="w-10 h-10 bg-tertiary/10 rounded-lg flex items-center justify-center text-tertiary"><iconify-icon icon="lucide:check-circle" class="text-xl"></iconify-icon></div>
        <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded">Active</span>
      </div>
      <p class="text-sm font-medium text-muted-foreground mb-1">Active Members</p>
      <p class="text-2xl font-bold text-secondary" id="tActive">-</p>
    </div>
    <div class="p-6 bg-card border border-border rounded-2xl shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <div class="w-10 h-10 bg-secondary/10 rounded-lg flex items-center justify-center text-secondary"><iconify-icon icon="lucide:archive" class="text-xl"></iconify-icon></div>
      </div>
      <p class="text-sm font-medium text-muted-foreground mb-1">Deceased / Archived</p>
      <p class="text-2xl font-bold text-secondary" id="tInactive">-</p>
    </div>
    <div class="p-6 bg-card border border-border rounded-2xl shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600"><iconify-icon icon="lucide:credit-card" class="text-xl"></iconify-icon></div>
      </div>
      <p class="text-sm font-medium text-muted-foreground mb-1">Grant Programs</p>
      <p class="text-2xl font-bold text-secondary" id="tGrants">-</p>
    </div>
  </div>

  <!-- Grant Beneficiaries -->
  <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden mb-8">
    <div class="p-6 border-b border-border">
      <h3 class="text-lg font-bold text-secondary">Grant Beneficiaries by Program</h3>
      <p class="text-sm text-muted-foreground">Number of verified beneficiaries per grant cycle.</p>
    </div>
    <div class="p-6" id="grantsList">
      <p class="text-muted-foreground text-sm">Loading...</p>
    </div>
  </div>

  <!-- Accountability Note -->
  <div class="bg-primary/5 border border-primary/10 rounded-2xl p-8 text-center">
    <iconify-icon icon="lucide:shield-check" class="text-4xl text-primary mb-4"></iconify-icon>
    <h3 class="text-xl font-bold text-secondary mb-2">Accountability is Our Foundation</h3>
    <p class="text-muted-foreground max-w-xl mx-auto">Every transaction, registration, and grant distribution is logged in our immutable audit trail. Community members can verify numbers are not inflated and that funds reach the right people.</p>
  </div>
</main>

<footer class="bg-secondary text-white py-12 mt-8">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row justify-between items-center gap-6 border-b border-white/10 pb-8 mb-8">
      <div class="flex items-center gap-3"><iconify-icon icon="lucide:shield" class="text-tertiary text-3xl"></iconify-icon><div><h4 class="font-bold text-lg">Institutional Integrity</h4><p class="text-xs text-secondary-foreground/60">Aleto Clan Social Registry & Transparency Portal</p></div></div>
    </div>
    <p class="text-center text-xs text-secondary-foreground/40">&copy; {{ date('Y') }} Aleto Clan Community Portal. Accountability is our Foundation.</p>
  </div>
</footer>

<script>
fetch('/api/public/stats').then(r=>r.json()).then(d=>{
  document.getElementById('tTotal').textContent=(d.total_members||0).toLocaleString();
  document.getElementById('tActive').textContent=(d.active||0).toLocaleString();
  document.getElementById('tInactive').textContent=((d.deceased||0)+(d.archived||0)).toLocaleString();
  document.getElementById('tGrants').textContent=(d.grant_stats||[]).length;
  const gl=document.getElementById('grantsList');
  if(d.grant_stats&&d.grant_stats.length){
    gl.innerHTML=d.grant_stats.map(g=>`<div class="flex items-center justify-between py-3 border-b border-border last:border-0"><div><p class="font-semibold text-secondary">${g.name}</p></div><div class="text-right"><span class="text-lg font-bold text-primary">${g.beneficiaries}</span><span class="text-xs text-muted-foreground ml-1">beneficiaries</span></div></div>`).join('');
  } else { gl.innerHTML='<p class="text-muted-foreground text-sm">No grant data available yet.</p>'; }
}).catch(()=>{});
</script>
</body>
</html>
