<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Members - Aleto Clan Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f8f9fa; }
        .navbar-custom { background: #1a5276; }
        .member-card { transition: transform 0.2s; border-radius: 12px; overflow: hidden; border: none; }
        .member-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
        .member-photo { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #e0e0e0; margin: 0 auto; display: block; }
        .member-photo-placeholder { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #1a5276, #27ae60); color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-weight: bold; font-size: 20px; }
        .search-bar { max-width: 500px; margin: 0 auto; }
        .stats-bar { background: #1a5276; color: white; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark navbar-custom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">🏘️ Aleto Clan Portal</a>
        <a href="/" class="btn btn-outline-light btn-sm">← Back to Home</a>
    </div>
</nav>

<!-- Stats Bar -->
<div class="stats-bar py-2">
    <div class="container text-center">
        <span class="me-4"><strong id="totalCount">-</strong> Total Members</span>
        <span class="me-4 text-success"><strong id="activeCount">-</strong> Active</span>
        <span class="text-warning"><strong id="archivedCount">-</strong> Archived</span>
    </div>
</div>

<!-- Search -->
<div class="container py-4">
    <h2 class="text-center mb-2">Community Members</h2>
    <p class="text-center text-muted mb-4">All registered and verified members of the Aleto Clan.</p>

    <div class="search-bar mb-4">
        <div class="input-group">
            <input type="text" class="form-control" id="searchInput" placeholder="Search by name...">
            <button class="btn btn-primary" onclick="searchMembers()"><i class="bi bi-search"></i></button>
        </div>
    </div>

    <!-- Members Grid -->
    <div class="row g-3" id="membersGrid"></div>

    <!-- Load More -->
    <div class="text-center mt-4" id="loadMoreSection" style="display:none;">
        <button class="btn btn-outline-primary" onclick="loadMore()">Load More Members</button>
    </div>

    <div class="text-center mt-3" id="loadingIndicator" style="display:none;">
        <div class="spinner-border text-primary"></div>
    </div>
</div>

<script>
    let currentPage = 1;
    let search = '';

    // Load stats
    fetch('/api/public/stats')
        .then(r => r.json())
        .then(data => {
            document.getElementById('totalCount').textContent = data.total_members;
            document.getElementById('activeCount').textContent = data.active;
            document.getElementById('archivedCount').textContent = data.archived;
        });

    // Load members
    function loadMembers(page, append) {
        document.getElementById('loadingIndicator').style.display = 'block';
        const params = new URLSearchParams({ page, per_page: 24 });
        if (search) params.append('search', search);

        fetch(`/api/public/members?${params}`)
            .then(r => r.json())
            .then(data => {
                const grid = document.getElementById('membersGrid');
                if (!append) grid.innerHTML = '';

                (data.data || []).forEach(m => {
                    const photo = m.passport_photo ? `/storage/${m.passport_photo}` : '';
                    const initials = m.full_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                    grid.innerHTML += `
                        <div class="col-6 col-md-4 col-lg-2">
                            <div class="card member-card text-center shadow-sm h-100">
                                <div class="card-body p-3">
                                    ${photo
                                        ? `<img src="${photo}" class="member-photo" alt="${m.full_name}">`
                                        : `<div class="member-photo-placeholder">${initials}</div>`
                                    }
                                    <p class="mb-0 mt-2 fw-semibold small">${m.full_name}</p>
                                    <p class="mb-0" style="font-size:10px;"><code>${m.unique_id}</code></p>
                                    ${m.village ? `<p class="mb-0 text-muted" style="font-size:10px;">${m.village}</p>` : ''}
                                </div>
                            </div>
                        </div>`;
                });

                document.getElementById('loadingIndicator').style.display = 'none';
                document.getElementById('loadMoreSection').style.display = data.last_page > currentPage ? 'block' : 'none';
            });
    }

    function loadMore() {
        currentPage++;
        loadMembers(currentPage, true);
    }

    function searchMembers() {
        search = document.getElementById('searchInput').value.trim();
        currentPage = 1;
        loadMembers(1, false);
    }

    // Search on Enter key
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') searchMembers();
    });

    // Initial load
    loadMembers(1, false);
</script>
</body>
</html>
