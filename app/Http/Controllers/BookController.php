<?php
namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->has('genre')) {
            $query->where('genre', $request->genre);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('author', 'like', "%$search%");
            });
        }

        if ($request->has('sort')) {
            $sortField = $request->sort === 'title' ? 'title' : 'publication_year';
            $query->orderBy($sortField, $request->get('direction', 'asc'));
        }

        return response()->json([
            'books' => $query->get(),
            'total' => Book::count()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'nullable|integer|min:0|max:' . date('Y'),
            'genre' => 'required|string|max:100',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $book = Book::create($request->all());
        return response()->json($book, 201);
    }

    public function update(Request $request, Book $book)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'nullable|integer|min:0|max:' . date('Y'),
            'genre' => 'required|string|max:100',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $book->update($request->all());
        return response()->json($book);
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(null, 204);
    }
    public function show(Book $book)
{
    return response()->json($book);
}
}