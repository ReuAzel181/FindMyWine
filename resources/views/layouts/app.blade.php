<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Wine Recommender - @yield('title', 'Home')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1A1A1A;
            --secondary: #B8860B;
            --accent: #DAA520;
            --bg-primary: #FDFBF7;
            --bg-secondary: #F5F1E8;
            --bg-accent: #EFE9DB;
            --text: #1A1A1A;
            --text-light: #666666;
            --gold: #DAA520;
            --gold-light: rgba(218, 165, 32, 0.1);
            --cream: #F9F6F0;
            --white: #FFFFFF;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            color: var(--text);
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            padding-top: 80px;
            padding-bottom: 0;
            background: var(--bg-primary);
            position: relative;
            overflow-x: hidden;
            letter-spacing: 0.3px;
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Elegant Navigation */
        .navbar {
            background: rgba(26, 26, 26, 0.98);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(218, 165, 32, 0.1);
            padding: 0.75rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            transition: all 0.3s ease;
            height: 80px;
        }
        
        .navbar-brand {
            color: var(--gold);
            font-family: 'Cormorant Garamond', serif;
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: 1px;
            position: relative;
            padding: 0.5rem 0;
        }

        .navbar-brand::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50%;
            height: 1px;
            background: linear-gradient(90deg, var(--gold), transparent);
            opacity: 0.5;
        }
        
        .nav-link {
            color: var(--white);
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.85rem;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--gold);
        }

        /* Luxurious Cards */
        .card {
            background: var(--white);
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            opacity: 0.5;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: var(--white);
            border-bottom: 1px solid rgba(218, 165, 32, 0.1);
            padding: 1.5rem;
        }

        .card-header h4 {
            color: var(--primary);
            margin: 0;
            font-size: 1.5rem;
        }

        /* Elegant Form Controls */
        .form-control, .form-select {
            border: 1px solid rgba(26, 26, 26, 0.1);
            border-radius: 6px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(218, 165, 32, 0.1);
        }

        /* Sophisticated Buttons */
        .btn-primary {
            background: var(--primary);
            border: 1px solid var(--primary);
            color: var(--white);
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary:hover {
            background: var(--gold);
            border-color: var(--gold);
            transform: translateY(-2px);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.5s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        /* Refined Background */
        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 50%, var(--bg-accent) 100%);
            opacity: 0.8;
        }

        .wine-swirl {
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(218, 165, 32, 0.05));
            filter: blur(80px);
            animation: swirl 30s infinite alternate;
            transform-origin: center;
            pointer-events: none;
        }

        /* Elegant Wine Icons */
        .particle {
            position: absolute;
            width: 20px;
            height: 20px;
            color: var(--gold);
            pointer-events: none;
            opacity: 0;
            animation: float 8s infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .particle i {
            opacity: 0.3;
            filter: drop-shadow(0 0 2px rgba(218, 165, 32, 0.2));
        }

        /* Refined Animations */
        @keyframes float {
            0% {
                transform: translate(0, 0) rotate(0deg);
                opacity: 0;
            }
            20% {
                opacity: 0.3;
            }
            80% {
                opacity: 0.3;
            }
            100% {
                transform: translate(var(--x-end), var(--y-end)) rotate(360deg);
                opacity: 0;
            }
        }

        @keyframes swirl {
            0% {
                transform: rotate(0deg) scale(1);
                opacity: 0.1;
            }
            50% {
                transform: rotate(180deg) scale(1.1);
                opacity: 0.15;
            }
            100% {
                transform: rotate(360deg) scale(1);
                opacity: 0.1;
            }
        }

        /* Elegant Footer */
        footer {
            background: var(--primary);
            color: var(--white);
            padding: 1.5rem 0;
            position: relative;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1020;
            border-top: 1px solid rgba(218, 165, 32, 0.1);
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-brand h5 {
            font-family: 'Cormorant Garamond', serif;
            margin: 0;
            font-size: 1.2rem;
            color: var(--gold);
            letter-spacing: 1px;
        }

        .footer-brand p {
            margin: 0.25rem 0 0;
            font-size: 0.8rem;
            opacity: 0.7;
            letter-spacing: 0.5px;
        }

        .footer-love {
            font-size: 0.8rem;
            opacity: 0.7;
            letter-spacing: 0.5px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }

            .navbar {
                height: 70px;
            }

            .navbar-brand {
                font-size: 1.5rem;
            }

            .nav-link {
                font-size: 0.8rem;
                padding: 0.5rem 1rem;
            }
        }

        /* Layout Structure */
        .container {
            max-width: 1800px; /* Increased container width */
            padding: 0 2rem;
            width: 100%;
            margin: 0 auto;
        }

        .main-content {
            flex: 1 0 auto;
            padding: 3rem 0;
            position: relative;
            z-index: 1;
            width: 100%;
            overflow: visible;
        }

        /* Grid Layout */
        .grid-layout {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 2.5rem;
            margin-bottom: 3rem;
        }

        /* Card Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        /* Wine Card Layout */
        .wine-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 0;
            overflow: visible;
        }

        .wine-card-header {
            padding: 2rem 2rem 1.5rem;
            border-bottom: 1px solid rgba(218, 165, 32, 0.1);
        }

        .wine-card-content {
            padding: 1.5rem 2rem;
            flex: 1;
        }

        .wine-card-footer {
            padding: 1.5rem 2rem;
            background: rgba(218, 165, 32, 0.02);
            border-top: 1px solid rgba(218, 165, 32, 0.1);
        }

        /* Wine Attributes Layout */
        .wine-attributes {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .wine-attribute {
            display: flex;
            flex-direction: column;
            background: rgba(255, 255, 255, 0.5);
            padding: 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .wine-attribute:hover {
            background: rgba(255, 255, 255, 0.8);
            transform: translateY(-2px);
        }

        .attribute-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .attribute-value {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem;
            color: var(--primary);
            font-weight: 600;
        }

        /* Price Display */
        .price-tag {
            display: inline-flex;
            align-items: center;
            background: var(--gold);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .price-tag::before {
            content: '₱';
            margin-right: 0.25rem;
        }

        /* Form Layout */
        .form-section {
            max-width: 1200px; /* Increased form width */
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.8);
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 2rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.75rem;
            color: var(--primary);
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 0.5rem;
            color: var(--gold);
        }

        .form-control, .form-select {
            padding: 0.875rem 1.125rem;
            font-size: 1rem;
            border-radius: 8px;
            background: white;
        }

        /* Helper Text */
        .helper-text {
            display: flex;
            align-items: center;
            color: var(--text-light);
            font-size: 0.9rem;
            margin-top: 0.75rem;
        }

        .helper-text i {
            margin-right: 0.5rem;
            font-size: 0.85rem;
        }

        /* User-friendly Tooltips */
        .tooltip-trigger {
            position: relative;
            display: inline-flex;
            align-items: center;
            cursor: help;
        }

        .tooltip-trigger i {
            margin-left: 0.5rem;
            color: var(--gold);
            font-size: 0.9rem;
        }

        /* Text coloring */
        .text-gold {
            color: var(--gold) !important;
        }

        /* Tooltip content */
        .tooltip-content {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            width: 200px;
            text-align: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .tooltip-trigger:hover .tooltip-content {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(5px);
        }

        /* Selection Highlights */
        ::selection {
            background: var(--gold);
            color: white;
        }

        /* Input Group Specific Styles */
        .input-group-text {
            border-color: rgba(26, 26, 26, 0.1);
            background-color: var(--bg-secondary);
            color: var(--text);
        }

        /* Custom Select Styling */
        .custom-select {
            height: auto !important;
            max-height: 240px !important;
            overflow-y: auto !important;
            overflow-x: hidden;
            background-color: var(--white);
            border-radius: 8px;
            border: 1px solid rgba(26, 26, 26, 0.1);
            padding: 0;
            position: relative;
            z-index: 10;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.03);
        }

        /* Custom scrollbar for select */
        .custom-select::-webkit-scrollbar {
            width: 8px;
            background-color: transparent;
        }
        
        .custom-select::-webkit-scrollbar-thumb {
            background-color: rgba(218, 165, 32, 0.2);
            border-radius: 10px;
        }
        
        .custom-select::-webkit-scrollbar-thumb:hover {
            background-color: rgba(218, 165, 32, 0.4);
        }
        
        .custom-select::-webkit-scrollbar-track {
            background-color: transparent;
            border-radius: 10px;
        }

        .custom-select option {
            padding: 10px 15px;
            margin-bottom: 1px;
            background-color: var(--white);
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
            height: 42px;
            line-height: 22px;
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 0.95rem;
        }

        .custom-select option:hover {
            background-color: var(--bg-secondary);
        }

        .custom-select option:checked {
            background-color: var(--gold-light) !important;
            color: var(--primary);
            font-weight: 500;
            border-left: 3px solid var(--gold);
        }

        .custom-select option:nth-child(odd) {
            background-color: var(--bg-primary);
        }

        /* Style for the multi-select element to look like a styled list */
        select[multiple] {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: none;
            padding: 0;
            background-color: var(--white);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }

        /* Remove the default outline and add a custom one */
        select[multiple]:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(218, 165, 32, 0.2);
        }
        
        /* Ensure card body properly contains all elements */
        .card-body {
            overflow: visible;
            position: relative;
            z-index: 1;
        }
        
        /* Fix for form rows */
        .row {
            position: relative;
            z-index: 1;
        }

        /* Star Rating Styles */
        .star-rating {
            display: flex;
            gap: 0.5rem;
        }

        .star {
            cursor: pointer;
            font-size: 1.5rem;
            color: #ccc;
            transition: color 0.3s ease;
        }

        .star:hover, .star.filled {
            color: var(--gold);
        }

        /* Responsive Adjustments */
        @media (max-width: 1600px) {
            .container {
                max-width: 1400px;
                padding: 0 2rem;
            }
        }

        @media (max-width: 1400px) {
            .container {
                max-width: 1200px;
            }
        }

        @media (max-width: 1200px) {
            .cards-grid {
                grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            }
        }

        @media (max-width: 992px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .wine-card {
                min-height: auto;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1.5rem;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .wine-attributes {
                grid-template-columns: 1fr;
            }

            .form-section {
                padding: 2rem;
            }
        }

        /* Card header with icon styling */
        .card-header h5 i {
            color: var(--gold);
        }

        /* Input styles to match the design */
        .input-group .input-group-text {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
            background-color: var(--bg-secondary);
            border-color: rgba(26, 26, 26, 0.1);
            color: var(--gold);
            width: 42px;
            display: flex;
            justify-content: center;
        }

        /* Section labels with icons */
        .form-label {
            font-weight: 500;
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }

        .form-label i {
            margin-right: 8px;
            font-size: 1.1rem;
        }

        /* Form text helper styling */
        .form-text {
            display: flex;
            align-items: center;
            margin-top: 0.5rem;
            color: var(--text-light);
            font-size: 0.85rem;
        }

        .form-text i {
            opacity: 0.7;
            margin-right: 5px;
        }

        /* Discovery mode toggle styling */
        .form-check-label {
            font-weight: 600;
            font-size: 1rem;
            color: var(--primary);
        }

        .form-check-input:checked {
            background-color: var(--gold);
            border-color: var(--gold);
        }
    </style>
    
    <!-- Fix for modal-backdrop issue -->
    <style>
        /* Force remove modal backdrop if it persists */
        .modal-backdrop.fade.show {
            display: none !important;
            opacity: 0 !important;
            z-index: -1 !important;
        }
        
        body.modal-open {
            overflow: auto !important;
            padding-right: 0 !important;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Dynamic Background -->
    <div class="background-animation">
        <div class="wine-bubbles" id="wineBubbles"></div>
        <div class="wine-swirl"></div>
        <div class="shimmer-layer"></div>
        <div id="particles"></div>
    </div>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('wine.home') }}">
                <i class="fas fa-wine-glass-alt me-2"></i>
                Wine Recommender
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('wine.home') }}">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('wine.profile.import.form') }}">
                            <i class="fas fa-upload me-1"></i> Import Profile
                        </a>
                    </li>
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dataset.index') }}">
                                <i class="fas fa-cog me-1"></i> Admin
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.account-settings') }}">
                                        <i class="fas fa-user-cog me-1"></i> Account Settings
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @yield('content')
        </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h5>Wine Recommender</h5>
                    <p>Your AI-powered guide to discovering exceptional wines</p>
                </div>
                <div class="footer-love">
                    <span>Crafted with</span>
                    <i class="fas fa-heart"></i>
                    <span>for wine enthusiasts</span>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Global modal backdrop cleanup - more aggressive approach
        $(document).ready(function() {
            // Handle standard modal closings
            $(document).on('hidden.bs.modal', '.modal', function() {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            });
            
            // Force cleanup for any stray modal backdrops every 500ms
            setInterval(function() {
                if (!$('.modal.show').length) {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    $('body').css('padding-right', '');
                }
            }, 500);
            
            // Add click handler to manually remove backdrop
            $(document).on('click', '.modal-backdrop', function() {
                $(this).remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
                $('.modal').modal('hide');
            });
        });
        
        // Create wine bubbles with enhanced randomization
        function createBubbles() {
            const bubbles = document.getElementById('wineBubbles');
            const bubbleCount = 20;

            for (let i = 0; i < bubbleCount; i++) {
                const bubble = document.createElement('div');
                bubble.className = 'bubble';
                
                // Random size between 10px and 50px
                const size = Math.random() * 40 + 10;
                bubble.style.width = `${size}px`;
                bubble.style.height = `${size}px`;
                
                // Random position and movement
                bubble.style.left = `${Math.random() * 100}%`;
                bubble.style.setProperty('--x-end', `${(Math.random() - 0.5) * 100}px`);
                
                // Random delay and duration
                const duration = 10 + Math.random() * 10;
                bubble.style.animation = `rise ${duration}s infinite`;
                bubble.style.animationDelay = `${Math.random() * 15}s`;
                
                bubbles.appendChild(bubble);
            }
        }

        // Updated createParticles function to use wine icons
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 20;
            const icons = ['wine-glass', 'wine-glass-alt', 'wine-bottle', 'glass-cheers'];

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Create icon element
                const icon = document.createElement('i');
                icon.className = `fas fa-${icons[Math.floor(Math.random() * icons.length)]}`;
                particle.appendChild(icon);
                
                // Random position
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                
                // Random movement
                particle.style.setProperty('--x-end', `${(Math.random() - 0.5) * 200}px`);
                particle.style.setProperty('--y-end', `${(Math.random() - 0.5) * 200}px`);
                
                // Random animation timing
                const duration = 4 + Math.random() * 4;
                particle.style.animation = `float ${duration}s infinite`;
                particle.style.animationDelay = `${Math.random() * 5}s`;
                
                particlesContainer.appendChild(particle);
            }
        }

        // Mouse interaction effect
        function addMouseInteraction() {
            document.addEventListener('mousemove', (e) => {
                const mouseX = e.clientX / window.innerWidth;
                const mouseY = e.clientY / window.innerHeight;
                
                document.querySelector('.wine-swirl').style.transform = 
                    `rotate(${mouseX * 20}deg) scale(${1 + mouseY * 0.1})`;
            });
        }

        // Initialize all effects
        document.addEventListener('DOMContentLoaded', () => {
            createBubbles();
            createParticles();
            addMouseInteraction();
        });

        // Existing scroll handler
        $(window).scroll(function() {
            if ($(window).scrollTop() > 10) {
                $('.navbar').addClass('scrolled');
            } else {
                $('.navbar').removeClass('scrolled');
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html> 