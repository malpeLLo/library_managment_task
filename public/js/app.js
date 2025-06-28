import axios from 'axios';
import * as bootstrap from 'bootstrap';

// Fetch and display books
async function loadBooks(params = {}) {
    try {
        const response = await axios.get('/api/books', { params });
        const books = response.data.books;
        const total = response.data.total;
        const bookList = document.getElementById('book-list');
        const totalBooks = document.getElementById('total-books');

        bookList.innerHTML = books.map(book => `
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">${book.title}</h5>
                    <p class="card-text">Author: ${book.author}</p>
                    <p class="card-text">Year: ${book.publication_year || 'N/A'}</p>
                    <p class="card-text">Genre: ${book.genre}</p>
                    <p class="card-text">${book.description || ''}</p>
                    <button class="btn btn-primary" onclick="editBook(${book.id})">Edit</button>
                    <button class="btn btn-danger" onclick="deleteBook(${book.id})">Delete</button>
                </div>
            </div>
        `).join('');
        totalBooks.textContent = `Total Books: ${total}`;
    } catch (error) {
        console.error('Error loading books:', error);
    }
}

// Handle add book form submission
document.getElementById('add-book-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    try {
        await axios.post('/api/books', Object.fromEntries(formData));
        bootstrap.Modal.getInstance(document.getElementById('addBookModal')).hide();
        loadBooks();
    } catch (error) {
        console.error('Error adding book:', error);
    }
});

// Filter by genre
document.getElementById('genre-filter')?.addEventListener('change', (e) => {
    loadBooks({ genre: e.target.value });
});

// Search
document.getElementById('search-input')?.addEventListener('input', (e) => {
    loadBooks({ search: e.target.value });
});

// Sort
document.getElementById('sort-filter')?.addEventListener('change', (e) => {
    const [sort, direction] = e.target.value.split('-');
    loadBooks({ sort, direction });
});

// Placeholder for edit and delete
window.editBook = (id) => {
    console.log('Edit book:', id);
    // Add modal logic here
};
window.deleteBook = async (id) => {
    if (confirm('Are you sure?')) {
        try {
            await axios.delete(`/api/books/${id}`);
            loadBooks();
        } catch (error) {
            console.error('Error deleting book:', error);
        }
    }
};

// Initialize
document.addEventListener('DOMContentLoaded', () => loadBooks());