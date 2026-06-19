<nav class="sticky top-0 z-50 w-full bg-card border-b border-border shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      <a href="/" class="flex items-center gap-2">
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center"><iconify-icon icon="lucide:shield" class="text-primary-foreground text-2xl"></iconify-icon></div>
        <span class="text-xl font-bold text-primary tracking-tight">Aleto Clan Portal</span>
      </a>
      <div class="hidden lg:flex items-center gap-6">
        <a href="/" class="{{ request()->is('/') ? 'text-primary font-semibold border-b-2 border-primary pb-1' : 'text-muted-foreground hover:text-primary transition-colors' }}">Home</a>
        <a href="/about" class="{{ request()->is('about') ? 'text-primary font-semibold border-b-2 border-primary pb-1' : 'text-muted-foreground hover:text-primary transition-colors' }}">About</a>
        <a href="/members" class="{{ request()->is('members') ? 'text-primary font-semibold border-b-2 border-primary pb-1' : 'text-muted-foreground hover:text-primary transition-colors' }}">Members</a>
        <a href="/verify" class="{{ request()->is('verify') ? 'text-primary font-semibold border-b-2 border-primary pb-1' : 'text-muted-foreground hover:text-primary transition-colors' }}">Verify</a>
        <a href="/transparency" class="{{ request()->is('transparency') ? 'text-primary font-semibold border-b-2 border-primary pb-1' : 'text-muted-foreground hover:text-primary transition-colors' }}">Transparency</a>
        <a href="/contact" class="{{ request()->is('contact') ? 'text-primary font-semibold border-b-2 border-primary pb-1' : 'text-muted-foreground hover:text-primary transition-colors' }}">Contact</a>
        <a href="/track-ticket" class="bg-primary text-primary-foreground px-5 py-2 rounded-lg font-medium hover:opacity-90">Track Ticket</a>
      </div>
      <button class="lg:hidden p-2 text-muted-foreground" onclick="document.getElementById('mobileNav').classList.toggle('hidden')">
        <iconify-icon icon="lucide:menu" class="text-2xl"></iconify-icon>
      </button>
    </div>
    <div id="mobileNav" class="hidden lg:hidden pb-4 space-y-2">
      <a href="/" class="block py-2 text-foreground">Home</a>
      <a href="/about" class="block py-2 text-foreground">About</a>
      <a href="/members" class="block py-2 text-foreground">Members</a>
      <a href="/verify" class="block py-2 text-foreground">Verify</a>
      <a href="/transparency" class="block py-2 text-foreground">Transparency</a>
      <a href="/contact" class="block py-2 text-foreground">Contact</a>
      <a href="/track-ticket" class="block py-2 text-primary font-bold">Track Ticket</a>
    </div>
  </div>
</nav>
