document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const genreSelect = document.getElementById('genre-select');
    const yearInput = document.getElementById('year-input');
    const ratingSelect = document.getElementById('rating-select');
    const isAdminInput = document.getElementById('is-admin');
    const movieGrid = document.getElementById('movie-grid');

    const isAdmin = isAdminInput && isAdminInput.value === '1';

    if (searchInput) searchInput.addEventListener('input', debounce(() => fetchMovies(), 300));
    if (genreSelect) genreSelect.addEventListener('change', () => fetchMovies());
    if (yearInput) yearInput.addEventListener('input', debounce(() => fetchMovies(), 300));
    if (ratingSelect) ratingSelect.addEventListener('change', () => fetchMovies());

    function fetchMovies() {
        const query = searchInput ? searchInput.value : '';
        const genre = genreSelect ? genreSelect.value : '';
        const year = yearInput ? yearInput.value : '';
        const rating = ratingSelect ? ratingSelect.value : '';



        let url = `api/search.php?q=${encodeURIComponent(query)}&genre=${encodeURIComponent(genre)}&year=${encodeURIComponent(year)}&rating=${encodeURIComponent(rating)}`;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                renderMovies(data);
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
    }

    function renderMovies(movies) {
        if (!movieGrid) return;
        movieGrid.innerHTML = '';

        if (!movies || movies.length === 0) {
            movieGrid.innerHTML = '<p style="color:var(--text-muted); grid-column: 1/-1; text-align: center; padding: 2rem;">No movies found matching your filters.</p>';
            return;
        }

        movies.forEach(movie => {
            const cardLink = document.createElement('a');
            cardLink.href = `movie.php?id=${movie.id}`;
            cardLink.className = 'movie-card-link';

            let adminActions = '';
            if (isAdmin) {
                adminActions = `
                    <div class="actions" style="margin-top: 10px;">
                        <object><a href="edit.php?id=${movie.id}" class="btn btn-primary" style="font-size: 0.8rem; height: auto; display: inline-block;">Edit</a></object>
                        <object><a href="delete.php?id=${movie.id}" class="btn btn-danger" style="font-size: 0.8rem; height: auto; display: inline-block; margin-left: 5px;" 
                            onclick="event.stopPropagation(); return confirm('Are you sure?');">Delete</a></object>
                    </div>
                `;
            }

            cardLink.innerHTML = `
                <div class="movie-card">
                    ${movie.poster_url ? `<img src="${movie.poster_url}" alt="${movie.title}" class="movie-poster">` : ''}
                    <div class="movie-info">
                        <h3 class="movie-title">${movie.title}</h3>
                        <div class="movie-meta">
                            <span>${movie.release_year}</span>
                            <span class="rating">★ ${movie.rating}</span>
                        </div>
                        ${adminActions}
                    </div>
                </div>
            `;
            movieGrid.appendChild(cardLink);
        });
    }

    // Mobile Menu Toggle
    const menuToggle = document.getElementById('menu-toggle');
    const navMenu = document.getElementById('nav-menu');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            // Animate toggle lines
            const spans = menuToggle.querySelectorAll('span');
            menuToggle.classList.toggle('active');
        });
    }

    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }
});
