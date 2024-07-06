<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Book;
use App\Http\Requests\StoreBookRequest;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::all();

        return response()->json([
            'status' => true,
            'books' => $books
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $book = Book::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Book successfully created!',
            'book' => $book
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        // TODO: Update book only if the auth user is the book's author
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        // TODO: Destroy book only if the auth user is the book's author
    }
}
