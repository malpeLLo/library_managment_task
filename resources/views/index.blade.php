<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление библиотекой</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .modal { display: none; }
        .modal.show { display: flex; }
        .card { transition: transform 0.2s; }
        .card:hover { transform: scale(1.02); }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4">Управление библиотекой</h1>
        
        <!-- Filters and Search -->
        <div class="mb-4 flex flex-wrap gap-4">
            <select id="genreFilter" class="p-2 border rounded">
                <option value="">Все жанры</option>
                <option value="Антиутопия">Антиутопия</option>
                <option value="Фэнтези">Фэнтези</option>
                <option value="Научная фантастика">Научная фантастика</option>
            </select>
            <input id="searchInput" type="text" placeholder="Найти по названию или автору" class="p-2 border rounded">
            <select id="sortSelect" class="p-2 border rounded">
                <option value="title|asc">Название (А-Я)</option>
                <option value="title|desc">Название (Я-А)</option>
                <option value="publication_year|asc">Год (От самого старого)</option>
                <option value="publication_year|desc">Год (От самого нового)</option>
            </select>
            <button id="addBookBtn" class="bg-blue-500 text-black px-4 py-2 rounded">Добавить книгу</button>
        </div>

        <!-- Statistics -->
        <div id="stats" class="mb-4">Всего книг: 0</div>

        <!-- Books Grid -->
        <div id="booksGrid" class="grid grid-cols-1 md:grid-cols-3 gap-4"></div>

        <!-- Modal -->
        <div id="bookModal" class="modal fixed inset-0 bg-black bg-opacity-50 justify-center items-center">
            <div class="bg-white p-6 rounded-lg w-full max-w-md">
                <h2 id="modalTitle" class="text-xl font-bold mb-4"></h2>
                <form id="bookForm">
                    <div class="mb-4">
                        <label class="block">Название</label>
                        <input type="text" name="title" class="w-full p-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block">Автор</label>
                        <input type="text" name="author" class="w-full p-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block">Год публикации</label>
                        <input type="number" name="publication_year" class="w-full p-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label class="block">Жанр</label>
                        <select name="genre" class="w-full p-2 border rounded" required>
                            <option value="Антиутопия">Антиутопия</option>
                            <option value="Фэнтези">Фэнтези</option>
                            <option value="Научаная фантастика">Научаная фантастика</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block">Описание</label>
                        <textarea name="description" class="w-full p-2 border rounded"></textarea>
                    </div>
                    <div>
                    <div class="mb-4">
                        <button type="submit" type="submit" class="bg-green-500 text-black px-4 py-2 rounded">Сохранить</button>
                        <button type="button" id="closeModal" class="bg-red-500 text-black px-4 py-2 rounded">Закрыть</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const apiUrl = '/api/books';
        let editingBookId = null;

        // Get elements
        const booksGrid = document.getElementById('booksGrid');
        const bookModal = document.getElementById('bookModal');
        const bookForm = document.getElementById('bookForm');
        const modalTitle = document.getElementById('modalTitle');
        const addBookBtn = document.getElementById('addBookBtn');
        const closeModal = document.getElementById('closeModal');
        const genreFilter = document.getElementById('genreFilter');
        const searchInput = document.getElementById('searchInput');
        const sortSelect = document.getElementById('sortSelect');
        const stats = document.getElementById('stats');

        // Fetch and render books
        async function fetchBooks() {
            const params = new URLSearchParams();
            if (genreFilter.value) params.append('genre', genreFilter.value);
            if (searchInput.value) params.append('search', searchInput.value);
            const [sortField, direction] = sortSelect.value.split('|');
            params.append('sort', sortField);
            params.append('direction', direction);

            const response = await fetch(`${apiUrl}?${params}`);
            const data = await response.json();

            booksGrid.innerHTML = '';
            data.books.forEach(book => {
                const card = document.createElement('div');
                card.className = 'card bg-white p-4 rounded shadow';
                card.innerHTML = `
                    <h3 class="text-lg font-bold">${book.title}</h3>
                    <p>Автор: ${book.author}</p>
                    <p>Год: ${book.publication_year || '-'}</p>
                    <p>Жанр: ${book.genre}</p>
                    <p>Описание: ${book.description || 'No description'}</p>
                    <div class="mt-2">
                        <button onclick="editBook(${book.id})" class="bg-yellow-500 text-black px-2 py-1 rounded mr-2">Редактировать</button>
                        <button onclick="deleteBook(${book.id})" class="bg-red-500 text-black px-2 py-1 rounded">Удалить</button>
                    </div>
                `;
                booksGrid.appendChild(card);
            });

            stats.textContent = `Всего книг: ${data.total}`;
        }

        // Show modal for adding/editing
        function showModal(title, book = {}) {
            modalTitle.textContent = title;
            bookModal.classList.add('show');
            bookForm.reset();
            if (book.id) {
                editingBookId = book.id;
                Object.keys(book).forEach(key => {
                    const input = bookForm.elements[key];
                    if (input) input.value = book[key] || '';
                });
            } else {
                editingBookId = null;
            }
        }

        // Edit book
        async function editBook(id) {
            const response = await fetch(`${apiUrl}/${id}`);
            const book = await response.json();
            showModal('Edit Book', book);
        }

        // Delete book
        async function deleteBook(id) {
            if (confirm('Are you sure you want to delete this book?')) {
                await fetch(`${apiUrl}/${id}`, { method: 'DELETE' });
                fetchBooks();
            }
        }

        // Handle form submission
        bookForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(bookForm);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch(editingBookId ? `${apiUrl}/${editingBookId}` : apiUrl, {
                    method: editingBookId ? 'PUT' : 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    const errors = await response.json();
                    alert('Validation errors: ' + JSON.stringify(errors.errors));
                    return;
                }

                bookModal.classList.remove('show');
                fetchBooks();
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });

        // Event listeners
        addBookBtn.addEventListener('click', () => showModal('Add Book'));
        closeModal.addEventListener('click', () => bookModal.classList.remove('show'));
        genreFilter.addEventListener('change', fetchBooks);
        searchInput.addEventListener('input', fetchBooks);
        sortSelect.addEventListener('change', fetchBooks);

        // Initial fetch
        fetchBooks();
    </script>
</body>
</html>