<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Management System - Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Academic Management System</h1>
            <p class="subtitle">Database Project 2025</p>
        </header>

        <main>
            <div class="search-options">
                <h2>Available Features</h2>
                
                <!-- Assignment 6: Search Component -->
                <div class="search-card">
                    <div class="card-icon">📚</div>
                    <h3>Course Popularity Analysis</h3>
                    <p>Search and analyze courses by popularity, semester, instructors, and student enrollment statistics.</p>
                    <a href="courses/search_form.php" class="btn btn-primary">Search Courses</a>
                </div>

                <div class="search-card">
                    <div class="card-icon">👥</div>
                    <h3>Team Composition & Projects</h3>
                    <p>Explore team compositions, project involvement, and team member statistics.</p>
                    <a href="teams/search_form.php" class="btn btn-primary">Search Teams</a>
                </div>

                <!-- Assignment 9: Linked Services -->
                <div class="search-card">
                    <div class="card-icon">🌍</div>
                    <h3>IP Geolocation Service</h3>
                    <p>View your current location on an interactive map based on your IP address using linked services.</p>
                    <a href="geolocation.php" class="btn btn-primary">View My Location</a>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2025 Academic Management System | Database Project</p>
            <p style="font-size: 0.9em; color: #999;">
                Assignments: 6 (Search Component) | 9 (Linked Services)
            </p>
        </footer>
    </div>
</body>
</html>