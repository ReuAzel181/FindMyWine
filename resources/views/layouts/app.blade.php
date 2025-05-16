<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wine Recommender - @yield('title', 'Home')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --wine-red: #722F37;
            --wine-burgundy: #4E0E2E;
            --wine-gold: #D4AF37;
            --wine-cream: #F5F5DC;
            --wine-cork: #9E6F50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .navbar {
            background-color: var(--wine-burgundy);
        }
        
        .navbar-brand {
            color: var(--wine-gold);
            font-weight: bold;
        }
        
        .nav-link {
            color: var(--wine-cream);
        }
        
        .nav-link:hover {
            color: var(--wine-gold);
        }
        
        .btn-primary {
            background-color: var(--wine-burgundy);
            border-color: var(--wine-burgundy);
        }
        
        .btn-primary:hover {
            background-color: var(--wine-red);
            border-color: var(--wine-red);
        }
        
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: var(--wine-burgundy);
            color: white;
            font-weight: bold;
        }
        
        footer {
            background-color: var(--wine-burgundy);
            color: var(--wine-cream);
            padding: 20px 0;
            margin-top: 50px;
        }
        
        .wine-card {
            border-left: 3px solid var(--wine-red);
        }
        
        .wine-rating {
            color: var(--wine-gold);
        }
        
        .star-rating {
            color: #ddd;
            font-size: 24px;
        }
        
        .star-rating .filled {
            color: var(--wine-gold);
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('wine.index') }}">
                <i class="fas fa-wine-glass-alt me-2"></i>
                Wine Recommender
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('wine.index') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('wine.profile.import.form') }}">Import Profile</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dataset.index') }}">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @yield('content')
    </div>
    
    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} Wine Recommender - A Desktop Wine Recommendation System</p>
        </div>
    </footer>
    
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
    
    @yield('scripts')
</body>
</html> 